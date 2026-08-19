<?php
require 'core.php';

$sv = $sinhvien->sinhvien__Get_By_Id(6);
echo "Avatar URL: " . $sv->anh_dai_dien;
?>
