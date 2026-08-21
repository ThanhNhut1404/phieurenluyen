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
                
                // Nhá»±t sá»­a: ThÃªm rÃ ng buá»™c báº£o máº­t backend cho Cá»‘ váº¥n há»c táº­p
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                // Kiá»ƒm tra lá»›p Ã¡p dá»¥ng cá»§a phiáº¿u
                $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($phieu->id_lop_ap_dung);
                if (!$lop_ap_dung) {
                    header("location: $href&status=failed");
                    exit();
                }

                // Kiá»ƒm tra xem Ä‘á»£t pháº£i Ä‘ang má»Ÿ
                // Nhá»±t sá»­a: Sá»­a lá»—i láº¥y id_dot tá»« phiáº¿u (phiáº¿u khÃ´ng cÃ³ id_dot, pháº£i láº¥y tá»« lá»›p Ã¡p dá»¥ng) vÃ  bá» kiá»ƒm tra trang_thai == 4
                $dot = $dotchamdiem->dotchamdiem__Get_By_Id($lop_ap_dung->id_dot);
                if (!$dot || $dot->trang_thai == 0) {
                    header("location: $href&status=failed");
                    exit();
                }

                // Nhá»±t sá»­a: Kiá»ƒm tra xem Ban cháº¥p hÃ nh ÄoÃ n khoa Ä‘Ã£ cháº¥m Ä‘iá»ƒm chÆ°a
                if (empty($phieu->kq_btdk)) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                $id_lop_hoc_target = $lop_ap_dung->id_lop_hoc;
                $phancong_list = $phancong->phancong__Get_By_Id_Giang_Vien_All($_SESSION['gv']->id_nguoi_dung);
                $is_assigned = false;
                foreach ($phancong_list as $pc) {
                    if ($pc->id_lop_hoc == $id_lop_hoc_target) {
                        $is_assigned = true;
                        break;
                    }
                }
                
                if (!$is_assigned) {
                    header("location: $href&status=failed");
                    exit();
                }

                $kq_gv = $_POST["kq_gv"];
                $kq = "";
                foreach($kq_gv as $item){
                    $val = ($item === "") ? "0" : $item;
                    $kq .= $val."|";
                }
                
                // QuÃ¢n sá»­a: Thá»±c hiá»‡n cáº­p nháº­t Ä‘iá»ƒm cá»§a cá»‘ váº¥n há»c táº­p vÃ  chuyá»ƒn hÆ°á»›ng thÃ nh cÃ´ng
                $phieuchamdiem->phieuchamdiem__Update_Kq_Gv($id_phieu, rtrim($kq, "|"));
                header("location: $href&status=success&msg=" . urlencode("Cáº­p nháº­t Ä‘iá»ƒm Ä‘Ã¡nh giÃ¡ thÃ nh cÃ´ng!"));
                break; 
        }
    }
?>
