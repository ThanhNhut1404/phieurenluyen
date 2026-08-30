<?php
    session_start();
    $href = $_SERVER["HTTP_REFERER"];
    if(strlen(strpos($href, "&status")) > 0){
        $href = explode("&status", $href)[0];
    }
    require "../../models/getModel.php";
    
    if (isset($_GET["req"])){
        switch($_GET["req"]){
            case "add":
                $status = 0;
                $id_phieu = $_POST["id_phieu"];
                
                // Nhựt sửa: Kiểm tra quyền lớp trưởng chỉ được chấm cho sinh viên cùng lớp & đợt đang mở
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                $sv_target = $sinhvien->sinhvien__Get_By_Id($phieu->id_sinh_vien);
                $lt_info = $sinhvien->sinhvien__Get_By_Id($_SESSION['lt']->id_nguoi_dung);
                if (!$sv_target || !$lt_info || $sv_target->id_lop_hoc != $lt_info->id_lop_hoc) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                // Quân sửa: Ngăn chặn lớp trưởng tự chấm điểm cho mình ở chế độ chấm cho lớp (req=add)
                if ($sv_target->id_sinh_vien == $lt_info->id_sinh_vien) {
                    header("location: $href&status=failed");
                    exit();
                }

                $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($phieu->id_lop_ap_dung);
                if (!$lop_ap_dung) {
                    header("location: $href&status=failed");
                    exit();
                }
                $dot = $dotchamdiem->dotchamdiem__Get_By_Id($lop_ap_dung->id_dot);
                if (!$dot) {
                    header("location: $href&status=failed");
                    exit();
                }
                $is_ended = (strtotime(date('Y-m-d')) > strtotime($dot->thoi_gian_ket_thuc));
                if ($is_ended && $dot->trang_thai == 0) {
                    header("location: $href&status=failed");
                    exit();
                }

                $kq_lt_bt = $_POST["kq_lt_bt"];
                $kq = "";
                foreach($kq_lt_bt as $item){
                    $val = ($item === "") ? "0" : $item;
                    $kq .= $val."|";
                }
                
                // Nhựt sửa: Thực hiện cập nhật điểm của lớp trưởng và chuyển hướng thành công
                $phieuchamdiem->phieuchamdiem__Update_Kq_LTBT($id_phieu, rtrim($kq, "|"));
                header("location: $href&status=success&msg=" . urlencode("Cập nhật điểm đánh giá thành công!"));
                break; 

            case "add_sv":
                $status = 0;
                $id_phieu = $_POST["id_phieu"];

                // Nhựt sửa: Kiểm tra quyền chấm hộ lớp trưởng và đợt đang mở
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                $sv_target = $sinhvien->sinhvien__Get_By_Id($phieu->id_sinh_vien);
                $lt_info = $sinhvien->sinhvien__Get_By_Id($_SESSION['lt']->id_nguoi_dung);
                if (!$sv_target || !$lt_info || $sv_target->id_lop_hoc != $lt_info->id_lop_hoc) {
                    header("location: $href&status=failed");
                    exit();
                }
                $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($phieu->id_lop_ap_dung);
                if (!$lop_ap_dung) {
                    header("location: $href&status=failed");
                    exit();
                }
                $dot = $dotchamdiem->dotchamdiem__Get_By_Id($lop_ap_dung->id_dot);
                if (!$dot || $dot->trang_thai == 0) {
                    header("location: $href&status=failed");
                    exit();
                }

                $kq_sv = $_POST["kq_sv"];
                $kq = "";
                foreach($kq_sv as $item){
                    $val = ($item === "") ? "0" : $item;
                    $kq .= $val."|";
                }
                
                // Xử lý nén và lưu ảnh minh chứng theo từng mục khi lớp trưởng chấm hộ
                if (isset($_FILES['minh_chung_muc'])) {
                    $files = $_FILES['minh_chung_muc'];
                    if (isset($files['name']) && is_array($files['name'])) {
                        foreach ($files['name'] as $id_muc => $file_names) {
                            foreach ($file_names as $index => $name) {
                                if ($files['error'][$id_muc][$index] == UPLOAD_ERR_OK && $name != "") {
                                    $tmp_name = $files['tmp_name'][$id_muc][$index];
                                    
                                    // Gọi hàm nén ảnh (max width 800, quality 60)
                                    $base64_img = $minhchung->minhchung__Compress_Image($tmp_name, 800, 60);
                                    
                                    if ($base64_img) {
                                        // Thêm vào database với id_muc
                                        $minhchung->minhchung__Add($id_phieu, $base64_img, date("Y-m-d H:i:s"), $id_muc);
                                    }
                                }
                            }
                        }
                    }
                }

                // Xử lý xóa các minh chứng đã đánh dấu
                if (isset($_POST['delete_minhchung']) && is_array($_POST['delete_minhchung'])) {
                    foreach ($_POST['delete_minhchung'] as $id_minh_chung_to_delete) {
                        // Xác minh bảo mật: minh chứng này phải thuộc về phiếu đang chấm
                        $mc = $minhchung->minhchung__Get_By_Id($id_minh_chung_to_delete);
                        if ($mc && $mc->id_phieu == $id_phieu) {
                            $minhchung->minhchung__Delete($id_minh_chung_to_delete);
                        }
                    }
                }

                // Chỉ cập nhật điểm nếu phiếu chưa được nộp (trạng thái = 1)
                // Nếu phiếu đã nộp, sinh viên (lớp trưởng) chỉ được cập nhật minh chứng
                $msg = "Cập nhật thành công";
                if ($phieu->trang_thai == 1) {
                    $phieuchamdiem->phieuchamdiem__Update_Kq_Sv($id_phieu, rtrim($kq, "|"));
                    $phieuchamdiem->phieuchamdiem__Update_Trang_Thai($id_phieu, 2);
                    $msg = "Nộp phiếu đánh giá thành công";
                } else if ($phieu->trang_thai == 2) {
                    $msg = "Cập nhật minh chứng thành công";
                }
                header("location: $href&status=success&msg=" . urlencode($msg));
                break; 
        }
    }
?>