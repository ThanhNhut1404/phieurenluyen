<?php

    require '../../models/getModel.php';

    // Nhựt sửa lỗi: kiểm tra trình độ có tồn tại trước khi gán cho giảng viên.
    function giangvien_trinhdo_exists($trinhdo, $id_trinh_do) {
        return $id_trinh_do && $id_trinh_do > 0 && $trinhdo->trinhdo__Get_By_Id($id_trinh_do);
    }
    
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
                $ma_giang_vien = isset($_POST['ma_giang_vien']) ? trim($_POST['ma_giang_vien']) : '';
                $ten_giang_vien = isset($_POST['ten_giang_vien']) ? trim($_POST['ten_giang_vien']) : '';
                $gioi_tinh = isset($_POST['gioi_tinh']) ? $_POST['gioi_tinh'] : '';
                $ngay_sinh = isset($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : '';
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $so_dien_thoai_1 = isset($_POST['so_dien_thoai_1']) ? trim($_POST['so_dien_thoai_1']) : '';
                $so_dien_thoai_2 = isset($_POST['so_dien_thoai_2']) ? trim($_POST['so_dien_thoai_2']) : '';
                
                $dc_ll_chitiet = isset($_POST['dc_ll_chitiet']) ? trim($_POST['dc_ll_chitiet']) : '';
                $dc_ll_xa = isset($_POST['dc_ll_xa']) ? trim($_POST['dc_ll_xa']) : '';
                $dc_ll_tinh = isset($_POST['dc_ll_tinh']) ? trim($_POST['dc_ll_tinh']) : '';
                
                $dc_tt_chitiet = isset($_POST['dc_tt_chitiet']) ? trim($_POST['dc_tt_chitiet']) : '';
                $dc_tt_xa = isset($_POST['dc_tt_xa']) ? trim($_POST['dc_tt_xa']) : '';
                $dc_tt_tinh = isset($_POST['dc_tt_tinh']) ? trim($_POST['dc_tt_tinh']) : '';

                $dia_chi_lien_lac = $dc_ll_chitiet . ', ' . $dc_ll_xa . ', ' . $dc_ll_tinh;
                $dia_chi_thuong_tru = $dc_tt_chitiet . ', ' . $dc_tt_xa . ', ' . $dc_tt_tinh;
                $id_trinh_do = isset($_POST['id_trinh_do']) ? $_POST['id_trinh_do'] : '';
                $id_lop_hoc = isset($_POST['id_lop_hoc']) ? $_POST['id_lop_hoc'] : '';
                
                $_SESSION['giangvien_old_input'] = array_merge($_POST, ['context' => 'add']);

                // Backend Validation
                if (!preg_match('/^0[0-9]{9,10}$/', $so_dien_thoai_1) || !preg_match('/^0[0-9]{9,10}$/', $so_dien_thoai_2)) {
                    header('location: ../index.php?page=quan-ly-giang-vien&status=invalid-sdt');
                    exit();
                }
                $dob = DateTime::createFromFormat('Y-m-d', $ngay_sinh);
                if (!$dob || $dob->format('Y-m-d') !== $ngay_sinh || (new DateTime())->diff($dob)->y < 20 || (new DateTime())->diff($dob)->y > 100) {
                    header('location: ../index.php?page=quan-ly-giang-vien&status=invalid-ngay');
                    exit();
                }

                if (!giangvien_trinhdo_exists($trinhdo, $id_trinh_do)) {
                    header('location: ../index.php?page=quan-ly-giang-vien&status=invalid');
                    exit();
                }
                
                // quân sửa: Kiểm tra trùng Mã HOẶC Email trước khi thêm
                if ($giangvien->giangvien__Check_Duplicate($ma_giang_vien, $email) > 0) {
                    header('location: ../index.php?page=quan-ly-giang-vien&status=duplicate-giangvien');
                    exit();
                }
                
                $status = $giangvien->giangvien__Add($ma_giang_vien, $ten_giang_vien, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_trinh_do);
                if($status != 0){
                    if (!empty($id_lop_hoc)) {
                        $new_gv = $giangvien->giangvien__Get_By_Ma($ma_giang_vien);
                        if ($new_gv) {
                            $phancong->phancong__Add($new_gv->id_giang_vien, $id_lop_hoc, "Phân công khi thêm giảng viên");
                        }
                    }
                    unset($_SESSION['giangvien_old_input']);
                    header('location: ../index.php?page=quan-ly-giang-vien&status=add-success');
                }else{
                    header('location: ../index.php?page=quan-ly-giang-vien&status=add-failed');
                }
                exit();
                
            case 'update':
                $id_giang_vien = isset($_POST['id_giang_vien']) ? $_POST['id_giang_vien'] : '';
                $ma_giang_vien = isset($_POST['ma_giang_vien']) ? trim($_POST['ma_giang_vien']) : '';
                $ten_giang_vien = isset($_POST['ten_giang_vien']) ? trim($_POST['ten_giang_vien']) : '';
                $gioi_tinh = isset($_POST['gioi_tinh']) ? $_POST['gioi_tinh'] : '';
                $ngay_sinh = isset($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : '';
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $so_dien_thoai_1 = isset($_POST['so_dien_thoai_1']) ? trim($_POST['so_dien_thoai_1']) : '';
                $so_dien_thoai_2 = isset($_POST['so_dien_thoai_2']) ? trim($_POST['so_dien_thoai_2']) : '';
                
                $dc_ll_chitiet = isset($_POST['dc_ll_chitiet']) ? trim($_POST['dc_ll_chitiet']) : '';
                $dc_ll_xa = isset($_POST['dc_ll_xa']) ? trim($_POST['dc_ll_xa']) : '';
                $dc_ll_tinh = isset($_POST['dc_ll_tinh']) ? trim($_POST['dc_ll_tinh']) : '';
                
                $dc_tt_chitiet = isset($_POST['dc_tt_chitiet']) ? trim($_POST['dc_tt_chitiet']) : '';
                $dc_tt_xa = isset($_POST['dc_tt_xa']) ? trim($_POST['dc_tt_xa']) : '';
                $dc_tt_tinh = isset($_POST['dc_tt_tinh']) ? trim($_POST['dc_tt_tinh']) : '';

                $dia_chi_lien_lac = $dc_ll_chitiet . ', ' . $dc_ll_xa . ', ' . $dc_ll_tinh;
                $dia_chi_thuong_tru = $dc_tt_chitiet . ', ' . $dc_tt_xa . ', ' . $dc_tt_tinh;
                $id_trinh_do = isset($_POST['id_trinh_do']) ? $_POST['id_trinh_do'] : '';
                $id_lop_hoc = isset($_POST['id_lop_hoc']) ? $_POST['id_lop_hoc'] : '';
                
                $_SESSION['giangvien_old_input'] = array_merge($_POST, ['context' => 'update']);

                // Backend Validation
                if (!preg_match('/^0[0-9]{9,10}$/', $so_dien_thoai_1) || !preg_match('/^0[0-9]{9,10}$/', $so_dien_thoai_2)) {
                    header('location: ../index.php?page=quan-ly-giang-vien&status=invalid-sdt');
                    exit();
                }
                $dob = DateTime::createFromFormat('Y-m-d', $ngay_sinh);
                if (!$dob || $dob->format('Y-m-d') !== $ngay_sinh || (new DateTime())->diff($dob)->y < 20 || (new DateTime())->diff($dob)->y > 100) {
                    header('location: ../index.php?page=quan-ly-giang-vien&status=invalid-ngay');
                    exit();
                }

                if (!giangvien_trinhdo_exists($trinhdo, $id_trinh_do)) {
                    header('location: ../index.php?page=quan-ly-giang-vien&status=invalid');
                    exit();
                }

                // quân sửa: Kiểm tra trùng Mã HOẶC Email trước khi cập nhật
                if ($giangvien->giangvien__Check_Duplicate($ma_giang_vien, $email, $id_giang_vien) > 0) {
                    header('location: ../index.php?page=quan-ly-giang-vien&status=duplicate-giangvien');
                    exit();
                }

                $giangvien->giangvien__Update($id_giang_vien, $ma_giang_vien, $ten_giang_vien, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_trinh_do);
                
                $current_phancong = $phancong->phancong__Get_By_Id_Giang_Vien($id_giang_vien);
                if (!empty($id_lop_hoc)) {
                    if ($current_phancong) {
                        $phancong->phancong__Update($current_phancong->id_phan_cong, $id_giang_vien, $id_lop_hoc, "Cập nhật phân công từ sửa giảng viên");
                    } else {
                        $phancong->phancong__Add($id_giang_vien, $id_lop_hoc, "Phân công khi sửa giảng viên");
                    }
                } else {
                    if ($current_phancong) {
                        $phancong->phancong__Delete($current_phancong->id_phan_cong);
                    }
                }
                
                unset($_SESSION['giangvien_old_input']);
                header('location: ../index.php?page=quan-ly-giang-vien&status=update-success');
                exit();

            case 'delete':
                $id_giang_vien = isset($_GET['id_giang_vien']) ? $_GET['id_giang_vien'] : '';
                $status = $giangvien->giangvien__Delete($id_giang_vien);
                if($status != 0){
                    header('location: ../index.php?page=quan-ly-giang-vien&status=delete-success');
                }else{
                    header('location: ../index.php?page=quan-ly-giang-vien&status=delete-failed');
                }
                exit();
        }
    }
?>
