<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
require_once "core.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
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
$sv = $sinhvien->sinhvien__Get_By_Id($id_sinh_vien);

if (!$sv) {
    response_json("error", "Không tìm thấy thông tin sinh viên", null, 404);
}

$id_lop_hoc = $sv->id_lop_hoc;

try {
    $ket_qua_lop = $ketquaxeploai->ketquaxeploai__Get_By_Id_Lop_Hoc_With_HocKy_NamHoc($id_lop_hoc);
    
    // Format the response
    $data = [];
    foreach ($ket_qua_lop as $item) {
        $data[] = [
            "id_dot" => $item->id_dot,
            "ten_dot" => $item->ten_dot,
            "ten_hoc_ky" => $item->ten_hoc_ky,
            "ten_nam_hoc" => $item->ten_nam_hoc,
            "ma_sinh_vien" => $item->ma_sinh_vien,
            "ten_sinh_vien" => $item->ten_sinh_vien,
            "ket_qua" => floatval($item->ket_qua),
            "xep_loai" => $item->xep_loai
        ];
    }
    
    response_json("success", "Lấy điểm lớp thành công", $data);
} catch (Exception $e) {
    response_json("error", "Lỗi server: " . $e->getMessage());
}
?>
