<?php

    session_start();
    // Nhựt sửa lỗi: chặn endpoint thêm/sửa/xóa khi chưa đăng nhập admin.
    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }

    require '../../models/getModel.php';

    // Nhựt sửa lỗi: gom redirect để luôn dừng xử lý sau khi chuyển trang.
    function khoa_redirect($status) {
        header('location: ../index.php?page=quan-ly-khoa&status=' . $status);
        exit();
    }

    // Nhựt sửa lỗi: kiểm tra CSRF token cho các request thay đổi dữ liệu.
    function khoa_valid_csrf() {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    // Nhựt sửa lỗi: lấy chuỗi POST an toàn và loại bỏ khoảng trắng thừa.
    function khoa_post_text($key) {
        return isset($_POST[$key]) ? trim($_POST[$key]) : '';
    }

    // Nhựt sửa lỗi: chuẩn hóa tên khoa trước khi validate và kiểm tra trùng.
    function khoa_normalize_ten_khoa($ten_khoa) {
        $ten_khoa = trim((string)$ten_khoa);
        $ten_khoa = preg_replace('/\s+/u', ' ', $ten_khoa);
        return $ten_khoa === null ? '' : $ten_khoa;
    }

    // Nhựt sửa lỗi: kiểm tra tên khoa không rỗng và không vượt quá varchar(50).
    function khoa_valid_ten_khoa($ten_khoa) {
        $ten_khoa = khoa_normalize_ten_khoa($ten_khoa);
        $length = function_exists('mb_strlen') ? mb_strlen($ten_khoa, 'UTF-8') : strlen($ten_khoa);
        return $ten_khoa !== '' && $length <= 50;
    }

    // Nhựt sửa lỗi: giới hạn ghi chú để tránh payload quá lớn hoặc dữ liệu không hợp lệ.
    function khoa_valid_ghi_chu($ghi_chu) {
        $ghi_chu = (string)$ghi_chu;
        $length = function_exists('mb_strlen') ? mb_strlen($ghi_chu, 'UTF-8') : strlen($ghi_chu);
        return $length <= 2000;
    }

    // Nhựt sửa lỗi: giữ lại dữ liệu vừa nhập khi validate hoặc CSRF lỗi để cải thiện UX.
    function khoa_store_old_input($context, $ten_khoa, $ghi_chu, $id_khoa = null) {
        $_SESSION['khoa_old_input'] = array(
            'context' => $context,
            'ten_khoa' => $ten_khoa,
            'ghi_chu' => $ghi_chu,
            'id_khoa' => $id_khoa,
        );
    }

    // Nhựt sửa lỗi: xóa dữ liệu tạm sau khi thao tác thành công.
    function khoa_clear_old_input() {
        unset($_SESSION['khoa_old_input']);
    }

    // Nhựt sửa lỗi: rotate CSRF token sau thao tác thành công, tránh token sống quá lâu.
    function khoa_rotate_csrf_token() {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }
    
    if (isset($_GET['req'])){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !khoa_valid_csrf()) {
            if ($_GET['req'] === 'add') {
                khoa_store_old_input('add', khoa_post_text('ten_khoa'), khoa_post_text('ghi_chu'));
            }
            if ($_GET['req'] === 'update') {
                khoa_store_old_input('update', khoa_post_text('ten_khoa'), khoa_post_text('ghi_chu'), filter_input(INPUT_POST, 'id_khoa', FILTER_VALIDATE_INT));
            }
            khoa_redirect('csrf');
        }

        // Nhựt sửa lỗi: bắt riêng lỗi DB/PDOException cho toàn bộ thao tác thêm/sửa/xóa khoa.
        try {
        switch($_GET['req']){
            case 'add':

                $ten_khoa = khoa_normalize_ten_khoa(khoa_post_text('ten_khoa'));
                $ghi_chu = khoa_post_text('ghi_chu');

                // Nhựt sửa lỗi: phân biệt lỗi validate và lỗi trùng tên khoa khi thêm.
                if (!khoa_valid_ten_khoa($ten_khoa)) {
                    khoa_store_old_input('add', $ten_khoa, $ghi_chu);
                    khoa_redirect('invalid-ten-khoa');
                }

                if (!khoa_valid_ghi_chu($ghi_chu)) {
                    khoa_store_old_input('add', $ten_khoa, $ghi_chu);
                    khoa_redirect('invalid-ghichu');
                }

                if ($khoa->khoa__Name_Exists($ten_khoa)) {
                    khoa_store_old_input('add', $ten_khoa, $ghi_chu);
                    khoa_redirect('duplicate');
                }

                $status = $khoa->khoa__Add($ten_khoa, $ghi_chu);
                khoa_clear_old_input();
                khoa_rotate_csrf_token();
                khoa_redirect($status != 0 ? 'success' : 'failed');
                
                break;
            case 'update':

                $id_khoa = filter_input(INPUT_POST, 'id_khoa', FILTER_VALIDATE_INT);
                $ten_khoa = khoa_normalize_ten_khoa(khoa_post_text('ten_khoa'));
                $ghi_chu = khoa_post_text('ghi_chu');

                // Nhựt sửa lỗi: phân biệt lỗi id sai, không tìm thấy, validate và trùng tên khi cập nhật.
                if (!$id_khoa || $id_khoa < 1) {
                    khoa_store_old_input('update', $ten_khoa, $ghi_chu, $id_khoa);
                    khoa_redirect('invalid');
                }

                if (!khoa_valid_ten_khoa($ten_khoa)) {
                    khoa_store_old_input('update', $ten_khoa, $ghi_chu, $id_khoa);
                    khoa_redirect('invalid-ten-khoa');
                }

                if (!khoa_valid_ghi_chu($ghi_chu)) {
                    khoa_store_old_input('update', $ten_khoa, $ghi_chu, $id_khoa);
                    khoa_redirect('invalid-ghichu');
                }

                $khoa->connect->beginTransaction();
                if (!$khoa->khoa__Lock_By_Id($id_khoa)) {
                    $khoa->connect->rollBack();
                    khoa_redirect('not-found');
                }

                if ($khoa->khoa__Name_Exists($ten_khoa, $id_khoa)) {
                    $khoa->connect->rollBack();
                    khoa_store_old_input('update', $ten_khoa, $ghi_chu, $id_khoa);
                    khoa_redirect('duplicate');
                }

                $status = $khoa->khoa__Update($id_khoa, $ten_khoa, $ghi_chu);
                // Nhựt sửa lỗi: cập nhật không đổi dữ liệu vẫn là thao tác hợp lệ.
                $khoa->connect->commit();
                khoa_clear_old_input();
                khoa_rotate_csrf_token();
                khoa_redirect($status !== false ? 'success' : 'failed');
                
                break;

            case 'delete':

                $id_khoa = filter_input(INPUT_POST, 'id_khoa', FILTER_VALIDATE_INT);

                // Nhựt sửa lỗi: phân biệt lỗi id sai, không tìm thấy và bị ràng buộc dữ liệu khi xóa.
                if (!$id_khoa || $id_khoa < 1) {
                    khoa_redirect('invalid');
                }

                $khoa->connect->beginTransaction();
                if (!$khoa->khoa__Lock_By_Id($id_khoa)) {
                    $khoa->connect->rollBack();
                    khoa_redirect('not-found');
                }

                if ($khoa->khoa__Has_Related_Data($id_khoa)) {
                    $khoa->connect->rollBack();
                    khoa_redirect('related');
                }

                $status = $khoa->khoa__Delete($id_khoa);
                $khoa->connect->commit();
                khoa_rotate_csrf_token();
                khoa_redirect($status != 0 ? 'success' : 'failed');
                
                break;
            default:
                // Nhựt sửa lỗi: req không hợp lệ thì báo lỗi validate rõ ràng.
                khoa_redirect('invalid');
        }
        } catch (PDOException $e) {
            if ($khoa->connect->inTransaction()) {
                $khoa->connect->rollBack();
            }
            if ($e->getCode() === '23000') {
                khoa_redirect($_GET['req'] === 'delete' ? 'related' : 'duplicate');
            }
            khoa_redirect('system');
        } catch (Throwable $e) {
            if ($khoa->connect->inTransaction()) {
                $khoa->connect->rollBack();
            }
            khoa_redirect('system');
        }
    } else {
        // Nhựt sửa lỗi: thiếu req thì báo lỗi validate rõ ràng.
        khoa_redirect('invalid');
    }

?>
