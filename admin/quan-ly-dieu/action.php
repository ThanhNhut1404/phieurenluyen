<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_dieu = trim($_POST['ten_dieu']);
                $ghi_chu = trim($_POST['ghi_chu']);
                $thu_tu = trim($_POST['thu_tu']);

                // quân sửa: Tự động xếp thứ tự hoặc Swap cho Điều
                if ($thu_tu == "") {
                    $thu_tu = $dieu->dieu__Get_Max_Thu_Tu() + 1;
                } else {
                    $conflict = $dieu->dieu__Get_By_Thu_Tu($thu_tu);
                    if ($conflict) {
                        $dieu->dieu__Update_Thu_Tu($conflict->id_dieu, $dieu->dieu__Get_Max_Thu_Tu() + 1);
                    }
                }

                $status = $dieu->dieu__Add($ten_dieu, $ghi_chu, $thu_tu);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-dieu&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-dieu&status=failed');
                }
                
                break;
            case 'update':

                $id_dieu = $_POST['id_dieu'];
                $ten_dieu = trim($_POST['ten_dieu']);
                $ghi_chu = trim($_POST['ghi_chu']);
                $thu_tu = trim($_POST['thu_tu']);

                // quân sửa: Swap thứ tự khi cập nhật Điều
                $old_dieu = $dieu->dieu__Get_By_Id($id_dieu);
                if ($thu_tu == "") {
                    $thu_tu = $old_dieu->thu_tu;
                } else if ($thu_tu != $old_dieu->thu_tu) {
                    $conflict = $dieu->dieu__Get_By_Thu_Tu($thu_tu);
                    if ($conflict) {
                        $dieu->dieu__Update_Thu_Tu($conflict->id_dieu, $old_dieu->thu_tu);
                    }
                }

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