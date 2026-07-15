<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_khoa_hoc = $_POST['ten_khoa_hoc'];
                $nam_nhap_hoc = $_POST['nam_nhap_hoc'];
                $he_dao_tao = $_POST['he_dao_tao'];
                $ghi_chu = $_POST['ghi_chu'];

                $status = $khoahoc->khoahoc__Add($ten_khoa_hoc,$nam_nhap_hoc, $he_dao_tao, $ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-khoa-hoc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-khoa-hoc&status=failed');
                }
                
                break;
            case 'update':

                $id_khoa_hoc = $_POST['id_khoa_hoc'];
                $ten_khoa_hoc = $_POST['ten_khoa_hoc'];
                $nam_nhap_hoc = $_POST['nam_nhap_hoc'];
                $he_dao_tao = $_POST['he_dao_tao'];
                $ghi_chu = $_POST['ghi_chu'];

                $status = $khoahoc->khoahoc__Update($id_khoa_hoc, $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-khoa-hoc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-khoa-hoc&status=failed');
                }
                
                break;

            case 'delete':

                $id_khoa_hoc = $_GET['id_khoa_hoc'];

                $status = $khoahoc->khoahoc__Delete($id_khoa_hoc);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-khoa-hoc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-khoa-hoc&status=failed');
                }
                
                break;
        }
    }

?>