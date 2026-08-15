<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);
require_once 'core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_json("error", "Invalid request method");
}

// Lấy token từ header
$token = get_bearer_token();
if (!$token) {
    response_json("error", "Vui lòng đăng nhập (Missing Token)", null, 401);
}

// Kiểm tra token có hợp lệ không
$tai_khoan = $taikhoan->taikhoan__Get_By_Token($token);
if ($tai_khoan == "0") {
    response_json("error", "Token không hợp lệ hoặc đã hết hạn", null, 401);
}

// 1. Tìm phiếu chấm điểm ĐANG MỞ của sinh viên này
// Đang mở = đợt chấm điểm đang trong thời gian (NOW() >= thoi_gian_bat_dau AND NOW() <= thoi_gian_ket_thuc)
$id_sinh_vien = $tai_khoan->id_nguoi_dung;
$active_phieu = null;
$active_lop_ap_dung = null;
$active_dot = null;

$tat_ca_phieu = $phieuchamdiem->phieuchamdiem__Get_All();
foreach ($tat_ca_phieu as $p) {
    if ($p->id_sinh_vien == $id_sinh_vien) {
        $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($p->id_lop_ap_dung);
        if ($lop_ap_dung) {
            $dot = $dotchamdiem->dotchamdiem__Get_By_Id($lop_ap_dung->id_dot);
            if ($dot && $dot->trang_thai == 1) { // 1 là đang mở (hoặc có thể kiểm tra thoi_gian)
                $active_phieu = $p;
                $active_lop_ap_dung = $lop_ap_dung;
                $active_dot = $dot;
                break;
            }
        }
    }
}

if (!$active_phieu) {
    response_json("error", "Hiện không có đợt chấm điểm nào mở cho bạn.");
}

// 2. Lấy cấu trúc phiếu (Điều -> Khoản -> Mục)
$id_mau_phieu = $active_lop_ap_dung->id_mau_phieu;
$bo_cau_hoi = $bocauhoi->bocauhoi__Get_By_Id_Mau_Phieu($id_mau_phieu);

$tieu_chi = [];

foreach ($bo_cau_hoi as $bch) {
    $d = $dieu->dieu__Get_By_Id($bch->id_dieu);
    if ($d && $d->is_deleted == 0) {
        $dieu_data = [
            'id_dieu' => $d->id_dieu,
            'ten_dieu' => $d->ten_dieu,
            'diem_toi_da' => 0, // Tính tổng sau
            'khoan' => []
        ];
        
        $diem_dieu = 0;
        $danh_sach_khoan = $khoan->khoan__Get_By_Id_Dieu($d->id_dieu);
        foreach ($danh_sach_khoan as $k) {
            $khoan_data = [
                'id_khoan' => $k->id_khoan,
                'ten_khoan' => $k->ten_khoan,
                'diem_toi_da' => $k->diem_toi_da,
                'muc' => []
            ];
            
            $diem_khoan_max = $k->diem_toi_da; // Có thể giới hạn điểm tối đa của khoản
            $danh_sach_muc = $muc->muc__Get_By_Id_Khoan($k->id_khoan);
            foreach ($danh_sach_muc as $m) {
                // Chỉ lấy những mục sinh viên được quyền chấm (quyen_sv == 1)
                // Hoặc trả về hết nhưng đánh dấu quyen_sv để app tự disable
                $muc_data = [
                    'id_muc' => $m->id_muc,
                    'ten_muc' => $m->ten_muc,
                    'diem_toi_da' => $m->diem_toi_da,
                    'quyen_sv' => $m->quyen_sv,
                    'co_minh_chung' => $m->co_minh_chung
                ];
                $khoan_data['muc'][] = $muc_data;
            }
            $dieu_data['khoan'][] = $khoan_data;
            // Ở đây tạm tính điểm tối đa của điều bằng tổng điểm tối đa của các khoản (nếu khoản có diem_toi_da)
            $diem_dieu += $k->diem_toi_da;
        }
        $dieu_data['diem_toi_da'] = $diem_dieu;
        $tieu_chi[] = $dieu_data;
    }
}

// 3. Lấy kết quả đã chấm (nếu có)
$kq_sv = [];
if (!empty($active_phieu->kq_sv)) {
    $kq_sv_raw = explode('|', $active_phieu->kq_sv); // ["5", "10", "0"]
    $idx = 0;
    
    foreach ($bo_cau_hoi as $bch) {
        $danh_sach_khoan = $khoan->khoan__Get_By_Id_Dieu($bch->id_dieu);
        foreach ($danh_sach_khoan as $k) {
            $danh_sach_muc = $muc->muc__Get_By_Id_Khoan($k->id_khoan);
            foreach ($danh_sach_muc as $m) {
                if (isset($kq_sv_raw[$idx]) && $kq_sv_raw[$idx] !== '' && !str_contains($kq_sv_raw[$idx], '-')) {
                    $kq_sv[] = $m->id_muc . '-' . $kq_sv_raw[$idx];
                } elseif (isset($kq_sv_raw[$idx]) && str_contains($kq_sv_raw[$idx], '-')) {
                    // Nếu dữ liệu cũ bị dính dạng 1-5 thì lấy nguyên (phòng hờ)
                    $kq_sv[] = $kq_sv_raw[$idx];
                }
                $idx++;
            }
        }
    }
}

// Lấy minh chứng đã nộp (nếu có)
$minh_chung_da_nop = [];
$danh_sach_mc = $minhchung->minhchung__Get_By_Id_Phieu($active_phieu->id_phieu);
if (is_array($danh_sach_mc)) {
    foreach ($danh_sach_mc as $mc) {
        if ($mc->id_muc != null) {
            $minh_chung_da_nop[] = [
                'id_minh_chung' => $mc->id_minh_chung,
                'id_muc' => $mc->id_muc,
                'hinh_anh' => $mc->hinh_anh
            ];
        }
    }
}

// Trả về JSON
$response_data = [
    'id_phieu' => $active_phieu->id_phieu,
    'ten_dot' => $active_dot->ten_dot,
    'kq_sv_da_cham' => $kq_sv,
    'minh_chung_da_nop' => $minh_chung_da_nop,
    'tieu_chi' => $tieu_chi
];

response_json("success", "Thành công", $response_data);
?>
