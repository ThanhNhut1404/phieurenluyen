<?php
require_once 'core.php';
require_once "../assets/vendor/PHPMailer/src/PHPMailer.php";
require_once "../assets/vendor/PHPMailer/src/Exception.php";
require_once "../assets/vendor/PHPMailer/src/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Đọc dữ liệu JSON từ request body
    $data = json_decode(file_get_contents("php://input"), true);
    $email = isset($data['email']) ? trim($data['email']) : (isset($_POST['email']) ? trim($_POST['email']) : '');

    if (empty($email)) {
        echo json_encode(["status" => "error", "message" => "Vui lòng cung cấp email."]);
        exit();
    }

    // Kiểm tra xem email có tồn tại không
    if (!$taikhoan->taikhoan__Exists_Email($email)) {
        echo json_encode(["status" => "error", "message" => "Email không tồn tại trong hệ thống."]);
        exit();
    }

    // Tạo mã OTP 6 chữ số
    $otp_code = sprintf("%06d", mt_rand(1, 999999));
    
    // Hạn sử dụng 3 phút
    $expires_at = date('Y-m-d H:i:s', strtotime('+3 minutes'));

    // Lưu vào DB
    $taikhoan->taikhoan__Set_OTP($email, $otp_code, $expires_at);

    // Gửi email
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'Lchsvhaugiang@tdu.edu.vn';
        $mail->Password   = 'lixovuuyoerwzdwv';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('Lchsvhaugiang@tdu.edu.vn', 'TDU - PRL');
        $mail->addAddress($email, $email);
        $mail->addCC('Lchsvhaugiang@tdu.edu.vn');

        $mail->isHTML(true);
        $mail->CharSet = PHPMailer::CHARSET_UTF8;

        $mail->Subject = 'Mã xác nhận khôi phục mật khẩu (TDU - PRL)';
        $mail->Body    = "
            <p>Chào bạn,</p>
            <p>Bạn đã yêu cầu khôi phục mật khẩu tài khoản TDU - PRL.</p>
            <p>Mã xác nhận (OTP) của bạn là: <b><span style='font-size: 20px; color: blue;'>$otp_code</span></b></p>
            <p>Mã này có hiệu lực trong vòng 3 phút.</p>
            <p>Nếu bạn không yêu cầu, vui lòng bỏ qua email này.</p>
        ";

        $mail->send();
        
        echo json_encode(["status" => "success", "message" => "Đã gửi mã OTP vào email."]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Không thể gửi email OTP. Lỗi: {$mail->ErrorInfo}"]);
    }
}
?>
