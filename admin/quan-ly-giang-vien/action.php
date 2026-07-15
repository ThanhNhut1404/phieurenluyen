<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':
                
                $ma_giang_vien = $_POST['ma_giang_vien'];
                $ten_giang_vien = $_POST['ten_giang_vien'];
                $gioi_tinh = $_POST['gioi_tinh'];
                $ngay_sinh = $_POST['ngay_sinh'];
                $email = $_POST['email'];
                $so_dien_thoai_1 = $_POST['so_dien_thoai_1'];
                $so_dien_thoai_2 = $_POST['so_dien_thoai_2'];
                $dia_chi_lien_lac = $_POST['dia_chi_lien_lac'];
                $dia_chi_thuong_tru = $_POST['dia_chi_thuong_tru'];
                $id_trinh_do = $_POST['id_trinh_do'];
                
                $status = $giangvien->giangvien__Add($ma_giang_vien, $ten_giang_vien, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_trinh_do);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-giang-vien&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-giang-vien&status=failed');
                }
                
                break;
            case 'update':

                $id_giang_vien = $_POST['id_giang_vien'];
                $ma_giang_vien = $_POST['ma_giang_vien'];
                $ten_giang_vien = $_POST['ten_giang_vien'];
                $gioi_tinh = $_POST['gioi_tinh'];
                $ngay_sinh = $_POST['ngay_sinh'];
                $email = $_POST['email'];
                $so_dien_thoai_1 = $_POST['so_dien_thoai_1'];
                $so_dien_thoai_2 = $_POST['so_dien_thoai_2'];
                $dia_chi_lien_lac = $_POST['dia_chi_lien_lac'];
                $dia_chi_thuong_tru = $_POST['dia_chi_thuong_tru'];
                $id_trinh_do = $_POST['id_trinh_do'];

                $status = $giangvien->giangvien__Update($id_giang_vien, $ma_giang_vien, $ten_giang_vien, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_trinh_do);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-giang-vien&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-giang-vien&status=failed');
                }
                
                break;

            case 'delete':

                $id_giang_vien = $_GET['id_giang_vien'];

                $status = $giangvien->giangvien__Delete($id_giang_vien);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-giang-vien&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-giang-vien&status=failed');
                }
                
                break;
        }
    }

?>