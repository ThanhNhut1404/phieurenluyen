<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_hoc_ky = $_POST['ten_hoc_ky'];
                $ghi_chu = $_POST['ghi_chu'];
                $id_nam_hoc = $_POST['id_nam_hoc'];

                $status = $hocky->hocky__Add($ten_hoc_ky, $ghi_chu, $id_nam_hoc);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-hoc-ky&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-hoc-ky&status=failed');
                }
                
                break;
            case 'update':

                $id_hoc_ky = $_POST['id_hoc_ky'];
                $ten_hoc_ky = $_POST['ten_hoc_ky'];
                $ghi_chu = $_POST['ghi_chu'];
                $id_nam_hoc = $_POST['id_nam_hoc'];

                $status = $hocky->hocky__Update($id_hoc_ky, $ten_hoc_ky, $ghi_chu, $id_nam_hoc);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-hoc-ky&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-hoc-ky&status=failed');
                }
                
                break;

            case 'delete':

                $id_hoc_ky = $_GET['id_hoc_ky'];

                $status = $hocky->hocky__Delete($id_hoc_ky);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-hoc-ky&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-hoc-ky&status=failed');
                }
                
                break;
        }
    }

?>