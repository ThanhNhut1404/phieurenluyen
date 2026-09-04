<?php
require "models/configs/config.php";
$db = new Database();

$stmt = $db->connect->prepare("SELECT id_sinh_vien, trang_thai FROM phieuchamdiem");
$stmt->execute();
$phieu = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Total phieu: " . count($phieu) . "\n";
$trang_thai_1 = 0;
foreach($phieu as $p) { if ($p["trang_thai"] == 1) $trang_thai_1++; }
echo "Phieu trang_thai=1: " . $trang_thai_1 . "\n";

$stmt2 = $db->connect->prepare("SELECT id_ket_qua, id_sinh_vien, id_phieu FROM ketquaxeploai");
$stmt2->execute();
$kq = $stmt2->fetchAll(PDO::FETCH_ASSOC);
echo "Total ketquaxeploai: " . count($kq) . "\n";

$stmt3 = $db->connect->prepare("SELECT id_dot, thoi_gian_ket_thuc, trang_thai FROM dotchamdiem");
$stmt3->execute();
$dot = $stmt3->fetchAll(PDO::FETCH_ASSOC);
foreach($dot as $d) {
    echo "Dot: " . $d["id_dot"] . ", End: " . $d["thoi_gian_ket_thuc"] . ", Trang thai: " . $d["trang_thai"] . "\n";
}
?>
