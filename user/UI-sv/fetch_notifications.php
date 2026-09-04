<?php
$list_thong_bao = [];

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
    
    if (empty($active_phieu->kq_sv)) {
        if ($so_ngay_con_lai > 0 && $so_ngay_con_lai <= 3) {
            $list_thong_bao[] = (object)[
                'id_thong_bao' => 0,
                'tieu_de' => 'Sắp hết hạn chấm điểm!',
                'noi_dung' => 'Chỉ còn ' . $so_ngay_con_lai . ' ngày để hoàn thành phiếu rèn luyện ' . $active_dot->ten_hoc_ky . ' - ' . $active_dot->ten_nam_hoc . '. Hãy nộp ngay!',
                'ngay_tao' => date('Y-m-d H:i:s'),
                'nguoi_gui' => 'Hệ thống',
                'is_read' => true
            ];
        } else {
            $list_thong_bao[] = (object)[
                'id_thong_bao' => 0,
                'tieu_de' => 'Đợt chấm điểm đang mở',
                'noi_dung' => 'Đợt chấm điểm ' . $active_dot->ten_hoc_ky . ' - ' . $active_dot->ten_nam_hoc . ' đang diễn ra. Hạn chót: ' . date('d/m/Y', $han_chot) . '.',
                'ngay_tao' => date('Y-m-d H:i:s'),
                'nguoi_gui' => 'Hệ thống',
                'is_read' => true
            ];
        }
    } else {
        if (empty($active_phieu->kq_gv) && empty($active_phieu->kq_lt_bt)) {
            $list_thong_bao[] = (object)[
                'id_thong_bao' => 0,
                'tieu_de' => 'Đã nộp phiếu tự đánh giá',
                'noi_dung' => 'Bạn đã nộp phiếu ' . $active_dot->ten_hoc_ky . '. Vui lòng chờ Ban cán sự lớp duyệt.',
                'ngay_tao' => date('Y-m-d H:i:s'),
                'nguoi_gui' => 'Hệ thống',
                'is_read' => true
            ];
        } else if (empty($active_phieu->kq_gv) && !empty($active_phieu->kq_lt_bt)) {
            $list_thong_bao[] = (object)[
                'id_thong_bao' => 0,
                'tieu_de' => 'Lớp trưởng đã duyệt',
                'noi_dung' => 'Phiếu rèn luyện ' . $active_dot->ten_hoc_ky . ' đã được duyệt bởi Ban cán sự. Đang chờ Cố vấn học tập duyệt cuối.',
                'ngay_tao' => date('Y-m-d H:i:s'),
                'nguoi_gui' => 'Hệ thống',
                'is_read' => true
            ];
        } else if (!empty($active_phieu->kq_gv)) {
            $diem = $phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua(explode('|', $active_phieu->kq_gv));
            $list_thong_bao[] = (object)[
                'id_thong_bao' => 0,
                'tieu_de' => 'Phiếu đã được duyệt!',
                'noi_dung' => 'Phiếu rèn luyện ' . $active_dot->ten_hoc_ky . ' của bạn đã hoàn tất. Điểm tổng: ' . $diem . ' điểm.',
                'ngay_tao' => date('Y-m-d H:i:s'),
                'nguoi_gui' => 'Hệ thống',
                'is_read' => true
            ];
        }
    }
}

try {
    if (isset($thongbao)) {
        $db_thong_bao = $thongbao->thongbao__Get_By_Sinh_Vien($id_sinh_vien);
        if ($db_thong_bao) {
            foreach ($db_thong_bao as $tb) {
                $list_thong_bao[] = $tb;
            }
        }
    }
} catch (PDOException $e) {}

usort($list_thong_bao, function($a, $b) {
    return strtotime($b->ngay_tao) - strtotime($a->ngay_tao);
});
?>
