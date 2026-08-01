<?php

    session_start();
    require '../../models/getModel.php';

    // Nhựt sửa lỗi: Dùng chung redirect cho các lỗi validate/ràng buộc của Quản lý Xếp loại.
    function xep_loai__Redirect($status) {
        header('location: ../index.php?page=quan-ly-xep-loai&status=' . $status);
        exit();
    }

    function xep_loai__Valid_Csrf() {
        // Nhựt sửa lỗi: Kiểm tra CSRF token cho Add/Update Xếp loại.
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    function xep_loai__Valid_Csrf_Get() {
        // Nhựt sửa lỗi: Kiểm tra CSRF token cho Delete Xếp loại đang gọi qua URL.
        return isset($_GET['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_GET['csrf_token']);
    }

    function xep_loai__Rotate_Csrf_Token() {
        // Nhựt sửa lỗi: Đổi CSRF token sau thao tác thành công để giảm rủi ro submit lại.
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    // Nhựt sửa lỗi: Kiểm tra id_xep_loai phải là số nguyên dương trước khi update/delete.
    function xep_loai__Is_Positive_Integer($value) {
        return preg_match('/^[1-9][0-9]*$/', $value);
    }

    // Nhựt sửa lỗi: can_duoi, can_tren, ha_bac phải là số và không được rỗng.
    function xep_loai__Is_Number($value) {
        // Nhựt sửa lỗi: Điểm rèn luyện và hạ bậc đang xử lý theo số nguyên, không nhận số thập phân hoặc dạng 1e2.
        return preg_match('/^[0-9]+$/', trim($value));
    }

    // Nhựt sửa lỗi: Validate server cho tên, thang điểm 0-100 và điểm hạ bậc 10-15.
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
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                // Nhựt sửa lỗi: Chặn Add khi CSRF token không hợp lệ.
                if (!xep_loai__Valid_Csrf()) {
                    xep_loai__Redirect('csrf');
                }

                $ten_xep_loai = isset($_POST['ten_xep_loai']) ? trim($_POST['ten_xep_loai']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? $_POST['ghi_chu'] : "";
                $can_tren = isset($_POST['can_tren']) ? trim($_POST['can_tren']) : "";
                $can_duoi = isset($_POST['can_duoi']) ? trim($_POST['can_duoi']) : "";
                $ha_bac = isset($_POST['ha_bac']) ? trim($_POST['ha_bac']) : "";
                // Nhựt sửa lỗi: Chuẩn hóa tên xếp loại trước khi validate, kiểm tra trùng và lưu.
                $ten_xep_loai = $xeploai->xeploai__Normalize_Name($ten_xep_loai);

                // Nhựt sửa lỗi: Không lưu xếp loại khi tên/điểm/hạ bậc không hợp lệ.
                if (!xep_loai__Validate_Data($ten_xep_loai, $can_duoi, $can_tren, $ha_bac)) {
                    xep_loai__Redirect('invalid');
                }

                try {
                    // Nhựt sửa lỗi: Bọc kiểm tra trùng/khoảng điểm và Add trong transaction để tránh ghi dở dang.
                    $xeploai->connect->beginTransaction();
                    $xeploai->xeploai__Lock_All();

                    // Nhựt sửa lỗi: Không cho thêm xếp loại trùng tên.
                    if ($xeploai->xeploai__Check_Name($ten_xep_loai)) {
                        $xeploai->connect->rollBack();
                        xep_loai__Redirect('duplicate-name');
                    }

                    // Nhựt sửa lỗi: Không cho thêm khoảng điểm chồng lên xếp loại khác.
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
                        // Nhựt sửa lỗi: Rollback toàn bộ nếu Add Xếp loại lỗi giữa chừng.
                        $xeploai->connect->rollBack();
                    }
                    xep_loai__Redirect('failed');
                }
                
                break;
            case 'update':

                // Nhựt sửa lỗi: Chặn Update khi CSRF token không hợp lệ.
                if (!xep_loai__Valid_Csrf()) {
                    xep_loai__Redirect('csrf');
                }

                $id_xep_loai = isset($_POST['id_xep_loai']) ? trim($_POST['id_xep_loai']) : "";
                $ten_xep_loai = isset($_POST['ten_xep_loai']) ? trim($_POST['ten_xep_loai']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? $_POST['ghi_chu'] : "";
                $can_tren = isset($_POST['can_tren']) ? trim($_POST['can_tren']) : "";
                $can_duoi = isset($_POST['can_duoi']) ? trim($_POST['can_duoi']) : "";
                $ha_bac = isset($_POST['ha_bac']) ? trim($_POST['ha_bac']) : "";
                // Nhựt sửa lỗi: Chuẩn hóa tên xếp loại trước khi validate, kiểm tra trùng và lưu.
                $ten_xep_loai = $xeploai->xeploai__Normalize_Name($ten_xep_loai);

                // Nhựt sửa lỗi: Không update khi id_xep_loai không tồn tại.
                if (!xep_loai__Is_Positive_Integer($id_xep_loai) || !$xeploai->xeploai__Get_By_Id($id_xep_loai)) {
                    xep_loai__Redirect('not-found');
                }

                // Nhựt sửa lỗi: Không cho cập nhật cấu hình xếp loại đã phát sinh kết quả để tránh làm sai dữ liệu lịch sử.
                if ($xeploai->xeploai__Is_Used_In_Ketquaxeploai($id_xep_loai)) {
                    xep_loai__Redirect('locked-xep-loai');
                }

                // Nhựt sửa lỗi: Không cập nhật xếp loại khi tên/điểm/hạ bậc không hợp lệ.
                if (!xep_loai__Validate_Data($ten_xep_loai, $can_duoi, $can_tren, $ha_bac)) {
                    xep_loai__Redirect('invalid');
                }

                try {
                    // Nhựt sửa lỗi: Bọc kiểm tra trùng/khoảng điểm và Update trong transaction để tránh ghi dở dang.
                    $xeploai->connect->beginTransaction();
                    $xeploai->xeploai__Lock_All();

                    // Nhựt sửa lỗi: Kiểm tra lại id và dữ liệu sử dụng trong transaction trước khi Update.
                    if (!$xeploai->xeploai__Get_By_Id($id_xep_loai)) {
                        $xeploai->connect->rollBack();
                        xep_loai__Redirect('not-found');
                    }
                    if ($xeploai->xeploai__Is_Used_In_Ketquaxeploai($id_xep_loai)) {
                        $xeploai->connect->rollBack();
                        xep_loai__Redirect('locked-xep-loai');
                    }

                    // Nhựt sửa lỗi: Không cho cập nhật tên xếp loại trùng với bản ghi khác.
                    if ($xeploai->xeploai__Check_Name_Update($id_xep_loai, $ten_xep_loai)) {
                        $xeploai->connect->rollBack();
                        xep_loai__Redirect('duplicate-name');
                    }

                    // Nhựt sửa lỗi: Khi update phải loại trừ chính bản ghi đang sửa lúc kiểm tra chồng khoảng điểm.
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
                        // Nhựt sửa lỗi: Rollback toàn bộ nếu Update Xếp loại lỗi giữa chừng.
                        $xeploai->connect->rollBack();
                    }
                    xep_loai__Redirect('failed');
                }
                
                break;

            case 'delete':

                $id_xep_loai = isset($_GET['id_xep_loai']) ? trim($_GET['id_xep_loai']) : "";

                // Nhựt sửa lỗi: Chặn Delete khi CSRF token không hợp lệ.
                if (!xep_loai__Valid_Csrf_Get()) {
                    xep_loai__Redirect('csrf');
                }

                // Nhựt sửa lỗi: Không delete khi id_xep_loai không tồn tại.
                if (!xep_loai__Is_Positive_Integer($id_xep_loai) || !$xeploai->xeploai__Get_By_Id($id_xep_loai)) {
                    xep_loai__Redirect('not-found');
                }

                // Nhựt sửa lỗi: Xếp loại đang được ketquaxeploai sử dụng thì không được xóa.
                if ($xeploai->xeploai__Is_Used_In_Ketquaxeploai($id_xep_loai)) {
                    xep_loai__Redirect('related-xep-loai');
                }

                try {
                    // Nhựt sửa lỗi: Bọc Delete trong transaction để kiểm tra cha-con và xóa nguyên tử.
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
                        // Nhựt sửa lỗi: Rollback toàn bộ nếu Delete Xếp loại lỗi giữa chừng.
                        $xeploai->connect->rollBack();
                    }
                    xep_loai__Redirect('failed');
                }
                
                break;
        }
    }

?>
