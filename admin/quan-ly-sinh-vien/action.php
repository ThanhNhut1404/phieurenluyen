<?php

require '../../models/getModel.php';
require("../../assets/vendor/PHPOffice/PHPExcel.php");

if (isset($_GET['req'])) {
    switch ($_GET['req']) {
        case 'add':

            $ma_sinh_vien = $_POST['ma_sinh_vien'];
            $ten_sinh_vien = $_POST['ten_sinh_vien'];
            $gioi_tinh = $_POST['gioi_tinh'];
            $ngay_sinh = $_POST['ngay_sinh'];
            $email = $_POST['email'];
            $so_dien_thoai_1 = $_POST['so_dien_thoai_1'];
            $so_dien_thoai_2 = $_POST['so_dien_thoai_2'];
            $dia_chi_lien_lac = $_POST['dia_chi_lien_lac'];
            $dia_chi_thuong_tru = $_POST['dia_chi_thuong_tru'];
            $chuc_vu = $_POST['chuc_vu'];
            $id_lop_hoc = $_POST['id_lop_hoc'];

            $status = $sinhvien->sinhvien__Add($ma_sinh_vien, $ten_sinh_vien, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $chuc_vu, $id_lop_hoc);
            if ($status != 0) {
                header('location: ../index.php?page=quan-ly-sinh-vien&status=success');
            } else {
                header('location: ../index.php?page=quan-ly-sinh-vien&status=failed');
            }

            break;
        case 'update':

            $id_sinh_vien = $_POST['id_sinh_vien'];
            $ma_sinh_vien = $_POST['ma_sinh_vien'];
            $ten_sinh_vien = $_POST['ten_sinh_vien'];
            $gioi_tinh = $_POST['gioi_tinh'];
            $ngay_sinh = $_POST['ngay_sinh'];
            $email = $_POST['email'];
            $so_dien_thoai_1 = $_POST['so_dien_thoai_1'];
            $so_dien_thoai_2 = $_POST['so_dien_thoai_2'];
            $dia_chi_lien_lac = $_POST['dia_chi_lien_lac'];
            $dia_chi_thuong_tru = $_POST['dia_chi_thuong_tru'];
            $chuc_vu = $_POST['chuc_vu'];
            $id_lop_hoc = $_POST['id_lop_hoc'];

            $status = $sinhvien->sinhvien__Update($id_sinh_vien, $ma_sinh_vien, $ten_sinh_vien, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $chuc_vu, $id_lop_hoc);
            if ($status != 0) {
                header('location: ../index.php?page=quan-ly-sinh-vien&status=success');
            } else {
                header('location: ../index.php?page=quan-ly-sinh-vien&status=failed');
            }

            break;

        case 'delete':

            $id_sinh_vien = $_GET['id_sinh_vien'];

            $status = $sinhvien->sinhvien__Delete($id_sinh_vien);
            if ($status != 0) {
                header('location: ../index.php?page=quan-ly-sinh-vien&status=success');
            } else {
                header('location: ../index.php?page=quan-ly-sinh-vien&status=failed');
            }

            break;

        case "import":
            $status = 0;
            $file = $_FILES["file"]["tmp_name"];
            $id_lop_hoc = $_POST['id_lop_hoc'];
            if (isset($file)) {
                $objReader = PHPExcel_IOFactory::createReaderForFile($file);
                // $objReader->setLoadSheetsOnly();
                $objExcel = $objReader->load($file);
                $sheetData = $objExcel->getActiveSheet()->toArray(null, true, true, true);
                $highestRow = $objExcel->setActiveSheetIndex()->getHighestRow();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $ma_sinh_vien = $sheetData[$row]['B'];
                    $ten_sinh_vien = $sheetData[$row]['C'];
                    $gioi_tinh = $sheetData[$row]['D'];
                    $ngay_sinh = $sheetData[$row]['E'];
                    $email = $sheetData[$row]['F'];
                    $so_dien_thoai_1 = $sheetData[$row]['G'];
                    $so_dien_thoai_2 = $sheetData[$row]['H'];
                    $dia_chi_lien_lac = $sheetData[$row]['I'];
                    $dia_chi_thuong_tru = $sheetData[$row]['J'];
                    $chuc_vu = $sheetData[$row]['K'];

                    $status += $sinhvien->sinhvien__Add($ma_sinh_vien, $ten_sinh_vien, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $chuc_vu, $id_lop_hoc);
                }
            }

            if ($status == 0) {
                header("location:../index.php?page=quan-ly-sinh-vien&status=fail");
            } else {
                header("location:../index.php?page=quan-ly-sinh-vien&status=success");
            }
            break;


        case 'export':
            $status = 0;
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'STT');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Mã số sinh viên');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Họ tên sinh viên');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Giới tính (Nam: 1, nữ: 0)');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Ngày sinh (YYYY-MM-DD)');
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Email');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Số điện thoại 1');
            $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Số điện thoại 2');
            $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Địa chỉ liên lạc');
            $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Địa chỉ thường trú');
            $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Chức vụ (Sinh viên: 0, Lớp trưởng: 1, Bí thư chi đoàn: 2)');

            $objPHPExcel->getActiveSheet()->SetCellValue('A2', '1');
            $objPHPExcel->getActiveSheet()->SetCellValue('B2', '187060001');
            $objPHPExcel->getActiveSheet()->SetCellValue('C2', 'Nguyễn Ngọc Thiên Xuân');
            $objPHPExcel->getActiveSheet()->SetCellValue('D2', '1');
            $objPHPExcel->getActiveSheet()->SetCellValue('E2', '2001-06-05');
            $objPHPExcel->getActiveSheet()->SetCellValue('F2', 'sv@tdu.edu.com');
            $objPHPExcel->getActiveSheet()->SetCellValue('G2', '0123456789');
            $objPHPExcel->getActiveSheet()->SetCellValue('H2', '0123456789');
            $objPHPExcel->getActiveSheet()->SetCellValue('I2', 'Cần Thơ');
            $objPHPExcel->getActiveSheet()->SetCellValue('J2', 'Cần Thơ');
            $objPHPExcel->getActiveSheet()->SetCellValue('K2', '0');

            // lưu file 
            $file = 'export.xlsx';
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save($file);

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
            $status .= unlink($file);

            if ($status == 0) {
                header("location:../index.php?page=quan-ly-sinh-vien&status=fail");
            } else {
                header("location:../index.php?page=quan-ly-sinh-vien&status=success");
            }

            break;
    }
}
