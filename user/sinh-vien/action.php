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

                if($status != "0" ){
                    header("location: $href&status=success");
                }else{
                    header("location: $href&status=failed");
                }
                break; 

           
        }
    }
?>