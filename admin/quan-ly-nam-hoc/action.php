<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_nam_hoc = $_POST['ten_nam_hoc'];
                $ghi_chu = $_POST['ghi_chu'];

                $status = $namhoc->namhoc__Add($ten_nam_hoc, $ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-nam-hoc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-nam-hoc&status=failed');
                }
                
                break;
            case 'update':

                $id_nam_hoc = $_POST['id_nam_hoc'];
                $ten_nam_hoc = $_POST['ten_nam_hoc'];
                $ghi_chu = $_POST['ghi_chu'];

                $status = $namhoc->namhoc__Update($id_nam_hoc, $ten_nam_hoc, $ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-nam-hoc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-nam-hoc&status=failed');
                }
                
                break;

            case 'delete':

                $id_nam_hoc = $_GET['id_nam_hoc'];

                $status = $namhoc->namhoc__Delete($id_nam_hoc);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-nam-hoc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-nam-hoc&status=failed');
                }
                
                break;
        }
    }

?>