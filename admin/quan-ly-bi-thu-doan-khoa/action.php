<?php

    require '../../models/getModel.php';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':
                $ten_bi_thu = isset($_POST['ten_bi_thu']) ? trim($_POST['ten_bi_thu']) : '';
                $gioi_tinh = isset($_POST['gioi_tinh']) ? $_POST['gioi_tinh'] : '';
                $ngay_sinh = isset($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : '';
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $so_dien_thoai_1 = isset($_POST['so_dien_thoai_1']) ? trim($_POST['so_dien_thoai_1']) : '';
                $so_dien_thoai_2 = isset($_POST['so_dien_thoai_2']) ? trim($_POST['so_dien_thoai_2']) : '';
                
                $dc_ll_chitiet = isset($_POST['dc_ll_chitiet']) ? trim($_POST['dc_ll_chitiet']) : '';
                $dc_ll_xa = isset($_POST['dc_ll_xa']) ? trim($_POST['dc_ll_xa']) : '';
                $dc_ll_tinh = isset($_POST['dc_ll_tinh']) ? trim($_POST['dc_ll_tinh']) : '';
                
                $dc_tt_chitiet = isset($_POST['dc_tt_chitiet']) ? trim($_POST['dc_tt_chitiet']) : '';
                $dc_tt_xa = isset($_POST['dc_tt_xa']) ? trim($_POST['dc_tt_xa']) : '';
                $dc_tt_tinh = isset($_POST['dc_tt_tinh']) ? trim($_POST['dc_tt_tinh']) : '';

                $dia_chi_lien_lac = $dc_ll_chitiet . ', ' . $dc_ll_xa . ', ' . $dc_ll_tinh;
                $dia_chi_thuong_tru = $dc_tt_chitiet . ', ' . $dc_tt_xa . ', ' . $dc_tt_tinh;
                $id_khoa = isset($_POST['id_khoa']) ? $_POST['id_khoa'] : '';
                
                // Nhựt sửa: Lưu lại giá trị form để fill lại khi lỗi
                $_SESSION['bithu_old_input'] = array_merge($_POST, ['context' => 'add']);

                // Backend Validation
                if (!preg_match('/^0[0-9]{9,10}$/', $so_dien_thoai_1) || !preg_match('/^0[0-9]{9,10}$/', $so_dien_thoai_2)) {
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=invalid-sdt');
                    exit();
                }
                $dob = DateTime::createFromFormat('Y-m-d', $ngay_sinh);
                if (!$dob || $dob->format('Y-m-d') !== $ngay_sinh || (new DateTime())->diff($dob)->y < 10 || (new DateTime())->diff($dob)->y > 100) {
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=invalid-ngay');
                    exit();
                }

                // Check if email already exists
                if ($bithudoankhoa->bithudoankhoa__Exists_Email($email)) {
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=duplicate-bithu');
                    exit();
                }
                
                $status = $bithudoankhoa->bithudoankhoa__Add($ten_bi_thu, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_khoa);
                
                if($status != 0){
                    unset($_SESSION['bithu_old_input']);
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=add-success');
                }else{
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=add-failed');
                }
                exit();
                
            case 'update':
                $id_bi_thu = isset($_POST['id_bi_thu']) ? $_POST['id_bi_thu'] : '';
                $ten_bi_thu = isset($_POST['ten_bi_thu']) ? trim($_POST['ten_bi_thu']) : '';
                $gioi_tinh = isset($_POST['gioi_tinh']) ? $_POST['gioi_tinh'] : '';
                $ngay_sinh = isset($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : '';
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $so_dien_thoai_1 = isset($_POST['so_dien_thoai_1']) ? trim($_POST['so_dien_thoai_1']) : '';
                $so_dien_thoai_2 = isset($_POST['so_dien_thoai_2']) ? trim($_POST['so_dien_thoai_2']) : '';
                
                $dc_ll_chitiet = isset($_POST['dc_ll_chitiet']) ? trim($_POST['dc_ll_chitiet']) : '';
                $dc_ll_xa = isset($_POST['dc_ll_xa']) ? trim($_POST['dc_ll_xa']) : '';
                $dc_ll_tinh = isset($_POST['dc_ll_tinh']) ? trim($_POST['dc_ll_tinh']) : '';
                
                $dc_tt_chitiet = isset($_POST['dc_tt_chitiet']) ? trim($_POST['dc_tt_chitiet']) : '';
                $dc_tt_xa = isset($_POST['dc_tt_xa']) ? trim($_POST['dc_tt_xa']) : '';
                $dc_tt_tinh = isset($_POST['dc_tt_tinh']) ? trim($_POST['dc_tt_tinh']) : '';

                $dia_chi_lien_lac = $dc_ll_chitiet . ', ' . $dc_ll_xa . ', ' . $dc_ll_tinh;
                $dia_chi_thuong_tru = $dc_tt_chitiet . ', ' . $dc_tt_xa . ', ' . $dc_tt_tinh;
                $id_khoa = isset($_POST['id_khoa']) ? $_POST['id_khoa'] : '';

                // Nhựt sửa: Lưu lại giá trị form để fill lại khi lỗi
                $_SESSION['bithu_old_input'] = array_merge($_POST, ['context' => 'update']);

                // Backend Validation
                if (!preg_match('/^0[0-9]{9,10}$/', $so_dien_thoai_1) || !preg_match('/^0[0-9]{9,10}$/', $so_dien_thoai_2)) {
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=invalid-sdt');
                    exit();
                }
                $dob = DateTime::createFromFormat('Y-m-d', $ngay_sinh);
                if (!$dob || $dob->format('Y-m-d') !== $ngay_sinh || (new DateTime())->diff($dob)->y < 10 || (new DateTime())->diff($dob)->y > 100) {
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=invalid-ngay');
                    exit();
                }

                // Check if email already exists (excluding current id_bi_thu)
                if ($bithudoankhoa->bithudoankhoa__Exists_Email($email, $id_bi_thu)) {
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=duplicate-bithu');
                    exit();
                }

                $bithudoankhoa->bithudoankhoa__Update($id_bi_thu, $ten_bi_thu, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_khoa);
                unset($_SESSION['bithu_old_input']);
                header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=update-success');
                exit();

            case 'delete':
                $id_bi_thu = isset($_GET['id_bi_thu']) ? $_GET['id_bi_thu'] : '';
                $status = $bithudoankhoa->bithudoankhoa__Delete($id_bi_thu);
                if($status != 0){
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=delete-success');
                }else{
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=delete-failed');
                }
                exit();
        }
    }
?>