<?php

    require '../../models/getModel.php';
    require('../../assets/vendor/PHPOffice/PHPExcel.php');
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'export':

                function vnn_first_word($str) { // lấy từ đầu tiên của chuỗi họ tên
                    $str2 = trim($str); // xóa khoảng trắng dư thừa
                    $word = mb_split(' ', $str2); // tách từ dựa trên khoảng trắng
                    $rs = $word[0]; // lấy từ đầu tiên trong mảng
                return $rs;
                }
                function vnn_end_word($str) { // từ cuối cùng của chuỗi họ tên
                    $rs = NULL; // cái này cũng giúp kiểm tra chuỗi có ít nhất 2 từ hay không
                    //nếu chỉ có một từ, nó sẽ trả về kết quả NULL
                    $str2 = trim($str); // loại bỏ khoảng trắng dư thừa 
                    $word = mb_split(' ', $str2); // tách từ
                    $n = count($word)-1; // lấy vị trí mảng của từ cuối cùng
                    if ($n > 0) {
                        for($i = 1; $i<=$n; $i++){
                            $rs .= " ".$word[$i];
                        }
                    } // lấy từ cuối cùng

                return ucwords(trim($rs), " ");
                }


                $status = 0;
                $id_dot = $_POST['id_dot'];
                $id_lop_hoc = $_POST['id_lop_hoc'];
                $res = $ketquaxeploai->ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot($id_lop_hoc, $id_dot);
                

                $objPHPExcel = new PHPExcel(); 
                $objDrawing = new PHPExcel_Worksheet_Drawing();    //create object for Worksheet drawing
              

                $row_hd = 11;
    
                $objPHPExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->SetCellValue('C1', "TRƯỜNG ĐẠI HỌC TÂY ĐÔ");
                $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->SetCellValue('C2', "PHÒNG CTCT - QLSV");

                $objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->SetCellValue('H1', "CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM");
                $objPHPExcel->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('H2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->SetCellValue('H2', "Độc lập - Tự do - Hạnh phúc");

                $objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->SetCellValue('E4', "KẾT QUẢ RÈN LUYỆN");

                $objPHPExcel->getActiveSheet()->SetCellValue('A5', "Học kỳ: " . $res[0]->ten_hoc_ky);
                $objPHPExcel->getActiveSheet()->SetCellValue('A6', "Bậc đào tạo: Đại học");
                $objPHPExcel->getActiveSheet()->SetCellValue('A7', "Khoa: ".$res[0]->ten_khoa);

                $objPHPExcel->getActiveSheet()->SetCellValue('F7', "Hệ đào tạo: Chính quy");
      

            

                $objDrawing->setPath('../../assets/img/logo.jpg');
                $objDrawing->setCoordinates('A1');  
                $objDrawing->setOffsetX(10); 
                $objDrawing->setOffsetY(10);                
                $objDrawing->setWidth(32); 
                $objDrawing->setHeight(32); 
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());


                $objPHPExcel->getActiveSheet()->getStyle('A10')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('B10')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('C10')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('D10')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('E10')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('F10')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('G10')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('H10')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

                $objPHPExcel->getActiveSheet()->getStyle('A10')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('B10')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('C10')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('D10')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('E10')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('F10')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('G10')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('H10')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $objPHPExcel->getActiveSheet()->SetCellValue('A10', "STT");
                $objPHPExcel->getActiveSheet()->SetCellValue('B10', "Mã số SV");
                $objPHPExcel->getActiveSheet()->SetCellValue('C10', "Họ");
                $objPHPExcel->getActiveSheet()->SetCellValue('D10', "Tên");
                $objPHPExcel->getActiveSheet()->SetCellValue('E10', "Điểm");
                $objPHPExcel->getActiveSheet()->SetCellValue('F10', "Xếp loại");
                $objPHPExcel->getActiveSheet()->SetCellValue('G10', "Ghi chú");
                $objPHPExcel->getActiveSheet()->SetCellValue('H10', "Lớp");

                $count = 1;
                foreach($res as $item){
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$row_hd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_hd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle('C'.$row_hd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle('D'.$row_hd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle('E'.$row_hd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle('F'.$row_hd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle('G'.$row_hd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle('H'.$row_hd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row_hd, "".$count++);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row_hd, "".$item->ma_sinh_vien);
                    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row_hd, "".vnn_first_word($item->ten_sinh_vien));
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row_hd, "".vnn_end_word($item->ten_sinh_vien));
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row_hd, "".$item->ket_qua);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$row_hd, "".$item->xep_loai);
                    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$row_hd, "");
                    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$row_hd, "".$item->ten_lop_hoc);

                    $row_hd +=1;
                }
                $objPHPExcel->getActiveSheet()->getStyle('F'.$row_hd + 2, "")->getFont()->setItalic(true);
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.$row_hd + 2, "Cần Thơ, ngày       tháng       năm ".date('Y'));

                $objPHPExcel->getActiveSheet()->getStyle('A'.$row_hd + 3, "")->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row_hd + 3, "Lớp trưởng");

                $objPHPExcel->getActiveSheet()->getStyle('C'.$row_hd + 3, "")->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row_hd + 3, "Bí thư đoàn Khoa");

                $objPHPExcel->getActiveSheet()->getStyle('E'.$row_hd + 3, "")->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row_hd + 3, "Cố vấn học tập");

                $objPHPExcel->getActiveSheet()->getStyle('H'.$row_hd + 3, "")->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.$row_hd + 3, $res[0]->ten_khoa);
                // lưu file 
                $file = 'export.xlsx';
                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
                $objWriter->save($file);
                
                // download
                header('Content-Description: File Transfer');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename='.basename($file));
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

                if($status == 0){
                    header("location:../index.php?page=quan-ly-thong-ke&status=export-failed");
                }else{
                    header("location:../index.php?page=quan-ly-thong-ke&status=export-success");
                }
                
                break;
            }
        }
        ?>