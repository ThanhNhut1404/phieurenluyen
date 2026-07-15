<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_dieu = $_POST['ten_dieu'];
                $ghi_chu = $_POST['ghi_chu'];
                $thu_tu = $_POST['thu_tu'];

                $status = $dieu->dieu__Add($ten_dieu, $ghi_chu, $thu_tu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-dieu&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-dieu&status=failed');
                }
                
                break;
            case 'update':

                $id_dieu = $_POST['id_dieu'];
                $ten_dieu = $_POST['ten_dieu'];
                $ghi_chu = $_POST['ghi_chu'];
                $thu_tu = $_POST['thu_tu'];

                $status = $dieu->dieu__Update($id_dieu, $ten_dieu, $ghi_chu, $thu_tu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-dieu&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-dieu&status=failed');
                }
                
                break;

            case 'delete':

                $id_dieu = $_GET['id_dieu'];

                $status = $dieu->dieu__Delete($id_dieu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-dieu&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-dieu&status=failed');
                }
                
                break;
        }
    }

?>