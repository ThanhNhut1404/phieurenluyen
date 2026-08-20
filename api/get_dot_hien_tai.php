<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);
require_once 'core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_json("error", "Invalid request method");
}

$token = get_bearer_token();
if (!$token) {
    response_json("error", "Vui lòng đăng nhập (Missing Token)", null, 401);
}

$tai_khoan = $taikhoan->taikhoan__Get_By_Token($token);
if ($tai_khoan == "0") {
    response_json("error", "Token không hợp lệ hoặc đã hết hạn", null, 401);
}

$id_sinh_vien = $tai_khoan->id_nguoi_dung;
$active_dot = null;

$tat_ca_phieu = $phieuchamdiem->phieuchamdiem__Get_All();
foreach ($tat_ca_phieu as $p) {
    if ($p->id_sinh_vien == $id_sinh_vien) {
        $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($p->id_lop_ap_dung);
        if ($lop_ap_dung) {
            $dot = $dotchamdiem->dotchamdiem__Get_By_Id($lop_ap_dung->id_dot);
            if ($dot && $dot->trang_thai == 1) { // 1 là đang mở
                $active_dot = $dot;
                break;
            }
        }
    }
}

if ($active_dot) {
    // Format date string to dd/mm/yyyy
    $bat_dau = date('d/m/Y', strtotime($active_dot->thoi_gian_bat_dau));
    $ket_thuc = date('d/m/Y', strtotime($active_dot->thoi_gian_ket_thuc));

    $data = [
        'ten_dot' => $active_dot->ten_dot,
        'thoi_gian_bat_dau' => $bat_dau,
        'thoi_gian_ket_thuc' => $ket_thuc,
        'ten_hoc_ky' => $active_dot->ten_hoc_ky,
        'ten_nam_hoc' => $active_dot->ten_nam_hoc
    ];
    response_json("success", "Lấy đợt hiện tại thành công", $data);
} else {
    response_json("error", "Chưa có đợt rèn luyện");
}
?>
