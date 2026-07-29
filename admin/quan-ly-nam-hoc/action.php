<?php

    session_start();
    // Nhựt sửa lỗi: chặn endpoint thêm/sửa/xóa năm học khi chưa đăng nhập admin.
    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }

    require '../../models/getModel.php';

    function namhoc_redirect($status) {
        header('location: ../index.php?page=quan-ly-nam-hoc&status=' . $status);
        exit();
    }

    function namhoc_valid_csrf() {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    function namhoc_post_text($key) {
        return isset($_POST[$key]) ? trim($_POST[$key]) : '';
    }

    function namhoc_normalize_ten_nam_hoc($ten_nam_hoc) {
        $ten_nam_hoc = trim((string)$ten_nam_hoc);
        $ten_nam_hoc = preg_replace('/\s+/u', ' ', $ten_nam_hoc);
        return $ten_nam_hoc === null ? '' : $ten_nam_hoc;
    }

    function namhoc_valid_ten_nam_hoc($ten_nam_hoc) {
        $ten_nam_hoc = namhoc_normalize_ten_nam_hoc($ten_nam_hoc);
        $length = function_exists('mb_strlen') ? mb_strlen($ten_nam_hoc, 'UTF-8') : strlen($ten_nam_hoc);
        return $ten_nam_hoc !== '' && $length <= 50;
    }

    function namhoc_valid_ghi_chu($ghi_chu) {
        $ghi_chu = (string)$ghi_chu;
        $length = function_exists('mb_strlen') ? mb_strlen($ghi_chu, 'UTF-8') : strlen($ghi_chu);
        return $length <= 2000;
    }

    // Nhựt sửa lỗi: giữ dữ liệu vừa nhập khi validate hoặc CSRF lỗi.
    function namhoc_store_old_input($context, $ten_nam_hoc, $ghi_chu, $id_nam_hoc = null) {
        $_SESSION['namhoc_old_input'] = array(
            'context' => $context,
            'ten_nam_hoc' => $ten_nam_hoc,
            'ghi_chu' => $ghi_chu,
            'id_nam_hoc' => $id_nam_hoc,
        );
    }

    function namhoc_clear_old_input() {
        unset($_SESSION['namhoc_old_input']);
    }

    function namhoc_rotate_csrf_token() {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    if (!isset($_GET['req'])) {
        namhoc_redirect('invalid');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !namhoc_valid_csrf()) {
        if ($_GET['req'] === 'add') {
            namhoc_store_old_input('add', namhoc_post_text('ten_nam_hoc'), namhoc_post_text('ghi_chu'));
        }
        if ($_GET['req'] === 'update') {
            namhoc_store_old_input('update', namhoc_post_text('ten_nam_hoc'), namhoc_post_text('ghi_chu'), filter_input(INPUT_POST, 'id_nam_hoc', FILTER_VALIDATE_INT));
        }
        namhoc_redirect('csrf');
    }

    try {
        switch ($_GET['req']) {
            case 'add':
                $ten_nam_hoc = namhoc_normalize_ten_nam_hoc(namhoc_post_text('ten_nam_hoc'));
                $ghi_chu = namhoc_post_text('ghi_chu');

                if (!namhoc_valid_ten_nam_hoc($ten_nam_hoc) || !namhoc_valid_ghi_chu($ghi_chu)) {
                    namhoc_store_old_input('add', $ten_nam_hoc, $ghi_chu);
                    namhoc_redirect('invalid');
                }

                if ($namhoc->namhoc__Name_Exists($ten_nam_hoc)) {
                    namhoc_store_old_input('add', $ten_nam_hoc, $ghi_chu);
                    namhoc_redirect('duplicate-nam-hoc');
                }

                $status = $namhoc->namhoc__Add($ten_nam_hoc, $ghi_chu);
                namhoc_clear_old_input();
                namhoc_rotate_csrf_token();
                namhoc_redirect($status != 0 ? 'success' : 'failed');

                break;

            case 'update':
                $id_nam_hoc = filter_input(INPUT_POST, 'id_nam_hoc', FILTER_VALIDATE_INT);
                $ten_nam_hoc = namhoc_normalize_ten_nam_hoc(namhoc_post_text('ten_nam_hoc'));
                $ghi_chu = namhoc_post_text('ghi_chu');

                if (!$id_nam_hoc || $id_nam_hoc < 1 || !namhoc_valid_ten_nam_hoc($ten_nam_hoc) || !namhoc_valid_ghi_chu($ghi_chu)) {
                    namhoc_store_old_input('update', $ten_nam_hoc, $ghi_chu, $id_nam_hoc);
                    namhoc_redirect('invalid');
                }

                $namhoc->connect->beginTransaction();
                if (!$namhoc->namhoc__Lock_By_Id($id_nam_hoc)) {
                    $namhoc->connect->rollBack();
                    namhoc_redirect('not-found');
                }

                if ($namhoc->namhoc__Name_Exists($ten_nam_hoc, $id_nam_hoc)) {
                    $namhoc->connect->rollBack();
                    namhoc_store_old_input('update', $ten_nam_hoc, $ghi_chu, $id_nam_hoc);
                    namhoc_redirect('duplicate-nam-hoc');
                }

                $status = $namhoc->namhoc__Update($id_nam_hoc, $ten_nam_hoc, $ghi_chu);
                $namhoc->connect->commit();
                namhoc_clear_old_input();
                namhoc_rotate_csrf_token();
                namhoc_redirect($status !== false ? 'success' : 'failed');

                break;

            case 'delete':
                $id_nam_hoc = filter_input(INPUT_POST, 'id_nam_hoc', FILTER_VALIDATE_INT);
                if (!$id_nam_hoc || $id_nam_hoc < 1) {
                    namhoc_redirect('invalid');
                }

                $namhoc->connect->beginTransaction();
                if (!$namhoc->namhoc__Lock_By_Id($id_nam_hoc)) {
                    $namhoc->connect->rollBack();
                    namhoc_redirect('not-found');
                }

                if ($namhoc->namhoc__Has_Related_Data($id_nam_hoc)) {
                    $namhoc->connect->rollBack();
                    namhoc_redirect('related-nam-hoc');
                }

                $status = $namhoc->namhoc__Delete($id_nam_hoc);
                $namhoc->connect->commit();
                namhoc_rotate_csrf_token();
                namhoc_redirect($status != 0 ? 'success' : 'failed');

                break;

            default:
                namhoc_redirect('invalid');
        }
    } catch (PDOException $e) {
        if ($namhoc->connect->inTransaction()) {
            $namhoc->connect->rollBack();
        }
        if ($e->getCode() === '23000') {
            namhoc_redirect($_GET['req'] === 'delete' ? 'related-nam-hoc' : 'duplicate-nam-hoc');
        }
        namhoc_redirect('system');
    } catch (Throwable $e) {
        if ($namhoc->connect->inTransaction()) {
            $namhoc->connect->rollBack();
        }
        namhoc_redirect('system');
    }

?>
