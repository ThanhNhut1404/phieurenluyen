<?php

    require '../../models/getModel.php';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':
                $id_giang_vien = isset($_POST['id_giang_vien']) ? trim($_POST['id_giang_vien']) : '';
                $id_lop_hoc = isset($_POST['id_lop_hoc']) ? trim($_POST['id_lop_hoc']) : '';
                $ghi_chu = isset($_POST['ghi_chu']) ? trim($_POST['ghi_chu']) : '';
                
                $_SESSION['phancong_old_input'] = array_merge($_POST, ['context' => 'add']);

                // Check duplicate assignment
                if ($phancong->phancong__Exists($id_giang_vien, $id_lop_hoc)) {
                    header('location: ../index.php?page=quan-ly-phan-cong&status=duplicate-phancong');
                    exit();
                }

                $status = $phancong->phancong__Add($id_giang_vien, $id_lop_hoc, $ghi_chu);
                if($status != 0){
                    unset($_SESSION['phancong_old_input']);
                    header('location: ../index.php?page=quan-ly-phan-cong&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-phan-cong&status=failed');
                }
                exit();
                
            case 'update':
                $id_phan_cong = isset($_POST['id_phan_cong']) ? trim($_POST['id_phan_cong']) : '';
                $id_giang_vien = isset($_POST['id_giang_vien']) ? trim($_POST['id_giang_vien']) : '';
                $id_lop_hoc = isset($_POST['id_lop_hoc']) ? trim($_POST['id_lop_hoc']) : '';
                $ghi_chu = isset($_POST['ghi_chu']) ? trim($_POST['ghi_chu']) : '';
                
                $_SESSION['phancong_old_input'] = array_merge($_POST, ['context' => 'update']);

                // Check duplicate assignment (excluding current assignment id)
                if ($phancong->phancong__Exists($id_giang_vien, $id_lop_hoc, $id_phan_cong)) {
                    header('location: ../index.php?page=quan-ly-phan-cong&status=duplicate-phancong');
                    exit();
                }

                $status = $phancong->phancong__Update($id_phan_cong, $id_giang_vien, $id_lop_hoc, $ghi_chu);
                if($status != 0){
                    unset($_SESSION['phancong_old_input']);
                    header('location: ../index.php?page=quan-ly-phan-cong&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-phan-cong&status=failed');
                }
                exit();

            case 'delete':
                $id_phan_cong = isset($_GET['id_phan_cong']) ? trim($_GET['id_phan_cong']) : '';
                $status = $phancong->phancong__Delete($id_phan_cong);
                if($status != 0){
                    header('location: ../index.php?page=quan-ly-phan-cong&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-phan-cong&status=failed');
                }
                exit();
        }
    }
?>