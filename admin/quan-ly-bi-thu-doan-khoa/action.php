<?php

    require '../../models/getModel.php';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
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
                
                $dc_ll_so_nha = isset($_POST['dc_ll_so_nha']) ? trim($_POST['dc_ll_so_nha']) : '';
                $dc_ll_ap = isset($_POST['dc_ll_ap']) ? trim($_POST['dc_ll_ap']) : '';
                $dc_ll_xa = isset($_POST['dc_ll_xa']) ? trim($_POST['dc_ll_xa']) : '';
                $dc_ll_tinh = isset($_POST['dc_ll_tinh']) ? trim($_POST['dc_ll_tinh']) : '';
                
                $dc_tt_so_nha = isset($_POST['dc_tt_so_nha']) ? trim($_POST['dc_tt_so_nha']) : '';
                $dc_tt_ap = isset($_POST['dc_tt_ap']) ? trim($_POST['dc_tt_ap']) : '';
                $dc_tt_xa = isset($_POST['dc_tt_xa']) ? trim($_POST['dc_tt_xa']) : '';
                $dc_tt_tinh = isset($_POST['dc_tt_tinh']) ? trim($_POST['dc_tt_tinh']) : '';

                $dia_chi_lien_lac = $dc_ll_so_nha . ', ' . $dc_ll_ap . ', ' . $dc_ll_xa . ', ' . $dc_ll_tinh;
                $dia_chi_thuong_tru = $dc_tt_so_nha . ', ' . $dc_tt_ap . ', ' . $dc_tt_xa . ', ' . $dc_tt_tinh;
                $id_khoa = isset($_POST['id_khoa']) ? $_POST['id_khoa'] : '';
                
                // Nhựt sửa: Lưu lại giá trị form để fill lại khi lỗi
                $_SESSION['bithu_old_input'] = array_merge($_POST, ['context' => 'add']);

                // Check if email already exists
                if ($bithudoankhoa->bithudoankhoa__Exists_Email($email)) {
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=duplicate-bithu');
                    exit();
                }
                
                $status = $bithudoankhoa->bithudoankhoa__Add($ten_bi_thu, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_khoa);
                
                if($status != 0){
                    unset($_SESSION['bithu_old_input']);
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=failed');
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
                
                $dc_ll_so_nha = isset($_POST['dc_ll_so_nha']) ? trim($_POST['dc_ll_so_nha']) : '';
                $dc_ll_ap = isset($_POST['dc_ll_ap']) ? trim($_POST['dc_ll_ap']) : '';
                $dc_ll_xa = isset($_POST['dc_ll_xa']) ? trim($_POST['dc_ll_xa']) : '';
                $dc_ll_tinh = isset($_POST['dc_ll_tinh']) ? trim($_POST['dc_ll_tinh']) : '';
                
                $dc_tt_so_nha = isset($_POST['dc_tt_so_nha']) ? trim($_POST['dc_tt_so_nha']) : '';
                $dc_tt_ap = isset($_POST['dc_tt_ap']) ? trim($_POST['dc_tt_ap']) : '';
                $dc_tt_xa = isset($_POST['dc_tt_xa']) ? trim($_POST['dc_tt_xa']) : '';
                $dc_tt_tinh = isset($_POST['dc_tt_tinh']) ? trim($_POST['dc_tt_tinh']) : '';

                $dia_chi_lien_lac = $dc_ll_so_nha . ', ' . $dc_ll_ap . ', ' . $dc_ll_xa . ', ' . $dc_ll_tinh;
                $dia_chi_thuong_tru = $dc_tt_so_nha . ', ' . $dc_tt_ap . ', ' . $dc_tt_xa . ', ' . $dc_tt_tinh;
                $id_khoa = isset($_POST['id_khoa']) ? $_POST['id_khoa'] : '';

                // Nhựt sửa: Lưu lại giá trị form để fill lại khi lỗi
                $_SESSION['bithu_old_input'] = array_merge($_POST, ['context' => 'update']);

                // Check if email already exists (excluding current id_bi_thu)
                if ($bithudoankhoa->bithudoankhoa__Exists_Email($email, $id_bi_thu)) {
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=duplicate-bithu');
                    exit();
                }

                $bithudoankhoa->bithudoankhoa__Update($id_bi_thu, $ten_bi_thu, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_khoa);
                unset($_SESSION['bithu_old_input']);
                header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=success');
                exit();

            case 'delete':
                $id_bi_thu = isset($_GET['id_bi_thu']) ? $_GET['id_bi_thu'] : '';
                $status = $bithudoankhoa->bithudoankhoa__Delete($id_bi_thu);
                if($status != 0){
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=failed');
                }
                exit();
        }
    }
?>