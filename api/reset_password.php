<?php
require_once 'core.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $email = isset($data['email']) ? trim($data['email']) : (isset($_POST['email']) ? trim($_POST['email']) : '');
    $otp_code = isset($data['otp_code']) ? trim($data['otp_code']) : (isset($_POST['otp_code']) ? trim($_POST['otp_code']) : '');
    $new_password = isset($data['new_password']) ? trim($data['new_password']) : (isset($_POST['new_password']) ? trim($_POST['new_password']) : '');

    if (empty($email) || empty($otp_code)) {
        echo json_encode(["status" => "error", "message" => "Thiếu thông tin yêu cầu."]);
        exit();
    }

    // Verify OTP first
    if (!$taikhoan->taikhoan__Verify_OTP($email, $otp_code)) {
        echo json_encode(["status" => "error", "message" => "Mã OTP không chính xác hoặc đã hết hạn."]);
        exit();
    }

    // Nếu new_password rỗng, thì frontend chỉ đang muốn verify mã OTP.
    if (empty($new_password)) {
        echo json_encode(["status" => "success", "message" => "Mã OTP hợp lệ."]);
        exit();
    }

    // Nếu có new_password, kiểm tra độ phức tạp trước khi đổi
    if (strlen($new_password) < 9 || !preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password) || !preg_match('/[^A-Za-z0-9]/', $new_password)) {
        echo json_encode(["status" => "error", "message" => "Mật khẩu phải từ 9 ký tự, gồm chữ, số và ký tự đặc biệt."]);
        exit();
    }

    $mat_khau_ma_hoa = $hashpassword->Encryption($new_password);
    
    $status = $taikhoan->taikhoan__Reset_Password_By_Email($email, $mat_khau_ma_hoa);
    
    if ($status) {
        echo json_encode(["status" => "success", "message" => "Mật khẩu đã được thay đổi thành công."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi hệ thống khi cập nhật mật khẩu."]);
    }
}
?>
