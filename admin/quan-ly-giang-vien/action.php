<?php

    require '../../models/getModel.php';

    // Nhựt sửa lỗi: kiểm tra trình độ có tồn tại trước khi gán cho giảng viên.
    function giangvien_trinhdo_exists($trinhdo, $id_trinh_do) {
        return $id_trinh_do && $id_trinh_do > 0 && $trinhdo->trinhdo__Get_By_Id($id_trinh_do);
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
                
                $dc_ll_so_nha = isset($_POST['dc_ll_so_nha']) ? trim($_POST['dc_ll_so_nha']) : '';
                $dc_ll_ap = isset($_POST['dc_ll_ap']) ? trim($_POST['dc_ll_ap']) : '';
                $dc_ll_xa = isset($_POST['dc_ll_xa']) ? trim($_POST['dc_ll_xa']) : '';
                $dc_ll_tinh = isset($_POST['dc_ll_tinh']) ? trim($_POST['dc_ll_tinh']) : '';
                
                $dc_tt_so_nha = isset($_POST['dc_tt_so_nha']) ? trim($_POST['dc_tt_so_nha']) : '';
                $dc_tt_ap = isset($_POST['dc_tt_ap']) ? trim($_POST['dc_tt_ap']) : '';
                $dc_tt_xa = isset($_POST['dc_tt_xa']) ? trim($_POST['dc_tt_xa']) : '';
                $dc_tt_tinh = isset($_POST['dc_tt_tinh']) ? trim($_POST['dc_tt_tinh']) : '';

                $dia_chi_lien_lac = $dc_ll_so_nha . ', ' . $dc_ll_ap . ', ' . $dc_ll_xa . ', ' . $dc_ll_tinh;
                $dia_chi_thuong_tru = $dc_tt_so_nha . ', ' . $dc_tt_ap . ', ' . $dc_tt_xa . ', ' . $dc_tt_tinh;
                $id_trinh_do = isset($_POST['id_trinh_do']) ? $_POST['id_trinh_do'] : '';

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
                    header('location: ../index.php?page=quan-ly-giang-vien&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-giang-vien&status=failed');
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
                
                $dc_ll_so_nha = isset($_POST['dc_ll_so_nha']) ? trim($_POST['dc_ll_so_nha']) : '';
                $dc_ll_ap = isset($_POST['dc_ll_ap']) ? trim($_POST['dc_ll_ap']) : '';
                $dc_ll_xa = isset($_POST['dc_ll_xa']) ? trim($_POST['dc_ll_xa']) : '';
                $dc_ll_tinh = isset($_POST['dc_ll_tinh']) ? trim($_POST['dc_ll_tinh']) : '';
                
                $dc_tt_so_nha = isset($_POST['dc_tt_so_nha']) ? trim($_POST['dc_tt_so_nha']) : '';
                $dc_tt_ap = isset($_POST['dc_tt_ap']) ? trim($_POST['dc_tt_ap']) : '';
                $dc_tt_xa = isset($_POST['dc_tt_xa']) ? trim($_POST['dc_tt_xa']) : '';
                $dc_tt_tinh = isset($_POST['dc_tt_tinh']) ? trim($_POST['dc_tt_tinh']) : '';

                $dia_chi_lien_lac = $dc_ll_so_nha . ', ' . $dc_ll_ap . ', ' . $dc_ll_xa . ', ' . $dc_ll_tinh;
                $dia_chi_thuong_tru = $dc_tt_so_nha . ', ' . $dc_tt_ap . ', ' . $dc_tt_xa . ', ' . $dc_tt_tinh;
                $id_trinh_do = isset($_POST['id_trinh_do']) ? $_POST['id_trinh_do'] : '';

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
                header('location: ../index.php?page=quan-ly-giang-vien&status=success');
                exit();

            case 'delete':
                $id_giang_vien = isset($_GET['id_giang_vien']) ? $_GET['id_giang_vien'] : '';
                $status = $giangvien->giangvien__Delete($id_giang_vien);
                if($status != 0){
                    header('location: ../index.php?page=quan-ly-giang-vien&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-giang-vien&status=failed');
                }
                exit();
        }
    }
?>
