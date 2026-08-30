<?php

require '../../models/getModel.php';
require("../../assets/vendor/PHPOffice/PHPExcel.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }

if (isset($_GET['req'])) {
    switch ($_GET['req']) {
        case 'add':
            $ma_sinh_vien = isset($_POST['ma_sinh_vien']) ? trim($_POST['ma_sinh_vien']) : '';
            $ten_sinh_vien = isset($_POST['ten_sinh_vien']) ? trim($_POST['ten_sinh_vien']) : '';
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
            $chuc_vu = isset($_POST['chuc_vu']) ? $_POST['chuc_vu'] : '';
            $id_lop_hoc = isset($_POST['id_lop_hoc']) ? $_POST['id_lop_hoc'] : '';

            $id_lop_hoc = isset($_POST['id_lop_hoc']) ? $_POST['id_lop_hoc'] : '';

            // Nhựt sửa: Lưu lại giá trị form để fill lại khi lỗi
            $_SESSION['sinhvien_old_input'] = array_merge($_POST, ['context' => 'add']);

            if ($sinhvien->sinhvien__Exists_Ma_Sinh_Vien($ma_sinh_vien)) {
                header('location: ../index.php?page=quan-ly-sinh-vien&status=duplicate-ma-sinh-vien');
                exit();
            }
            if ($sinhvien->sinhvien__Exists_Email($email)) {
                header('location: ../index.php?page=quan-ly-sinh-vien&status=duplicate-email-sinh-vien');
                exit();
            }

            $status = $sinhvien->sinhvien__Add($ma_sinh_vien, $ten_sinh_vien, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $chuc_vu, $id_lop_hoc);
            if ($status != 0) {
                unset($_SESSION['sinhvien_old_input']);
                header('location: ../index.php?page=quan-ly-sinh-vien&status=success');
            } else {
                header('location: ../index.php?page=quan-ly-sinh-vien&status=failed');
            }
            exit();

        case 'update':
            $id_sinh_vien = isset($_POST['id_sinh_vien']) ? $_POST['id_sinh_vien'] : '';
            $ma_sinh_vien = isset($_POST['ma_sinh_vien']) ? trim($_POST['ma_sinh_vien']) : '';
            $ten_sinh_vien = isset($_POST['ten_sinh_vien']) ? trim($_POST['ten_sinh_vien']) : '';
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
            $chuc_vu = isset($_POST['chuc_vu']) ? $_POST['chuc_vu'] : '';
            $id_lop_hoc = isset($_POST['id_lop_hoc']) ? $_POST['id_lop_hoc'] : '';

            $id_lop_hoc = isset($_POST['id_lop_hoc']) ? $_POST['id_lop_hoc'] : '';

            // Nhựt sửa: Lưu lại giá trị form để fill lại khi lỗi
            $_SESSION['sinhvien_old_input'] = array_merge($_POST, ['context' => 'update']);

            if ($sinhvien->sinhvien__Exists_Ma_Sinh_Vien($ma_sinh_vien, $id_sinh_vien)) {
                header('location: ../index.php?page=quan-ly-sinh-vien&status=duplicate-ma-sinh-vien');
                exit();
            }
            if ($sinhvien->sinhvien__Exists_Email($email, $id_sinh_vien)) {
                header('location: ../index.php?page=quan-ly-sinh-vien&status=duplicate-email-sinh-vien');
                exit();
            }

            $sinhvien->sinhvien__Update($id_sinh_vien, $ma_sinh_vien, $ten_sinh_vien, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $chuc_vu, $id_lop_hoc);
            unset($_SESSION['sinhvien_old_input']);
            header('location: ../index.php?page=quan-ly-sinh-vien&status=success');
            exit();

        case 'delete':
            $id_sinh_vien = isset($_GET['id_sinh_vien']) ? $_GET['id_sinh_vien'] : '';
            $status = $sinhvien->sinhvien__Delete($id_sinh_vien);
            if ($status != 0) {
                header('location: ../index.php?page=quan-ly-sinh-vien&status=success');
            } else {
                header('location: ../index.php?page=quan-ly-sinh-vien&status=failed');
            }
            exit();

        case "import":
            if (!isset($_FILES["file"]["tmp_name"]) || empty($_FILES["file"]["tmp_name"])) {
                header("location:../index.php?page=quan-ly-sinh-vien&status=failed");
                exit();
            }
            
            $file = $_FILES["file"]["tmp_name"];
            $id_lop_hoc = isset($_POST['id_lop_hoc']) ? $_POST['id_lop_hoc'] : '';
            $success_count = 0;
            $duplicate_rows = [];

            try {
                $objReader = PHPExcel_IOFactory::createReaderForFile($file);
                $objExcel = $objReader->load($file);
                $sheetData = $objExcel->getActiveSheet()->toArray(null, true, true, true);
                $highestRow = $objExcel->setActiveSheetIndex()->getHighestRow();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $ma_sinh_vien = isset($sheetData[$row]['B']) ? trim($sheetData[$row]['B']) : '';
                    $ten_sinh_vien = isset($sheetData[$row]['C']) ? trim($sheetData[$row]['C']) : '';
                    $gioi_tinh = isset($sheetData[$row]['D']) ? trim($sheetData[$row]['D']) : '';
                    $ngay_sinh = isset($sheetData[$row]['E']) ? trim($sheetData[$row]['E']) : '';
                    $email = isset($sheetData[$row]['F']) ? trim($sheetData[$row]['F']) : '';
                    $so_dien_thoai_1 = isset($sheetData[$row]['G']) ? trim($sheetData[$row]['G']) : '';
                    $so_dien_thoai_2 = isset($sheetData[$row]['H']) ? trim($sheetData[$row]['H']) : '';
                    $dia_chi_lien_lac = isset($sheetData[$row]['I']) ? trim($sheetData[$row]['I']) : '';
                    $dia_chi_thuong_tru = isset($sheetData[$row]['J']) ? trim($sheetData[$row]['J']) : '';
                    $chuc_vu = isset($sheetData[$row]['K']) ? trim($sheetData[$row]['K']) : '';

                    if (empty($ma_sinh_vien)) {
                        continue;
                    }

                    // Skip if duplicate code or email
                    if ($sinhvien->sinhvien__Exists_Ma_Sinh_Vien($ma_sinh_vien) || (!empty($email) && $sinhvien->sinhvien__Exists_Email($email))) {
                        $duplicate_rows[] = $sheetData[$row];
                        continue;
                    }

                    $add_status = $sinhvien->sinhvien__Add($ma_sinh_vien, $ten_sinh_vien, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $chuc_vu, $id_lop_hoc);
                    if ($add_status > 0) {
                        $success_count++;
                    }
                }
            } catch (Exception $e) {
                header("location:../index.php?page=quan-ly-sinh-vien&status=invalid-import-file");
                exit();
            }

            if (count($duplicate_rows) > 0) {
                $_SESSION['import_duplicate_rows'] = $duplicate_rows;
            }

            if ($success_count == 0) {
                if (count($duplicate_rows) > 0) {
                    header("location:../index.php?page=quan-ly-sinh-vien&status=import-duplicate");
                } else {
                    header("location:../index.php?page=quan-ly-sinh-vien&status=failed");
                }
            } else {
                $_SESSION['import_success_count'] = $success_count;
                if (count($duplicate_rows) > 0) {
                    header("location:../index.php?page=quan-ly-sinh-vien&status=import-partial-success");
                } else {
                    header("location:../index.php?page=quan-ly-sinh-vien&status=import-success");
                }
            }
            exit();

        case 'export':
            try {
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'STT');
                $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Mã số sinh viên');
                $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Họ tên sinh viên');
                $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Giới tính (Nam: 1, nữ: 0)');
                $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Ngày sinh (YYYY-MM-DD)');
                $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Email');
                $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Số điện thoại 1');
                $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Số điện thoại 2');
                $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Địa chỉ liên lạc (Số nhà, Ấp, Xã, Tỉnh phân cách bằng dấu phẩy)');
                $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Địa chỉ thường trú (Số nhà, Ấp, Xã, Tỉnh phân cách bằng dấu phẩy)');
                $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Chức vụ (Sinh viên: 0, Lớp trưởng: 1, Bí thư chi đoàn: 2)');

                $objPHPExcel->getActiveSheet()->SetCellValue('A2', '1');
                $objPHPExcel->getActiveSheet()->SetCellValue('B2', '187060001');
                $objPHPExcel->getActiveSheet()->SetCellValue('C2', 'Nguyễn Ngọc Thiên Xuân');
                $objPHPExcel->getActiveSheet()->SetCellValue('D2', '1');
                $objPHPExcel->getActiveSheet()->SetCellValue('E2', '2001-06-05');
                $objPHPExcel->getActiveSheet()->SetCellValue('F2', 'sv@tdu.edu.com');
                $objPHPExcel->getActiveSheet()->SetCellValue('G2', '0123456789');
                $objPHPExcel->getActiveSheet()->SetCellValue('H2', '0123456789');
                $objPHPExcel->getActiveSheet()->SetCellValue('I2', 'Số 1, Phường An Bình, Quận Ninh Kiều, Cần Thơ');
                $objPHPExcel->getActiveSheet()->SetCellValue('J2', 'Số 1, Phường An Bình, Quận Ninh Kiều, Cần Thơ');
                $objPHPExcel->getActiveSheet()->SetCellValue('K2', '0');

                // lưu file 
                $file = 'export.xlsx';
                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                $objWriter->save($file);

                if (file_exists($file)) {
                    // download
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment; filename=' . basename($file));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file));
                    ob_clean();
                    flush();
                    readfile($file);
                    // xóa file tạm
                    unlink($file);
                    exit();
                } else {
                    header("location:../index.php?page=quan-ly-sinh-vien&status=failed");
                    exit();
                }
            } catch (Exception $e) {
                header("location:../index.php?page=quan-ly-sinh-vien&status=failed");
                exit();
            }
        case 'export-error':
            if (!isset($_SESSION['import_duplicate_rows']) || empty($_SESSION['import_duplicate_rows'])) {
                header("location:../index.php?page=quan-ly-sinh-vien");
                exit();
            }

            try {
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'STT');
                $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Mã số sinh viên');
                $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Họ tên sinh viên');
                $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Giới tính (Nam: 1, nữ: 0)');
                $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Ngày sinh (YYYY-MM-DD)');
                $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Email');
                $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Số điện thoại 1');
                $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Số điện thoại 2');
                $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Địa chỉ liên lạc (Số nhà, Ấp, Xã, Tỉnh phân cách bằng dấu phẩy)');
                $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Địa chỉ thường trú (Số nhà, Ấp, Xã, Tỉnh phân cách bằng dấu phẩy)');
                $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Chức vụ (Sinh viên: 0, Lớp trưởng: 1, Bí thư chi đoàn: 2)');

                $row_num = 2;
                foreach ($_SESSION['import_duplicate_rows'] as $row_data) {
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $row_num, $row_num - 1);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $row_num, $row_data['B'] ?? '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $row_num, $row_data['C'] ?? '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $row_num, $row_data['D'] ?? '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $row_num, $row_data['E'] ?? '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $row_num, $row_data['F'] ?? '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $row_num, $row_data['G'] ?? '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $row_num, $row_data['H'] ?? '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $row_num, $row_data['I'] ?? '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $row_num, $row_data['J'] ?? '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $row_num, $row_data['K'] ?? '');
                    $row_num++;
                }

                // lưu file 
                $file = 'danh_sach_sinh_vien_loi_trung_lap.xlsx';
                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                $objWriter->save($file);

                if (file_exists($file)) {
                    // download
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment; filename=' . basename($file));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file));
                    ob_clean();
                    flush();
                    readfile($file);
                    // xóa file tạm
                    unlink($file);
                    unset($_SESSION['import_duplicate_rows']);
                    exit();
                } else {
                    header("location:../index.php?page=quan-ly-sinh-vien&status=failed");
                    exit();
                }
            } catch (Exception $e) {
                header("location:../index.php?page=quan-ly-sinh-vien&status=failed");
                exit();
            }
    }
}
