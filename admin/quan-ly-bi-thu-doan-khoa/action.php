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
                $dia_chi_lien_lac = $_POST['dia_chi_lien_lac'];
                $dia_chi_thuong_tru = $_POST['dia_chi_thuong_tru'];
                $id_khoa = $_POST['id_khoa'];
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
                $dia_chi_lien_lac = $_POST['dia_chi_lien_lac'];
                $dia_chi_thuong_tru = $_POST['dia_chi_thuong_tru'];
                $id_khoa = $_POST['id_khoa'];
                $status = $bithudoankhoa->bithudoankhoa__Update($id_bi_thu,$ten_bi_thu, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_khoa);
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