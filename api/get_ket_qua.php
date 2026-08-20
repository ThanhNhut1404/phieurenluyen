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

$lich_su = $phieuchamdiem->phieuchamdiem__Get_Lich_Su_By_Id_Sinh_Vien($id_sinh_vien);

$data = [];
foreach ($lich_su as $item) {
    // kq_sv: "5|10|0..." => sum
    $diem_sv = null;
    if (!empty($item->kq_sv)) {
        $diem_sv = $phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua(explode('|', $item->kq_sv));
    }

    $diem_gv = null;
    if (!empty($item->kq_gv)) {
        $diem_gv = $phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua(explode('|', $item->kq_gv));
    }

    $data[] = [
        'id_phieu' => $item->id_phieu,
        'ten_dot' => $item->ten_dot,
        'ten_hoc_ky' => $item->ten_hoc_ky,
        'ten_nam_hoc' => $item->ten_nam_hoc,
        'diem_sv' => $diem_sv,
        'diem_gv' => $diem_gv,
        'tong_diem_xep_loai' => $item->tong_diem_xep_loai ?? null,
        'xep_loai' => $item->xep_loai ?? "Chưa xét",
        'ngay_thuc_hien' => $item->ngay_thuc_hien
    ];
}

response_json("success", "Thành công", $data);
?>
