<?php

    require '../../models/getModel.php';

    function muc__Redirect($status) {
        header('location: ../index.php?page=quan-ly-muc&status=' . $status);
        exit();
    }

    function muc_store_old_input($context, $id_khoan, $ten_muc, $ghi_chu, $thu_tu, $diem_toi_da, $co_minh_chung, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $id_muc = null) {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        $_SESSION['muc_old_input'] = array(
            'context' => $context,
            'id_khoan' => $id_khoan,
            'ten_muc' => $ten_muc,
            'ghi_chu' => $ghi_chu,
            'thu_tu' => $thu_tu,
            'diem_toi_da' => $diem_toi_da,
            'co_minh_chung' => $co_minh_chung,
            'quyen_sv' => $quyen_sv,
            'quyen_lt' => $quyen_lt,
            'quyen_btdk' => $quyen_btdk,
            'quyen_gv' => $quyen_gv,
            'id_muc' => $id_muc,
        );
    }

    function muc_clear_old_input() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        unset($_SESSION['muc_old_input']);
    }

    function muc__Valid_Csrf() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    function muc__Valid_Csrf_Get() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        return isset($_GET['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_GET['csrf_token']);
    }

    function muc__Rotate_Csrf_Token() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    // Nhựt sửa lỗi: Khóa danh sách Mục trong transaction để bảo toàn dữ liệu thứ tự và quỹ điểm
    function muc__Lock_All($muc) {
        $obj = $muc->connect->query("SELECT id_muc FROM muc ORDER BY id_muc FOR UPDATE");
        $obj->fetchAll();
    }
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':
                if (!muc__Valid_Csrf()) {
                    muc__Redirect('csrf');
                }

                $ten_muc = isset($_POST['ten_muc']) ? trim($_POST['ten_muc']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? trim($_POST['ghi_chu']) : "";
                $id_khoan = isset($_POST['id_khoan']) ? trim($_POST['id_khoan']) : "";
                $thu_tu = isset($_POST['thu_tu']) ? trim($_POST['thu_tu']) : "";
                $diem_toi_da = isset($_POST['diem_toi_da']) ? trim($_POST['diem_toi_da']) : "";

                $quyen_sv = isset($_POST['quyen_sv']) ? 1 : 0;
                $quyen_lt = isset($_POST['quyen_lt']) ? 1 : 0;
                $quyen_btdk = isset($_POST['quyen_btdk']) ? 1 : 0;
                $quyen_gv = isset($_POST['quyen_gv']) ? 1 : 0;
                $co_minh_chung = isset($_POST['co_minh_chung']) ? 1 : 0;

                if ($ten_muc == "") {
                    muc_store_old_input('add', $id_khoan, $ten_muc, $ghi_chu, $thu_tu, $diem_toi_da, $co_minh_chung, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv);
                    muc__Redirect('invalid-ten');
                }

                if (!preg_match('/^[1-9][0-9]*$/', $id_khoan)) {
                    muc_store_old_input('add', $id_khoan, $ten_muc, $ghi_chu, $thu_tu, $diem_toi_da, $co_minh_chung, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv);
                    muc__Redirect('invalid-khoan');
                }

                if (!preg_match('/^[0-9]+$/', $diem_toi_da)) {
                    muc_store_old_input('add', $id_khoan, $ten_muc, $ghi_chu, $thu_tu, $diem_toi_da, $co_minh_chung, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv);
                    muc__Redirect('invalid-diem');
                }

                try {
                    $muc->connect->beginTransaction();
                    muc__Lock_All($muc);

                    // Kiểm tra tồn tại Khoản
                    $khoan_info = $khoan->khoan__Get_By_Id($id_khoan);
                    if (!$khoan_info) {
                        throw new Exception('invalid-khoan');
                    }

                    // Kiểm tra quỹ điểm
                    $diem_toi_da = (int)$diem_toi_da;
                    $current_total_diem = $muc->muc__Get_Total_Diem_By_Khoan($id_khoan);
                    if ($current_total_diem + $diem_toi_da > $khoan_info->can_tren) {
                        throw new Exception('failed_over_limit');
                    }

                    // Tự động gán thứ tự cuối nếu chưa nhập
                    if ($thu_tu == "") {
                        $thu_tu = $muc->muc__Get_Max_Thu_Tu($id_khoan) + 1;
                    } else if (!preg_match('/^[1-9][0-9]*$/', $thu_tu)) {
                        throw new Exception('invalid-thutu');
                    } else {
                        $thu_tu = (int)$thu_tu;
                    }

                    // Xử lý swap thứ tự nếu trùng
                    $conflict = $muc->muc__Get_By_Thu_Tu($id_khoan, $thu_tu);
                    if ($conflict) {
                        $max_thu_tu = $muc->muc__Get_Max_Thu_Tu($id_khoan);
                        $muc->muc__Update_Thu_Tu($conflict->id_muc, $max_thu_tu + 1);
                    }

                    $status = $muc->muc__Add($ten_muc, $ghi_chu, $thu_tu, $id_khoan, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $diem_toi_da, $co_minh_chung);
                    if($status == 0){
                        throw new Exception('failed');
                    }
                    
                    $muc->connect->commit();
                    muc_clear_old_input();
                    muc__Rotate_Csrf_Token();
                    muc__Redirect('success');
                } catch (Exception $e) {
                    if ($muc->connect->inTransaction()) {
                        $muc->connect->rollBack();
                    }
                    muc_store_old_input('add', $id_khoan, $ten_muc, $ghi_chu, $thu_tu, $diem_toi_da, $co_minh_chung, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv);
                    $msg = $e->getMessage();
                    muc__Redirect(in_array($msg, ['invalid-khoan', 'failed_over_limit', 'invalid-thutu']) ? $msg : 'failed');
                }
                
                break;
            case 'update':
                if (!muc__Valid_Csrf()) {
                    muc__Redirect('csrf');
                }

                $id_muc = isset($_POST['id_muc']) ? trim($_POST['id_muc']) : "";
                $ten_muc = isset($_POST['ten_muc']) ? trim($_POST['ten_muc']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? trim($_POST['ghi_chu']) : "";
                $id_khoan = isset($_POST['id_khoan']) ? trim($_POST['id_khoan']) : "";
                $thu_tu = isset($_POST['thu_tu']) ? trim($_POST['thu_tu']) : "";
                $diem_toi_da = isset($_POST['diem_toi_da']) ? trim($_POST['diem_toi_da']) : "";

                $quyen_sv = isset($_POST['quyen_sv']) ? 1 : 0;
                $quyen_lt = isset($_POST['quyen_lt']) ? 1 : 0;
                $quyen_btdk = isset($_POST['quyen_btdk']) ? 1 : 0;
                $quyen_gv = isset($_POST['quyen_gv']) ? 1 : 0;
                $co_minh_chung = isset($_POST['co_minh_chung']) ? 1 : 0;

                if (!preg_match('/^[1-9][0-9]*$/', $id_muc)) {
                    muc__Redirect('not-found');
                }

                if ($ten_muc == "") {
                    muc_store_old_input('update', $id_khoan, $ten_muc, $ghi_chu, $thu_tu, $diem_toi_da, $co_minh_chung, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $id_muc);
                    muc__Redirect('invalid-ten');
                }

                if (!preg_match('/^[1-9][0-9]*$/', $id_khoan)) {
                    muc_store_old_input('update', $id_khoan, $ten_muc, $ghi_chu, $thu_tu, $diem_toi_da, $co_minh_chung, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $id_muc);
                    muc__Redirect('invalid-khoan');
                }

                if (!preg_match('/^[0-9]+$/', $diem_toi_da)) {
                    muc_store_old_input('update', $id_khoan, $ten_muc, $ghi_chu, $thu_tu, $diem_toi_da, $co_minh_chung, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $id_muc);
                    muc__Redirect('invalid-diem');
                }

                try {
                    $muc->connect->beginTransaction();
                    muc__Lock_All($muc);

                    $old_muc = $muc->muc__Get_By_Id($id_muc);
                    if (!$old_muc) {
                        throw new Exception('not-found');
                    }

                    if ($muc->muc__Is_Edit_Locked($id_muc)) {
                        throw new Exception('locked_update');
                    }

                    $khoan_info = $khoan->khoan__Get_By_Id($id_khoan);
                    if (!$khoan_info) {
                        throw new Exception('invalid-khoan');
                    }

                    // Kiểm tra quỹ điểm (có bù trừ điểm của Mục hiện tại nếu cùng Khoản)
                    $diem_toi_da = (int)$diem_toi_da;
                    $current_total_diem = $muc->muc__Get_Total_Diem_By_Khoan($id_khoan);
                    $diem_hien_tai_neu_cung_khoan = ($old_muc->id_khoan == $id_khoan) ? $old_muc->diem_toi_da : 0;
                    
                    if ($current_total_diem - $diem_hien_tai_neu_cung_khoan + $diem_toi_da > $khoan_info->can_tren) {
                        throw new Exception('failed_over_limit');
                    }

                    // Giữ nguyên thứ tự nếu để trống
                    if ($thu_tu == "") {
                        $thu_tu = (int)$old_muc->thu_tu;
                    } else if (!preg_match('/^[1-9][0-9]*$/', $thu_tu)) {
                        throw new Exception('invalid-thutu');
                    } else {
                        $thu_tu = (int)$thu_tu;
                    }

                    // Xử lý swap thứ tự nếu thay đổi
                    if ($thu_tu != $old_muc->thu_tu || $id_khoan != $old_muc->id_khoan) {
                        $conflict = $muc->muc__Get_By_Thu_Tu($id_khoan, $thu_tu);
                        if ($conflict && $conflict->id_muc != $id_muc) {
                            $muc->muc__Update_Thu_Tu($conflict->id_muc, $old_muc->thu_tu);
                        }
                    }

                    $status = $muc->muc__Update($id_muc, $ten_muc, $ghi_chu, $thu_tu, $id_khoan, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $diem_toi_da, $co_minh_chung);
                    if($status == 0){
                        throw new Exception('failed');
                    }

                    $muc->connect->commit();
                    muc_clear_old_input();
                    muc__Rotate_Csrf_Token();
                    muc__Redirect('success');
                } catch (Exception $e) {
                    if ($muc->connect->inTransaction()) {
                        $muc->connect->rollBack();
                    }
                    $msg = $e->getMessage();
                    if (in_array($msg, ['not-found', 'locked_update'])) {
                        muc__Redirect($msg);
                    }
                    muc_store_old_input('update', $id_khoan, $ten_muc, $ghi_chu, $thu_tu, $diem_toi_da, $co_minh_chung, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $id_muc);
                    muc__Redirect(in_array($msg, ['invalid-khoan', 'failed_over_limit', 'invalid-thutu']) ? $msg : 'failed');
                }
                
                break;

            case 'copy':
                if (!muc__Valid_Csrf_Get()) {
                    muc__Redirect('csrf');
                }

                $id_muc_cu = isset($_GET['id_muc']) ? trim($_GET['id_muc']) : "";

                if (!preg_match('/^[1-9][0-9]*$/', $id_muc_cu)) {
                    muc__Redirect('not-found');
                }

                try {
                    $muc->connect->beginTransaction();
                    muc__Lock_All($muc);

                    $old_muc = $muc->muc__Get_By_Id($id_muc_cu);
                    if (!$old_muc) {
                        throw new Exception('not-found');
                    }

                    $khoan_info = $khoan->khoan__Get_By_Id($old_muc->id_khoan);
                    if (!$khoan_info) {
                        throw new Exception('invalid-khoan');
                    }

                    // Kiểm tra quỹ điểm (Mục mới thêm vào nên không bù trừ)
                    $current_total_diem = $muc->muc__Get_Total_Diem_By_Khoan($old_muc->id_khoan);
                    if ($current_total_diem + $old_muc->diem_toi_da > $khoan_info->can_tren) {
                        throw new Exception('failed_over_limit');
                    }

                    $ten_muc_moi = $old_muc->ten_muc;

                    $stmt_count = $muc->connect->prepare("SELECT COUNT(*) FROM muc WHERE ten_muc = ?");
                    $stmt_count->execute([$ten_muc_moi]);
                    $count_ban_sao = (int)$stmt_count->fetchColumn();

                    $ghi_chu_cu = trim($old_muc->ghi_chu);
                    $ghi_chu_moi = $ghi_chu_cu ? $ghi_chu_cu . " (Bản sao thứ " . $count_ban_sao . ")" : "Bản sao thứ " . $count_ban_sao;
                    
                    $status = $muc->muc__Add($ten_muc_moi, $ghi_chu_moi, $old_muc->thu_tu, $old_muc->id_khoan, $old_muc->quyen_sv, $old_muc->quyen_lt, $old_muc->quyen_btdk, $old_muc->quyen_gv, $old_muc->diem_toi_da, $old_muc->co_minh_chung);
                    if($status == 0){
                        throw new Exception('failed');
                    }

                    $muc->connect->commit();
                    muc__Rotate_Csrf_Token();
                    muc__Redirect('copy_success');
                } catch (Exception $e) {
                    if ($muc->connect->inTransaction()) {
                        $muc->connect->rollBack();
                    }
                    $msg = $e->getMessage();
                    if (in_array($msg, ['not-found'])) {
                        muc__Redirect($msg);
                    }
                    muc__Redirect(in_array($msg, ['failed_over_limit']) ? $msg : 'failed');
                }
                
                break;

            case 'delete':
                if (!muc__Valid_Csrf_Get()) {
                    muc__Redirect('csrf');
                }

                $id_muc = isset($_GET['id_muc']) ? trim($_GET['id_muc']) : "";
                
                if (!preg_match('/^[1-9][0-9]*$/', $id_muc)) {
                    muc__Redirect('not-found');
                }

                try {
                    $muc->connect->beginTransaction();
                    muc__Lock_All($muc);

                    if ($muc->muc__Is_In_Active_DotChamDiem($id_muc)) {
                        throw new Exception('locked_by_dotchamdiem');
                    }

                    $old_muc = $muc->muc__Get_By_Id($id_muc);
                    if (!$old_muc) {
                        throw new Exception('not-found');
                    }

                    $status = $muc->muc__Delete($id_muc);
                    if($status == 0){
                        throw new Exception('failed');
                    }

                    $muc->connect->commit();
                    muc__Rotate_Csrf_Token();
                    muc__Redirect('success');
                } catch (Exception $e) {
                    if ($muc->connect->inTransaction()) {
                        $muc->connect->rollBack();
                    }
                    $msg = $e->getMessage();
                    muc__Redirect(in_array($msg, ['locked_by_dotchamdiem', 'not-found']) ? $msg : 'failed');
                }
                
                break;
            default:
                muc__Redirect('invalid');
        }
    } else {
        muc__Redirect('invalid');
    }
?>