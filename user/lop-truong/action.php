<?php
    session_start();
    if (!isset($_SESSION['lt'])) {
        header("location: ../../auth/");
        exit();
    }
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
                
                // BẮT ĐẦU XỬ LÝ UPLOAD/DELETE (Đồng bộ với sinh viên)
                if (isset($_FILES['minh_chung_muc'])) {
                    $sv_target = $sinhvien->sinhvien__Get_By_Id($phieu->id_sinh_vien);
                    $ten_sv = $sv_target ? $sv_target->ten_sinh_vien : "";
                    $mssv = $sv_target ? $sv_target->ma_sinh_vien : "Unknown";
                    $base_folder = preg_replace('/[^A-Za-z0-9\-\_]/', '', str_replace(' ', '_', $dot->ten_hoc_ky . "_" . $dot->ten_nam_hoc));
                    $ten_sv_clean = preg_replace('/[^A-Za-z0-9\-\_\s]/', '', $ten_sv);
                    $folder_name = $base_folder . "/" . $mssv . "_" . str_replace(' ', '', $ten_sv_clean);

                    $files = $_FILES['minh_chung_muc'];
                    $upload_requests = [];
                    $upload_mappings = [];
                    
                    $order_counter = [];
                    require_once '../../models/model/muc.php';
                    $muc = new muc();

                    if (isset($files['name']) && is_array($files['name'])) {
                        foreach ($files['name'] as $id_muc => $file_names) {
                            if (!isset($order_counter[$id_muc])) $order_counter[$id_muc] = 0;
                            foreach ($file_names as $index => $name) {
                                if ($files['error'][$id_muc][$index] == UPLOAD_ERR_OK && $name != "") {
                                    $order_counter[$id_muc]++;
                                    $order = $order_counter[$id_muc];
                                    
                                    $tmp_name = $files['tmp_name'][$id_muc][$index];
                                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                    
                                    $file_content = file_get_contents($tmp_name);
                                    $base64_file = base64_encode($file_content);
                                    
                                    if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                                         $compressed_base64 = $minhchung->minhchung__Compress_Image($tmp_name, 1024, 70);
                                         if ($compressed_base64) {
                                             $base64_file = $compressed_base64;
                                         } else {
                                             $base64_file = "data:image/" . $ext . ";base64," . $base64_file;
                                         }
                                    } else {
                                         $base64_file = "data:application/pdf;base64," . $base64_file;
                                    }

                                    $muc_info = $muc->muc__Get_By_Id($id_muc);
                                    $ten_muc_clean = "Muc" . $id_muc;
                                    if ($muc_info) {
                                        $cleaned = preg_replace('/[^A-Za-z0-9\-\_\s]/', '', $muc_info->ten_muc);
                                        if (!empty(trim($cleaned))) {
                                            $ten_muc_clean = trim($cleaned);
                                        }
                                    }
                                    $file_name = $mssv . "_" . $ten_muc_clean . "_" . $order . "_" . time() . ".jpg";
                                    if ($ext == 'pdf') $file_name = str_replace('.jpg', '.pdf', $file_name);

                                    $upload_requests[] = [
                                        'file_name' => $file_name,
                                        'folder_name' => $folder_name,
                                        'base64' => $base64_file
                                    ];
                                    $upload_mappings[] = $id_muc;
                                }
                            }
                        }
                    }
                    
                    if (!empty($upload_requests)) {
                        $uploaded_fileIds = $minhchung->minhchung__Upload_Google_Drive_Multi($upload_requests);
                        foreach ($uploaded_fileIds as $idx => $fileId) {
                            if ($fileId) {
                                $id_muc = $upload_mappings[$idx];
                                $full_url = "https://drive.google.com/uc?id=" . $fileId;
                                $minhchung->minhchung__Add($id_phieu, $full_url, date("Y-m-d H:i:s"), $id_muc, 0, null);
                            }
                        }
                    }
                }

                if (isset($_POST['delete_minhchung']) && is_array($_POST['delete_minhchung'])) {
                    foreach ($_POST['delete_minhchung'] as $id_minh_chung_to_delete) {
                        $mc = $minhchung->minhchung__Get_By_Id($id_minh_chung_to_delete);
                        if ($mc && $mc->id_phieu == $id_phieu) {
                            $old_fileId = $mc->hinh_anh;
                            if (strlen($old_fileId) < 100 || strpos($old_fileId, 'https://') === 0) {
                                $drive_fileId = $old_fileId;
                                if (strpos($old_fileId, 'id=') !== false) {
                                    $drive_fileId = explode('id=', $old_fileId)[1];
                                }
                                $minhchung->minhchung__Delete_Google_Drive($drive_fileId);
                            }
                            $minhchung->minhchung__Delete($id_minh_chung_to_delete);
                        }
                    }
                }
                // KẾT THÚC XỬ LÝ UPLOAD/DELETE

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