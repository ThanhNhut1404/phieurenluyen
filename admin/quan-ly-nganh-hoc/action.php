<?php

    session_start();
    // Nhựt sửa lỗi: chặn endpoint thêm/sửa/xóa ngành học khi chưa đăng nhập admin.
    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }

    require '../../models/getModel.php';

    // Nhựt sửa lỗi: gom redirect để luôn dừng xử lý sau khi chuyển trang.
    function nganhhoc_redirect($status) {
        header('location: ../index.php?page=quan-ly-nganh-hoc&status=' . $status);
        exit();
    }

    // Nhựt sửa lỗi: kiểm tra CSRF token cho các request thay đổi dữ liệu.
    function nganhhoc_valid_csrf() {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    // Nhựt sửa lỗi: lấy chuỗi POST an toàn và loại bỏ khoảng trắng thừa.
    function nganhhoc_post_text($key) {
        return isset($_POST[$key]) ? trim($_POST[$key]) : '';
    }

    // Nhựt sửa lỗi: chuẩn hóa tên ngành học trước khi validate và kiểm tra trùng.
    function nganhhoc_normalize_ten_nganh_hoc($ten_nganh_hoc) {
        $ten_nganh_hoc = trim((string)$ten_nganh_hoc);
        $ten_nganh_hoc = preg_replace('/\s+/u', ' ', $ten_nganh_hoc);
        return $ten_nganh_hoc === null ? '' : $ten_nganh_hoc;
    }

    // Nhựt sửa lỗi: kiểm tra tên ngành học không rỗng và không vượt quá varchar(50).
    function nganhhoc_valid_ten_nganh_hoc($ten_nganh_hoc) {
        $ten_nganh_hoc = nganhhoc_normalize_ten_nganh_hoc($ten_nganh_hoc);
        $length = function_exists('mb_strlen') ? mb_strlen($ten_nganh_hoc, 'UTF-8') : strlen($ten_nganh_hoc);
        return $ten_nganh_hoc !== '' && $length <= 50;
    }

    // Nhựt sửa lỗi: giới hạn ghi chú để tránh payload quá lớn hoặc dữ liệu không hợp lệ.
    function nganhhoc_valid_ghi_chu($ghi_chu) {
        $ghi_chu = (string)$ghi_chu;
        $length = function_exists('mb_strlen') ? mb_strlen($ghi_chu, 'UTF-8') : strlen($ghi_chu);
        return $length <= 2000;
    }

    // Nhựt sửa lỗi: kiểm tra khoa được chọn có tồn tại.
    function nganhhoc_khoa_exists($khoa, $id_khoa) {
        return $id_khoa && $id_khoa > 0 && $khoa->khoa__Get_By_Id($id_khoa);
    }

    // Nhựt sửa lỗi: giữ lại dữ liệu vừa nhập khi validate hoặc CSRF lỗi để cải thiện UX.
    function nganhhoc_store_old_input($context, $id_khoa, $ten_nganh_hoc, $ghi_chu, $id_nganh_hoc = null) {
        $_SESSION['nganhhoc_old_input'] = array(
            'context' => $context,
            'id_khoa' => $id_khoa,
            'ten_nganh_hoc' => $ten_nganh_hoc,
            'ghi_chu' => $ghi_chu,
            'id_nganh_hoc' => $id_nganh_hoc,
        );
    }

    // Nhựt sửa lỗi: xóa dữ liệu tạm sau khi thao tác thành công.
    function nganhhoc_clear_old_input() {
        unset($_SESSION['nganhhoc_old_input']);
    }

    // Nhựt sửa lỗi: rotate CSRF token sau thao tác thành công, tránh token sống quá lâu.
    function nganhhoc_rotate_csrf_token() {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }
    
    if (isset($_GET['req'])){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !nganhhoc_valid_csrf()) {
            if ($_GET['req'] === 'add') {
                nganhhoc_store_old_input('add', filter_input(INPUT_POST, 'id_khoa', FILTER_VALIDATE_INT), nganhhoc_post_text('ten_nganh_hoc'), nganhhoc_post_text('ghi_chu'));
            }
            if ($_GET['req'] === 'update') {
                nganhhoc_store_old_input('update', filter_input(INPUT_POST, 'id_khoa', FILTER_VALIDATE_INT), nganhhoc_post_text('ten_nganh_hoc'), nganhhoc_post_text('ghi_chu'), filter_input(INPUT_POST, 'id_nganh_hoc', FILTER_VALIDATE_INT));
            }
            nganhhoc_redirect('csrf');
        }

        // Nhựt sửa lỗi: bắt riêng lỗi DB/PDOException cho toàn bộ thao tác thêm/sửa/xóa ngành học.
        try {
        switch($_GET['req']){
            case 'add':

                $id_khoa = filter_input(INPUT_POST, 'id_khoa', FILTER_VALIDATE_INT);
                $ten_nganh_hoc = nganhhoc_normalize_ten_nganh_hoc(nganhhoc_post_text('ten_nganh_hoc'));
                $ghi_chu = nganhhoc_post_text('ghi_chu');

                // Nhựt sửa lỗi: phân biệt lỗi validate, khoa không tồn tại và lỗi trùng tên ngành học khi thêm.
                if (!nganhhoc_khoa_exists($khoa, $id_khoa)) {
                    nganhhoc_store_old_input('add', $id_khoa, $ten_nganh_hoc, $ghi_chu);
                    nganhhoc_redirect('invalid-khoa');
                }

                if (!nganhhoc_valid_ten_nganh_hoc($ten_nganh_hoc)) {
                    nganhhoc_store_old_input('add', $id_khoa, $ten_nganh_hoc, $ghi_chu);
                    nganhhoc_redirect('invalid-ten-nganh-hoc');
                }

                if (!nganhhoc_valid_ghi_chu($ghi_chu)) {
                    nganhhoc_store_old_input('add', $id_khoa, $ten_nganh_hoc, $ghi_chu);
                    nganhhoc_redirect('invalid-ghichu');
                }

                if ($nganhhoc->nganhhoc__Name_Exists($ten_nganh_hoc, $id_khoa)) {
                    nganhhoc_store_old_input('add', $id_khoa, $ten_nganh_hoc, $ghi_chu);
                    nganhhoc_redirect('duplicate-nganh-hoc');
                }

                $status = $nganhhoc->nganhhoc__Add($ten_nganh_hoc, $ghi_chu, $id_khoa);
                nganhhoc_clear_old_input();
                nganhhoc_rotate_csrf_token();
                nganhhoc_redirect($status != 0 ? 'add-success' : 'add-failed');
                
                break;
            case 'update':

                $id_nganh_hoc = filter_input(INPUT_POST, 'id_nganh_hoc', FILTER_VALIDATE_INT);
                $id_khoa = filter_input(INPUT_POST, 'id_khoa', FILTER_VALIDATE_INT);
                $ten_nganh_hoc = nganhhoc_normalize_ten_nganh_hoc(nganhhoc_post_text('ten_nganh_hoc'));
                $ghi_chu = nganhhoc_post_text('ghi_chu');

                // Nhựt sửa lỗi: phân biệt lỗi id sai, không tìm thấy, validate và trùng tên khi cập nhật.
                if (!$id_nganh_hoc || $id_nganh_hoc < 1) {
                    nganhhoc_store_old_input('update', $id_khoa, $ten_nganh_hoc, $ghi_chu, $id_nganh_hoc);
                    nganhhoc_redirect('invalid');
                }

                if (!nganhhoc_khoa_exists($khoa, $id_khoa)) {
                    nganhhoc_store_old_input('update', $id_khoa, $ten_nganh_hoc, $ghi_chu, $id_nganh_hoc);
                    nganhhoc_redirect('invalid-khoa');
                }

                if (!nganhhoc_valid_ten_nganh_hoc($ten_nganh_hoc)) {
                    nganhhoc_store_old_input('update', $id_khoa, $ten_nganh_hoc, $ghi_chu, $id_nganh_hoc);
                    nganhhoc_redirect('invalid-ten-nganh-hoc');
                }

                if (!nganhhoc_valid_ghi_chu($ghi_chu)) {
                    nganhhoc_store_old_input('update', $id_khoa, $ten_nganh_hoc, $ghi_chu, $id_nganh_hoc);
                    nganhhoc_redirect('invalid-ghichu');
                }

                $nganhhoc->connect->beginTransaction();
                if (!$nganhhoc->nganhhoc__Lock_By_Id($id_nganh_hoc)) {
                    $nganhhoc->connect->rollBack();
                    nganhhoc_redirect('not-found');
                }

                if ($nganhhoc->nganhhoc__Name_Exists($ten_nganh_hoc, $id_khoa, $id_nganh_hoc)) {
                    $nganhhoc->connect->rollBack();
                    nganhhoc_store_old_input('update', $id_khoa, $ten_nganh_hoc, $ghi_chu, $id_nganh_hoc);
                    nganhhoc_redirect('duplicate-nganh-hoc');
                }

                $status = $nganhhoc->nganhhoc__Update($id_nganh_hoc, $ten_nganh_hoc, $ghi_chu, $id_khoa);
                $nganhhoc->connect->commit();
                nganhhoc_clear_old_input();
                nganhhoc_rotate_csrf_token();
                nganhhoc_redirect($status !== false ? 'update-success' : 'update-failed');
                
                break;

            case 'delete':

                $id_nganh_hoc = filter_input(INPUT_POST, 'id_nganh_hoc', FILTER_VALIDATE_INT);

                // Nhựt sửa lỗi: phân biệt lỗi id sai, không tìm thấy và bị ràng buộc dữ liệu khi xóa.
                if (!$id_nganh_hoc || $id_nganh_hoc < 1) {
                    nganhhoc_redirect('invalid');
                }

                $nganhhoc->connect->beginTransaction();
                if (!$nganhhoc->nganhhoc__Lock_By_Id($id_nganh_hoc)) {
                    $nganhhoc->connect->rollBack();
                    nganhhoc_redirect('not-found');
                }

                if ($nganhhoc->nganhhoc__Has_Related_Data($id_nganh_hoc)) {
                    $nganhhoc->connect->rollBack();
                    nganhhoc_redirect('related-nganh-hoc');
                }

                $status = $nganhhoc->nganhhoc__Delete($id_nganh_hoc);
                $nganhhoc->connect->commit();
                nganhhoc_rotate_csrf_token();
                nganhhoc_redirect($status != 0 ? 'delete-success' : 'delete-failed');
                
                break;
            default:
                // Nhựt sửa lỗi: req không hợp lệ thì báo lỗi validate rõ ràng.
                nganhhoc_redirect('invalid');
        }
        } catch (PDOException $e) {
            if ($nganhhoc->connect->inTransaction()) {
                $nganhhoc->connect->rollBack();
            }
            if ($e->getCode() === '23000') {
                nganhhoc_redirect($_GET['req'] === 'delete' ? 'related-nganh-hoc' : 'duplicate-nganh-hoc');
            }
            nganhhoc_redirect('system');
        } catch (Throwable $e) {
            if ($nganhhoc->connect->inTransaction()) {
                $nganhhoc->connect->rollBack();
            }
            nganhhoc_redirect('system');
        }
    } else {
        // Nhựt sửa lỗi: thiếu req thì báo lỗi validate rõ ràng.
        nganhhoc_redirect('invalid');
    }

?>
