<?php
require 'core.php';

$all_sv = $sinhvien->sinhvien__Get_All();
foreach($all_sv as $sv) {
    if(!empty($sv->anh_dai_dien)) {
        echo "ID: " . $sv->id_sinh_vien . " - Avatar: " . $sv->anh_dai_dien . "\n";
    }
}
?>
