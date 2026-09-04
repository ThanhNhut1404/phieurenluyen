<?php
// Gửi email cho giảng viên danh sách sinh viên chưa chấm
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/../assets/vendor/PHPMailer/src/PHPMailer.php";
require_once __DIR__ . "/../assets/vendor/PHPMailer/src/Exception.php";
require_once __DIR__ . "/../assets/vendor/PHPMailer/src/SMTP.php";

function cron_send_emails_for_dot($id_dot) {
    global $lopapdung, $sinhvien, $phieuchamdiem, $phancong, $giangvien, $lophoc, $dotchamdiem;

    $dot = $dotchamdiem->dotchamdiem__Get_By_Id($id_dot);
    if (!$dot) return;

    $ds_lop_ap_dung = $lopapdung->lopapdung__Get_By_Id_Dot($id_dot);
    
    foreach ($ds_lop_ap_dung as $lap) {
        $id_lop_hoc = $lap->id_lop_hoc;
        $lh = $lophoc->lophoc__Get_By_Id($id_lop_hoc);
        if (!$lh) continue;

        // 1. Tìm các sinh viên chưa chấm điểm
        $sv_chua_cham = [];
        $ds_sinh_vien = $sinhvien->sinhvien__Get_By_Id_Lop_Hoc($id_lop_hoc);
        
        foreach ($ds_sinh_vien as $sv) {
            $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id_SV_Lop_Ap_Dung($sv->id_sinh_vien, $lap->id_lop_ap_dung);
            if (!$phieu || empty($phieu->kq_sv)) {
                $sv_chua_cham[] = $sv;
            }
        }

        // Nếu có sinh viên chưa chấm
        if (count($sv_chua_cham) > 0) {
            // 2. Tìm giảng viên phụ trách
            // Get all phancong for this lop
            $ds_phancong = $phancong->phancong__Get_By_Id_Lop_Hoc($id_lop_hoc);
            if (count($ds_phancong) > 0) {
                // Thường 1 lớp có 1 cố vấn
                $id_gv = $ds_phancong[0]->id_giang_vien;
                $gv = $giangvien->giangvien__Get_By_Id($id_gv);
                if ($gv && !empty($gv->email)) {
                    cron_send_mail_gv($gv, $lh, $dot, $sv_chua_cham);
                }
            }
        }
    }
}

function cron_send_mail_gv($gv, $lh, $dot, $sv_chua_cham) {
    $mail = new PHPMailer(true);
    try {
        global $thongbao;
        if (isset($thongbao)) {
            $so_luong = count($sv_chua_cham);
            $thongbao->thongbao__Add(
                "Có $so_luong sinh viên chưa nộp phiếu rèn luyện",
                "Đợt chấm điểm {$dot->ten_hoc_ky} đã kết thúc. Lớp {$lh->ten_lop_hoc} hiện có $so_luong sinh viên chưa tự đánh giá. Kính nhờ Thầy/Cô kiểm tra email hoặc vào hệ thống để nhắc nhở và chấm điểm thay.",
                1, 'Hệ thống', -1 * $gv->id_giang_vien
            );
        }
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'Lchsvhaugiang@tdu.edu.vn';                     
        $mail->Password   = 'xwqsfhydjgmjtiod';                               
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            
        $mail->Port       = 587;                                    

        $mail->setFrom('Lchsvhaugiang@tdu.edu.vn', 'TDU - Hệ thống Đánh giá Rèn luyện');
        $mail->addAddress($gv->email, $gv->ten_giang_vien);     

        $mail->isHTML(true);
        $mail->CharSet = PHPMailer::CHARSET_UTF8;

        $mail->Subject = "Danh sách Sinh viên chưa nộp phiếu rèn luyện - Lớp {$lh->ten_lop_hoc} ({$dot->ten_hoc_ky})";
        
        $body = "
        <p>Kính gửi Thầy/Cô <b>{$gv->ten_giang_vien}</b>,</p>
        <p>Thời gian tự đánh giá điểm rèn luyện <b>{$dot->ten_hoc_ky} - {$dot->ten_nam_hoc}</b> dành cho sinh viên lớp <b>{$lh->ten_lop_hoc}</b> đã kết thúc.</p>
        <p>Dưới đây là danh sách các sinh viên chưa nộp phiếu tự đánh giá. Kính nhờ Thầy/Cô nhắc nhở hoặc tiến hành chấm điểm thay mặt cho các em theo quy chế.</p>
        
        <table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%; max-width: 600px;'>
            <thead>
                <tr style='background-color: #f2f2f2;'>
                    <th>STT</th>
                    <th>Mã sinh viên</th>
                    <th>Tên sinh viên</th>
                </tr>
            </thead>
            <tbody>
        ";

        $stt = 1;
        foreach ($sv_chua_cham as $sv) {
            $body .= "
                <tr>
                    <td style='text-align: center;'>{$stt}</td>
                    <td style='text-align: center;'>{$sv->ma_sinh_vien}</td>
                    <td>{$sv->ten_sinh_vien}</td>
                </tr>
            ";
            $stt++;
        }

        $body .= "
            </tbody>
        </table>
        <p>Vui lòng đăng nhập vào Hệ thống Quản lý để xem chi tiết và duyệt điểm.</p>
        <p>Trân trọng,<br>Phòng Công tác Sinh viên</p>
        ";

        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        // Log lỗi hoặc bỏ qua
    }
}

function cron_send_emails_sv($id_dot, $type) {
    global $lopapdung, $sinhvien, $phieuchamdiem, $lophoc, $dotchamdiem, $taikhoan;

    $dot = $dotchamdiem->dotchamdiem__Get_By_Id($id_dot);
    if (!$dot) return;

    // Giới hạn max 400 email để test (phòng hờ Gmail sập)
    $email_limit = 400;
    $email_count = 0;

    $ds_lop_ap_dung = $lopapdung->lopapdung__Get_By_Id_Dot($id_dot);
    
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'Lchsvhaugiang@tdu.edu.vn';                     
        $mail->Password   = 'xwqsfhydjgmjtiod';                               
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            
        $mail->Port       = 587;                                    
        $mail->setFrom('Lchsvhaugiang@tdu.edu.vn', 'TDU - Hệ thống Đánh giá Rèn luyện');
        $mail->isHTML(true);
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
    } catch(Exception $e) {
        return;
    }

    foreach ($ds_lop_ap_dung as $lap) {
        if ($email_count >= $email_limit) break;

        $id_lop_hoc = $lap->id_lop_hoc;
        $ds_sinh_vien = $sinhvien->sinhvien__Get_By_Id_Lop_Hoc($id_lop_hoc);
        
        foreach ($ds_sinh_vien as $sv) {
            if ($email_count >= $email_limit) break;

            // Nếu là nhắc nhở hoặc 1 ngày, chỉ gửi cho người CHƯA nộp
            if ($type == 'mid' || $type == '1day') {
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id_SV_Lop_Ap_Dung($sv->id_sinh_vien, $lap->id_lop_ap_dung);
                if ($phieu && !empty($phieu->kq_sv)) {
                    continue; // Đã nộp, bỏ qua
                }
            }

            // Lấy email từ bảng taikhoan
            $tk = $taikhoan->taikhoan__Get_By_Id_Nguoi_Dung($sv->id_sinh_vien);
            // Nếu không có tk, lấy theo format mặc định (hoặc nếu sv có lưu email)
            $email = $tk ? $tk->email : null;
            
            if (empty($email)) continue;

            try {
                $mail->clearAllRecipients();
                $mail->addAddress($email, $sv->ten_sinh_vien);

                if ($type == 'start') {
                    $mail->Subject = "Thông báo: Mở đợt tự đánh giá Điểm rèn luyện {$dot->ten_hoc_ky}";
                    $mail->Body = "
                        <p>Chào <b>{$sv->ten_sinh_vien}</b>,</p>
                        <p>Hệ thống vừa mở đợt tự đánh giá Điểm rèn luyện cho <b>{$dot->ten_hoc_ky} - {$dot->ten_nam_hoc}</b>.</p>
                        <p>Thời hạn nộp phiếu: <b>" . date('d/m/Y', strtotime($dot->thoi_gian_ket_thuc)) . "</b></p>
                        <p>Vui lòng đăng nhập vào ứng dụng TDU - PRL để hoàn thành phiếu đánh giá đúng hạn.</p>
                    ";
                } elseif ($type == 'mid') {
                    $mail->Subject = "Nhắc nhở: Bạn chưa nộp phiếu rèn luyện {$dot->ten_hoc_ky}";
                    $mail->Body = "
                        <p>Chào <b>{$sv->ten_sinh_vien}</b>,</p>
                        <p>Đợt chấm điểm <b>{$dot->ten_hoc_ky}</b> đã trôi qua một nửa thời gian, nhưng hệ thống vẫn chưa ghi nhận phiếu tự đánh giá của bạn.</p>
                        <p>Vui lòng đăng nhập vào ứng dụng TDU - PRL và hoàn thành sớm nhất có thể.</p>
                    ";
                } elseif ($type == '1day') {
                    $mail->Subject = "KHẨN: Hạn chót nộp phiếu rèn luyện vào ngày mai!";
                    $mail->Body = "
                        <p>Chào <b>{$sv->ten_sinh_vien}</b>,</p>
                        <p>Chỉ còn <b>1 ngày duy nhất</b> để bạn tự đánh giá điểm rèn luyện <b>{$dot->ten_hoc_ky}</b>.</p>
                        <p>Hệ thống sẽ đóng vào ngày mai (" . date('d/m/Y', strtotime($dot->thoi_gian_ket_thuc)) . "). Tránh tình trạng bị 0 điểm, bạn vui lòng nộp phiếu NGAY BÂY GIỜ trên ứng dụng TDU - PRL.</p>
                    ";
                }

                $mail->send();
                $email_count++;
            } catch (Exception $e) {
                // Tiếp tục gửi người khác
            }
        }
    }
}
?>
