<?php

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

                // Nhựt sửa lỗi: Không tổng kết nếu Đợt/Lớp không có Phiếu chấm điểm.
                if ($phieuchamdiem->phieuchamdiem__Count_By_Id_Dot_And_Id_Lop_Hoc($id_dot, $id_lop_hoc) == 0) {
                    header("location: $href&status=invalid");
                    exit();
                }

                // Nhựt sửa lỗi: Không cho sinh kết quả xếp loại khi còn Phiếu chưa có kết quả cố vấn.
                if ($phieuchamdiem->phieuchamdiem__Has_Unscored_Gv_By_Id_Dot_And_Id_Lop_Hoc($id_dot, $id_lop_hoc)) {
                    header("location: $href&status=unscored-phieu");
                    exit();
                }

                // Nhựt sửa lỗi: Không cho tạo trùng kết quả xếp loại cho cùng một Đợt và Lớp học.
                $ket_qua_da_ton_tai = $ketquaxeploai->ketquaxeploai__Get_By_Id_Dot_All($id_dot, $id_lop_hoc);
                if ($ket_qua_da_ton_tai && isset($ket_qua_da_ton_tai->sum) && $ket_qua_da_ton_tai->sum > 0) {
                    header("location: ../index.php?page=quan-ly-ket-qua&id_dot=$id_dot&id_lop_hoc=$id_lop_hoc&status=duplicate-ket-qua");
                    exit();
                }

                $phieuchamdiem__Get_By_Id_Dot_And_Id_Lop_Hoc = $phieuchamdiem->phieuchamdiem__Get_By_Id_Dot_And_Id_Lop_Hoc($id_dot, $id_lop_hoc);

                try {
                    // Nhựt sửa lỗi: Bọc toàn bộ quá trình sinh kết quả trong transaction để tránh ghi dở dang.
                    $ketquaxeploai->connect->beginTransaction();
                    $loi = false;
                    $status_loi = 'failed';

                    foreach($phieuchamdiem__Get_By_Id_Dot_And_Id_Lop_Hoc as $item){
                        if ($ketquaxeploai->ketquaxeploai__Exists_By_Id_Phieu($item->id_phieu)) {
                            $loi = true;
                            $status_loi = 'duplicate-ket-qua';
                            break;
                        }

                        $arr = $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($item->kq_gv);
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

                        if ($ketquaxeploai->ketquaxeploai__Add($item->id_phieu, $xeploai__Get_By_Kq->id_xep_loai, $item->id_sinh_vien, $item->id_lop_hoc, $item->id_dot, $sinhvien__Get_By_Id->ma_sinh_vien, $sinhvien__Get_By_Id->ten_sinh_vien, $lophoc__Get_By_Id->ten_lop_hoc, $sum, $xeploai__Get_By_Kq->ten_xep_loai, date('Y-m-d')) <= 0) {
                            $loi = true;
                            break;
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
                    header("location: ../index.php?page=quan-ly-ket-qua&id_dot=$id_dot&id_lop_hoc=$id_lop_hoc&status=success");
                    exit();
                } catch (Throwable $e) {
                    if ($ketquaxeploai->connect->inTransaction()) {
                        // Nhựt sửa lỗi: Rollback toàn bộ nếu sinh kết quả bị lỗi SQL giữa chừng.
                        $ketquaxeploai->connect->rollBack();
                    }
                    header("location: $href&status=failed");
                    exit();
                }

                break;
        }
    }

?>
