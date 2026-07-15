<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_lop_hoc = $_POST['ten_lop_hoc'];
                $ghi_chu = $_POST['ghi_chu'];
                $id_khoa_hoc = $_POST['id_khoa_hoc'];
                $id_nganh_hoc = $_POST['id_nganh_hoc'];

                $status = $lophoc->lophoc__Add($ten_lop_hoc, $ghi_chu,$id_khoa_hoc, $id_nganh_hoc);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-lop-hoc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-lop-hoc&status=failed');
                }
                
                break;
            case 'update':

                $id_lop_hoc = $_POST['id_lop_hoc'];
                $ten_lop_hoc = $_POST['ten_lop_hoc'];
                $ghi_chu = $_POST['ghi_chu'];
                $id_khoa_hoc = $_POST['id_khoa_hoc'];
                $id_nganh_hoc = $_POST['id_nganh_hoc'];
                
                $status = $lophoc->lophoc__Update($id_lop_hoc, $ten_lop_hoc, $ghi_chu, $id_khoa_hoc, $id_nganh_hoc);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-lop-hoc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-lop-hoc&status=failed');
                }
                
                break;

            case 'delete':

                $id_lop_hoc = $_GET['id_lop_hoc'];

                $status = $lophoc->lophoc__Delete($id_lop_hoc);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-lop-hoc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-lop-hoc&status=failed');
                }
                
                break;
        }
    }

?>