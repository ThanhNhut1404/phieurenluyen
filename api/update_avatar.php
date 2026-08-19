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

$id_sinh_vien = $tai_khoan->id_nguoi_dung;

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    response_json("error", "Không tìm thấy file ảnh hoặc quá trình tải lên thất bại");
}

$file = $_FILES['avatar'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
if (!in_array($ext, $allowed_ext)) {
    response_json("error", "Chỉ cho phép định dạng ảnh JPG, PNG, GIF, WEBP (Sai định dạng: $ext)");
}

$upload_dir = '../uploads/avatars/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = "avatar_" . $id_sinh_vien . "_" . time() . "." . $ext;
$target_path = $upload_dir . $filename;

if (move_uploaded_file($file['tmp_name'], $target_path)) {
    $db_path = "uploads/avatars/" . $filename;
    $sinhvien->sinhvien__Update_Avatar($id_sinh_vien, $db_path);
    
    response_json("success", "Cập nhật ảnh đại diện thành công", ['anh_dai_dien' => $db_path]);
} else {
    response_json("error", "Không thể lưu file ảnh");
}
?>
