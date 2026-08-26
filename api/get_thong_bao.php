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

$thong_bao = [];

$active_phieu = null;
$active_lop_ap_dung = null;
$active_dot = null;

$tat_ca_phieu = $phieuchamdiem->phieuchamdiem__Get_All();
foreach ($tat_ca_phieu as $p) {
    if ($p->id_sinh_vien == $id_sinh_vien) {
        $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($p->id_lop_ap_dung);
        if ($lop_ap_dung) {
            $dot = $dotchamdiem->dotchamdiem__Get_By_Id($lop_ap_dung->id_dot);
            if ($dot && $dot->trang_thai == 1) { 
                $active_phieu = $p;
                $active_lop_ap_dung = $lop_ap_dung;
                $active_dot = $dot;
                break;
            }
        }
    }
}

if ($active_dot) {
    $ngay_hien_tai = time();
    $han_chot = strtotime($active_dot->thoi_gian_ket_thuc);
    $so_ngay_con_lai = ceil(($han_chot - $ngay_hien_tai) / (60 * 60 * 24));
    
    if (!$active_phieu) {
        if ($so_ngay_con_lai > 0 && $so_ngay_con_lai <= 3) {
            $thong_bao[] = [
                'type' => 'warning',
                'title' => 'Sắp hết hạn chấm điểm!',
                'message' => 'Chỉ còn ' . $so_ngay_con_lai . ' ngày để hoàn thành phiếu rèn luyện ' . $active_dot->ten_hoc_ky . ' - ' . $active_dot->ten_nam_hoc . '. Hãy nộp ngay!',
                'time' => date('d/m/Y')
            ];
        } else {
            $thong_bao[] = [
                'type' => 'info',
                'title' => 'Đợt chấm điểm đang mở',
                'message' => 'Đợt chấm điểm ' . $active_dot->ten_hoc_ky . ' - ' . $active_dot->ten_nam_hoc . ' đang diễn ra. Hạn chót: ' . date('d/m/Y', $han_chot) . '.',
                'time' => date('d/m/Y')
            ];
        }
    } else {
        // Đã nộp
        if (empty($active_phieu->kq_gv) && empty($active_phieu->kq_lt_bt)) {
            $thong_bao[] = [
                'type' => 'warning',
                'title' => 'Đã nộp phiếu tự đánh giá',
                'message' => 'Bạn đã nộp phiếu ' . $active_dot->ten_hoc_ky . '. Vui lòng chờ Ban cán sự lớp duyệt.',
                'time' => date('d/m/Y')
            ];
        } else if (empty($active_phieu->kq_gv) && !empty($active_phieu->kq_lt_bt)) {
            $thong_bao[] = [
                'type' => 'info',
                'title' => 'Lớp trưởng đã duyệt',
                'message' => 'Phiếu rèn luyện ' . $active_dot->ten_hoc_ky . ' đã được duyệt bởi Ban cán sự. Đang chờ Cố vấn học tập duyệt cuối.',
                'time' => date('d/m/Y')
            ];
        } else if (!empty($active_phieu->kq_gv)) {
            $diem = $phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua(explode('|', $active_phieu->kq_gv));
            $thong_bao[] = [
                'type' => 'success',
                'title' => 'Phiếu đã được duyệt!',
                'message' => 'Phiếu rèn luyện ' . $active_dot->ten_hoc_ky . ' của bạn đã hoàn tất. Điểm tổng: ' . $diem . ' điểm.',
                'time' => date('d/m/Y')
            ];
        }
    }
} else {
    // Không có đợt nào đang mở
    $thong_bao[] = [
        'type' => 'info',
        'title' => 'Chưa có đợt đánh giá mới',
        'message' => 'Hiện tại nhà trường chưa mở đợt chấm điểm rèn luyện nào cho lớp của bạn.',
        'time' => date('d/m/Y')
    ];
}

// 2. Có thể thêm thông báo chung nếu muốn
if (count($thong_bao) == 0) {
    $thong_bao[] = [
        'type' => 'info',
        'title' => 'Chào mừng bạn quay lại',
        'message' => 'Chúc bạn một ngày học tập và làm việc hiệu quả. Đừng quên theo dõi điểm rèn luyện thường xuyên nhé!',
        'time' => date('d/m/Y')
    ];
}

response_json("success", "Lấy thông báo thành công", $thong_bao);
?>
