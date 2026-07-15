<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':
                $status  = 0;
                $ten_dot = $_POST['ten_dot'];
                $ghi_chu = $_POST['ghi_chu'];
                $thoi_gian_bat_dau = $_POST['thoi_gian_bat_dau'];
                $thoi_gian_ket_thuc = $_POST['thoi_gian_ket_thuc'];
                $id_hoc_ky = $_POST['id_hoc_ky'];

                $id_mau_phieu = $_POST['id_mau_phieu'];
                $id_lop_hoc = $_POST['id_lop_hoc'];

                $id_dot = $dotchamdiem->dotchamdiem__Add($ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_hoc_ky);
                $status .= $id_dot;

                foreach($id_lop_hoc as $item_1){
                    $id_lop_ap_dung = $lopapdung->lopapdung__Add($id_dot, $id_mau_phieu, $item_1);
                
                    $sinhvien__Get_By_Id_Lop_Hoc = $sinhvien->sinhvien__Get_By_Id_Lop_Hoc($item_1);
                    $status .= count($sinhvien__Get_By_Id_Lop_Hoc);

                    foreach($sinhvien__Get_By_Id_Lop_Hoc as $item){
                        $status .= $phieuchamdiem->phieuchamdiem__Add($id_lop_ap_dung, $item->id_sinh_vien, "", "", "", date("Y-m-d"));
                    }
                }
              
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-dot-cham-diem&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-dot-cham-diem&status=failed');
                }
                
                break;
            case 'update':
                $status  = 0;
                $id_dot = $_POST['id_dot'];
                $ten_dot = $_POST['ten_dot'];
                $ghi_chu = $_POST['ghi_chu'];
                $thoi_gian_bat_dau = $_POST['thoi_gian_bat_dau'];
                $thoi_gian_ket_thuc = $_POST['thoi_gian_ket_thuc'];
                $id_hoc_ky = $_POST['id_hoc_ky'];


                $status += $dotchamdiem->dotchamdiem__Update($id_dot, $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_hoc_ky);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-dot-cham-diem&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-dot-cham-diem&status=failed');
                }
                
                break;

            case 'delete':

                $id_dot = $_GET['id_dot'];

                $status = $lopapdung->lopapdung__Delete_By_Id_Dot($id_dot);
                $status = $dotchamdiem->dotchamdiem__Delete($id_dot);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-dot-cham-diem&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-dot-cham-diem&status=failed');
                }
                
                break;
        }
    }

?>