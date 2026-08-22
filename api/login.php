<?php
require_once 'core.php';

// Chỉ cho phép POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_json("error", "Invalid request method");
}

// Đọc dữ liệu JSON từ request body
$data = json_decode(file_get_contents("php://input"), true);

// Nếu không truyền dạng JSON, thử lấy từ $_POST (phòng hờ)
$email = $data['email'] ?? $_POST['email'] ?? '';
$mat_khau = $data['password'] ?? $_POST['password'] ?? '';

if (empty($email) || empty($mat_khau)) {
    response_json("error", "Vui lòng nhập email và mật khẩu");
}

// Kiểm tra đăng nhập
$status = $taikhoan->taikhoan__Check_Login($email, $mat_khau);

if ($status != "0") {
    // Đăng nhập thành công, tạo token ngẫu nhiên
    $token = bin2hex(random_bytes(32)); // 64 ký tự ngẫu nhiên
    
    // Cập nhật token vào database
    $taikhoan->taikhoan__Update_Token($status->id_tai_khoan, $token);

    // Lấy thêm thông tin sinh viên (nếu là sinh viên)
    $thong_tin = [
        "id_tai_khoan" => $status->id_tai_khoan,
        "email" => $status->email,
        "id_phan_quyen" => $status->id_phan_quyen,
        "id_phan_nhom" => $status->id_phan_nhom
    ];

    if ($phannhom->phannhom__Get_By_Id($status->id_phan_nhom)->cap_bac == 2 || $phannhom->phannhom__Get_By_Id($status->id_phan_nhom)->cap_bac == 3) {
        $sv = $sinhvien->sinhvien__Get_By_Id($status->id_nguoi_dung);
        if ($sv) {
            $thong_tin['ten_sinh_vien'] = $sv->ten_sinh_vien;
            $thong_tin['ma_sinh_vien'] = $sv->ma_sinh_vien;
            $thong_tin['id_lop_hoc'] = $sv->id_lop_hoc;
        }
    }

    response_json("success", "Đăng nhập thành công", [
        "token" => $token,
        "user" => $thong_tin
    ]);
} else {
    response_json("error", "Email hoặc mật khẩu không chính xác");
}
