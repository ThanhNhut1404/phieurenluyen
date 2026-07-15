<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_phan_nhom = $_POST['ten_phan_nhom'];
                $cap_bac = $_POST['cap_bac'];
                $ghi_chu = $_POST['ghi_chu'];

                $status = $phannhom->phannhom__Add($ten_phan_nhom, $cap_bac, $ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-phan-nhom&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-phan-nhom&status=failed');
                }
                
                break;
            case 'update':

                $id_phan_nhom = $_POST['id_phan_nhom'];
                $ten_phan_nhom = $_POST['ten_phan_nhom'];
                $cap_bac = $_POST['cap_bac'];
                $ghi_chu = $_POST['ghi_chu'];

                $status = $phannhom->phannhom__Update($id_phan_nhom, $ten_phan_nhom, $cap_bac, $ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-phan-nhom&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-phan-nhom&status=failed');
                }
                
                break;

            case 'delete':

                $id_phan_nhom = $_GET['id_phan_nhom'];

                $status = $phannhom->phannhom__Delete($id_phan_nhom);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-phan-nhom&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-phan-nhom&status=failed');
                }
                
                break;
        }
    }

?>