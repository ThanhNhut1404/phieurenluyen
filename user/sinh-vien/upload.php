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
                // Tải lên chung không có id_muc, gán mặc định NULL
                $status .= $minhchung->minhchung__Add($id_phieu, $base64_img, date("Y-m-d H:i:s"), null);
            }
        }
?>