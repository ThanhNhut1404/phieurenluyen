<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $id_giang_vien = $_POST['id_giang_vien'];
                $id_lop_hoc = $_POST['id_lop_hoc'];
                $ghi_chu = $_POST['ghi_chu'];

                $status = $phancong->phancong__Add($id_giang_vien, $id_lop_hoc,$ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-phan-cong&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-phan-cong&status=failed');
                }
                
                break;
            case 'update':

                $id_phan_cong = $_POST['id_phan_cong'];
                $id_giang_vien = $_POST['id_giang_vien'];
                $id_lop_hoc = $_POST['id_lop_hoc'];
                $ghi_chu = $_POST['ghi_chu'];

                $status = $phancong->phancong__Update($id_phan_cong, $id_giang_vien, $id_lop_hoc, $ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-phan-cong&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-phan-cong&status=failed');
                }
                
                break;

            case 'delete':

                $id_phan_cong = $_GET['id_phan_cong'];

                $status = $phancong->phancong__Delete($id_phan_cong);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-phan-cong&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-phan-cong&status=failed');
                }
                
                break;
        }
    }

?>