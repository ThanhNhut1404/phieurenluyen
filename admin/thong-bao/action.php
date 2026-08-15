<?php
require "../../models/getModel.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function vietTatChuCaiDau($str) {
    $unicode = array(
        "a" => "á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ",
        "d" => "đ",
        "e" => "é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ",
        "i" => "í|ì|ỉ|ĩ|ị",
        "o" => "ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ",
        "u" => "ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự",
        "y" => "ý|ỳ|ỷ|ỹ|ỵ",
        "A" => "Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ",
        "D" => "Đ",
        "E" => "É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ",
        "I" => "Í|Ì|Ỉ|Ĩ|Ị",
        "O" => "Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ",
        "U" => "Ú|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự",
        "Y" => "Ý|Ỳ|Ỷ|Ỹ|Ỵ",
    );
    foreach ($unicode as $nonUnicode => $uni) {
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }
    $words = explode(" ", trim($str));
    $acronym = "";
    foreach ($words as $w) {
        if (trim($w) != "") {
            $acronym .= mb_substr(trim($w), 0, 1, "UTF-8");
        }
    }
    return strtoupper($acronym);
}

if (isset($_GET["req"]) && $_GET["req"] == 'tao_tai_khoan') {
    $id_yeu_cau = isset($_POST['id_yeu_cau']) ? $_POST['id_yeu_cau'] : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (!$email || !$id_yeu_cau) {
        echo json_encode(["status" => "error", "message" => "Thiếu thông tin."]);
        exit();
    }

    $sv = $sinhvien->sinhvien__Get_By_Email($email);
    if (!$sv) {
        echo json_encode(["status" => "error", "message" => "Email không thuộc về sinh viên nào trong hệ thống."]);
        exit();
    }

    // Các ID mặc định cho sinh viên
    $id_phan_quyen = $phanquyen->phanquyen__Get_By_Cap_Bac(2)->id_phan_quyen;
    $id_phan_nhom = 3; // Default fallback
    $all_pn = $phannhom->phannhom__Get_All();
    foreach($all_pn as $pn) {
        if ($pn->cap_bac == 2) {
            $id_phan_nhom = $pn->id_phan_nhom;
            break;
        }
    }

    $ten_lop = '';
    $lh = $lophoc->lophoc__Get_By_Id($sv->id_lop_hoc);
    if ($lh) {
        $ten_lop = $lh->ten_lop_hoc;
    }

    $mat_khau_goc = vietTatChuCaiDau($sv->ten_sinh_vien) . "_" . $ten_lop . "#1234";
    $ghi_chu = date("Y-m-d H:i:s");

    $mat_khau_ma_hoa = $hashpassword->Encryption($mat_khau_goc);

    // Kiểm tra xem tài khoản đã tồn tại chưa
    if (!$taikhoan->taikhoan__Exists_Email($email)) {
        $status = $taikhoan->taikhoan__Add($email, $mat_khau_ma_hoa, $ghi_chu, $id_phan_quyen, $id_phan_nhom, $sv->id_sinh_vien);
        if ($status == 0) {
            echo json_encode(["status" => "error", "message" => "Lỗi khi tạo tài khoản."]);
            exit();
        }
    } else {
        // Tài khoản đã tồn tại, ta chỉ reset lại mật khẩu để gửi mail
        $tk_exist = $taikhoan->taikhoan__Get_By_Email($email);
        $taikhoan->taikhoan__Reset($tk_exist->id_tai_khoan, $mat_khau_ma_hoa);
    }

    // Đánh dấu yêu cầu đã xử lý
    $yeucaukichhoat->yeucaukichhoat__Update_Status($id_yeu_cau, 1);

    // Trả về password mã hoá để Frontend ném qua mail.php
    echo json_encode([
        "status" => "success", 
        "password" => $mat_khau_ma_hoa
    ]);
    exit();
}
?>
