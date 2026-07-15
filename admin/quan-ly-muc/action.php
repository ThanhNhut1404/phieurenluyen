<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_muc = $_POST['ten_muc'];
                $ghi_chu = $_POST['ghi_chu'];
                $thu_tu = $_POST['thu_tu'];
                $id_khoan = $_POST['id_khoan'];

                $status = $muc->muc__Add($ten_muc, $ghi_chu, $thu_tu, $id_khoan);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-muc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-muc&status=failed');
                }
                
                break;
            case 'update':

                $id_muc = $_POST['id_muc'];
                $ten_muc = $_POST['ten_muc'];
                $ghi_chu = $_POST['ghi_chu'];
                $thu_tu = $_POST['thu_tu'];
                $id_khoan = $_POST['id_khoan'];

                $status = $muc->muc__Update($id_muc, $ten_muc, $ghi_chu, $thu_tu, $id_khoan);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-muc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-muc&status=failed');
                }
                
                break;

            case 'delete':

                $id_muc = $_GET['id_muc'];

                $status = $muc->muc__Delete($id_muc);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-muc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-muc&status=failed');
                }
                
                break;
        }
    }

?>