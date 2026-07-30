<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_khoan = $_POST['ten_khoan'];
                $ghi_chu = $_POST['ghi_chu'];
                $can_tren = $_POST['can_tren'];
                $id_dieu = $_POST['id_dieu'];
                $so_luong_muc = $_POST['so_luong_muc'];

                // quân sửa: Đồng bộ thứ tự Khoản theo Điều, không cần nhập thủ công
                $dieu_info = $dieu->dieu__Get_By_Id($id_dieu);
                $thu_tu = $dieu_info ? $dieu_info->thu_tu : 1;

                $status = $khoan->khoan__Add($ten_khoan, $ghi_chu, $can_tren, $thu_tu, $id_dieu, $so_luong_muc);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-khoan&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-khoan&status=failed');
                }
                
                break;
            case 'update':

                $id_khoan = $_POST['id_khoan'];
                $ten_khoan = $_POST['ten_khoan'];
                $ghi_chu = $_POST['ghi_chu'];
                $can_tren = $_POST['can_tren'];
                $id_dieu = $_POST['id_dieu'];
                $so_luong_muc = $_POST['so_luong_muc'];

                // quân sửa: Đồng bộ thứ tự Khoản theo Điều, không cần nhập thủ công
                $dieu_info = $dieu->dieu__Get_By_Id($id_dieu);
                $thu_tu = $dieu_info ? $dieu_info->thu_tu : 1;

                $status = $khoan->khoan__Update($id_khoan, $ten_khoan, $ghi_chu, $can_tren, $thu_tu, $id_dieu, $so_luong_muc);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-khoan&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-khoan&status=failed');
                }
                
                break;

            case 'delete':

                $id_khoan = $_GET['id_khoan'];

                $status = $khoan->khoan__Delete($id_khoan);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-khoan&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-khoan&status=failed');
                }
                
                break;
        }
    }

?>