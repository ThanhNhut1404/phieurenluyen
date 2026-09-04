<?php
require "models/configs/config.php";
$db = new Database();

// Lấy 1 học sinh chưa tự chấm để test
$stmt = $db->connect->prepare("SELECT p.id_phieu, p.id_lop_ap_dung, p.id_sinh_vien, p.trang_thai, l.id_dot, l.id_lop_hoc, d.thoi_gian_ket_thuc, d.trang_thai as dot_trang_thai
                               FROM phieuchamdiem p
                               JOIN lopapdung l ON p.id_lop_ap_dung = l.id_lop_ap_dung
                               JOIN dotchamdiem d ON l.id_dot = d.id_dot
                               WHERE p.trang_thai = 1 LIMIT 1");
$stmt->execute();
$phieu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$phieu) {
    echo "Khong tim thay phieu nao co trang_thai = 1\n";
    exit;
}

echo "Testing with Phieu ID: " . $phieu['id_phieu'] . " | SV: " . $phieu['id_sinh_vien'] . "\n";
echo "Dot ID: " . $phieu['id_dot'] . " | End Date: " . $phieu['thoi_gian_ket_thuc'] . "\n";

$is_ended = (strtotime(date('Y-m-d')) >= strtotime($phieu['thoi_gian_ket_thuc']));
echo "is_ended: " . ($is_ended ? "TRUE" : "FALSE") . "\n";

$stmt2 = $db->connect->prepare("SELECT * FROM ketquaxeploai WHERE id_phieu = ?");
$stmt2->execute([$phieu['id_phieu']]);
$kq = $stmt2->fetch(PDO::FETCH_ASSOC);

$has_kq = $kq ? true : false;
echo "has_kq (id_ket_qua): " . ($has_kq ? "TRUE" : "FALSE") . "\n";

echo "trang_thai = 1: TRUE\n";

if ($is_ended && $has_kq && $phieu['trang_thai'] == 1) {
    if (isset($kq['ket_qua'])) {
        echo "=> BUTTON WILL SHOW UP! Base score: " . $kq['ket_qua'] . "\n";
    } else {
        echo "=> BUTTON HIDDEN: ket_qua is not set in ketquaxeploai.\n";
    }
} else {
    echo "=> BUTTON HIDDEN because:\n";
    if (!$is_ended) echo " - is_ended is false\n";
    if (!$has_kq) echo " - id_ket_qua is not set (Admin hasn't processed results)\n";
    if ($phieu['trang_thai'] != 1) echo " - trang_thai != 1\n";
}
?>
