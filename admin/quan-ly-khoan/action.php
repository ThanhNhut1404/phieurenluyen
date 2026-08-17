<?php

    session_start();
    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }
    require '../../models/getModel.php';

    function khoan__Redirect($status) {
        header('location: ../index.php?page=quan-ly-khoan&status=' . $status);
        exit();
    }

    function khoan__Valid_Csrf() {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    function khoan__Valid_Csrf_Get() {
        return isset($_GET['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_GET['csrf_token']);
    }

    function khoan__Rotate_Csrf_Token() {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    function khoan__Is_Positive_Integer($value) {
        return preg_match('/^[1-9][0-9]*$/', $value);
    }

    function khoan__Is_Number($value) {
        return preg_match('/^[0-9]+$/', trim($value));
    }

    function khoan__Validate_Data($id_dieu, $ten_khoan, $can_tren, $so_luong_muc) {
        if (trim($ten_khoan) == "") {
            return 'invalid-ten';
        }
        if (!khoan__Is_Positive_Integer($id_dieu)) {
            return 'invalid-dieu';
        }
        if ($can_tren !== "" && !khoan__Is_Number($can_tren)) {
            return 'invalid-diem';
        }
        if (!khoan__Is_Positive_Integer($so_luong_muc)) {
            return 'invalid-soluong';
        }
        return 'valid';
    }

    function khoan_store_old_input($context, $id_dieu, $ten_khoan, $ghi_chu, $can_tren, $so_luong_muc, $id_khoan = null) {
        $_SESSION['khoan_old_input'] = array(
            'context' => $context,
            'id_dieu' => $id_dieu,
            'ten_khoan' => $ten_khoan,
            'ghi_chu' => $ghi_chu,
            'can_tren' => $can_tren,
            'so_luong_muc' => $so_luong_muc,
            'id_khoan' => $id_khoan,
        );
    }

    function khoan_clear_old_input() {
        unset($_SESSION['khoan_old_input']);
    }
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':
                if (!khoan__Valid_Csrf()) {
                    khoan__Redirect('csrf');
                }

                $ten_khoan = isset($_POST['ten_khoan']) ? trim($_POST['ten_khoan']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? $_POST['ghi_chu'] : "";
                $can_tren = isset($_POST['can_tren']) ? trim($_POST['can_tren']) : "";
                $id_dieu = isset($_POST['id_dieu']) ? trim($_POST['id_dieu']) : "";
                $so_luong_muc = isset($_POST['so_luong_muc']) ? trim($_POST['so_luong_muc']) : "";

                $validate_status = khoan__Validate_Data($id_dieu, $ten_khoan, $can_tren, $so_luong_muc);
                if ($validate_status !== 'valid') {
                    khoan_store_old_input('add', $id_dieu, $ten_khoan, $ghi_chu, $can_tren, $so_luong_muc);
                    khoan__Redirect($validate_status);
                }

                try {
                    $khoan->connect->beginTransaction();

                    $dieu_info = $dieu->dieu__Get_By_Id($id_dieu);
                    $thu_tu = $dieu_info ? $dieu_info->thu_tu : 1;

                    // Kiểm tra xem Điều đã được sử dụng chưa
                    $khoan__Get_All = $khoan->khoan__Get_All();
                    $used_dieu = [];
                    foreach ($khoan__Get_All as $k) {
                        $used_dieu[] = $k->id_dieu;
                    }
                    if (in_array($id_dieu, $used_dieu)) {
                        $khoan->connect->rollBack();
                        khoan_store_old_input('add', $id_dieu, $ten_khoan, $ghi_chu, $can_tren, $so_luong_muc);
                        khoan__Redirect('duplicate-dieu');
                    }

                    $status = $khoan->khoan__Add($ten_khoan, $ghi_chu, $can_tren, $thu_tu, $id_dieu, $so_luong_muc);
                    if($status !=0 ){
                        $khoan->connect->commit();
                        khoan_clear_old_input();
                        khoan__Rotate_Csrf_Token();
                        khoan__Redirect('success');
                    }else{
                        $khoan->connect->rollBack();
                        khoan_store_old_input('add', $id_dieu, $ten_khoan, $ghi_chu, $can_tren, $so_luong_muc);
                        khoan__Redirect('failed');
                    }
                } catch (Throwable $e) {
                    if ($khoan->connect->inTransaction()) {
                        $khoan->connect->rollBack();
                    }
                    khoan_store_old_input('add', $id_dieu, $ten_khoan, $ghi_chu, $can_tren, $so_luong_muc);
                    khoan__Redirect('failed');
                }
                
                break;
            case 'update':
                if (!khoan__Valid_Csrf()) {
                    khoan__Redirect('csrf');
                }

                $id_khoan = isset($_POST['id_khoan']) ? trim($_POST['id_khoan']) : "";
                $ten_khoan = isset($_POST['ten_khoan']) ? trim($_POST['ten_khoan']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? $_POST['ghi_chu'] : "";
                $can_tren = isset($_POST['can_tren']) ? trim($_POST['can_tren']) : "";
                $id_dieu = isset($_POST['id_dieu']) ? trim($_POST['id_dieu']) : "";
                $so_luong_muc = isset($_POST['so_luong_muc']) ? trim($_POST['so_luong_muc']) : "";

                if (!khoan__Is_Positive_Integer($id_khoan) || !$khoan->khoan__Get_By_Id($id_khoan)) {
                    khoan__Redirect('not-found');
                }

                if ($khoan->khoan__Is_Edit_Locked($id_khoan)) {
                    khoan__Redirect('locked_update');
                }

                $validate_status = khoan__Validate_Data($id_dieu, $ten_khoan, $can_tren, $so_luong_muc);
                if ($validate_status !== 'valid') {
                    khoan_store_old_input('update', $id_dieu, $ten_khoan, $ghi_chu, $can_tren, $so_luong_muc, $id_khoan);
                    khoan__Redirect($validate_status);
                }

                try {
                    $khoan->connect->beginTransaction();

                    if (!$khoan->khoan__Get_By_Id($id_khoan)) {
                        $khoan->connect->rollBack();
                        khoan__Redirect('not-found');
                    }

                    $dieu_info = $dieu->dieu__Get_By_Id($id_dieu);
                    $thu_tu = $dieu_info ? $dieu_info->thu_tu : 1;

                    // Kiểm tra xem Điều đã được sử dụng chưa (ngoại trừ khoản hiện tại)
                    $khoan__Get_All = $khoan->khoan__Get_All();
                    $used_dieu = [];
                    foreach ($khoan__Get_All as $k) {
                        if ($k->id_khoan != $id_khoan) {
                            $used_dieu[] = $k->id_dieu;
                        }
                    }
                    if (in_array($id_dieu, $used_dieu)) {
                        $khoan->connect->rollBack();
                        khoan_store_old_input('update', $id_dieu, $ten_khoan, $ghi_chu, $can_tren, $so_luong_muc, $id_khoan);
                        khoan__Redirect('duplicate-dieu');
                    }

                    $status = $khoan->khoan__Update($id_khoan, $ten_khoan, $ghi_chu, $can_tren, $thu_tu, $id_dieu, $so_luong_muc);
                    if($status !=0 ){
                        $khoan->connect->commit();
                        khoan_clear_old_input();
                        khoan__Rotate_Csrf_Token();
                        khoan__Redirect('success');
                    }else{
                        $khoan->connect->rollBack();
                        khoan_store_old_input('update', $id_dieu, $ten_khoan, $ghi_chu, $can_tren, $so_luong_muc, $id_khoan);
                        khoan__Redirect('failed');
                    }
                } catch (Throwable $e) {
                    if ($khoan->connect->inTransaction()) {
                        $khoan->connect->rollBack();
                    }
                    khoan_store_old_input('update', $id_dieu, $ten_khoan, $ghi_chu, $can_tren, $so_luong_muc, $id_khoan);
                    khoan__Redirect('failed');
                }
                
                break;

            case 'delete':
                if (!khoan__Valid_Csrf_Get()) {
                    khoan__Redirect('csrf');
                }

                $id_khoan = isset($_GET['id_khoan']) ? trim($_GET['id_khoan']) : "";

                if (!khoan__Is_Positive_Integer($id_khoan) || !$khoan->khoan__Get_By_Id($id_khoan)) {
                    khoan__Redirect('not-found');
                }

                if ($khoan->khoan__Is_In_Active_DotChamDiem($id_khoan)) {
                    khoan__Redirect('locked_by_dotchamdiem');
                }

                try {
                    $khoan->connect->beginTransaction();

                    if (!$khoan->khoan__Get_By_Id($id_khoan)) {
                        $khoan->connect->rollBack();
                        khoan__Redirect('not-found');
                    }

                    $status = $khoan->khoan__Delete($id_khoan);
                    if($status !=0 ){
                        $khoan->connect->commit();
                        khoan__Rotate_Csrf_Token();
                        khoan__Redirect('success');
                    }else{
                        $khoan->connect->rollBack();
                        khoan__Redirect('failed');
                    }
                } catch (Throwable $e) {
                    if ($khoan->connect->inTransaction()) {
                        $khoan->connect->rollBack();
                    }
                    khoan__Redirect('failed');
                }
                
                break;
        }
    }

?>