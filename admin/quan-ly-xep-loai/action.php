<?php

    session_start();
    require '../../models/getModel.php';

    // Nhá»±t sá»­a lá»—i: DÃ¹ng chung redirect cho cÃ¡c lá»—i validate/rÃ ng buá»™c cá»§a Quáº£n lÃ½ Xáº¿p loáº¡i.
    function xep_loai__Redirect($status) {
        header('location: ../index.php?page=quan-ly-xep-loai&status=' . $status);
        exit();
    }

    function xep_loai__Valid_Csrf() {
        // Nhá»±t sá»­a lá»—i: Kiá»ƒm tra CSRF token cho Add/Update Xáº¿p loáº¡i.
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    function xep_loai__Valid_Csrf_Get() {
        // Nhá»±t sá»­a lá»—i: Kiá»ƒm tra CSRF token cho Delete Xáº¿p loáº¡i Ä‘ang gá»i qua URL.
        return isset($_GET['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_GET['csrf_token']);
    }

    function xep_loai__Rotate_Csrf_Token() {
        // Nhá»±t sá»­a lá»—i: Äá»•i CSRF token sau thao tÃ¡c thÃ nh cÃ´ng Ä‘á»ƒ giáº£m rá»§i ro submit láº¡i.
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    // Nhá»±t sá»­a lá»—i: Kiá»ƒm tra id_xep_loai pháº£i lÃ  sá»‘ nguyÃªn dÆ°Æ¡ng trÆ°á»›c khi update/delete.
    function xep_loai__Is_Positive_Integer($value) {
        return preg_match('/^[1-9][0-9]*$/', $value);
    }

    // Nhá»±t sá»­a lá»—i: can_duoi, can_tren, ha_bac pháº£i lÃ  sá»‘ vÃ  khÃ´ng Ä‘Æ°á»£c rá»—ng.
    function xep_loai__Is_Number($value) {
        // Nhá»±t sá»­a lá»—i: Äiá»ƒm rÃ¨n luyá»‡n vÃ  háº¡ báº­c Ä‘ang xá»­ lÃ½ theo sá»‘ nguyÃªn, khÃ´ng nháº­n sá»‘ tháº­p phÃ¢n hoáº·c dáº¡ng 1e2.
        return preg_match('/^[0-9]+$/', trim($value));
    }

    // Nhá»±t sá»­a lá»—i: Validate server cho tÃªn, thang Ä‘iá»ƒm 0-100 vÃ  Ä‘iá»ƒm háº¡ báº­c 10-15.
    function xep_loai__Validate_Data($ten_xep_loai, $can_duoi, $can_tren, $ha_bac) {
        if (trim($ten_xep_loai) == "") {
            return false;
        }
        if (!xep_loai__Is_Number($can_duoi) || !xep_loai__Is_Number($can_tren) || !xep_loai__Is_Number($ha_bac)) {
            return false;
        }
        $can_duoi = (float)$can_duoi;
        $can_tren = (float)$can_tren;
        $ha_bac = (float)$ha_bac;

        return $can_duoi >= 0 && $can_duoi <= $can_tren && $can_tren <= 100 && $ha_bac >= 10 && $ha_bac <= 15;
    }

    function xep_loai__Has_Dot_Dang_Dien_Ra($dotchamdiem) {
        // Nhá»±t sá»­a lá»—i: KhÃ´ng cho thay Ä‘á»•i cáº¥u hÃ¬nh Xáº¿p loáº¡i khi Ä‘ang cÃ³ Äá»£t cháº¥m Ä‘iá»ƒm diá»…n ra.
        return $dotchamdiem->dotchamdiem__Has_Dang_Dien_Ra(date('Y-m-d'));
    }
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                // Nhá»±t sá»­a lá»—i: Cháº·n Add khi CSRF token khÃ´ng há»£p lá»‡.
                if (!xep_loai__Valid_Csrf()) {
                    xep_loai__Redirect('csrf');
                }

                // Nhá»±t sá»­a lá»—i: KhÃ´ng cho thÃªm Xáº¿p loáº¡i khi Ä‘ang cÃ³ Äá»£t cháº¥m Ä‘iá»ƒm diá»…n ra Ä‘á»ƒ trÃ¡nh áº£nh hÆ°á»Ÿng cÃ¡ch tÃ­nh hiá»‡n táº¡i.

                if (xep_loai__Has_Dot_Dang_Dien_Ra($dotchamdiem)) {
                    xep_loai__Redirect('active-dot-xep-loai');
                }

                $ten_xep_loai = isset($_POST['ten_xep_loai']) ? trim($_POST['ten_xep_loai']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? $_POST['ghi_chu'] : "";
                $can_tren = isset($_POST['can_tren']) ? trim($_POST['can_tren']) : "";
                $can_duoi = isset($_POST['can_duoi']) ? trim($_POST['can_duoi']) : "";
                $ha_bac = isset($_POST['ha_bac']) ? trim($_POST['ha_bac']) : "";
                // Nhá»±t sá»­a lá»—i: Chuáº©n hÃ³a tÃªn xáº¿p loáº¡i trÆ°á»›c khi validate, kiá»ƒm tra trÃ¹ng vÃ  lÆ°u.
                $ten_xep_loai = $xeploai->xeploai__Normalize_Name($ten_xep_loai);

                // Nhá»±t sá»­a lá»—i: KhÃ´ng lÆ°u xáº¿p loáº¡i khi tÃªn/Ä‘iá»ƒm/háº¡ báº­c khÃ´ng há»£p lá»‡.
                if (!xep_loai__Validate_Data($ten_xep_loai, $can_duoi, $can_tren, $ha_bac)) {
                    xep_loai__Redirect('invalid');
                }

                try {
                    // Nhá»±t sá»­a lá»—i: Bá»c kiá»ƒm tra trÃ¹ng/khoáº£ng Ä‘iá»ƒm vÃ  Add trong transaction Ä‘á»ƒ trÃ¡nh ghi dá»Ÿ dang.
                    $xeploai->connect->beginTransaction();
                    $xeploai->xeploai__Lock_All();

                    // Nhá»±t sá»­a lá»—i: KhÃ´ng cho thÃªm xáº¿p loáº¡i trÃ¹ng tÃªn.
                    if ($xeploai->xeploai__Check_Name($ten_xep_loai)) {
                        $xeploai->connect->rollBack();
                        xep_loai__Redirect('duplicate-name');
                    }

                    // Nhá»±t sá»­a lá»—i: KhÃ´ng cho thÃªm khoáº£ng Ä‘iá»ƒm chá»“ng lÃªn xáº¿p loáº¡i khÃ¡c.
                    if ($xeploai->xeploai__Check_Khoang_Diem_Ton_Tai($can_duoi, $can_tren)) {
                        $xeploai->connect->rollBack();
                        xep_loai__Redirect('overlap-xep-loai');
                    }

                    $status = $xeploai->xeploai__Add($ten_xep_loai, $can_tren, $can_duoi, $ha_bac, $ghi_chu);
                    if($status !=0 ){
                        $xeploai->connect->commit();
                        xep_loai__Rotate_Csrf_Token();
                        xep_loai__Redirect('success');
                    }else{
                        $xeploai->connect->rollBack();
                        xep_loai__Redirect('failed');
                    }
                } catch (Throwable $e) {
                    if ($xeploai->connect->inTransaction()) {
                        // Nhá»±t sá»­a lá»—i: Rollback toÃ n bá»™ náº¿u Add Xáº¿p loáº¡i lá»—i giá»¯a chá»«ng.
                        $xeploai->connect->rollBack();
                    }
                    xep_loai__Redirect('failed');
                }
                
                break;
            case 'update':

                // Nhá»±t sá»­a lá»—i: Cháº·n Update khi CSRF token khÃ´ng há»£p lá»‡.
                if (!xep_loai__Valid_Csrf()) {
                    xep_loai__Redirect('csrf');
                }

                // Nhá»±t sá»­a lá»—i: KhÃ´ng cho cáº­p nháº­t Xáº¿p loáº¡i khi Ä‘ang cÃ³ Äá»£t cháº¥m Ä‘iá»ƒm diá»…n ra Ä‘á»ƒ káº¿t quáº£ Ä‘ang xá»­ lÃ½ khÃ´ng bá»‹ Ä‘á»•i cáº¥u hÃ¬nh.
                if (xep_loai__Has_Dot_Dang_Dien_Ra($dotchamdiem)) {
                    xep_loai__Redirect('active-dot-xep-loai');
                }

                $id_xep_loai = isset($_POST['id_xep_loai']) ? trim($_POST['id_xep_loai']) : "";
                $ten_xep_loai = isset($_POST['ten_xep_loai']) ? trim($_POST['ten_xep_loai']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? $_POST['ghi_chu'] : "";
                $can_tren = isset($_POST['can_tren']) ? trim($_POST['can_tren']) : "";
                $can_duoi = isset($_POST['can_duoi']) ? trim($_POST['can_duoi']) : "";
                $ha_bac = isset($_POST['ha_bac']) ? trim($_POST['ha_bac']) : "";
                // Nhá»±t sá»­a lá»—i: Chuáº©n hÃ³a tÃªn xáº¿p loáº¡i trÆ°á»›c khi validate, kiá»ƒm tra trÃ¹ng vÃ  lÆ°u.
                $ten_xep_loai = $xeploai->xeploai__Normalize_Name($ten_xep_loai);

                // Nhá»±t sá»­a lá»—i: KhÃ´ng update khi id_xep_loai khÃ´ng tá»“n táº¡i.
                if (!xep_loai__Is_Positive_Integer($id_xep_loai) || !$xeploai->xeploai__Get_By_Id($id_xep_loai)) {
                    xep_loai__Redirect('not-found');
                }

                // Nhựt sửa lỗi: Kết quả cũ trong ketquaxeploai là snapshot nên sau khi Đợt kết thúc vẫn cho phép cập nhật Xếp loại.

                // Nhá»±t sá»­a lá»—i: KhÃ´ng cáº­p nháº­t xáº¿p loáº¡i khi tÃªn/Ä‘iá»ƒm/háº¡ báº­c khÃ´ng há»£p lá»‡.
                if (!xep_loai__Validate_Data($ten_xep_loai, $can_duoi, $can_tren, $ha_bac)) {
                    xep_loai__Redirect('invalid');
                }

                try {
                    // Nhá»±t sá»­a lá»—i: Bá»c kiá»ƒm tra trÃ¹ng/khoáº£ng Ä‘iá»ƒm vÃ  Update trong transaction Ä‘á»ƒ trÃ¡nh ghi dá»Ÿ dang.
                    $xeploai->connect->beginTransaction();
                    $xeploai->xeploai__Lock_All();

                    // Nhá»±t sá»­a lá»—i: Kiá»ƒm tra láº¡i id vÃ  dá»¯ liá»‡u sá»­ dá»¥ng trong transaction trÆ°á»›c khi Update.
                    if (!$xeploai->xeploai__Get_By_Id($id_xep_loai)) {
                        $xeploai->connect->rollBack();
                        xep_loai__Redirect('not-found');
                    }

                    // Nhá»±t sá»­a lá»—i: KhÃ´ng cho cáº­p nháº­t tÃªn xáº¿p loáº¡i trÃ¹ng vá»›i báº£n ghi khÃ¡c.
                    if ($xeploai->xeploai__Check_Name_Update($id_xep_loai, $ten_xep_loai)) {
                        $xeploai->connect->rollBack();
                        xep_loai__Redirect('duplicate-name');
                    }

                    // Nhá»±t sá»­a lá»—i: Khi update pháº£i loáº¡i trá»« chÃ­nh báº£n ghi Ä‘ang sá»­a lÃºc kiá»ƒm tra chá»“ng khoáº£ng Ä‘iá»ƒm.
                    if ($xeploai->xeploai__Check_Khoang_Diem_Ton_Tai($can_duoi, $can_tren, $id_xep_loai)) {
                        $xeploai->connect->rollBack();
                        xep_loai__Redirect('overlap-xep-loai');
                    }

                    $status = $xeploai->xeploai__Update($id_xep_loai, $ten_xep_loai, $can_tren, $can_duoi, $ha_bac, $ghi_chu);
                    if($status !=0 ){
                        $xeploai->connect->commit();
                        xep_loai__Rotate_Csrf_Token();
                        xep_loai__Redirect('success');
                    }else{
                        $xeploai->connect->rollBack();
                        xep_loai__Redirect('failed');
                    }
                } catch (Throwable $e) {
                    if ($xeploai->connect->inTransaction()) {
                        // Nhá»±t sá»­a lá»—i: Rollback toÃ n bá»™ náº¿u Update Xáº¿p loáº¡i lá»—i giá»¯a chá»«ng.
                        $xeploai->connect->rollBack();
                    }
                    xep_loai__Redirect('failed');
                }
                
                break;

            case 'delete':

                $id_xep_loai = isset($_GET['id_xep_loai']) ? trim($_GET['id_xep_loai']) : "";
                // Nhá»±t sá»­a lá»—i: Cháº·n Delete khi CSRF token khÃ´ng há»£p lá»‡.
                if (!xep_loai__Valid_Csrf_Get()) {
                    xep_loai__Redirect('csrf');
                }

                // Nhá»±t sá»­a lá»—i: KhÃ´ng cho xÃ³a Xáº¿p loáº¡i khi Ä‘ang cÃ³ Äá»£t cháº¥m Ä‘iá»ƒm diá»…n ra Ä‘á»ƒ trÃ¡nh máº¥t cáº¥u hÃ¬nh Ä‘ang dÃ¹ng.
                if (xep_loai__Has_Dot_Dang_Dien_Ra($dotchamdiem)) {
                    xep_loai__Redirect('active-dot-xep-loai');
                }

                // Nhá»±t sá»­a lá»—i: KhÃ´ng delete khi id_xep_loai khÃ´ng tá»“n táº¡i.
                if (!xep_loai__Is_Positive_Integer($id_xep_loai) || !$xeploai->xeploai__Get_By_Id($id_xep_loai)) {
                    xep_loai__Redirect('not-found');
                }

                // Nhá»±t sá»­a lá»—i: Xáº¿p loáº¡i Ä‘ang Ä‘Æ°á»£c ketquaxeploai sá»­ dá»¥ng thÃ¬ khÃ´ng Ä‘Æ°á»£c xÃ³a.
                if ($xeploai->xeploai__Is_Used_In_Ketquaxeploai($id_xep_loai)) {
                    xep_loai__Redirect('related-xep-loai');
                }

                try {
                    // Nhá»±t sá»­a lá»—i: Bá»c Delete trong transaction Ä‘á»ƒ kiá»ƒm tra cha-con vÃ  xÃ³a nguyÃªn tá»­.
                    $xeploai->connect->beginTransaction();
                    $xeploai->xeploai__Lock_All();

                    if (!$xeploai->xeploai__Get_By_Id($id_xep_loai)) {
                        $xeploai->connect->rollBack();
                        xep_loai__Redirect('not-found');
                    }
                    if ($xeploai->xeploai__Is_Used_In_Ketquaxeploai($id_xep_loai)) {
                        $xeploai->connect->rollBack();
                        xep_loai__Redirect('related-xep-loai');
                    }

                    $status = $xeploai->xeploai__Delete($id_xep_loai);
                    if($status !=0 ){
                        $xeploai->connect->commit();
                        xep_loai__Rotate_Csrf_Token();
                        xep_loai__Redirect('success');
                    }else{
                        $xeploai->connect->rollBack();
                        xep_loai__Redirect('failed');
                    }
                } catch (Throwable $e) {
                    if ($xeploai->connect->inTransaction()) {
                        // Nhá»±t sá»­a lá»—i: Rollback toÃ n bá»™ náº¿u Delete Xáº¿p loáº¡i lá»—i giá»¯a chá»«ng.
                        $xeploai->connect->rollBack();
                    }
                    xep_loai__Redirect('failed');
                }
                
                break;
        }
    }

?>
