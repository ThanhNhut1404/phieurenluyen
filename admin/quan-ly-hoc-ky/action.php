<?php

    session_start();
    // Nhựt sửa lỗi: chặn endpoint thêm/sửa/xóa học kỳ khi chưa đăng nhập admin.
    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }

    require '../../models/getModel.php';

    function hocky_redirect($status) {
        header('location: ../index.php?page=quan-ly-hoc-ky&status=' . $status);
        exit();
    }

    function hocky_valid_csrf() {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    function hocky_post_text($key) {
        return isset($_POST[$key]) ? trim($_POST[$key]) : '';
    }

    function hocky_normalize_ten_hoc_ky($ten_hoc_ky) {
        $ten_hoc_ky = trim((string)$ten_hoc_ky);
        $ten_hoc_ky = preg_replace('/\s+/u', ' ', $ten_hoc_ky);
        return $ten_hoc_ky === null ? '' : $ten_hoc_ky;
    }

    function hocky_valid_ten_hoc_ky($ten_hoc_ky) {
        $ten_hoc_ky = hocky_normalize_ten_hoc_ky($ten_hoc_ky);
        $length = function_exists('mb_strlen') ? mb_strlen($ten_hoc_ky, 'UTF-8') : strlen($ten_hoc_ky);
        return $ten_hoc_ky !== '' && $length <= 50;
    }

    function hocky_valid_ghi_chu($ghi_chu) {
        $ghi_chu = (string)$ghi_chu;
        $length = function_exists('mb_strlen') ? mb_strlen($ghi_chu, 'UTF-8') : strlen($ghi_chu);
        return $length <= 2000;
    }

    function hocky_valid_date($date) {
        $value = DateTime::createFromFormat('Y-m-d', (string)$date);
        $errors = DateTime::getLastErrors();
        return $value && $value->format('Y-m-d') === $date && (!$errors || ($errors['warning_count'] === 0 && $errors['error_count'] === 0));
    }

    function hocky_valid_date_range($ngay_bat_dau, $ngay_ket_thuc) {
        return hocky_valid_date($ngay_bat_dau)
            && hocky_valid_date($ngay_ket_thuc)
            && strtotime($ngay_ket_thuc) > strtotime($ngay_bat_dau);
    }

    function hocky_store_old_input($context, $ten_hoc_ky, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu, $id_nam_hoc, $id_hoc_ky = null) {
        $_SESSION['hocky_old_input'] = array(
            'context' => $context,
            'ten_hoc_ky' => $ten_hoc_ky,
            'ngay_bat_dau' => $ngay_bat_dau,
            'ngay_ket_thuc' => $ngay_ket_thuc,
            'ghi_chu' => $ghi_chu,
            'id_nam_hoc' => $id_nam_hoc,
            'id_hoc_ky' => $id_hoc_ky,
        );
    }

    function hocky_clear_old_input() {
        unset($_SESSION['hocky_old_input']);
    }

    function hocky_rotate_csrf_token() {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    if (!isset($_GET['req'])) {
        hocky_redirect('invalid');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hocky_valid_csrf()) {
        if ($_GET['req'] === 'add') {
            hocky_store_old_input('add', hocky_post_text('ten_hoc_ky'), hocky_post_text('ngay_bat_dau'), hocky_post_text('ngay_ket_thuc'), hocky_post_text('ghi_chu'), filter_input(INPUT_POST, 'id_nam_hoc', FILTER_VALIDATE_INT));
        }
        if ($_GET['req'] === 'update') {
            hocky_store_old_input('update', hocky_post_text('ten_hoc_ky'), hocky_post_text('ngay_bat_dau'), hocky_post_text('ngay_ket_thuc'), hocky_post_text('ghi_chu'), filter_input(INPUT_POST, 'id_nam_hoc', FILTER_VALIDATE_INT), filter_input(INPUT_POST, 'id_hoc_ky', FILTER_VALIDATE_INT));
        }
        hocky_redirect('csrf');
    }

    try {
        switch ($_GET['req']) {
            case 'add':
                $id_nam_hoc = filter_input(INPUT_POST, 'id_nam_hoc', FILTER_VALIDATE_INT);
                $ten_hoc_ky = hocky_normalize_ten_hoc_ky(hocky_post_text('ten_hoc_ky'));
                $ngay_bat_dau = hocky_post_text('ngay_bat_dau');
                $ngay_ket_thuc = hocky_post_text('ngay_ket_thuc');
                $ghi_chu = hocky_post_text('ghi_chu');

                if (!$id_nam_hoc || $id_nam_hoc < 1 || !$namhoc->namhoc__Get_By_Id($id_nam_hoc) || !hocky_valid_ten_hoc_ky($ten_hoc_ky) || !hocky_valid_date_range($ngay_bat_dau, $ngay_ket_thuc) || !hocky_valid_ghi_chu($ghi_chu)) {
                    hocky_store_old_input('add', $ten_hoc_ky, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu, $id_nam_hoc);
                    hocky_redirect('invalid');
                }

                if (!$hocky->hocky__Is_Within_Nam_Hoc($id_nam_hoc, $ngay_bat_dau, $ngay_ket_thuc)) {
                    hocky_store_old_input('add', $ten_hoc_ky, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu, $id_nam_hoc);
                    hocky_redirect('out-of-range-hoc-ky');
                }

                // Nhựt sửa lỗi: gom kiểm tra số lượng/trùng/chồng ngày và insert vào một transaction.
                $hocky->connect->beginTransaction();
                // Nhựt sửa lỗi: Mỗi năm học được phép tối đa 3 học kỳ, nên chỉ khóa khi đã có đủ 3 học kỳ.
                if ($hocky->hocky__Count_By_Nam_Hoc($id_nam_hoc) >= 3) {
                    $hocky->connect->rollBack();
                    hocky_store_old_input('add', $ten_hoc_ky, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu, $id_nam_hoc);
                    hocky_redirect('limit-hoc-ky');
                }

                if ($hocky->hocky__Name_Exists($ten_hoc_ky, $id_nam_hoc)) {
                    $hocky->connect->rollBack();
                    hocky_store_old_input('add', $ten_hoc_ky, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu, $id_nam_hoc);
                    hocky_redirect('duplicate-hoc-ky');
                }

                if ($hocky->hocky__Has_Overlap($id_nam_hoc, $ngay_bat_dau, $ngay_ket_thuc)) {
                    $hocky->connect->rollBack();
                    hocky_store_old_input('add', $ten_hoc_ky, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu, $id_nam_hoc);
                    hocky_redirect('overlap-hoc-ky');
                }

                $status = $hocky->hocky__Add($ten_hoc_ky, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu, $id_nam_hoc);
                $hocky->connect->commit();
                hocky_clear_old_input();
                hocky_rotate_csrf_token();
                hocky_redirect($status != 0 ? 'success' : 'failed');

                break;

            case 'update':
                $id_hoc_ky = filter_input(INPUT_POST, 'id_hoc_ky', FILTER_VALIDATE_INT);
                $id_nam_hoc = filter_input(INPUT_POST, 'id_nam_hoc', FILTER_VALIDATE_INT);
                $ten_hoc_ky = hocky_normalize_ten_hoc_ky(hocky_post_text('ten_hoc_ky'));
                $ngay_bat_dau = hocky_post_text('ngay_bat_dau');
                $ngay_ket_thuc = hocky_post_text('ngay_ket_thuc');
                $ghi_chu = hocky_post_text('ghi_chu');

                if (!$id_hoc_ky || $id_hoc_ky < 1 || !$id_nam_hoc || $id_nam_hoc < 1 || !$namhoc->namhoc__Get_By_Id($id_nam_hoc) || !hocky_valid_ten_hoc_ky($ten_hoc_ky) || !hocky_valid_date_range($ngay_bat_dau, $ngay_ket_thuc) || !hocky_valid_ghi_chu($ghi_chu)) {
                    hocky_store_old_input('update', $ten_hoc_ky, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu, $id_nam_hoc, $id_hoc_ky);
                    hocky_redirect('invalid');
                }

                $hocky->connect->beginTransaction();
                if (!$hocky->hocky__Lock_By_Id($id_hoc_ky)) {
                    $hocky->connect->rollBack();
                    hocky_redirect('not-found');
                }

                if (!$hocky->hocky__Is_Within_Nam_Hoc($id_nam_hoc, $ngay_bat_dau, $ngay_ket_thuc)) {
                    $hocky->connect->rollBack();
                    hocky_store_old_input('update', $ten_hoc_ky, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu, $id_nam_hoc, $id_hoc_ky);
                    hocky_redirect('out-of-range-hoc-ky');
                }

                // Nhựt sửa lỗi: Mỗi năm học được phép tối đa 3 học kỳ, nên chỉ khóa khi số học kỳ khác đã đủ 3.
                if ($hocky->hocky__Count_By_Nam_Hoc($id_nam_hoc, $id_hoc_ky) >= 3) {
                    $hocky->connect->rollBack();
                    hocky_store_old_input('update', $ten_hoc_ky, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu, $id_nam_hoc, $id_hoc_ky);
                    hocky_redirect('limit-hoc-ky');
                }

                if ($hocky->hocky__Name_Exists($ten_hoc_ky, $id_nam_hoc, $id_hoc_ky)) {
                    $hocky->connect->rollBack();
                    hocky_store_old_input('update', $ten_hoc_ky, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu, $id_nam_hoc, $id_hoc_ky);
                    hocky_redirect('duplicate-hoc-ky');
                }

                if ($hocky->hocky__Has_Overlap($id_nam_hoc, $ngay_bat_dau, $ngay_ket_thuc, $id_hoc_ky)) {
                    $hocky->connect->rollBack();
                    hocky_store_old_input('update', $ten_hoc_ky, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu, $id_nam_hoc, $id_hoc_ky);
                    hocky_redirect('overlap-hoc-ky');
                }

                $status = $hocky->hocky__Update($id_hoc_ky, $ten_hoc_ky, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu, $id_nam_hoc);
                $hocky->connect->commit();
                hocky_clear_old_input();
                hocky_rotate_csrf_token();
                hocky_redirect($status !== false ? 'success' : 'failed');

                break;

            case 'delete':
                $id_hoc_ky = filter_input(INPUT_POST, 'id_hoc_ky', FILTER_VALIDATE_INT);
                if (!$id_hoc_ky || $id_hoc_ky < 1) {
                    hocky_redirect('invalid');
                }

                $hocky->connect->beginTransaction();
                if (!$hocky->hocky__Lock_By_Id($id_hoc_ky)) {
                    $hocky->connect->rollBack();
                    hocky_redirect('not-found');
                }

                if ($hocky->hocky__Has_Related_Data($id_hoc_ky)) {
                    $hocky->connect->rollBack();
                    hocky_redirect('related-hoc-ky');
                }

                $status = $hocky->hocky__Delete($id_hoc_ky);
                $hocky->connect->commit();
                hocky_rotate_csrf_token();
                hocky_redirect($status != 0 ? 'success' : 'failed');

                break;

            default:
                hocky_redirect('invalid');
        }
    } catch (PDOException $e) {
        if ($hocky->connect->inTransaction()) {
            $hocky->connect->rollBack();
        }
        if ($e->getCode() === '23000') {
            hocky_redirect($_GET['req'] === 'delete' ? 'related-hoc-ky' : 'duplicate-hoc-ky');
        }
        hocky_redirect('system');
    } catch (Throwable $e) {
        if ($hocky->connect->inTransaction()) {
            $hocky->connect->rollBack();
        }
        hocky_redirect('system');
    }

?>
