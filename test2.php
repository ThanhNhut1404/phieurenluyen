<?php
require "models/configs/config.php";
$db = new Database();

$stmt = $db->connect->prepare("
    SELECT p.id_phieu, p.id_sinh_vien, p.trang_thai, k.id_ket_qua
    FROM phieuchamdiem p
    JOIN ketquaxeploai k ON p.id_phieu = k.id_phieu
    WHERE p.trang_thai = 1
");
$stmt->execute();
$kq = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Count: " . count($kq) . "\n";
if (count($kq) > 0) {
    echo "Phieu co the hien thi nut: " . $kq[0]['id_phieu'] . " cho SV: " . $kq[0]['id_sinh_vien'] . "\n";
}
?>
