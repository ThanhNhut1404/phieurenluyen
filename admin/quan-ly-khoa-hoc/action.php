<?php

    session_start();
    // Nhựt sửa lỗi: chặn endpoint thêm/sửa/xóa khóa học khi chưa đăng nhập admin.
    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }

    require '../../models/getModel.php';

    function khoahoc_redirect($status) {
        header('location: ../index.php?page=quan-ly-khoa-hoc&status=' . $status);
        exit();
    }

    function khoahoc_valid_csrf() {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    function khoahoc_post_text($key) {
        return isset($_POST[$key]) ? trim($_POST[$key]) : '';
    }

    function khoahoc_normalize_ten_khoa_hoc($ten_khoa_hoc) {
        $ten_khoa_hoc = trim((string)$ten_khoa_hoc);
        $ten_khoa_hoc = preg_replace('/\s+/u', ' ', $ten_khoa_hoc);
        return $ten_khoa_hoc === null ? '' : $ten_khoa_hoc;
    }

    function khoahoc_valid_ten_khoa_hoc($ten_khoa_hoc) {
        $ten_khoa_hoc = khoahoc_normalize_ten_khoa_hoc($ten_khoa_hoc);
        $length = function_exists('mb_strlen') ? mb_strlen($ten_khoa_hoc, 'UTF-8') : strlen($ten_khoa_hoc);
        return $ten_khoa_hoc !== '' && $length <= 50;
    }

    function khoahoc_valid_nam_nhap_hoc($nam_nhap_hoc) {
        return filter_var($nam_nhap_hoc, FILTER_VALIDATE_INT) !== false
            && (int)$nam_nhap_hoc >= 2006
            && (int)$nam_nhap_hoc <= 2099;
    }

    function khoahoc_valid_he_dao_tao($he_dao_tao) {
        if (!is_numeric($he_dao_tao)) {
            return false;
        }
        $he_dao_tao = (float)$he_dao_tao;
        return $he_dao_tao >= 2 && $he_dao_tao <= 8;
    }

    function khoahoc_valid_ghi_chu($ghi_chu) {
        $ghi_chu = (string)$ghi_chu;
        $length = function_exists('mb_strlen') ? mb_strlen($ghi_chu, 'UTF-8') : strlen($ghi_chu);
        return $length <= 2000;
    }

    // Nhựt sửa lỗi: giữ dữ liệu vừa nhập khi validate hoặc CSRF lỗi.
    function khoahoc_store_old_input($context, $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu, $id_khoa_hoc = null) {
        $_SESSION['khoahoc_old_input'] = array(
            'context' => $context,
            'ten_khoa_hoc' => $ten_khoa_hoc,
            'nam_nhap_hoc' => $nam_nhap_hoc,
            'he_dao_tao' => $he_dao_tao,
            'ghi_chu' => $ghi_chu,
            'id_khoa_hoc' => $id_khoa_hoc,
        );
    }

    function khoahoc_clear_old_input() {
        unset($_SESSION['khoahoc_old_input']);
    }

    function khoahoc_rotate_csrf_token() {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    if (!isset($_GET['req'])) {
        khoahoc_redirect('invalid');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !khoahoc_valid_csrf()) {
        if ($_GET['req'] === 'add') {
            khoahoc_store_old_input('add', khoahoc_post_text('ten_khoa_hoc'), khoahoc_post_text('nam_nhap_hoc'), khoahoc_post_text('he_dao_tao'), khoahoc_post_text('ghi_chu'));
        }
        if ($_GET['req'] === 'update') {
            khoahoc_store_old_input('update', khoahoc_post_text('ten_khoa_hoc'), khoahoc_post_text('nam_nhap_hoc'), khoahoc_post_text('he_dao_tao'), khoahoc_post_text('ghi_chu'), filter_input(INPUT_POST, 'id_khoa_hoc', FILTER_VALIDATE_INT));
        }
        khoahoc_redirect('csrf');
    }

    try {
        switch ($_GET['req']) {
            case 'add':
                $ten_khoa_hoc = khoahoc_normalize_ten_khoa_hoc(khoahoc_post_text('ten_khoa_hoc'));
                $nam_nhap_hoc = khoahoc_post_text('nam_nhap_hoc');
                $he_dao_tao = khoahoc_post_text('he_dao_tao');
                $ghi_chu = khoahoc_post_text('ghi_chu');

                // Nhựt sửa lỗi: validate server-side cho các trường bắt buộc và giới hạn ghi chú.
                if (!khoahoc_valid_ten_khoa_hoc($ten_khoa_hoc)) {
                    khoahoc_store_old_input('add', $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu);
                    khoahoc_redirect('invalid-ten-khoahoc');
                }

                if (!khoahoc_valid_nam_nhap_hoc($nam_nhap_hoc)) {
                    khoahoc_store_old_input('add', $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu);
                    khoahoc_redirect('invalid-nam-nhap-hoc');
                }

                if (!khoahoc_valid_he_dao_tao($he_dao_tao)) {
                    khoahoc_store_old_input('add', $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu);
                    khoahoc_redirect('invalid-he-dao-tao');
                }

                if (!khoahoc_valid_ghi_chu($ghi_chu)) {
                    khoahoc_store_old_input('add', $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu);
                    khoahoc_redirect('invalid-ghichu');
                }

                if ($khoahoc->khoahoc__Name_Exists($ten_khoa_hoc)) {
                    khoahoc_store_old_input('add', $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu);
                    khoahoc_redirect('duplicate-khoa-hoc');
                }

                $status = $khoahoc->khoahoc__Add($ten_khoa_hoc, (int)$nam_nhap_hoc, (float)$he_dao_tao, $ghi_chu);
                khoahoc_clear_old_input();
                khoahoc_rotate_csrf_token();
                khoahoc_redirect($status != 0 ? 'add-success' : 'add-failed');

                break;

            case 'update':
                $id_khoa_hoc = filter_input(INPUT_POST, 'id_khoa_hoc', FILTER_VALIDATE_INT);
                $ten_khoa_hoc = khoahoc_normalize_ten_khoa_hoc(khoahoc_post_text('ten_khoa_hoc'));
                $nam_nhap_hoc = khoahoc_post_text('nam_nhap_hoc');
                $he_dao_tao = khoahoc_post_text('he_dao_tao');
                $ghi_chu = khoahoc_post_text('ghi_chu');

                if (!$id_khoa_hoc || $id_khoa_hoc < 1) {
                    khoahoc_store_old_input('update', $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu, $id_khoa_hoc);
                    khoahoc_redirect('invalid');
                }

                if (!khoahoc_valid_ten_khoa_hoc($ten_khoa_hoc)) {
                    khoahoc_store_old_input('update', $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu, $id_khoa_hoc);
                    khoahoc_redirect('invalid-ten-khoahoc');
                }

                if (!khoahoc_valid_nam_nhap_hoc($nam_nhap_hoc)) {
                    khoahoc_store_old_input('update', $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu, $id_khoa_hoc);
                    khoahoc_redirect('invalid-nam-nhap-hoc');
                }

                if (!khoahoc_valid_he_dao_tao($he_dao_tao)) {
                    khoahoc_store_old_input('update', $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu, $id_khoa_hoc);
                    khoahoc_redirect('invalid-he-dao-tao');
                }

                if (!khoahoc_valid_ghi_chu($ghi_chu)) {
                    khoahoc_store_old_input('update', $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu, $id_khoa_hoc);
                    khoahoc_redirect('invalid-ghichu');
                }

                $khoahoc->connect->beginTransaction();
                if (!$khoahoc->khoahoc__Lock_By_Id($id_khoa_hoc)) {
                    $khoahoc->connect->rollBack();
                    khoahoc_redirect('not-found');
                }

                if ($khoahoc->khoahoc__Name_Exists($ten_khoa_hoc, $id_khoa_hoc)) {
                    $khoahoc->connect->rollBack();
                    khoahoc_store_old_input('update', $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu, $id_khoa_hoc);
                    khoahoc_redirect('duplicate-khoa-hoc');
                }

                $status = $khoahoc->khoahoc__Update($id_khoa_hoc, $ten_khoa_hoc, (int)$nam_nhap_hoc, (float)$he_dao_tao, $ghi_chu);
                $khoahoc->connect->commit();
                khoahoc_clear_old_input();
                khoahoc_rotate_csrf_token();
                khoahoc_redirect($status !== false ? 'update-success' : 'update-failed');

                break;

            case 'delete':
                $id_khoa_hoc = filter_input(INPUT_POST, 'id_khoa_hoc', FILTER_VALIDATE_INT);
                if (!$id_khoa_hoc || $id_khoa_hoc < 1) {
                    khoahoc_redirect('invalid');
                }

                $khoahoc->connect->beginTransaction();
                if (!$khoahoc->khoahoc__Lock_By_Id($id_khoa_hoc)) {
                    $khoahoc->connect->rollBack();
                    khoahoc_redirect('not-found');
                }

                if ($khoahoc->khoahoc__Has_Related_Data($id_khoa_hoc)) {
                    $khoahoc->connect->rollBack();
                    khoahoc_redirect('related-khoa-hoc');
                }

                $status = $khoahoc->khoahoc__Delete($id_khoa_hoc);
                $khoahoc->connect->commit();
                khoahoc_rotate_csrf_token();
                khoahoc_redirect($status != 0 ? 'delete-success' : 'delete-failed');

                break;

            default:
                khoahoc_redirect('invalid');
        }
    } catch (PDOException $e) {
        if ($khoahoc->connect->inTransaction()) {
            $khoahoc->connect->rollBack();
        }
        if ($e->getCode() === '23000') {
            khoahoc_redirect($_GET['req'] === 'delete' ? 'related-khoa-hoc' : 'duplicate-khoa-hoc');
        }
        khoahoc_redirect('system');
    } catch (Throwable $e) {
        if ($khoahoc->connect->inTransaction()) {
            $khoahoc->connect->rollBack();
        }
        khoahoc_redirect('system');
    }

?>
