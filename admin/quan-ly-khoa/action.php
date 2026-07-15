<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_khoa = $_POST['ten_khoa'];
                $ghi_chu = $_POST['ghi_chu'];

                $status = $khoa->khoa__Add($ten_khoa, $ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-khoa&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-khoa&status=failed');
                }
                
                break;
            case 'update':

                $id_khoa = $_POST['id_khoa'];
                $ten_khoa = $_POST['ten_khoa'];
                $ghi_chu = $_POST['ghi_chu'];

                $status = $khoa->khoa__Update($id_khoa, $ten_khoa, $ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-khoa&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-khoa&status=failed');
                }
                
                break;

            case 'delete':

                $id_khoa = $_GET['id_khoa'];

                $status = $khoa->khoa__Delete($id_khoa);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-khoa&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-khoa&status=failed');
                }
                
                break;
        }
    }

?>