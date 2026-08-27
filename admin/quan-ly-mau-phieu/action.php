<?php

    session_start();
    require '../../models/getModel.php';

    // Nhựt sửa lỗi: Hàm lưu lại dữ liệu nhập khi xảy ra lỗi validation.
    function mauphieu_store_old_input($context, $ten_mau_phieu, $ghi_chu, $id_dieu = [], $id_mau_phieu = null) {
        $_SESSION['mauphieu_old_input'] = array(
            'context' => $context,
            'ten_mau_phieu' => $ten_mau_phieu,
            'ghi_chu' => $ghi_chu,
            'id_dieu' => is_array($id_dieu) ? $id_dieu : [],
            'id_mau_phieu' => $id_mau_phieu
        );
    }

    function mauphieu_clear_old_input() {
        unset($_SESSION['mauphieu_old_input']);
    }

    function mauphieu__Redirect($status) {
        header('location: ../index.php?page=quan-ly-mau-phieu&status=' . $status);
        exit();
    }

    function mauphieu__Valid_Csrf() {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    function mauphieu__Valid_Csrf_Get() {
        return isset($_GET['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_GET['csrf_token']);
    }

    function mauphieu__Rotate_Csrf_Token() {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':
                if (!mauphieu__Valid_Csrf()) {
                    mauphieu__Redirect('csrf');
                }

                $ten_mau_phieu = isset($_POST['ten_mau_phieu']) ? trim($_POST['ten_mau_phieu']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? $_POST['ghi_chu'] : "";
                $id_dieu = isset($_POST['id_dieu']) ? $_POST['id_dieu'] : [];

                if ($ten_mau_phieu == "") {
                    mauphieu_store_old_input('add', $ten_mau_phieu, $ghi_chu, $id_dieu);
                    mauphieu__Redirect('invalid-ten');
                }
                
                if (!is_array($id_dieu) || count($id_dieu) == 0) {
                    mauphieu_store_old_input('add', $ten_mau_phieu, $ghi_chu, $id_dieu);
                    mauphieu__Redirect('invalid-dieu');
                }

                $bocauhoi->connect = $mauphieu->connect;
                try {
                    $mauphieu->connect->beginTransaction();
                    $id_mau_phieu = $mauphieu->mauphieu__Add($ten_mau_phieu, $ghi_chu);
                    if (!$id_mau_phieu) {
                        throw new Exception('failed-add-mauphieu');
                    }
                    foreach($id_dieu as $item){
                        $status = $bocauhoi->bocauhoi__Add($id_mau_phieu, $item);
                        if ($status == 0) {
                            throw new Exception('failed-add-dieu');
                        }
                    }
                    $mauphieu->connect->commit();
                    mauphieu_clear_old_input();
                    mauphieu__Rotate_Csrf_Token();
                    mauphieu__Redirect('success');
                } catch (Throwable $e) {
                    if ($mauphieu->connect->inTransaction()) {
                        $mauphieu->connect->rollBack();
                    }
                    mauphieu_store_old_input('add', $ten_mau_phieu, $ghi_chu, $id_dieu);
                    mauphieu__Redirect('failed');
                }
                break;
            case 'update':
                if (!mauphieu__Valid_Csrf()) {
                    mauphieu__Redirect('csrf');
                }
                
                $id_mau_phieu = isset($_POST['id_mau_phieu']) ? trim($_POST['id_mau_phieu']) : "";
                $ten_mau_phieu = isset($_POST['ten_mau_phieu']) ? trim($_POST['ten_mau_phieu']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? $_POST['ghi_chu'] : "";

                if ($mauphieu->mauphieu__Is_Edit_Locked($id_mau_phieu)) {
                    mauphieu_store_old_input('update', $ten_mau_phieu, $ghi_chu, [], $id_mau_phieu);
                    mauphieu__Redirect('locked');
                }

                if ($ten_mau_phieu == "") {
                    mauphieu_store_old_input('update', $ten_mau_phieu, $ghi_chu, [], $id_mau_phieu);
                    mauphieu__Redirect('invalid-ten');
                }
                
                $status = $mauphieu->mauphieu__Update($id_mau_phieu, $ten_mau_phieu, $ghi_chu);
                if($status != 0 ){
                    mauphieu_clear_old_input();
                    mauphieu__Rotate_Csrf_Token();
                    mauphieu__Redirect('success');
                }else{
                    mauphieu_store_old_input('update', $ten_mau_phieu, $ghi_chu, [], $id_mau_phieu);
                    mauphieu__Redirect('failed');
                }
                break;
            case 'update_dieu':
                if (!mauphieu__Valid_Csrf()) {
                    mauphieu__Redirect('csrf');
                }
                
                $id_mau_phieu = isset($_POST['id_mau_phieu']) ? trim($_POST['id_mau_phieu']) : "";
                $id_dieu = isset($_POST['id_dieu']) ? $_POST['id_dieu'] : [];

                if ($mauphieu->mauphieu__Is_Edit_Locked($id_mau_phieu)) {
                    mauphieu_store_old_input('update_dieu', '', '', $id_dieu, $id_mau_phieu);
                    mauphieu__Redirect('locked');
                }
                
                if (!is_array($id_dieu) || count($id_dieu) == 0) {
                    mauphieu_store_old_input('update_dieu', '', '', $id_dieu, $id_mau_phieu);
                    mauphieu__Redirect('invalid-dieu');
                }

                $bocauhoi->connect = $mauphieu->connect;
                try {
                    $mauphieu->connect->beginTransaction();
                    $bocauhoi->bocauhoi__Delete_Mau_Phieu($id_mau_phieu);

                    foreach($id_dieu as $item){
                        $status = $bocauhoi->bocauhoi__Add($id_mau_phieu, $item);
                        if ($status == 0) {
                            throw new Exception('failed-add-dieu');
                        }
                    }
                    $mauphieu->connect->commit();
                    mauphieu_clear_old_input();
                    mauphieu__Rotate_Csrf_Token();
                    mauphieu__Redirect('success');
                } catch (Throwable $e) {
                    if ($mauphieu->connect->inTransaction()) {
                        $mauphieu->connect->rollBack();
                    }
                    mauphieu_store_old_input('update_dieu', '', '', $id_dieu, $id_mau_phieu);
                    mauphieu__Redirect('failed');
                }
                break;

            case 'delete':
                $id_mau_phieu = isset($_GET['id_mau_phieu']) ? trim($_GET['id_mau_phieu']) : "";
                if (!mauphieu__Valid_Csrf_Get()) {
                    mauphieu__Redirect('csrf');
                }

                $bocauhoi->connect = $mauphieu->connect;
                try {
                    $mauphieu->connect->beginTransaction();
                    $bocauhoi->bocauhoi__Delete_Mau_Phieu($id_mau_phieu);
                    $status = $mauphieu->mauphieu__Delete($id_mau_phieu);
                    if ($status == 0) {
                        throw new Exception('delete-failed');
                    }
                    $mauphieu->connect->commit();
                    mauphieu__Rotate_Csrf_Token();
                    mauphieu__Redirect('delete-success');
                } catch (Throwable $e) {
                    if ($mauphieu->connect->inTransaction()) {
                        $mauphieu->connect->rollBack();
                    }
                    mauphieu__Redirect('failed');
                }
                break;
        }
    }

?>