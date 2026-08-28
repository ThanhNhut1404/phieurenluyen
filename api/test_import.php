<?php
require '../models/getModel.php';
require('../assets/vendor/PHPOffice/PHPExcel.php');

$file = '../import/import_cntt16B.xlsx';
$objReader = PHPExcel_IOFactory::createReaderForFile($file);
$objExcel = $objReader->load($file);
$sheetData = $objExcel->getActiveSheet()->toArray(null, true, true, true);

print_r($sheetData[2]);
?>
