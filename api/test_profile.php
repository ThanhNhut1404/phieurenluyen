<?php
require 'core.php';

$id_sinh_vien = 234;
$sv = $sinhvien->sinhvien__Get_By_Id($id_sinh_vien);

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

$avatar_url = null;
if (!empty($sv->anh_dai_dien)) {
    $avatar_url = "http://localhost/phieurenluyen/" . $sv->anh_dai_dien;
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

print_r($data);
?>
