<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':
                $status = 0;
                $ten_mau_phieu = $_POST['ten_mau_phieu'];
                $ghi_chu = $_POST['ghi_chu'];
                $id_dieu = $_POST['id_dieu'];
                $id_mau_phieu = $mauphieu->mauphieu__Add($ten_mau_phieu, $ghi_chu);
                foreach($id_dieu as $item){
                    $status .= $bocauhoi->bocauhoi__Add($id_mau_phieu, $item);
                }
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-mau-phieu&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-mau-phieu&status=failed');
                }
                
                break;
            case 'update':
                $status =0;
                $id_mau_phieu = $_POST['id_mau_phieu'];
                $ten_mau_phieu = $_POST['ten_mau_phieu'];
                $ghi_chu = $_POST['ghi_chu'];
                $status += $mauphieu->mauphieu__Update($id_mau_phieu, $ten_mau_phieu, $ghi_chu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-mau-phieu&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-mau-phieu&status=failed');
                }
                
                break;
            case 'update_dieu':
                $status =0;
                $id_mau_phieu = $_POST['id_mau_phieu'];
                $id_dieu = $_POST['id_dieu'];

                $bocauhoi->bocauhoi__Delete_Mau_Phieu($id_mau_phieu);

                foreach($id_dieu as $item){
                    $status .= $bocauhoi->bocauhoi__Add($id_mau_phieu, $item);
                }
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-mau-phieu&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-mau-phieu&status=failed');
                }
                
                break;
            case 'delete':

                $id_mau_phieu = $_GET['id_mau_phieu'];

                // quân sửa: Phải xoá dữ liệu con trong bocauhoi trước khi xoá mauphieu cha
                $bocauhoi->bocauhoi__Delete_Mau_Phieu($id_mau_phieu);
                
                $status = $mauphieu->mauphieu__Delete($id_mau_phieu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-mau-phieu&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-mau-phieu&status=failed');
                }
                
                break;
        }
    }

?>