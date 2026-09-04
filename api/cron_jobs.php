<?php
// Tự động kiểm tra và chạy cron mỗi ngày
if (!isset($thongbao)) {
    require_once '../models/getModel.php';
}

$cron_date = date('Y-m-d');
$cron_key_daily = 'daily_cron_' . $cron_date;

try {
    $check_daily = $thongbao->connect->prepare("SELECT * FROM cron_log WHERE job_key = ?");
    $check_daily->execute([$cron_key_daily]);
    if ($check_daily->rowCount() > 0) {
        return; // Đã chạy hôm nay
    }
} catch (PDOException $e) {
    return; // Bảng chưa tồn tại hoặc lỗi DB, bỏ qua cron
}

try {
    $insert_cron = $thongbao->connect->prepare("INSERT INTO cron_log (job_key) VALUES (?)");
    $insert_cron->execute([$cron_key_daily]);
} catch(Exception $e) {
    return; // Có request khác đã insert
}

// -----------------------------------------
// BẮT ĐẦU XỬ LÝ LOGIC CRON
// -----------------------------------------

if (!function_exists('cron_has_run')) {
    function cron_has_run($db, $key) {
        $stmt = $db->prepare("SELECT * FROM cron_log WHERE job_key = ?");
        $stmt->execute([$key]);
        return $stmt->rowCount() > 0;
    }
    function cron_mark_run($db, $key) {
        try {
            $stmt = $db->prepare("INSERT INTO cron_log (job_key) VALUES (?)");
            $stmt->execute([$key]);
        } catch(Exception $e) {}
    }
}

$tat_ca_dot = $dotchamdiem->dotchamdiem__Get_All();
$today_time = strtotime($cron_date);

foreach ($tat_ca_dot as $dot) {
    if ($dot->trang_thai != 1) continue; // Chỉ xử lý đợt đang mở (hoặc vừa đóng chưa đổi trạng thái)

    $t_start = strtotime($dot->thoi_gian_bat_dau);
    $t_end = strtotime($dot->thoi_gian_ket_thuc);
    
    $start_str = date('Y-m-d', $t_start);
    $end_str = date('Y-m-d', $t_end);
    $mid_time = $t_start + ($t_end - $t_start) / 2;
    $mid_str = date('Y-m-d', $mid_time);
    $one_day_before_str = date('Y-m-d', $t_end - 86400);

    // 1. Notification: Start
    if ($cron_date == $start_str) {
        $key = 'start_' . $dot->id_dot;
        if (!cron_has_run($thongbao->connect, $key)) {
            $thongbao->thongbao__Add(
                "Đợt chấm điểm đã bắt đầu!",
                "Đợt chấm điểm {$dot->ten_hoc_ky} - {$dot->ten_nam_hoc} đã chính thức mở. Vui lòng vào ứng dụng để nộp phiếu tự đánh giá trước ngày " . date('d/m/Y', $t_end) . ".",
                1, 'Hệ thống', 0
            );

            // Gửi email cho sinh viên
            require_once __DIR__ . '/cron_email_giang_vien.php';
            cron_send_emails_sv($dot->id_dot, 'start');

            cron_mark_run($thongbao->connect, $key);
        }
    }

    // 2. Notification: Midway
    if ($cron_date == $mid_str && $cron_date != $start_str) {
        $key = 'mid_' . $dot->id_dot;
        if (!cron_has_run($thongbao->connect, $key)) {
            $thongbao->thongbao__Add(
                "Nhắc nhở: Đợt chấm điểm đang diễn ra",
                "Đã qua nửa thời gian của đợt chấm điểm {$dot->ten_hoc_ky}. Các bạn sinh viên chưa nộp phiếu hãy nhanh chóng hoàn thành nhé.",
                1, 'Hệ thống', 0
            );

            // Gửi email nhắc nhở
            require_once __DIR__ . '/cron_email_giang_vien.php';
            cron_send_emails_sv($dot->id_dot, 'mid');

            cron_mark_run($thongbao->connect, $key);
        }
    }

    // 3. Notification: 1 day left
    if ($cron_date == $one_day_before_str && $cron_date != $start_str) {
        $key = '1day_' . $dot->id_dot;
        if (!cron_has_run($thongbao->connect, $key)) {
            $thongbao->thongbao__Add(
                "Chỉ còn 1 ngày! Sắp hết hạn chấm điểm",
                "Ngày mai (" . date('d/m/Y', $t_end) . ") là hạn chót nộp phiếu rèn luyện {$dot->ten_hoc_ky}. Bạn nào chưa nộp vui lòng hoàn thành ngay để không bị 0 điểm.",
                1, 'Hệ thống', 0
            );

            // Gửi email 1 ngày left
            require_once __DIR__ . '/cron_email_giang_vien.php';
            cron_send_emails_sv($dot->id_dot, '1day');

            cron_mark_run($thongbao->connect, $key);
        }
    }

    // 4. Notification: Ended + Teacher emails
    if ($today_time > $t_end) {
        $key = 'end_' . $dot->id_dot;
        if (!cron_has_run($thongbao->connect, $key)) {
            // Thông báo sinh viên
            $thongbao->thongbao__Add(
                "Đợt chấm điểm đã kết thúc",
                "Thời gian tự đánh giá rèn luyện {$dot->ten_hoc_ky} - {$dot->ten_nam_hoc} đã kết thúc. Xin vui lòng chờ Ban cán sự và Cố vấn học tập duyệt điểm.",
                1, 'Hệ thống', 0
            );

            // Gửi email cho giảng viên
            require_once __DIR__ . '/cron_email_giang_vien.php';
            cron_send_emails_for_dot($dot->id_dot);

            cron_mark_run($thongbao->connect, $key);
        }
    }
}
?>
