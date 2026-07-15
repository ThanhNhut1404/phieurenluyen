<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_phan_quyen = $_POST['ten_phan_quyen'];
                $cap_bac = $_POST['cap_bac'];
                $ghi_chu = $_POST['ghi_chu'];

                $status = $phanquyen->phanquyen__Add($ten_phan_quyen, $cap_bac, $ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-phan-quyen&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-phan-quyen&status=failed');
                }
                
                break;
            case 'update':

                $id_phan_quyen = $_POST['id_phan_quyen'];
                $ten_phan_quyen = $_POST['ten_phan_quyen'];
                $cap_bac = $_POST['cap_bac'];
                $ghi_chu = $_POST['ghi_chu'];

                $status = $phanquyen->phanquyen__Update($id_phan_quyen, $ten_phan_quyen, $cap_bac, $ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-phan-quyen&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-phan-quyen&status=failed');
                }
                
                break;

            case 'delete':

                $id_phan_quyen = $_GET['id_phan_quyen'];

                $status = $phanquyen->phanquyen__Delete($id_phan_quyen);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-phan-quyen&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-phan-quyen&status=failed');
                }
                
                break;
        }
    }

?>