<?php
require 'models/configs/config.php';

$db = new Database();
$pdo = $db->connect;

// Assuming $id_nam_hoc = 1 (or whatever the latest is)
$stmt = $pdo->query("SELECT * FROM namhoc ORDER BY id_nam_hoc DESC LIMIT 1");
$namhoc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$namhoc) {
    die("No nam hoc found.");
}

echo "Nam hoc: {$namhoc['ngay_bat_dau']} -> {$namhoc['ngay_ket_thuc']}\n";

$id_nam_hoc = $namhoc['id_nam_hoc'];
$ten_hoc_ky = "HK1";
$ngay_bat_dau = "2025-09-03";
$ngay_ket_thuc = "2025-12-31";

require 'models/model/hocky.php';
$hocky = new HocKy();

if (!$hocky->hocky__Is_Within_Nam_Hoc($id_nam_hoc, $ngay_bat_dau, $ngay_ket_thuc)) {
    echo "Error: out-of-range-hoc-ky\n";
} else if ($hocky->hocky__Count_By_Nam_Hoc($id_nam_hoc) >= 3) {
    echo "Error: limit-hoc-ky\n";
} else if ($hocky->hocky__Name_Exists($ten_hoc_ky, $id_nam_hoc)) {
    echo "Error: duplicate-ten-hocky\n";
} else if ($hocky->hocky__Is_Overlap($id_nam_hoc, $ngay_bat_dau, $ngay_ket_thuc)) {
    echo "Error: overlap-hocky\n";
} else {
    echo "All checks passed. Can add.\n";
}
