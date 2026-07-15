<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_trinh_do = $_POST['ten_trinh_do'];
                $ghi_chu = $_POST['ghi_chu'];

                $status = $trinhdo->trinhdo__Add($ten_trinh_do, $ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-trinh-do&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-trinh-do&status=failed');
                }
                
                break;
            case 'update':

                $id_trinh_do = $_POST['id_trinh_do'];
                $ten_trinh_do = $_POST['ten_trinh_do'];
                $ghi_chu = $_POST['ghi_chu'];

                $status = $trinhdo->trinhdo__Update($id_trinh_do, $ten_trinh_do, $ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-trinh-do&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-trinh-do&status=failed');
                }
                
                break;

            case 'delete':

                $id_trinh_do = $_GET['id_trinh_do'];

                $status = $trinhdo->trinhdo__Delete($id_trinh_do);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-trinh-do&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-trinh-do&status=failed');
                }
                
                break;
        }
    }

?>