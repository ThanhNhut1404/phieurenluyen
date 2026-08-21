

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
                
                // QuÃ¢n sá»­a: Kiá»ƒm tra quyá»n BÃ­ thÆ° Ä‘oÃ n khoa
                if (!isset($_SESSION["btdk"])) {
                    header("location: ../auth/");
                    exit();
                }
                $bithudoankhoa__Get_By_Id = $bithudoankhoa->bithudoankhoa__Get_By_Id($_SESSION["btdk"]->id_nguoi_dung);
                
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                // Kiá»ƒm tra xem sinh viÃªn cÃ³ thuá»™c khoa cá»§a BTDK khÃ´ng
                $sv = $sinhvien->sinhvien__Get_By_Id($phieu->id_sinh_vien);
                $lop = $lophoc->lophoc__Get_By_Id($sv->id_lop_hoc);
                // QuÃ¢n sá»­a: Láº¥y thÃ´ng tin ngÃ nh há»c Ä‘á»ƒ kiá»ƒm tra id_khoa cá»§a lá»›p há»c
                $nganh = $nganhhoc->nganhhoc__Get_By_Id($lop->id_nganh_hoc);
                if ($nganh->id_khoa != $bithudoankhoa__Get_By_Id->id_khoa) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                // Nhá»±t sá»­a: Kiá»ƒm tra xem Ä‘á»£t pháº£i Ä‘ang má»Ÿ á»Ÿ backend
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
                
                // Kiá»ƒm tra xem Lá»›p trÆ°á»Ÿng Ä‘Ã£ cháº¥m chÆ°a
                if (empty($phieu->kq_lt_bt)) {
                    header("location: $href&status=failed");
                    exit();
                }

                $kq_btdk = $_POST["kq_btdk"];
                $kq = "";
                foreach($kq_btdk as $item){
                    $val = ($item === "") ? "0" : $item;
                    $kq .= $val."|";
                }
                
                // QuÃ¢n sá»­a: Thá»±c hiá»‡n cáº­p nháº­t Ä‘iá»ƒm vÃ  chuyá»ƒn hÆ°á»›ng thÃ nh cÃ´ng
                $phieuchamdiem->phieuchamdiem__Update_Kq_BTDK($id_phieu, rtrim($kq, "|"));
                header("location: $href&status=success&msg=" . urlencode("Cáº­p nháº­t Ä‘iá»ƒm Ä‘Ã¡nh giÃ¡ thÃ nh cÃ´ng!"));
                break; 
        }
    }
?>
