<?php
        require '../../models/getModel.php'; 
        if(strlen($_FILES['hinh_anh']['name'])>0){
            $status = "";
            // $upload_dir = "./uploads/";
            // $ten_tap_tin = $_FILES['hinh_anh']['name'];
            // move_uploaded_file($_FILES['hinh_anh']['tmp_name'],$upload_dir.$ten_tap_tin);
            $id_phieu  = $_POST['id_phieu'];
            $tmp_name = $_FILES['hinh_anh']['tmp_name'];
            
            // Sử dụng hàm nén ảnh từ model thay vì lưu base64 nguyên gốc
            $base64_img = $minhchung->minhchung__Compress_Image($tmp_name, 800, 60);
            
            if ($base64_img) {
                // Đẩy lên Google Drive
                $file_name = "Web_Phieu" . $id_phieu . "_" . time() . ".jpg";
                $folder_name = "PhieuRenLuyen_MinhChung"; 
                // Cố gắng lấy tên thư mục theo Đợt nếu có thể
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
                if ($phieu) {
                    $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($phieu->id_lop_ap_dung);
                    if ($lop_ap_dung) {
                        $dot_cham = $dotchamdiem->dotchamdiem__Get_By_Id($lop_ap_dung->id_dot);
                        if ($dot_cham) {
                            $sv = $sinhvien->sinhvien__Get_By_Id($phieu->id_sinh_vien);
                            $ten_sv = $sv ? $sv->ten_sinh_vien : "";
                            $mssv = $sv ? $sv->ma_sinh_vien : "Unknown";
                            
                            $base_folder = preg_replace('/[^A-Za-z0-9\-\_]/', '', str_replace(' ', '_', $dot_cham->ten_hoc_ky . "_" . $dot_cham->ten_nam_hoc));
                            $ten_sv_clean = preg_replace('/[^A-Za-z0-9\-\_\s]/', '', $ten_sv);
                            
                            $folder_name = $base_folder . "/" . $mssv . "_" . str_replace(' ', '', $ten_sv_clean);
                        }
                    }
                }

                $fileId = $minhchung->minhchung__Upload_Google_Drive($base64_img, $file_name, $folder_name);
                
                if ($fileId) {
                    $full_url = "https://drive.google.com/uc?id=" . $fileId;
                    // Tải lên chung không có id_muc, gán mặc định NULL
                    $status .= $minhchung->minhchung__Add($id_phieu, $full_url, date("Y-m-d H:i:s"), null);
                }
            }
        }
?>