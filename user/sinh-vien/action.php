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

                // Nhựt sửa: Thêm bảo mật kiểm tra chủ sở hữu phiếu và đợt chấm điểm đang mở ở backend
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
                if (!$phieu || $phieu->id_sinh_vien != $_SESSION['sv']->id_nguoi_dung) {
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
                    $kq .= $item."|";
                }
                $status .= $phieuchamdiem->phieuchamdiem__Update_Kq_Sv($id_phieu, rtrim($kq, "|"));

                // Xử lý nén và lưu ảnh minh chứng theo từng mục
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

                // Nhựt sửa: Thực hiện cập nhật điểm tự chấm của sinh viên và chuyển hướng thành công
                header("location: $href&status=success");
                break; 

           
        }
    }
?>