<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function

require "../../assets/vendor/PHPMailer/src/PHPMailer.php";
require "../../assets/vendor/PHPMailer/src/Exception.php";
require "../../assets/vendor/PHPMailer/src/SMTP.php";
require "../../models/getModel.php";


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

$href = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
if (strlen(strpos($href, '&status')) > 0) {
    $href = explode('&status', $href)[0];
}

function vietTatChuCaiDau($str) {
    $unicode = array(
        "a" => "á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ", "d" => "đ", "e" => "é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ",
        "i" => "í|ì|ỉ|ĩ|ị", "o" => "ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ",
        "u" => "ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự", "y" => "ý|ỳ|ỷ|ỹ|ỵ",
        "A" => "Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ", "D" => "Đ", "E" => "É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ",
        "I" => "Í|Ì|Ỉ|Ĩ|Ị", "O" => "Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ",
        "U" => "Ú|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự", "Y" => "Ý|Ỳ|Ỷ|Ỹ|Ỵ",
    );
    foreach ($unicode as $nonUnicode => $uni) {
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }
    $words = explode(" ", trim($str));
    $acronym = "";
    foreach ($words as $w) {
        if (trim($w) != "") {
            $word = trim($w);
            if (preg_match('/[0-9]/', $word)) {
                $acronym .= $word;
            } else {
                $acronym .= mb_substr($word, 0, 1, "UTF-8");
            }
        }
    }
    return strtoupper($acronym);
}

$status = 0;
$email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
$pass = isset($_POST["password"]) ? trim($_POST["password"]) : '';

if (empty($email)) {
    echo "Thiếu địa chỉ email.";
    exit;
}

$tk = $taikhoan->taikhoan__Get_By_Email($email);
if (!$tk) {
    echo "Không tìm thấy tài khoản với email: $email";
    exit;
}

$password = '';

// 1. Kiểm tra nếu password được gửi qua từ reset_ajax (chuỗi mã hóa AES)
if (!empty($pass) && strpos($pass, '$2y$') !== 0) {
    $dec = $hashpassword->Decryption($pass);
    if ($dec && password_verify($dec, $tk->mat_khau)) {
        $password = $dec;
    }
}

// 2. Kiểm tra mật khẩu khởi tạo ngẫu nhiên trong ghi_chu (INIT:AES_PAYLOAD)
if (empty($password) && !empty($tk->ghi_chu) && strpos($tk->ghi_chu, 'INIT:') === 0) {
    $enc_init = substr($tk->ghi_chu, 5);
    $dec_init = $hashpassword->Decryption($enc_init);
    if ($dec_init && password_verify($dec_init, $tk->mat_khau)) {
        $password = $dec_init;
    }
}

// 3. Kiểm tra các mật khẩu mặc định khác (#TDU1234, #TDU123, 123456)
if (empty($password)) {
    if (password_verify("#TDU1234", $tk->mat_khau)) {
        $password = "#TDU1234";
    } elseif (password_verify("#TDU123", $tk->mat_khau)) {
        $password = "#TDU123";
    } elseif (password_verify("123456", $tk->mat_khau)) {
        $password = "123456";
    }
}

// 4. Kiểm tra mật khẩu theo định dạng cũ của sinh viên (Ten_Lop#1234)
if (empty($password) && $tk->id_phan_nhom == 3) {
    $sv = $sinhvien->sinhvien__Get_By_Id($tk->id_nguoi_dung);
    if ($sv) {
        $lh = $lophoc->lophoc__Get_By_Id($sv->id_lop_hoc);
        $ten_lop = $lh ? $lh->ten_lop_hoc : '';
        $legacy_pass = vietTatChuCaiDau($sv->ten_sinh_vien) . "_" . vietTatChuCaiDau($ten_lop) . "#1234";
        if (password_verify($legacy_pass, $tk->mat_khau)) {
            $password = $legacy_pass;
        }
    }
}

// Nếu người dùng đã đổi mật khẩu cá nhân và không khớp với mật khẩu ban đầu
if (empty($password)) {
    echo "Tài khoản đã đổi mật khẩu cá nhân, không thể gửi lại mật khẩu ban đầu.";
    exit;
}

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Disable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'Lchsvhaugiang@tdu.edu.vn';                     //SMTP username
    $mail->Password   = 'xwqsfhydjgmjtiod';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('Lchsvhaugiang@tdu.edu.vn', 'TDU - PRL');
    $mail->addAddress($email, $email);     //Add a recipient
    // $mail->addReplyTo('info@gmail.com', 'Information');
    $mail->addCC('Lchsvhaugiang@tdu.edu.vn');
    // $mail->addBCC('bcc@gmail.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);
    $mail->CharSet = PHPMailer::CHARSET_UTF8;

    // Lấy tên người dùng để hiển thị "Thân chào [Tên]"
    $ho_ten = "bạn";
    $ten_goi = "bạn";
    $pn_info = $phannhom->phannhom__Get_By_Id($tk->id_phan_nhom);
    if ($pn_info) {
        if ($pn_info->cap_bac == 0) {
            $ho_ten = "Admin";
        } elseif ($pn_info->cap_bac == 1) {
            $ho_ten = "Manager";
        } elseif ($pn_info->cap_bac == 2) {
            $sv_info = $sinhvien->sinhvien__Get_By_Id($tk->id_nguoi_dung);
            if ($sv_info) $ho_ten = $sv_info->ten_sinh_vien;
        } elseif ($pn_info->cap_bac == 3) {
            $bt_info = $bithudoankhoa->bithudoankhoa__Get_By_Id($tk->id_nguoi_dung);
            if ($bt_info) $ho_ten = $bt_info->ten_bi_thu;
        } elseif ($pn_info->cap_bac == 4) {
            $gv_info = $giangvien->giangvien__Get_By_Id($tk->id_nguoi_dung);
            if ($gv_info) $ho_ten = $gv_info->ten_giang_vien;
        }
    }

    if ($ho_ten !== "bạn") {
        $ten_goi = trim($ho_ten);
    }

    $mail->Subject = 'Thông tin đăng nhập';
    $mail->Body    =
        "
        <p>Xin chào $ten_goi,</p>
        <p>Thông tin tài khoản của bạn để sử dụng ứng dụng <b>TDU - DRL</b> là:</p>
        <p>Email: <b>$email</b></p>
        <p>Password: <b>$password</b></p>
        <hr/>
        <p>Hướng dẫn cài đặt</p>
        <p>Bước 1: Tải xuống ứng dụng tại đây  <a href='https://drive.google.com/file/d/13MW44mTnpcFP_I_-NIIVzQQwwBsn7d7V/view?usp=sharing'><b>Tải ứng dụng</b></a></p>
        <p>Bước 2: Nếu xuất hiện cảnh báo về nguồn cài đặt thì nhấn CHO PHÉP cài đặt ứng dụng</p>
        <p>Bước 3: Đăng nhập theo tài khoản đã được cấp</p>
    ";
    $status = $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
