<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);
require_once 'core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

// Đọc JSON body
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['old_password']) || !isset($data['new_password'])) {
    response_json("error", "Dữ liệu không hợp lệ. Yêu cầu old_password và new_password.");
}

$old_password = $data['old_password'];
$new_password = $data['new_password'];

// Kiểm tra mật khẩu cũ (cần mã hóa trước khi so sánh)
$old_password_hash = $hashpassword->Encryption($old_password);
if ($tai_khoan->mat_khau !== $old_password_hash) {
    response_json("error", "Mật khẩu cũ không chính xác.");
}

// Mã hóa mật khẩu mới trước khi lưu
$new_password_hash = $hashpassword->Encryption($new_password);

// LOG FOR DEBUG
$log_msg = "User ID: " . $tai_khoan->id_tai_khoan . "\n";
$log_msg .= "Old Pass: " . $old_password . " | Hash: " . $old_password_hash . "\n";
$log_msg .= "New Pass: " . $new_password . " | Hash: " . $new_password_hash . "\n";
file_put_contents('debug_password.txt', $log_msg, FILE_APPEND);

// Cập nhật mật khẩu mới
$status = $taikhoan->taikhoan__Reset($tai_khoan->id_tai_khoan, $new_password_hash);

file_put_contents('debug_password.txt', "Reset Status: " . ($status ? 'true' : 'false') . "\n", FILE_APPEND);

if ($status) {
    response_json("success", "Đổi mật khẩu thành công.");
} else {
    response_json("error", "Có lỗi xảy ra khi đổi mật khẩu.");
}
?>
