<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'core.php';

$id_sinh_vien = 375; // Or any student
$tat_ca_phieu = $phieuchamdiem->phieuchamdiem__Get_All();
foreach ($tat_ca_phieu as $p) {
    $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($p->id_lop_ap_dung);
    if ($lop_ap_dung) {
        $dot = $dotchamdiem->dotchamdiem__Get_By_Id($lop_ap_dung->id_dot);
        if ($dot && $dot->trang_thai == 1) { 
            // Access it!
            $start = strtotime($dot->thoi_gian_bat_dau);
            echo "Accessing thoi_gian_bat_dau for dot: " . $dot->id_dot . " => " . $dot->thoi_gian_bat_dau . "\n";
        }
    }
}
echo "Done loop.\n";
