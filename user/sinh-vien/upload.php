<?php
        require '../../models/getModel.php'; 
        if(strlen($_FILES['hinh_anh']['name'])>0){
            $status = "";
            // $upload_dir = "./uploads/";
            // $ten_tap_tin = $_FILES['hinh_anh']['name'];
            // move_uploaded_file($_FILES['hinh_anh']['tmp_name'],$upload_dir.$ten_tap_tin);
            $id_phieu  = $_POST['id_phieu'];
            $img = file_get_contents($_FILES['hinh_anh']['tmp_name']);
            $data = base64_encode($img);
            $base64_img = "data:image/png;base64,".$data;
            $status .= $minhchung->minhchung__Add($id_phieu, $base64_img, date("Y-m-d H:i:s"));
            // echo $status;
        }
?>