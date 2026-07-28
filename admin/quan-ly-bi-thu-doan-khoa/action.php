<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':
                
                $ten_bi_thu = $_POST['ten_bi_thu'];
                $gioi_tinh = $_POST['gioi_tinh'];
                $ngay_sinh = $_POST['ngay_sinh'];
                $email = $_POST['email'];
                $so_dien_thoai_1 = $_POST['so_dien_thoai_1'];
                $so_dien_thoai_2 = $_POST['so_dien_thoai_2'];
                // quân sửa: Ghép 4 trường địa chỉ lại thành 1 chuỗi
                $dia_chi_lien_lac = $_POST['dc_ll_so_nha'] . ', ' . $_POST['dc_ll_ap'] . ', ' . $_POST['dc_ll_xa'] . ', ' . $_POST['dc_ll_tinh'];
                $dia_chi_thuong_tru = $_POST['dc_tt_so_nha'] . ', ' . $_POST['dc_tt_ap'] . ', ' . $_POST['dc_tt_xa'] . ', ' . $_POST['dc_tt_tinh'];
                $id_khoa = $_POST['id_khoa'];
                
                // quân sửa: Kiểm tra trùng Tên HOẶC Email trước khi thêm
                if ($bithudoankhoa->bithudoankhoa__Check_Duplicate($ten_bi_thu, $email) > 0) {
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=duplicate-bithu');
                    break;
                }
                
                $status = $bithudoankhoa->bithudoankhoa__Add($ten_bi_thu, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_khoa);
                
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=failed');
                }
                
                break;
            case 'update':

                $id_bi_thu = $_POST['id_bi_thu'];
                $ten_bi_thu = $_POST['ten_bi_thu'];
                $gioi_tinh = $_POST['gioi_tinh'];
                $ngay_sinh = $_POST['ngay_sinh'];
                $email = $_POST['email'];
                $so_dien_thoai_1 = $_POST['so_dien_thoai_1'];
                $so_dien_thoai_2 = $_POST['so_dien_thoai_2'];
                // quân sửa: Ghép 4 trường địa chỉ lại thành 1 chuỗi
                $dia_chi_lien_lac = $_POST['dc_ll_so_nha'] . ', ' . $_POST['dc_ll_ap'] . ', ' . $_POST['dc_ll_xa'] . ', ' . $_POST['dc_ll_tinh'];
                $dia_chi_thuong_tru = $_POST['dc_tt_so_nha'] . ', ' . $_POST['dc_tt_ap'] . ', ' . $_POST['dc_tt_xa'] . ', ' . $_POST['dc_tt_tinh'];
                $id_khoa = $_POST['id_khoa'];

                // quân sửa: Kiểm tra trùng Tên HOẶC Email trước khi cập nhật (bỏ qua id hiện tại)
                if ($bithudoankhoa->bithudoankhoa__Check_Duplicate($ten_bi_thu, $email, $id_bi_thu) > 0) {
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=duplicate-bithu');
                    break;
                }

                $status = $bithudoankhoa->bithudoankhoa__Update($id_bi_thu, $ten_bi_thu, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_khoa);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=failed');
                }
                
                break;

            case 'delete':

                $id_bi_thu = $_GET['id_bi_thu'];

                $status = $bithudoankhoa->bithudoankhoa__Delete($id_bi_thu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-bi-thu-doan-khoa&status=failed');
                }
                
                break;
        }
    }