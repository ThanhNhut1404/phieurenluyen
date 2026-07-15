<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_xep_loai = $_POST['ten_xep_loai'];
                $ghi_chu = $_POST['ghi_chu'];
                $can_tren = $_POST['can_tren'];
                $can_duoi = $_POST['can_duoi'];
                $tru_tre_han = $_POST['tru_tre_han'];

                $status = $xeploai->xeploai__Add($ten_xep_loai, $ghi_chu, $can_tren, $can_duoi, $tru_tre_han);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-xep-loai&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-xep-loai&status=failed');
                }
                
                break;
            case 'update':

                $id_xep_loai = $_POST['id_xep_loai'];
                $ten_xep_loai = $_POST['ten_xep_loai'];
                $ghi_chu = $_POST['ghi_chu'];
                $can_tren = $_POST['can_tren'];
                $can_duoi = $_POST['can_duoi'];
                $tru_tre_han = $_POST['tru_tre_han'];

                $status = $xeploai->xeploai__Update($id_xep_loai, $ten_xep_loai, $ghi_chu, $can_tren, $can_duoi, $tru_tre_han);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-xep-loai&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-xep-loai&status=failed');
                }
                
                break;

            case 'delete':

                $id_xep_loai = $_GET['id_xep_loai'];

                $status = $xeploai->xeploai__Delete($id_xep_loai);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-xep-loai&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-xep-loai&status=failed');
                }
                
                break;
        }
    }

?>