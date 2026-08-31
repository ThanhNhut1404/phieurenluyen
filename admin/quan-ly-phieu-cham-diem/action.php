<?php
    // Nhựt sửa lỗi: Thêm session_start và kiểm tra quyền Admin tránh bypass auth.
    session_start();
    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }

    require '../../models/getModel.php';
    $href = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "../index.php?page=quan-ly-phieu-cham-diem";
    if(strlen(strpos($href, "&status")) > 0){
        $href = explode("&status", $href)[0];
    }

    function phieuchamdiem__Is_Positive_Integer($value) {
        // Nhựt sửa lỗi: Validate id_dot/id_lop_hoc trước khi tổng kết để tránh request sai kiểu.
        return preg_match('/^[1-9][0-9]*$/', $value);
    }

    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':
                // Nhựt sửa lỗi: Kiểm tra Token CSRF tránh tấn công giả mạo yêu cầu.
                if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    header("location: $href&status=csrf");
                    exit();
                }

                $status = 0;
                $id_dot = isset($_POST['id_dot']) ? trim($_POST['id_dot']) : "";
                $id_lop_hoc = isset($_POST['id_lop_hoc']) ? trim($_POST['id_lop_hoc']) : "";

                // Nhựt sửa lỗi: Không xử lý tổng kết nếu id_dot/id_lop_hoc sai hoặc lớp không thuộc Đợt.
                if (!phieuchamdiem__Is_Positive_Integer($id_dot) || !phieuchamdiem__Is_Positive_Integer($id_lop_hoc) || !$dotchamdiem->dotchamdiem__Get_By_Id($id_dot) || !$lopapdung->lopapdung__Exists_By_Id_Dot_And_Id_Lop_Hoc($id_dot, $id_lop_hoc)) {
                    header("location: $href&status=invalid");
                    exit();
                }

                // Nhựt sửa lỗi: Chặn tạo kết quả khi hệ thống chưa cấu hình đủ xếp loại.
                if (!$xeploai->xeploai__Check_Du_Khoang_Diem()) {
                    header("location: $href&status=incomplete-xep-loai");
                    exit();
                }

                // Nhựt sửa lỗi: Không cho xử lý nếu đợt chấm điểm chưa kết thúc
                $current_dot = $dotchamdiem->dotchamdiem__Get_By_Id($id_dot);
                if ($current_dot && date('Y-m-d') <= $current_dot->thoi_gian_ket_thuc) {
                    header("location: $href&status=dot-chua-ket-thuc");
                    exit();
                }

                // Nhựt sửa lỗi: Không tổng kết nếu Đợt/Lớp không có Phiếu chấm điểm.
                if ($phieuchamdiem->phieuchamdiem__Count_By_Id_Dot_And_Id_Lop_Hoc($id_dot, $id_lop_hoc) == 0) {
                    header("location: $href&status=invalid");
                    exit();
                }

                // Nhựt sửa lỗi: Cho phép cập nhật lại kết quả xếp loại nếu đã tồn tại thay vì báo lỗi (Cách 1).
                // $ket_qua_da_ton_tai = $ketquaxeploai->ketquaxeploai__Get_By_Id_Dot_All($id_dot, $id_lop_hoc);
                // if ($ket_qua_da_ton_tai && isset($ket_qua_da_ton_tai->sum) && $ket_qua_da_ton_tai->sum > 0) {
                //     header("location: ../index.php?page=quan-ly-ket-qua&id_dot=$id_dot&id_lop_hoc=$id_lop_hoc&status=duplicate-ket-qua");
                //     exit();
                // }

                $phieuchamdiem__Get_By_Id_Dot_And_Id_Lop_Hoc = $phieuchamdiem->phieuchamdiem__Get_By_Id_Dot_And_Id_Lop_Hoc($id_dot, $id_lop_hoc);

                try {
                    // Nhựt sửa lỗi: Bọc toàn bộ quá trình sinh kết quả trong transaction để tránh ghi dở dang.
                    $ketquaxeploai->connect->beginTransaction();
                    $loi = false;
                    $status_loi = 'failed';

                    foreach($phieuchamdiem__Get_By_Id_Dot_And_Id_Lop_Hoc as $item){
                        // Đã bỏ kiểm tra Exists_By_Id_Phieu để cho phép cập nhật kết quả.

                        // Logic lấy điểm fallback từ các role trước nếu role sau chưa chấm
                        $kq_final = trim($item->kq_gv);
                        if (empty($kq_final)) {
                            $kq_final = trim($item->kq_btdk);
                        }
                        if (empty($kq_final)) {
                            $kq_final = trim($item->kq_lt_bt);
                        }
                        if (empty($kq_final)) {
                            $kq_final = trim($item->kq_sv);
                        }

                        $arr = $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($kq_final);
                        $sum = $phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua($arr);
                        $xeploai__Get_By_Kq = $xeploai->xeploai__Get_By_Kq($sum);

                        if (!$xeploai__Get_By_Kq) {
                            $loi = true;
                            $status_loi = 'incomplete-xep-loai';
                            break;
                        }

                        $sinhvien__Get_By_Id = $sinhvien->sinhvien__Get_By_Id($item->id_sinh_vien);
                        $lophoc__Get_By_Id = $lophoc->lophoc__Get_By_Id($item->id_lop_hoc);

                        if (!$sinhvien__Get_By_Id || !$lophoc__Get_By_Id) {
                            $loi = true;
                            break;
                        }

                        $existing_kq = $ketquaxeploai->ketquaxeploai__Get_By_Id_Phieu($item->id_lop_hoc, $id_dot, $item->id_sinh_vien);

                        if ($existing_kq) {
                            $new_score = $sum;
                            $new_xeploai = $xeploai__Get_By_Kq;
                            
                            $ghi_chu_new = $existing_kq->ghi_chu;
                            
                            // Nếu đã có ghi chú Hạ bậc hoặc Không tự đánh giá, tính toán trừ điểm trên nền điểm mới
                            if (stripos($existing_kq->ghi_chu, 'hạ bậc') !== false || stripos($existing_kq->ghi_chu, 'Không tự đánh giá') !== false) {
                                $deduction = $xeploai__Get_By_Kq ? $xeploai__Get_By_Kq->ha_bac : 0;
                                if ($sum == 100) {
                                    $deduction = 11;
                                }
                                $new_score = max(0, $sum - $deduction);
                                $new_xeploai = $xeploai->xeploai__Get_By_Kq($new_score);
                                if (!$new_xeploai) {
                                    $loi = true;
                                    $status_loi = 'incomplete-xep-loai';
                                    break;
                                }
                                
                                // Cập nhật số điểm bị trừ trong ghi chú nếu có
                                if (preg_match('/\(Trừ \d+đ\)/', $existing_kq->ghi_chu)) {
                                    $ghi_chu_new = preg_replace('/\(Trừ \d+đ\)/', '(Trừ ' . $deduction . 'đ)', $existing_kq->ghi_chu);
                                }
                            }
                            
                            if ($ketquaxeploai->ketquaxeploai__Update($existing_kq->id_ket_qua, $item->id_phieu, $new_xeploai->id_xep_loai, $item->id_sinh_vien, $item->id_lop_hoc, $item->id_dot, $sinhvien__Get_By_Id->ma_sinh_vien, $sinhvien__Get_By_Id->ten_sinh_vien, $lophoc__Get_By_Id->ten_lop_hoc, $new_score, $new_xeploai->ten_xep_loai, date('Y-m-d')) === false) {
                                $loi = true;
                                break;
                            }
                            
                            // Lưu lại ghi chú mới nếu có thay đổi
                            if ($ghi_chu_new !== $existing_kq->ghi_chu) {
                                $ketquaxeploai->ketquaxeploai__Update_Ha_Bac($existing_kq->id_ket_qua, $new_score, $new_xeploai->ten_xep_loai, date('Y-m-d'), $ghi_chu_new);
                            }
                        } else {
                            if ($ketquaxeploai->ketquaxeploai__Add($item->id_phieu, $xeploai__Get_By_Kq->id_xep_loai, $item->id_sinh_vien, $item->id_lop_hoc, $item->id_dot, $sinhvien__Get_By_Id->ma_sinh_vien, $sinhvien__Get_By_Id->ten_sinh_vien, $lophoc__Get_By_Id->ten_lop_hoc, $sum, $xeploai__Get_By_Kq->ten_xep_loai, date('Y-m-d')) <= 0) {
                                $loi = true;
                                break;
                            }
                        }

                        $status++;
                    }

                    if ($loi || $status == 0) {
                        if ($ketquaxeploai->connect->inTransaction()) {
                            $ketquaxeploai->connect->rollBack();
                        }
                        header("location: $href&status=$status_loi");
                        exit();
                    }

                    $ketquaxeploai->connect->commit();
                    header("location: ../index.php?page=quan-ly-ket-qua&id_dot=$id_dot&id_lop_hoc=$id_lop_hoc&status=update-success");
                    exit();
                } catch (Throwable $e) {
                    if ($ketquaxeploai->connect->inTransaction()) {
                        // Nhựt sửa lỗi: Rollback toàn bộ nếu sinh kết quả bị lỗi SQL giữa chừng.
                        $ketquaxeploai->connect->rollBack();
                    }
                    header("location: $href&status=update-failed");
                    exit();
                }

                break;
        }
    }

?>
