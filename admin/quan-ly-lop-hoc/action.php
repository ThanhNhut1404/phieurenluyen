<?php

    session_start();
    // Nhựt sửa lỗi: chặn endpoint thêm/sửa/xóa lớp học khi chưa đăng nhập admin.
    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }

    require '../../models/getModel.php';

    // Nhựt sửa lỗi: gom redirect để luôn dừng xử lý sau khi chuyển trang.
    function lophoc_redirect($status) {
        header('location: ../index.php?page=quan-ly-lop-hoc&status=' . $status);
        exit();
    }

    // Nhựt sửa lỗi: kiểm tra CSRF token cho các request thay đổi dữ liệu.
    function lophoc_valid_csrf() {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    // Nhựt sửa lỗi: lấy chuỗi POST an toàn và loại bỏ khoảng trắng thừa.
    function lophoc_post_text($key) {
        return isset($_POST[$key]) ? trim($_POST[$key]) : '';
    }

    // Nhựt sửa lỗi: chuẩn hóa tên lớp học trước khi validate và kiểm tra trùng.
    function lophoc_normalize_ten_lop_hoc($ten_lop_hoc) {
        $ten_lop_hoc = trim((string)$ten_lop_hoc);
        $ten_lop_hoc = preg_replace('/\s+/u', ' ', $ten_lop_hoc);
        return $ten_lop_hoc === null ? '' : $ten_lop_hoc;
    }

    // Nhựt sửa lỗi: kiểm tra tên lớp học không rỗng và không vượt quá varchar(50).
    function lophoc_valid_ten_lop_hoc($ten_lop_hoc) {
        $ten_lop_hoc = lophoc_normalize_ten_lop_hoc($ten_lop_hoc);
        $length = function_exists('mb_strlen') ? mb_strlen($ten_lop_hoc, 'UTF-8') : strlen($ten_lop_hoc);
        return $ten_lop_hoc !== '' && $length <= 50;
    }

    // Nhựt sửa lỗi: giới hạn ghi chú để tránh payload quá lớn hoặc dữ liệu không hợp lệ.
    function lophoc_valid_ghi_chu($ghi_chu) {
        $ghi_chu = (string)$ghi_chu;
        $length = function_exists('mb_strlen') ? mb_strlen($ghi_chu, 'UTF-8') : strlen($ghi_chu);
        return $length <= 2000;
    }

    // Nhựt sửa lỗi: kiểm tra khóa học được chọn có tồn tại.
    function lophoc_khoahoc_exists($khoahoc, $id_khoa_hoc) {
        return $id_khoa_hoc && $id_khoa_hoc > 0 && $khoahoc->khoahoc__Get_By_Id($id_khoa_hoc);
    }

    // Nhựt sửa lỗi: kiểm tra ngành học được chọn có tồn tại.
    function lophoc_nganhhoc_exists($nganhhoc, $id_nganh_hoc) {
        return $id_nganh_hoc && $id_nganh_hoc > 0 && $nganhhoc->nganhhoc__Get_By_Id($id_nganh_hoc);
    }

    // Nhựt sửa lỗi: giữ lại dữ liệu vừa nhập khi validate hoặc CSRF lỗi để cải thiện UX.
    function lophoc_store_old_input($context, $id_khoa_hoc, $id_nganh_hoc, $ten_lop_hoc, $ghi_chu, $id_lop_hoc = null) {
        $_SESSION['lophoc_old_input'] = array(
            'context' => $context,
            'id_khoa_hoc' => $id_khoa_hoc,
            'id_nganh_hoc' => $id_nganh_hoc,
            'ten_lop_hoc' => $ten_lop_hoc,
            'ghi_chu' => $ghi_chu,
            'id_lop_hoc' => $id_lop_hoc,
        );
    }

    // Nhựt sửa lỗi: xóa dữ liệu tạm sau khi thao tác thành công.
    function lophoc_clear_old_input() {
        unset($_SESSION['lophoc_old_input']);
    }

    // Nhựt sửa lỗi: rotate CSRF token sau thao tác thành công, tránh token sống quá lâu.
    function lophoc_rotate_csrf_token() {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }
    
    if (isset($_GET['req'])){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !lophoc_valid_csrf()) {
            if ($_GET['req'] === 'add') {
                lophoc_store_old_input('add', filter_input(INPUT_POST, 'id_khoa_hoc', FILTER_VALIDATE_INT), filter_input(INPUT_POST, 'id_nganh_hoc', FILTER_VALIDATE_INT), lophoc_post_text('ten_lop_hoc'), lophoc_post_text('ghi_chu'));
            }
            if ($_GET['req'] === 'update') {
                lophoc_store_old_input('update', filter_input(INPUT_POST, 'id_khoa_hoc', FILTER_VALIDATE_INT), filter_input(INPUT_POST, 'id_nganh_hoc', FILTER_VALIDATE_INT), lophoc_post_text('ten_lop_hoc'), lophoc_post_text('ghi_chu'), filter_input(INPUT_POST, 'id_lop_hoc', FILTER_VALIDATE_INT));
            }
            lophoc_redirect('csrf');
        }

        // Nhựt sửa lỗi: bắt riêng lỗi DB/PDOException cho toàn bộ thao tác thêm/sửa/xóa lớp học.
        try {
        switch($_GET['req']){
            case 'add':

                $id_khoa_hoc = filter_input(INPUT_POST, 'id_khoa_hoc', FILTER_VALIDATE_INT);
                $id_nganh_hoc = filter_input(INPUT_POST, 'id_nganh_hoc', FILTER_VALIDATE_INT);
                $ten_lop_hoc = lophoc_normalize_ten_lop_hoc(lophoc_post_text('ten_lop_hoc'));
                $ghi_chu = lophoc_post_text('ghi_chu');

                // Nhựt sửa lỗi: phân biệt lỗi validate, khoa/ngành không tồn tại và lỗi trùng tên lớp khi thêm.
                if (!lophoc_khoahoc_exists($khoahoc, $id_khoa_hoc)) {
                    lophoc_store_old_input('add', $id_khoa_hoc, $id_nganh_hoc, $ten_lop_hoc, $ghi_chu);
                    lophoc_redirect('invalid-khoa-hoc');
                }

                if (!lophoc_nganhhoc_exists($nganhhoc, $id_nganh_hoc)) {
                    lophoc_store_old_input('add', $id_khoa_hoc, $id_nganh_hoc, $ten_lop_hoc, $ghi_chu);
                    lophoc_redirect('invalid-nganh-hoc');
                }

                if (!lophoc_valid_ten_lop_hoc($ten_lop_hoc)) {
                    lophoc_store_old_input('add', $id_khoa_hoc, $id_nganh_hoc, $ten_lop_hoc, $ghi_chu);
                    lophoc_redirect('invalid-ten-lop-hoc');
                }

                if (!lophoc_valid_ghi_chu($ghi_chu)) {
                    lophoc_store_old_input('add', $id_khoa_hoc, $id_nganh_hoc, $ten_lop_hoc, $ghi_chu);
                    lophoc_redirect('invalid-ghichu');
                }

                if ($lophoc->lophoc__Name_Exists($ten_lop_hoc, $id_khoa_hoc, $id_nganh_hoc)) {
                    lophoc_store_old_input('add', $id_khoa_hoc, $id_nganh_hoc, $ten_lop_hoc, $ghi_chu);
                    lophoc_redirect('duplicate-lop-hoc');
                }

                $status = $lophoc->lophoc__Add($ten_lop_hoc, $ghi_chu, $id_khoa_hoc, $id_nganh_hoc);
                lophoc_clear_old_input();
                lophoc_rotate_csrf_token();
                lophoc_redirect($status != 0 ? 'add-success' : 'add-failed');
                
                break;
            case 'update':

                $id_lop_hoc = filter_input(INPUT_POST, 'id_lop_hoc', FILTER_VALIDATE_INT);
                $id_khoa_hoc = filter_input(INPUT_POST, 'id_khoa_hoc', FILTER_VALIDATE_INT);
                $id_nganh_hoc = filter_input(INPUT_POST, 'id_nganh_hoc', FILTER_VALIDATE_INT);
                $ten_lop_hoc = lophoc_normalize_ten_lop_hoc(lophoc_post_text('ten_lop_hoc'));
                $ghi_chu = lophoc_post_text('ghi_chu');
                
                // Nhựt sửa lỗi: phân biệt lỗi id sai, không tìm thấy, validate và trùng tên khi cập nhật.
                if (!$id_lop_hoc || $id_lop_hoc < 1) {
                    lophoc_store_old_input('update', $id_khoa_hoc, $id_nganh_hoc, $ten_lop_hoc, $ghi_chu, $id_lop_hoc);
                    lophoc_redirect('invalid');
                }

                if (!lophoc_khoahoc_exists($khoahoc, $id_khoa_hoc)) {
                    lophoc_store_old_input('update', $id_khoa_hoc, $id_nganh_hoc, $ten_lop_hoc, $ghi_chu, $id_lop_hoc);
                    lophoc_redirect('invalid-khoa-hoc');
                }

                if (!lophoc_nganhhoc_exists($nganhhoc, $id_nganh_hoc)) {
                    lophoc_store_old_input('update', $id_khoa_hoc, $id_nganh_hoc, $ten_lop_hoc, $ghi_chu, $id_lop_hoc);
                    lophoc_redirect('invalid-nganh-hoc');
                }

                if (!lophoc_valid_ten_lop_hoc($ten_lop_hoc)) {
                    lophoc_store_old_input('update', $id_khoa_hoc, $id_nganh_hoc, $ten_lop_hoc, $ghi_chu, $id_lop_hoc);
                    lophoc_redirect('invalid-ten-lop-hoc');
                }

                if (!lophoc_valid_ghi_chu($ghi_chu)) {
                    lophoc_store_old_input('update', $id_khoa_hoc, $id_nganh_hoc, $ten_lop_hoc, $ghi_chu, $id_lop_hoc);
                    lophoc_redirect('invalid-ghichu');
                }

                $lophoc->connect->beginTransaction();
                if (!$lophoc->lophoc__Lock_By_Id($id_lop_hoc)) {
                    $lophoc->connect->rollBack();
                    lophoc_redirect('not-found');
                }

                if ($lophoc->lophoc__Name_Exists($ten_lop_hoc, $id_khoa_hoc, $id_nganh_hoc, $id_lop_hoc)) {
                    $lophoc->connect->rollBack();
                    lophoc_store_old_input('update', $id_khoa_hoc, $id_nganh_hoc, $ten_lop_hoc, $ghi_chu, $id_lop_hoc);
                    lophoc_redirect('duplicate-lop-hoc');
                }

                $status = $lophoc->lophoc__Update($id_lop_hoc, $ten_lop_hoc, $ghi_chu, $id_khoa_hoc, $id_nganh_hoc);
                $lophoc->connect->commit();
                lophoc_clear_old_input();
                lophoc_rotate_csrf_token();
                lophoc_redirect($status !== false ? 'update-success' : 'update-failed');
                
                break;

            case 'delete':

                $id_lop_hoc = filter_input(INPUT_POST, 'id_lop_hoc', FILTER_VALIDATE_INT);

                // Nhựt sửa lỗi: phân biệt lỗi id sai, không tìm thấy và bị ràng buộc dữ liệu khi xóa.
                if (!$id_lop_hoc || $id_lop_hoc < 1) {
                    lophoc_redirect('invalid');
                }

                $lophoc->connect->beginTransaction();
                if (!$lophoc->lophoc__Lock_By_Id($id_lop_hoc)) {
                    $lophoc->connect->rollBack();
                    lophoc_redirect('not-found');
                }

                if ($lophoc->lophoc__Has_Related_Data($id_lop_hoc)) {
                    $lophoc->connect->rollBack();
                    lophoc_redirect('related-lop-hoc');
                }

                $status = $lophoc->lophoc__Delete($id_lop_hoc);
                $lophoc->connect->commit();
                lophoc_rotate_csrf_token();
                lophoc_redirect($status != 0 ? 'delete-success' : 'delete-failed');
                
                break;
            default:
                // Nhựt sửa lỗi: req không hợp lệ thì báo lỗi validate rõ ràng.
                lophoc_redirect('invalid');
        }
        } catch (PDOException $e) {
            if ($lophoc->connect->inTransaction()) {
                $lophoc->connect->rollBack();
            }
            if ($e->getCode() === '23000') {
                lophoc_redirect($_GET['req'] === 'delete' ? 'related-lop-hoc' : 'duplicate-lop-hoc');
            }
            lophoc_redirect('system');
        } catch (Throwable $e) {
            if ($lophoc->connect->inTransaction()) {
                $lophoc->connect->rollBack();
            }
            lophoc_redirect('system');
        }
    } else {
        // Nhựt sửa lỗi: thiếu req thì báo lỗi validate rõ ràng.
        lophoc_redirect('invalid');
    }

?>
