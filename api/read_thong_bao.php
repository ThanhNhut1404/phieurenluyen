<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);
require_once 'core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_json("error", "Invalid request method");
}

$id_sinh_vien = "";
$token = get_bearer_token();
if ($token) {
    $tai_khoan = $taikhoan->taikhoan__Get_By_Token($token);
    if ($tai_khoan != "0") {
        $id_sinh_vien = $tai_khoan->id_nguoi_dung;
    }
}

if (empty($id_sinh_vien)) {
    session_start();
    if(isset($_SESSION['sv'])) $id_sinh_vien = $_SESSION['sv']->id_nguoi_dung;
    else if(isset($_SESSION['lt'])) $id_sinh_vien = $_SESSION['lt']->id_nguoi_dung;
    else if(isset($_SESSION['bt'])) $id_sinh_vien = $_SESSION['bt']->id_nguoi_dung;
}

if (empty($id_sinh_vien)) {
    response_json("error", "Vui lòng đăng nhập (Missing Token or Session)", null, 401);
}

$sv = $sinhvien->sinhvien__Get_By_Id($id_sinh_vien);
if (!$sv) {
    response_json("error", "Không tìm thấy thông tin sinh viên");
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id_thong_bao'])) {
    response_json("error", "Thiếu ID thông báo");
}

$id_thong_bao = (int)$data['id_thong_bao'];
if ($id_thong_bao <= 0) {
    // Bỏ qua các thông báo động (id = 0)
    response_json("success", "Đã đánh dấu thông báo hệ thống");
}

try {
    $row = $thongbao->thongbao__Mark_As_Read($id_thong_bao, $id_sinh_vien);
    if ($row > 0) {
        response_json("success", "Đã đánh dấu đã đọc");
    } else {
        response_json("success", "Thông báo đã được đọc từ trước");
    }
} catch (PDOException $e) {
    response_json("success", "Chưa tạo bảng CSDL");
}
?>
