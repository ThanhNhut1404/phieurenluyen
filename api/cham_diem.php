<?php
require_once 'core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_json("error", "Invalid request method");
}

// Lấy token từ header
$token = get_bearer_token();
if (!$token) {
    response_json("error", "Vui lòng đăng nhập (Missing Token)", null, 401);
}

// Kiểm tra token có hợp lệ không
$tai_khoan = $taikhoan->taikhoan__Get_By_Token($token);
if ($tai_khoan == "0") {
    response_json("error", "Token không hợp lệ hoặc đã hết hạn", null, 401);
}

// Lấy dữ liệu chấm điểm từ App
$data = json_decode(file_get_contents("php://input"), true);
$id_phieu = $data['id_phieu'] ?? '';
$kq_sv = $data['kq_sv'] ?? []; // Mảng chứa điểm của từng mục
$minh_chung = $data['minh_chung'] ?? []; // Mảng chứa minh chứng base64

if (empty($id_phieu) || empty($kq_sv) || !is_array($kq_sv)) {
    response_json("error", "Dữ liệu chấm điểm không hợp lệ");
}

// Kiểm tra quyền sở hữu phiếu
$phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
if (!$phieu || $phieu->id_sinh_vien != $tai_khoan->id_nguoi_dung) {
    response_json("error", "Bạn không có quyền chấm phiếu này");
}

// Chuyển mảng thành chuỗi lưu database: "id_muc-diem|id_muc-diem" (Ví dụ: "1-5|2-10|3-2")
$kq_string = implode("|", $kq_sv);

// Lưu vào cơ sở dữ liệu
$status = $phieuchamdiem->phieuchamdiem__Update_Kq_Sv($id_phieu, $kq_string);

// Xử lý lưu minh chứng
if (is_array($minh_chung) && count($minh_chung) > 0) {
    // Xóa các minh chứng cũ của phiếu này để cập nhật mới (vì app sẽ gửi full danh sách)
    $danh_sach_mc_cu = $minhchung->minhchung__Get_By_Id_Phieu($id_phieu);
    if (is_array($danh_sach_mc_cu)) {
        foreach ($danh_sach_mc_cu as $mc) {
            $minhchung->minhchung__Delete($mc->id_minh_chung);
        }
    }

    foreach ($minh_chung as $mc_item) {
        if (!empty($mc_item['id_muc']) && !empty($mc_item['base64'])) {
            // Lưu trực tiếp chuỗi base64 vào DB
            $minhchung->minhchung__Add($id_phieu, $mc_item['base64'], date("Y-m-d H:i:s"), $mc_item['id_muc']);
        }
    }
}

if ($status) {
    response_json("success", "Lưu điểm rèn luyện thành công!");
} else {
    response_json("error", "Lỗi khi lưu điểm rèn luyện");
}
