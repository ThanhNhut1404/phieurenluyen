<?php

    session_start();
    // Nhựt sửa lỗi: chặn endpoint thêm/sửa/xóa trình độ khi chưa đăng nhập admin.
    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }

    require '../../models/getModel.php';

    function trinhdo_redirect($status) {
        header('location: ../index.php?page=quan-ly-trinh-do&status=' . $status);
        exit();
    }

    function trinhdo_valid_csrf() {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    function trinhdo_post_text($key) {
        return isset($_POST[$key]) ? trim($_POST[$key]) : '';
    }

    function trinhdo_normalize_ten_trinh_do($ten_trinh_do) {
        $ten_trinh_do = trim((string)$ten_trinh_do);
        $ten_trinh_do = preg_replace('/\s+/u', ' ', $ten_trinh_do);
        return $ten_trinh_do === null ? '' : $ten_trinh_do;
    }

    function trinhdo_valid_ten_trinh_do($ten_trinh_do) {
        $ten_trinh_do = trinhdo_normalize_ten_trinh_do($ten_trinh_do);
        $length = function_exists('mb_strlen') ? mb_strlen($ten_trinh_do, 'UTF-8') : strlen($ten_trinh_do);
        return $ten_trinh_do !== '' && $length <= 50;
    }

    function trinhdo_valid_ghi_chu($ghi_chu) {
        $ghi_chu = (string)$ghi_chu;
        $length = function_exists('mb_strlen') ? mb_strlen($ghi_chu, 'UTF-8') : strlen($ghi_chu);
        return $length <= 2000;
    }

    // Nhựt sửa lỗi: giữ dữ liệu vừa nhập khi validate hoặc CSRF lỗi.
    function trinhdo_store_old_input($context, $ten_trinh_do, $ghi_chu, $id_trinh_do = null) {
        $_SESSION['trinhdo_old_input'] = array(
            'context' => $context,
            'ten_trinh_do' => $ten_trinh_do,
            'ghi_chu' => $ghi_chu,
            'id_trinh_do' => $id_trinh_do,
        );
    }

    function trinhdo_clear_old_input() {
        unset($_SESSION['trinhdo_old_input']);
    }

    function trinhdo_rotate_csrf_token() {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    if (!isset($_GET['req'])) {
        trinhdo_redirect('invalid');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !trinhdo_valid_csrf()) {
        if ($_GET['req'] === 'add') {
            trinhdo_store_old_input('add', trinhdo_post_text('ten_trinh_do'), trinhdo_post_text('ghi_chu'));
        }
        if ($_GET['req'] === 'update') {
            trinhdo_store_old_input('update', trinhdo_post_text('ten_trinh_do'), trinhdo_post_text('ghi_chu'), filter_input(INPUT_POST, 'id_trinh_do', FILTER_VALIDATE_INT));
        }
        trinhdo_redirect('csrf');
    }

    try {
        switch ($_GET['req']) {
            case 'add':
                $ten_trinh_do = trinhdo_normalize_ten_trinh_do(trinhdo_post_text('ten_trinh_do'));
                $ghi_chu = trinhdo_post_text('ghi_chu');

                // Nhựt sửa lỗi: validate server-side cho tên trình độ và ghi chú.
                if (!trinhdo_valid_ten_trinh_do($ten_trinh_do)) {
                    trinhdo_store_old_input('add', $ten_trinh_do, $ghi_chu);
                    trinhdo_redirect('invalid-ten-trinh-do');
                }

                if (!trinhdo_valid_ghi_chu($ghi_chu)) {
                    trinhdo_store_old_input('add', $ten_trinh_do, $ghi_chu);
                    trinhdo_redirect('invalid-ghichu');
                }

                if ($trinhdo->trinhdo__Name_Exists($ten_trinh_do)) {
                    trinhdo_store_old_input('add', $ten_trinh_do, $ghi_chu);
                    trinhdo_redirect('duplicate-trinh-do');
                }

                $status = $trinhdo->trinhdo__Add($ten_trinh_do, $ghi_chu);
                trinhdo_clear_old_input();
                trinhdo_rotate_csrf_token();
                trinhdo_redirect($status != 0 ? 'add-success' : 'add-failed');

                break;

            case 'update':
                $id_trinh_do = filter_input(INPUT_POST, 'id_trinh_do', FILTER_VALIDATE_INT);
                $ten_trinh_do = trinhdo_normalize_ten_trinh_do(trinhdo_post_text('ten_trinh_do'));
                $ghi_chu = trinhdo_post_text('ghi_chu');

                if (!$id_trinh_do || $id_trinh_do < 1) {
                    trinhdo_store_old_input('update', $ten_trinh_do, $ghi_chu, $id_trinh_do);
                    trinhdo_redirect('invalid');
                }

                if (!trinhdo_valid_ten_trinh_do($ten_trinh_do)) {
                    trinhdo_store_old_input('update', $ten_trinh_do, $ghi_chu, $id_trinh_do);
                    trinhdo_redirect('invalid-ten-trinh-do');
                }

                if (!trinhdo_valid_ghi_chu($ghi_chu)) {
                    trinhdo_store_old_input('update', $ten_trinh_do, $ghi_chu, $id_trinh_do);
                    trinhdo_redirect('invalid-ghichu');
                }

                $trinhdo->connect->beginTransaction();
                if (!$trinhdo->trinhdo__Lock_By_Id($id_trinh_do)) {
                    $trinhdo->connect->rollBack();
                    trinhdo_redirect('not-found');
                }

                if ($trinhdo->trinhdo__Name_Exists($ten_trinh_do, $id_trinh_do)) {
                    $trinhdo->connect->rollBack();
                    trinhdo_store_old_input('update', $ten_trinh_do, $ghi_chu, $id_trinh_do);
                    trinhdo_redirect('duplicate-trinh-do');
                }

                $status = $trinhdo->trinhdo__Update($id_trinh_do, $ten_trinh_do, $ghi_chu);
                $trinhdo->connect->commit();
                trinhdo_clear_old_input();
                trinhdo_rotate_csrf_token();
                trinhdo_redirect($status !== false ? 'update-success' : 'update-failed');

                break;

            case 'delete':
                $id_trinh_do = filter_input(INPUT_POST, 'id_trinh_do', FILTER_VALIDATE_INT);
                if (!$id_trinh_do || $id_trinh_do < 1) {
                    trinhdo_redirect('invalid');
                }

                $trinhdo->connect->beginTransaction();
                if (!$trinhdo->trinhdo__Lock_By_Id($id_trinh_do)) {
                    $trinhdo->connect->rollBack();
                    trinhdo_redirect('not-found');
                }

                if ($trinhdo->trinhdo__Has_Related_Data($id_trinh_do)) {
                    $trinhdo->connect->rollBack();
                    trinhdo_redirect('related-trinh-do');
                }

                $status = $trinhdo->trinhdo__Delete($id_trinh_do);
                $trinhdo->connect->commit();
                trinhdo_rotate_csrf_token();
                trinhdo_redirect($status != 0 ? 'delete-success' : 'delete-failed');

                break;

            default:
                trinhdo_redirect('invalid');
        }
    } catch (PDOException $e) {
        if ($trinhdo->connect->inTransaction()) {
            $trinhdo->connect->rollBack();
        }
        if ($e->getCode() === '23000') {
            trinhdo_redirect($_GET['req'] === 'delete' ? 'related-trinh-do' : 'duplicate-trinh-do');
        }
        trinhdo_redirect('system');
    } catch (Throwable $e) {
        if ($trinhdo->connect->inTransaction()) {
            $trinhdo->connect->rollBack();
        }
        trinhdo_redirect('system');
    }

?>
