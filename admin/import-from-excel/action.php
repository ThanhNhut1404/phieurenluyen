<?php

    require '../../models/getModel.php';
    require('../../assets/vendor/PHPOffice/PHPExcel.php');
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case "import":
                $status = 0;
                
                $file = $_FILES["file"]["tmp_name"];
                if(isset($file)){
                    $objReader = PHPExcel_IOFactory::createReaderForFile($file);
                     $objReader->setLoadSheetsOnly();
                     $objExcel = $objReader->load($file);
                     $sheetData = $objExcel->getActiveSheet()->toArray(null, true, true,true);
                     $highestRow = $objExcel->setActiveSheetIndex()->getHighestRow();


                    for($row = 2; $row<=$highestRow; $row++){
                        $id          = $sheetData[$row]['A'];
                        $note      = $sheetData[$row]['B'];
                        $status .= $import->import__Add($id, $note);
                    }
                }
               
                if($status == 0){
                    header("location:../index.php?page=import-from-excel&status=fail");
                }else{
                    header("location:../index.php?page=import-from-excel&status=success");
                }
            break;  
            
            case "export":
                               
                $status = 0;
                
                $import__Get_All = $import->import__Get_All();

                $objPHPExcel = new PHPExcel(); 
                $objPHPExcel->setActiveSheetIndex(0); 

                $row_hd = 2;
    
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);

                $objPHPExcel->getActiveSheet()->SetCellValue('A1', "ID");
                $objPHPExcel->getActiveSheet()->SetCellValue('B1', "NOTE");
                foreach($import__Get_All as $item){
                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row_hd, "".$item->id);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row_hd, "".$item->npte);

                    $row_hd +=1;
                }
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
                    header("location:../index.php?page=import-from-excel&status=fail");
                }else{
                    header("location:../index.php?page=import-from-excel&status=success");
                }
                break; 
        }
    }

?>