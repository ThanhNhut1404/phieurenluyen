<?php
require "../../models/getModel.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
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
            $word = trim($w);
            // Nếu từ có chứa số (như 17A, 1), ta giữ nguyên cả từ đó
            if (preg_match('/[0-9]/', $word)) {
                $acronym .= $word;
            } else {
                $acronym .= mb_substr($word, 0, 1, "UTF-8");
            }
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
    $gv = null;
    $bt = null;
    $id_nguoi_dung = null;
    $is_sinhvien = false;

    if ($sv) {
        $is_sinhvien = true;
        $id_nguoi_dung = $sv->id_sinh_vien;
    } else {
        $gv = $giangvien->giangvien__Get_By_Email($email);
        if ($gv) {
            $id_nguoi_dung = $gv->id_giang_vien;
        } else {
            $bt = $bithudoankhoa->bithudoankhoa__Get_By_Email($email);
            if ($bt) {
                $id_nguoi_dung = $bt->id_bi_thu;
            }
        }
    }

    if (!$id_nguoi_dung) {
        echo json_encode(["status" => "error", "message" => "Email không thuộc về thành viên nào trong hệ thống."]);
        exit();
    }

    $id_phan_quyen = 0;
    $id_phan_nhom = 0;
    $all_pn = $phannhom->phannhom__Get_All();

    if ($is_sinhvien) {
        $id_phan_quyen = $phanquyen->phanquyen__Get_By_Cap_Bac(2)->id_phan_quyen;
        $id_phan_nhom = 3;
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
        $mat_khau_goc = vietTatChuCaiDau($sv->ten_sinh_vien) . "_" . vietTatChuCaiDau($ten_lop) . "#1234";
    } else {
        // Cố vấn học tập (giảng viên) hoặc Bí thư đoàn khoa
        $id_phan_quyen = $phanquyen->phanquyen__Get_By_Cap_Bac(2)->id_phan_quyen; 
        
        if ($gv) {
            $id_phan_nhom = 5;
            foreach($all_pn as $pn) {
                if ($pn->cap_bac == 4) { // Cố vấn
                    $id_phan_nhom = $pn->id_phan_nhom;
                    break;
                }
            }
        } else if ($bt) {
            $id_phan_nhom = 4;
            foreach($all_pn as $pn) {
                if ($pn->cap_bac == 3) { // Bí thư
                    $id_phan_nhom = $pn->id_phan_nhom;
                    break;
                }
            }
        }
        $mat_khau_goc = "#TDU123";
    }

    $ghi_chu = date("Y-m-d H:i:s");
    $mat_khau_ma_hoa = password_hash($mat_khau_goc, PASSWORD_BCRYPT);

    $tk_exist = [];
    if ($is_sinhvien) {
        $tk_exist = $taikhoan->taikhoan__Get_By_Sinh_Vien($id_nguoi_dung);
    } else if ($gv) {
        $tk_exist = $taikhoan->taikhoan__Get_By_Giang_Vien($id_nguoi_dung);
    } else if ($bt) {
        $tk_exist = $taikhoan->taikhoan__Get_By_Bi_Thu($id_nguoi_dung);
    }
    
    if (count($tk_exist) == 0) {
        $status = $taikhoan->taikhoan__Add($email, $mat_khau_ma_hoa, $ghi_chu, $id_phan_quyen, $id_phan_nhom, $id_nguoi_dung);
        if ($status == 0) {
            echo json_encode(["status" => "error", "message" => "Lỗi khi tạo tài khoản."]);
            exit();
        }
    } else {
        $id_tai_khoan = $tk_exist[0]->id_tai_khoan;
        $taikhoan->taikhoan__Update($id_tai_khoan, $email, $mat_khau_ma_hoa, $ghi_chu, $id_phan_quyen, $id_phan_nhom, $id_nguoi_dung);
    }

    // Đánh dấu yêu cầu đã xử lý
    $yeucaukichhoat->yeucaukichhoat__Update_Status($id_yeu_cau, 1);

    // Trả về password mã hoá AES để Frontend ném qua mail.php (mail.php sẽ Decryption lại)
    echo json_encode([
        "status" => "success", 
        "password" => $hashpassword->Encryption($mat_khau_goc)
    ]);
    exit();
}
?>
