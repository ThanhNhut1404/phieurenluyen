<?php

require "../../models/getModel.php";
$href = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
if (strlen(strpos($href, '&status')) > 0) {
    $href = explode('&status', $href)[0];
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function locDau($str)
{
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
    $str = trim(strtolower(str_replace(" ", "", $str)));

    return $str;
}

// quân sửa: Hàm lấy các chữ cái đầu tiên (Viết tắt)
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

if (isset($_GET["req"])) {
    switch ($_GET["req"]) {

        case "add_admin":
            $status  = 0;

            $id_phan_quyen = isset($_POST["id_phan_quyen"]) ? $_POST["id_phan_quyen"] : '';
            $id_phan_nhom = isset($_POST["id_phan_nhom"]) ? $_POST["id_phan_nhom"] : '';
            $id_nguoi_dung = 0;
            $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
            $mat_khau = isset($_POST["mat_khau"]) ? $_POST["mat_khau"] : '';
            $ghi_chu = date("Y-m-d H:i:s");
            
            $_SESSION['taikhoan_old_input'] = array_merge($_POST, ['context' => 'add']);

            // Check if email already exists in taikhoan table
            if ($taikhoan->taikhoan__Exists_Email($email)) {
                header("location: $href&status=duplicate-email-sinh-vien"); // Using duplicate-email alert mapping
                exit();
            }

            $status = $taikhoan->taikhoan__Add($email, $hashpassword->Encryption($mat_khau), $ghi_chu, $id_phan_quyen, $id_phan_nhom, $id_nguoi_dung);
            if ($status != 0) {
                unset($_SESSION['taikhoan_old_input']);
                header("location: $href&status=success");
            } else {
                header("location: $href&status=failed");
            }
            exit();

        case "add_sv":
            $status  = 0;

            $id_phan_quyen = isset($_POST["id_phan_quyen"]) ? $_POST["id_phan_quyen"] : '';
            $id_phan_nhom = isset($_POST["id_phan_nhom"]) ? $_POST["id_phan_nhom"] : '';
            $id_nguoi_dung = isset($_POST["id_nguoi_dung"]) ? $_POST["id_nguoi_dung"] : array();

            foreach ($id_nguoi_dung as $item) {
                $sinhvien__Get_By_Id = $sinhvien->sinhvien__Get_By_Id($item);
                if ($sinhvien__Get_By_Id) {
                    $email = $sinhvien__Get_By_Id->email;
                    $ten_lop = '';
                    $lh = $lophoc->lophoc__Get_By_Id($sinhvien__Get_By_Id->id_lop_hoc);
                    if ($lh) {
                        $ten_lop = $lh->ten_lop_hoc;
                    }
                    // quân sửa: Đổi mật khẩu mặc định thành: TênViếtTắt_TênLớp#1234
                    $mat_khau = vietTatChuCaiDau($sinhvien__Get_By_Id->ten_sinh_vien) . "_" . $ten_lop . "#1234";
                    $ghi_chu = date("Y-m-d H:i:s");

                    // Check duplicate email to avoid DB UNIQUE error
                    if (!$taikhoan->taikhoan__Exists_Email($email)) {
                        $status += $taikhoan->taikhoan__Add($email, $hashpassword->Encryption($mat_khau), $ghi_chu, $id_phan_quyen, $id_phan_nhom, $item);
                    }
                }
            }

            if ($status != 0) {
                unset($_SESSION['taikhoan_old_input']);
                header("location: $href&status=success");
            } else {
                header("location: $href&status=failed");
            }
            exit();

        case "add_btdk":
            $status  = 0;

            $id_phan_quyen = isset($_POST["id_phan_quyen"]) ? $_POST["id_phan_quyen"] : '';
            $id_phan_nhom = isset($_POST["id_phan_nhom"]) ? $_POST["id_phan_nhom"] : '';
            $id_nguoi_dung = isset($_POST["id_nguoi_dung"]) ? $_POST["id_nguoi_dung"] : array();

            foreach ($id_nguoi_dung as $item) {
                $bithudoankhoa__Get_By_Id = $bithudoankhoa->bithudoankhoa__Get_By_Id($item);
                if ($bithudoankhoa__Get_By_Id) {
                    $email = $bithudoankhoa__Get_By_Id->email;
                    $mat_khau = locDau($bithudoankhoa__Get_By_Id->ten_bi_thu) . date("@is");
                    $ghi_chu = date("Y-m-d H:i:s");

                    if (!$taikhoan->taikhoan__Exists_Email($email)) {
                        $status += $taikhoan->taikhoan__Add($email, $hashpassword->Encryption($mat_khau), $ghi_chu, $id_phan_quyen, $id_phan_nhom, $item);
                    }
                }
            }

            if ($status != 0) {
                unset($_SESSION['taikhoan_old_input']);
                header("location: $href&status=success");
            } else {
                header("location: $href&status=failed");
            }
            exit();

        // quân sửa: Thêm luồng tạo tài khoản riêng cho Giảng viên (Cố vấn học tập)
        case "add_gv":
            $status  = 0;

            $id_phan_quyen = isset($_POST["id_phan_quyen"]) ? $_POST["id_phan_quyen"] : '';
            $id_phan_nhom = isset($_POST["id_phan_nhom"]) ? $_POST["id_phan_nhom"] : '';
            $id_nguoi_dung = isset($_POST["id_nguoi_dung"]) ? $_POST["id_nguoi_dung"] : array();
            $id_lop_hoc = isset($_POST["id_lop_hoc"]) ? $_POST["id_lop_hoc"] : '';
            $ten_lop = '';
            $lh = $lophoc->lophoc__Get_By_Id($id_lop_hoc);
            if ($lh) {
                $ten_lop = $lh->ten_lop_hoc;
            }

            foreach ($id_nguoi_dung as $item) {
                $giangvien__Get_By_Id = $giangvien->giangvien__Get_By_Id($item);
                if ($giangvien__Get_By_Id) {
                    $email = $giangvien__Get_By_Id->email;
                    $mat_khau = vietTatChuCaiDau($giangvien__Get_By_Id->ten_giang_vien) . "_" . $ten_lop . "#1234";
                    $ghi_chu = date("Y-m-d H:i:s");

                    if (!$taikhoan->taikhoan__Exists_Email($email)) {
                        $status += $taikhoan->taikhoan__Add($email, $hashpassword->Encryption($mat_khau), $ghi_chu, $id_phan_quyen, $id_phan_nhom, $item);
                    }
                }
            }

            if ($status != 0) {
                unset($_SESSION['taikhoan_old_input']);
                header("location: $href&status=success");
            } else {
                header("location: $href&status=failed");
            }
            exit();

        case "delete":
            $status = 0;
            $id_tai_khoan = isset($_GET["id_tai_khoan"]) ? $_GET["id_tai_khoan"] : '';
            $status = $taikhoan->taikhoan__Delete($id_tai_khoan);

            if ($status != 0) {
                header("location: $href&status=success");
            } else {
                header("location: $href&status=failed");
            }
            exit();

        case "reset":
            $status = 0;
            $id_tai_khoan = isset($_GET["id_tai_khoan"]) ? $_GET["id_tai_khoan"] : '';
            $mat_khau = '123456';
            $status = $taikhoan->taikhoan__Reset($id_tai_khoan, $hashpassword->Encryption($mat_khau));

            if ($status != 0) {
                header("location: $href&status=success");
            } else {
                header("location: $href&status=failed");
            }
            exit();

        case "active":
            $status = 0;
            $id_tai_khoan = isset($_GET["id_tai_khoan"]) ? $_GET["id_tai_khoan"] : '';
            $trang_thai = (isset($_GET["trang_thai"]) && $_GET["trang_thai"] == 1) ? 0 : 1;
            $status = $taikhoan->taikhoan__Active($id_tai_khoan, $trang_thai);

            if ($status != 0) {
                header("location: $href&status=success");
            } else {
                header("location: $href&status=failed");
            }
            exit();
    }
}
?>
