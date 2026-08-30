<?php

    require '../../models/getModel.php';

    // Nhựt sửa lỗi: Tập trung redirect để sau khi xử lý POST/GET luôn quay về danh sách, tránh submit lại khi refresh.
    function dieu__Redirect($status) {
        header('location: ../index.php?page=quan-ly-dieu&status=' . $status);
        exit();
    }

    // Nhựt sửa lỗi: Chặn id_dieu/thu_tu sai kiểu, bằng 0 hoặc số âm.
    function dieu__Is_Positive_Integer($value) {
        return preg_match('/^[1-9][0-9]*$/', $value);
    }

    // Nhựt sửa lỗi: Khóa danh sách Điều trong transaction để tránh swap/add/delete cập nhật một phần khi submit trùng.
    function dieu__Lock_All($dieu) {
        $obj = $dieu->connect->query("SELECT id_dieu FROM dieu ORDER BY id_dieu FOR UPDATE");
        $obj->fetchAll();
    }

    function dieu_store_old_input($context, $ten_dieu, $ghi_chu, $thu_tu, $id_dieu = null) {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }

    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }
        $_SESSION['dieu_old_input'] = array(
            'context' => $context,
            'ten_dieu' => $ten_dieu,
            'ghi_chu' => $ghi_chu,
            'thu_tu' => $thu_tu,
            'id_dieu' => $id_dieu,
        );
    }

    function dieu_clear_old_input() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        unset($_SESSION['dieu_old_input']);
    }

    function dieu__Valid_Csrf() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    function dieu__Valid_Csrf_Get() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        return isset($_GET['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_GET['csrf_token']);
    }

    function dieu__Rotate_Csrf_Token() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':
                if (!dieu__Valid_Csrf()) {
                    dieu__Redirect('csrf');
                }

                // Nhựt sửa lỗi: Request thiếu field vẫn phải xử lý an toàn, không đọc trực tiếp $_POST gây warning.
                $ten_dieu = isset($_POST['ten_dieu']) ? trim($_POST['ten_dieu']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? trim($_POST['ghi_chu']) : "";
                $thu_tu = isset($_POST['thu_tu']) ? trim($_POST['thu_tu']) : "";

                // Nhựt sửa lỗi: Tên Điều rỗng hoặc chỉ có khoảng trắng vẫn lọt validate HTML required.
                if ($ten_dieu == "") {
                    dieu_store_old_input('add', $ten_dieu, $ghi_chu, $thu_tu);
                    dieu__Redirect('invalid');
                }

                try {
                    // Nhựt sửa lỗi: Thêm Điều có swap gồm nhiều câu SQL nên phải dùng transaction.
                    $dieu->connect->beginTransaction();
                    dieu__Lock_All($dieu);


                    // Nhựt sửa lỗi: Nếu dữ liệu thứ tự hiện tại đã trùng/hở/NULL thì không xử lý tiếp làm sai thêm.
                    if (!$dieu->dieu__Check_Thu_Tu_Hop_Le()) {
                        throw new Exception("Thu tu dieu khong hop le");
                    }

                    $max_thu_tu = $dieu->dieu__Get_Max_Thu_Tu();
                    // Nhựt sửa lỗi: Thứ tự thêm phải là số nguyên dương và không vượt quá max + 1.
                    if ($thu_tu == "") {
                        $thu_tu = $max_thu_tu + 1;
                    } else if (!dieu__Is_Positive_Integer($thu_tu) || (int)$thu_tu > $max_thu_tu + 1) {
                        $error_status = 'invalid-thutu';
                        throw new Exception("Thu tu them khong hop le");
                    } else {
                        $thu_tu = (int)$thu_tu;
                    }

                    // Nhựt sửa lỗi: Khi thêm vào thứ tự đã tồn tại, chỉ hoán đổi với Điều bị trùng, không shift danh sách.
                    $conflict = $dieu->dieu__Get_By_Thu_Tu($thu_tu);
                    if ($conflict) {
                        $dieu->dieu__Update_Thu_Tu($conflict->id_dieu, $max_thu_tu + 1);
                    }

                    $status = $dieu->dieu__Add($ten_dieu, $ghi_chu, $thu_tu);
                    // Nhựt sửa lỗi: Sau khi thêm phải kiểm tra lại không trùng, không âm/0/NULL và không hở thứ tự.
                    if ($status == 0 || !$dieu->dieu__Check_Thu_Tu_Hop_Le()) {
                        throw new Exception("Them dieu that bai");
                    }

                    $dieu->connect->commit();
                    dieu_clear_old_input();
                    dieu__Rotate_Csrf_Token();
                    dieu__Redirect('success');
                } catch (Exception $e) {
                    // Nhựt sửa lỗi: Có lỗi trong quá trình swap/thêm thì rollback toàn bộ.
                    if ($dieu->connect->inTransaction()) {
                        $dieu->connect->rollBack();
                    }
                    dieu_store_old_input('add', $ten_dieu, $ghi_chu, $thu_tu);
                    dieu__Redirect(isset($error_status) ? $error_status : 'invalid');
                }
                
                break;
            case 'update':
                if (!dieu__Valid_Csrf()) {
                    dieu__Redirect('csrf');
                }

                // Nhựt sửa lỗi: Request update thiếu field vẫn phải xử lý an toàn, không đọc trực tiếp $_POST gây warning.
                $id_dieu = isset($_POST['id_dieu']) ? trim($_POST['id_dieu']) : "";
                $ten_dieu = isset($_POST['ten_dieu']) ? trim($_POST['ten_dieu']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? trim($_POST['ghi_chu']) : "";
                $thu_tu = isset($_POST['thu_tu']) ? trim($_POST['thu_tu']) : "";

                // Nhựt sửa lỗi: id_dieu update sai kiểu hoặc không hợp lệ thì quay về danh sách.
                if (!dieu__Is_Positive_Integer($id_dieu)) {
                    dieu__Redirect('not-found');
                }

                // Nhựt sửa lỗi: Không cho update tên Điều rỗng hoặc chỉ chứa khoảng trắng.
                if ($ten_dieu == "") {
                    dieu_store_old_input('update', $ten_dieu, $ghi_chu, $thu_tu, $id_dieu);
                    dieu__Redirect('invalid');
                }

                $error_status = 'invalid';
                try {
                    // Nhựt sửa lỗi: Cập nhật Điều có thể swap nhiều câu SQL nên phải dùng transaction.
                    $dieu->connect->beginTransaction();
                    dieu__Lock_All($dieu);

                    // Nhựt sửa lỗi: id_dieu không tồn tại thì không update và quay về danh sách.
                    $old_dieu = $dieu->dieu__Get_By_Id($id_dieu);
                    if (!$old_dieu) {
                        $error_status = 'not-found';
                        throw new Exception("Khong tim thay dieu");
                    }

                    // Quân sửa: Cập nhật điều kiện khóa Sửa (Dựa vào phát sinh phiếu chấm hoặc đợt chấm)
                    if ($dieu->dieu__Is_Edit_Locked($id_dieu)) {
                        $error_status = 'locked_update';
                        throw new Exception("Dieu da phat sinh trong dot cham diem");
                    }


                    // Nhựt sửa lỗi: Dữ liệu thứ tự đang lỗi thì không swap/update tiếp.
                    if (!$dieu->dieu__Check_Thu_Tu_Hop_Le()) {
                        throw new Exception("Thu tu dieu khong hop le");
                    }

                    $max_thu_tu = $dieu->dieu__Get_Max_Thu_Tu();
                    // Nhựt sửa lỗi: Update để trống thứ tự thì giữ nguyên, nhập thì phải là số nguyên trong khoảng hiện có.
                    if ($thu_tu == "") {
                        $thu_tu = (int)$old_dieu->thu_tu;
                    } else if (!dieu__Is_Positive_Integer($thu_tu) || (int)$thu_tu > $max_thu_tu) {
                        $error_status = 'invalid-thutu';
                        throw new Exception("Thu tu cap nhat khong hop le");
                    } else {
                        $thu_tu = (int)$thu_tu;
                    }

                    // Nhựt sửa lỗi: Nhập đúng thứ tự hiện tại thì không swap; nhập thứ tự khác thì chỉ swap đúng 2 Điều.
                    if ($thu_tu != $old_dieu->thu_tu) {
                        $conflict = $dieu->dieu__Get_By_Thu_Tu($thu_tu);
                        if (!$conflict || $conflict->id_dieu == $id_dieu) {
                            throw new Exception("Khong tim thay dieu can swap");
                        }
                        $dieu->dieu__Update_Thu_Tu($conflict->id_dieu, $old_dieu->thu_tu);
                    }

                    $dieu->dieu__Update($id_dieu, $ten_dieu, $ghi_chu, $thu_tu);
                    // Nhựt sửa lỗi: Sau update/swap phải kiểm tra lại toàn vẹn thứ tự.
                    if (!$dieu->dieu__Check_Thu_Tu_Hop_Le()) {
                        throw new Exception("Cap nhat dieu that bai");
                    }

                    $dieu->connect->commit();
                    dieu_clear_old_input();
                    dieu__Rotate_Csrf_Token();
                    dieu__Redirect('success');
                } catch (Exception $e) {
                    // Nhựt sửa lỗi: Có lỗi trong quá trình swap/update thì rollback toàn bộ.
                    if ($dieu->connect->inTransaction()) {
                        $dieu->connect->rollBack();
                    }
                    dieu_store_old_input('update', $ten_dieu, $ghi_chu, $thu_tu, $id_dieu);
                    dieu__Redirect($error_status);
                }
                
                break;

            case 'copy':
                if (!dieu__Valid_Csrf_Get()) {
                    dieu__Redirect('csrf');
                }

                $id_dieu_cu = isset($_GET['id_dieu']) ? trim($_GET['id_dieu']) : "";

                if (!dieu__Is_Positive_Integer($id_dieu_cu)) {
                    dieu__Redirect('not-found');
                }

                try {
                    $dieu->connect->beginTransaction();
                    dieu__Lock_All($dieu);

                    $dieu_cu = $dieu->dieu__Get_By_Id($id_dieu_cu);
                    if (!$dieu_cu) {
                        throw new Exception("not-found");
                    }

                    // 1. Tạo Điều mới
                    $ten_dieu_moi = $dieu_cu->ten_dieu;

                    $stmt_count = $dieu->connect->prepare("SELECT COUNT(*) FROM dieu WHERE ten_dieu = ?");
                    $stmt_count->execute([$ten_dieu_moi]);
                    $count_ban_sao = (int)$stmt_count->fetchColumn();

                    $ghi_chu_cu = trim($dieu_cu->ghi_chu);
                    $ghi_chu_moi = $ghi_chu_cu ? $ghi_chu_cu . " (Bản sao thứ " . $count_ban_sao . ")" : "Bản sao thứ " . $count_ban_sao;

                    $thu_tu_moi = $dieu->dieu__Get_Max_Thu_Tu() + 1;

                    $status_dieu = $dieu->dieu__Add($ten_dieu_moi, $ghi_chu_moi, $thu_tu_moi);
                    if (!$status_dieu) throw new Exception('copy-failed');
                    $id_dieu_moi = $dieu->connect->lastInsertId();

                    // 2. Nhân bản Khoản
                    $khoan->connect = $dieu->connect;
                    $muc->connect = $dieu->connect;
                    
                    $khoan_cu_list = $khoan->khoan__Get_All_By_Id_Dieu($id_dieu_cu);
                    foreach ($khoan_cu_list as $kc) {
                        $status_khoan = $khoan->khoan__Add($kc->ten_khoan, $kc->ghi_chu, $kc->can_tren, $kc->thu_tu, $id_dieu_moi, $kc->so_luong_muc);
                        if (!$status_khoan) throw new Exception('copy-failed');
                        $id_khoan_moi = $khoan->connect->lastInsertId();

                        // 3. Nhân bản Mục
                        $muc_cu_list = $muc->muc__Get_All_By_Id_Khoan($kc->id_khoan);
                        foreach ($muc_cu_list as $mc) {
                            $status_muc = $muc->muc__Add($mc->ten_muc, $mc->ghi_chu, $mc->thu_tu, $id_khoan_moi, $mc->quyen_sv, $mc->quyen_lt, $mc->quyen_btdk, $mc->quyen_gv, $mc->diem_toi_da, $mc->co_minh_chung);
                            if (!$status_muc) throw new Exception('copy-failed');
                        }
                    }

                    $dieu->connect->commit();
                    dieu__Rotate_Csrf_Token();
                    dieu__Redirect('copy_success');
                } catch (Exception $e) {
                    if ($dieu->connect->inTransaction()) {
                        $dieu->connect->rollBack();
                    }
                    dieu__Redirect($e->getMessage() === 'not-found' ? 'not-found' : 'failed');
                }
                break;

            case 'delete':
                if (!dieu__Valid_Csrf_Get()) {
                    dieu__Redirect('csrf');
                }

                // Nhựt sửa lỗi: Request delete thiếu id_dieu vẫn phải xử lý an toàn.
                $id_dieu = isset($_GET['id_dieu']) ? trim($_GET['id_dieu']) : "";

                // Nhựt sửa lỗi: id_dieu delete sai kiểu hoặc không hợp lệ thì quay về danh sách.
                if (!dieu__Is_Positive_Integer($id_dieu)) {
                    dieu__Redirect('not-found');
                }

                $error_status = 'invalid';
                try {
                    // Nhựt sửa lỗi: Xóa Điều có thể cần đổi thứ tự Điều cuối nên phải dùng transaction.
                    $dieu->connect->beginTransaction();
                    dieu__Lock_All($dieu);

                    // Nhựt sửa lỗi: id_dieu không tồn tại thì không xóa và quay về danh sách.
                    $old_dieu = $dieu->dieu__Get_By_Id($id_dieu);
                    if (!$old_dieu) {
                        $error_status = 'not-found';
                        throw new Exception("Khong tim thay dieu");
                    }

                    // quân sửa: Thay thế ràng buộc Mẫu phiếu bằng ràng buộc Đợt chấm điểm
                    if ($dieu->dieu__Is_In_Active_DotChamDiem($id_dieu)) {
                        $error_status = 'locked_by_dotchamdiem';
                        throw new Exception("Dieu dang bi khoa boi dot cham diem active");
                    }

                    // Quân sửa: Đã loại bỏ ràng buộc kiểm tra Khoản con vì hệ thống hỗ trợ xóa cascade (xóa toàn bộ Khoản, Mục con).

                    // Nhựt sửa lỗi: Dữ liệu thứ tự đang lỗi thì không xóa tiếp làm sai thêm.
                    if (!$dieu->dieu__Check_Thu_Tu_Hop_Le()) {
                        throw new Exception("Thu tu dieu khong hop le");
                    }

                    $thu_tu_xoa = $old_dieu->thu_tu;
                    $status = $dieu->dieu__DeleteWithChildren($id_dieu);
                    // Nhựt sửa lỗi: Chỉ dồn thứ tự sau khi đã xóa thành công để tránh trùng thứ tự tạm thời.
                    if ($status == 0 || !$dieu->dieu__Giam_Thu_Tu_Sau_Khi_Xoa($thu_tu_xoa) || !$dieu->dieu__Check_Thu_Tu_Hop_Le()) {
                        throw new Exception("Xoa dieu that bai");
                    }

                    $dieu->connect->commit();
                    dieu__Rotate_Csrf_Token();
                    dieu__Redirect('success');
                } catch (Exception $e) {
                    // Nhựt sửa lỗi: Có lỗi trong quá trình đổi thứ tự/xóa thì rollback toàn bộ.
                    if ($dieu->connect->inTransaction()) {
                        $dieu->connect->rollBack();
                    }
                    dieu__Redirect($error_status);
                }
                
                break;
            default:
                dieu__Redirect('invalid');
        }
    } else {
        dieu__Redirect('invalid');
    }

?>
