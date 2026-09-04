<?php
    session_start();
    if (!isset($_SESSION['gv'])) {
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
                
                // Nhß╗▒t sß╗¡a: Th├¬m r├áng buß╗Öc bß║úo mß║¡t backend cho Cß╗æ vß║Ñn hß╗ìc tß║¡p
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                // Kiß╗âm tra lß╗¢p ├íp dß╗Ñng cß╗ºa phiß║┐u
                $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($phieu->id_lop_ap_dung);
                if (!$lop_ap_dung) {
                    header("location: $href&status=failed");
                    exit();
                }

                // Kiß╗âm tra xem ─æß╗út phß║úi ─æang mß╗ƒ
                // Nhß╗▒t sß╗¡a: Sß╗¡a lß╗ùi lß║Ñy id_dot tß╗½ phiß║┐u (phiß║┐u kh├┤ng c├│ id_dot, phß║úi lß║Ñy tß╗½ lß╗¢p ├íp dß╗Ñng) v├á bß╗Å kiß╗âm tra trang_thai == 4
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

                // Nhß╗▒t sß╗¡a: Kiß╗âm tra xem Ban chß║Ñp h├ánh ─Éo├án khoa ─æ├ú chß║Ñm ─æiß╗âm ch╞░a
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
                    $val = ($item === "") ? "0" : $item; $kq .= $val."|";
                }
                
                // Qu├ón sß╗¡a: Thß╗▒c hiß╗çn cß║¡p nhß║¡t ─æiß╗âm cß╗ºa cß╗æ vß║Ñn hß╗ìc tß║¡p v├á chuyß╗ân h╞░ß╗¢ng th├ánh c├┤ng
                $phieuchamdiem->phieuchamdiem__Update_Kq_Gv($id_phieu, rtrim($kq, "|"));
                header("location: $href&status=success&msg=" . urlencode("Cß║¡p nhß║¡t ─æiß╗âm ─æ├ính gi├í th├ánh c├┤ng!"));
                break; 

            case "ha_bac":
                $id_ket_qua = $_POST["id_ket_qua"];
                $ly_do = isset($_POST["ly_do"]) ? $_POST["ly_do"] : "Kh├┤ng nß╗Öp phiß║┐u ─æ├ính gi├í (Bß╗ï hß║í bß║¡c)";
                
                $kq_xl = $ketquaxeploai->ketquaxeploai__Get_By_Id($id_ket_qua);
                if (!$kq_xl) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($kq_xl->id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                // ─Éiß╗âm gß╗æc dß╗▒a v├áo ─æiß╗âm cß╗ºa Cß╗æ vß║Ñn
                $kq_gv_arr = $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieu->kq_gv);
                $base_score = $phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua($kq_gv_arr);
                
                $original_xeploai = $xeploai->xeploai__Get_By_Kq($base_score);
                if (!$original_xeploai) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                // Xß╗¡ l├╜ trß╗½ ─æiß╗âm hß║í bß║¡c
                $deduction = $original_xeploai->ha_bac;
                if ($base_score == 100) {
                    $deduction = 11; // Xß╗¡ l├╜ ─æß║╖c biß╗çt theo quy chß║┐ (100 ─æiß╗âm th├¼ trß╗½ 11)
                }
                
                if (strpos($ly_do, '(Bß╗ï hß║í bß║¡c)') !== false) {
                    $ly_do = str_replace('(Bß╗ï hß║í bß║¡c)', '(Trß╗½ ' . $deduction . '─æ)', $ly_do);
                }
                
                $new_score = $base_score - $deduction;
                if ($new_score < 0) $new_score = 0;
                
                $new_xeploai = $xeploai->xeploai__Get_By_Kq($new_score);
                
                $ketquaxeploai->ketquaxeploai__Update_Ha_Bac($id_ket_qua, $new_score, $new_xeploai->ten_xep_loai, date('Y-m-d'), $ly_do);
                header("location: $href&status=success&msg=" . urlencode("Hß║í bß║¡c th├ánh c├┤ng!"));
                break;
                
            case "huy_ha_bac":
                $id_ket_qua = $_POST["id_ket_qua"];
                
                $kq_xl = $ketquaxeploai->ketquaxeploai__Get_By_Id($id_ket_qua);
                if (!$kq_xl) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($kq_xl->id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                // Kh├┤i phß╗Ñc lß║íi ─æiß╗âm gß╗æc
                $kq_gv_arr = $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieu->kq_gv);
                $base_score = $phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua($kq_gv_arr);
                
                $original_xeploai = $xeploai->xeploai__Get_By_Kq($base_score);
                
                $ketquaxeploai->ketquaxeploai__Update_Ha_Bac($id_ket_qua, $base_score, $original_xeploai->ten_xep_loai, date('Y-m-d'), "");
                header("location: $href&status=success&msg=" . urlencode("Hß╗ºy hß║í bß║¡c th├ánh c├┤ng!"));
                break;
        }
    }
?>
