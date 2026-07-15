<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_nganh_hoc = $_POST['ten_nganh_hoc'];
                $ghi_chu = $_POST['ghi_chu'];
                $id_khoa = $_POST['id_khoa'];

                $status = $nganhhoc->nganhhoc__Add($ten_nganh_hoc, $ghi_chu, $id_khoa);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-nganh-hoc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-nganh-hoc&status=failed');
                }
                
                break;
            case 'update':

                $id_nganh_hoc = $_POST['id_nganh_hoc'];
                $ten_nganh_hoc = $_POST['ten_nganh_hoc'];
                $ghi_chu = $_POST['ghi_chu'];
                $id_khoa = $_POST['id_khoa'];
                $status = $nganhhoc->nganhhoc__Update($id_nganh_hoc, $ten_nganh_hoc, $ghi_chu, $id_khoa);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-nganh-hoc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-nganh-hoc&status=failed');
                }
                
                break;

            case 'delete':

                $id_nganh_hoc = $_GET['id_nganh_hoc'];

                $status = $nganhhoc->nganhhoc__Delete($id_nganh_hoc);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-nganh-hoc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-nganh-hoc&status=failed');
                }
                
                break;
        }
    }

?>