<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require "core.php";

$data = json_decode(file_get_contents("php://input"));

if (isset($data->email)) {
    $email = trim($data->email);
    
    // Kiểm tra email có trong bảng sinhvien hay không
    $sv = $sinhvien->sinhvien__Get_By_Email($email);
    
    if($sv) {
        // Kiểm tra xem đã có tài khoản chưa
        if ($taikhoan->taikhoan__Exists_Email($email)) {
            echo json_encode([
                "status" => "error",
                "message" => "Tài khoản của email này đã tồn tại."
            ]);
            exit();
        }
        
        $res = $yeucaukichhoat->yeucaukichhoat__Add($email);
        if($res > 0) {
            echo json_encode([
                "status" => "success",
                "message" => "Yêu cầu kích hoạt đã được ghi nhận."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Đã có lỗi xảy ra hoặc yêu cầu đang chờ xử lý."
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Email không tồn tại trong hệ thống."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Vui lòng cung cấp email."
    ]);
}
?>
