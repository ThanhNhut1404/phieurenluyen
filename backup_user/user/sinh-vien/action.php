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

                // Nhá»±t sá»­a: ThÃªm báº£o máº­t kiá»ƒm tra chá»§ sá»Ÿ há»¯u phiáº¿u vÃ  Ä‘á»£t cháº¥m Ä‘iá»ƒm Ä‘ang má»Ÿ á»Ÿ backend
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

                // Chá»‰ cáº­p nháº­t Ä‘iá»ƒm náº¿u phiáº¿u chÆ°a Ä‘Æ°á»£c ná»™p (tráº¡ng thÃ¡i = 1)
                // Náº¿u phiáº¿u Ä‘Ã£ ná»™p, sinh viÃªn chá»‰ Ä‘Æ°á»£c cáº­p nháº­t minh chá»©ng
                if ($phieu->trang_thai == 1) {
                    $kq_sv = $_POST["kq_sv"];
                    $kq = "";
                    foreach($kq_sv as $item){
                        $val = ($item === "") ? "0" : $item;
                    $kq .= $val."|";
                    }
                    $status .= $phieuchamdiem->phieuchamdiem__Update_Kq_Sv($id_phieu, rtrim($kq, "|"));
                    // Cáº­p nháº­t tráº¡ng thÃ¡i thÃ nh 2 (ÄÃ£ ná»™p, chá» ban cÃ¡n sá»± duyá»‡t)
                    $phieuchamdiem->phieuchamdiem__Update_Trang_Thai($id_phieu, 2);
                }

                // Xá»­ lÃ½ nÃ©n vÃ  lÆ°u áº£nh minh chá»©ng theo tá»«ng má»¥c
                if (isset($_FILES['minh_chung_muc'])) {
                    $files = $_FILES['minh_chung_muc'];
                    if (isset($files['name']) && is_array($files['name'])) {
                        foreach ($files['name'] as $id_muc => $file_names) {
                            foreach ($file_names as $index => $name) {
                                if ($files['error'][$id_muc][$index] == UPLOAD_ERR_OK && $name != "") {
                                    $tmp_name = $files['tmp_name'][$id_muc][$index];
                                    
                                    // Gá»i hÃ m nÃ©n áº£nh (max width 800, quality 60)
                                    $base64_img = $minhchung->minhchung__Compress_Image($tmp_name, 800, 60);
                                    
                                    if ($base64_img) {
                                        // ThÃªm vÃ o database vá»›i id_muc
                                        $minhchung->minhchung__Add($id_phieu, $base64_img, date("Y-m-d H:i:s"), $id_muc);
                                    }
                                }
                            }
                        }
                    }
                }

                // Xá»­ lÃ½ xÃ³a cÃ¡c minh chá»©ng Ä‘Ã£ Ä‘Ã¡nh dáº¥u
                if (isset($_POST['delete_minhchung']) && is_array($_POST['delete_minhchung'])) {
                    foreach ($_POST['delete_minhchung'] as $id_minh_chung_to_delete) {
                        // XÃ¡c minh báº£o máº­t: minh chá»©ng nÃ y pháº£i thuá»™c vá» phiáº¿u Ä‘ang cháº¥m
                        $mc = $minhchung->minhchung__Get_By_Id($id_minh_chung_to_delete);
                        if ($mc && $mc->id_phieu == $id_phieu) {
                            $minhchung->minhchung__Delete($id_minh_chung_to_delete);
                        }
                    }
                }

                // Nhá»±t sá»­a: Thá»±c hiá»‡n cáº­p nháº­t Ä‘iá»ƒm tá»± cháº¥m cá»§a sinh viÃªn vÃ  chuyá»ƒn hÆ°á»›ng thÃ nh cÃ´ng
                $msg = "Cáº­p nháº­t thÃ nh cÃ´ng";
                if ($phieu->trang_thai == 1) {
                    $msg = "Ná»™p phiáº¿u Ä‘Ã¡nh giÃ¡ thÃ nh cÃ´ng";
                } else if ($phieu->trang_thai == 2) {
                    $msg = "Cáº­p nháº­t minh chá»©ng thÃ nh cÃ´ng";
                }
                header("location: $href&status=success&msg=" . urlencode($msg));
                break; 

           
        }
    }
?>
