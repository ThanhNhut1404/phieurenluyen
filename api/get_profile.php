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
$sv = $sinhvien->sinhvien__Get_By_Id($id_sinh_vien);

if (!$sv) {
    response_json("error", "Không tìm thấy thông tin sinh viên");
}

// Lấy thông tin lớp học, ngành, khoa
$ten_lop_hoc = "Đang cập nhật";
$ten_nganh_hoc = "Đang cập nhật";
$ten_khoa = "Đang cập nhật";

if ($sv->id_lop_hoc) {
    $lh = $lophoc->lophoc__Get_By_Id($sv->id_lop_hoc);
    if ($lh) {
        $ten_lop_hoc = $lh->ten_lop_hoc;
        
        $nh = $nganhhoc->nganhhoc__Get_By_Id($lh->id_nganh_hoc);
        if ($nh) {
            $ten_nganh_hoc = $nh->ten_nganh_hoc;
            
            $kh = $khoa->khoa__Get_By_Id($nh->id_khoa);
            if ($kh) {
                $ten_khoa = $kh->ten_khoa;
            }
        }
    }
}

// Build URL cho ảnh đại diện nếu có
$avatar_url = null;
if (!empty($sv->anh_dai_dien)) {
    $avatar_url = $sv->anh_dai_dien;
}

$data = [
    'ma_sinh_vien' => $sv->ma_sinh_vien,
    'ten_sinh_vien' => $sv->ten_sinh_vien,
    'gioi_tinh' => $sv->gioi_tinh == 1 ? "Nam" : "Nữ",
    'ngay_sinh' => date('d/m/Y', strtotime($sv->ngay_sinh)),
    'email' => $sv->email,
    'so_dien_thoai_1' => $sv->so_dien_thoai_1,
    'dia_chi_lien_lac' => $sv->dia_chi_lien_lac,
    'chuc_vu' => $sv->chuc_vu,
    'anh_dai_dien' => $avatar_url,
    'ten_lop_hoc' => $ten_lop_hoc,
    'ten_nganh_hoc' => $ten_nganh_hoc,
    'ten_khoa' => $ten_khoa,
];

response_json("success", "Lấy thông tin thành công", $data);
?>
