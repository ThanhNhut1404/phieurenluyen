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

                // Lấy id sinh viên từ session
                $id_sinh_vien = "";
                if(isset($_SESSION['sv'])){
                    $id_sinh_vien = $_SESSION['sv']->id_nguoi_dung;
                } else if(isset($_SESSION['lt'])){
                    $id_sinh_vien = $_SESSION['lt']->id_nguoi_dung;
                } else if(isset($_SESSION['bt'])){
                    $id_sinh_vien = $_SESSION['bt']->id_nguoi_dung;
                }

                // Nhựt sửa: Thêm bảo mật kiểm tra chủ sở hữu phiếu và đợt chấm điểm đang mở ở backend
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
                if (!$phieu || $phieu->id_sinh_vien != $id_sinh_vien) {
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
                if ($is_ended || $dot->trang_thai == 0) {
                    header("location: $href&status=failed");
                    exit();
                }

                // Chỉ cập nhật điểm nếu phiếu chưa được nộp (trạng thái = 1)
                // Nếu phiếu đã nộp, sinh viên chỉ được cập nhật minh chứng
                if ($phieu->trang_thai == 1) {
                    $kq_sv = $_POST["kq_sv"];
                    $kq = "";
                    foreach($kq_sv as $item){
                        $val = ($item === "") ? "0" : $item; $kq .= $val."|";
                    }
                    $status .= $phieuchamdiem->phieuchamdiem__Update_Kq_Sv($id_phieu, rtrim($kq, "|"));
                    // Cập nhật trạng thái thành 2 (Đã nộp, chờ ban cán sự duyệt)
                    $phieuchamdiem->phieuchamdiem__Update_Trang_Thai($id_phieu, 2);
                }

                // Xử lý nén và lưu ảnh minh chứng lên Google Drive
                if (isset($_FILES['minh_chung_muc'])) {
                    $files = $_FILES['minh_chung_muc'];
                    $upload_requests = [];
                    $upload_mappings = [];

                    if (isset($files['name']) && is_array($files['name'])) {
                        foreach ($files['name'] as $id_muc => $file_names) {
                            foreach ($file_names as $index => $name) {
                                if ($files['error'][$id_muc][$index] == UPLOAD_ERR_OK && $name != "") {
                                    $tmp_name = $files['tmp_name'][$id_muc][$index];
                                    
                                    // Tạo tên file
                                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                    $mime_type = mime_content_type($tmp_name);
                                    
                                    // Chuyển file thành base64 để gửi lên GAS
                                    $file_content = file_get_contents($tmp_name);
                                    $base64_file = base64_encode($file_content);
                                    
                                    // Nếu là ảnh, có thể nén trước khi lấy base64 (chỉ JPG, PNG)
                                    if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                                         // Sử dụng hàm nén (max width 1024, quality 70)
                                         $compressed_base64 = $minhchung->minhchung__Compress_Image($tmp_name, 1024, 70);
                                         if ($compressed_base64) {
                                             // minhchung__Compress_Image trả về dạng data:image/jpeg;base64,...
                                             $base64_file = explode(',', $compressed_base64)[1];
                                             $mime_type = 'image/jpeg';
                                             $ext = 'jpg';
                                         }
                                    }

                                    $file_name = "Dot_" . $dot->id_dot . "/" . "Phieu_" . $id_phieu . "_Muc_" . $id_muc . "_" . time() . "_" . $index . "." . $ext;

                                    $upload_requests[] = [
                                        'file_name' => $file_name,
                                        'mime_type' => $mime_type,
                                        'base64_data' => $base64_file
                                    ];
                                    $upload_mappings[] = $id_muc;
                                }
                            }
                        }
                    }
                    
                    // Gửi lên Google Drive
                    if (!empty($upload_requests)) {
                        $uploaded_fileIds = $minhchung->minhchung__Upload_Google_Drive_Multi($upload_requests);
                        foreach ($uploaded_fileIds as $idx => $fileId) {
                            if ($fileId) {
                                $id_muc = $upload_mappings[$idx];
                                $full_url = "https://drive.google.com/uc?id=" . $fileId;
                                $minhchung->minhchung__Add($id_phieu, $full_url, date("Y-m-d H:i:s"), $id_muc);
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

                // Nhựt sửa: Thực hiện cập nhật điểm tự chấm của sinh viên và chuyển hướng thành công
                $msg = "Cập nhật thành công";
                if ($phieu->trang_thai == 1) {
                    $msg = "Nộp phiếu đánh giá thành công";
                } else if ($phieu->trang_thai == 2) {
                    $msg = "Cập nhật minh chứng thành công";
                }
                header("location: $href&status=success&msg=" . urlencode($msg));
                break; 

           
        }
    }
?>