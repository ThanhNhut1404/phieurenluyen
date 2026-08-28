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

$href = $_SERVER["HTTP_REFERER"];
if (strlen(strpos($href, '&status')) > 0) {
    $href = explode('&status', $href)[0];
}

$status = 0;
$id_lop = $_POST["id_lop"];
$taikhoan__Get_By_Lop_Hoc = $taikhoan->taikhoan__Get_By_Lop_Hoc($id_lop);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Disable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'Lchsvhaugiang@tdu.edu.vn';                     //SMTP username
    $mail->Password   = 'lixovuuyoerwzdwv';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`



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

    foreach ($taikhoan__Get_By_Lop_Hoc as $item) {
        $mail->clearAddresses();
        $mail->clearCCs();
        $mail->setFrom('Lchsvhaugiang@tdu.edu.vn', 'TDU - PRL');
        $mail->addCC('Lchsvhaugiang@tdu.edu.vn');
        $mail->addAddress($item->email, $item->email);     //Add a recipient
        $mail->isHTML(true);
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->Subject = 'Thông tin đăng nhập';
        $email = $item->email;
        $pass = $item->mat_khau;
        if (strpos($pass, '$2y$') === 0) {
            if (password_verify("123456", $pass)) {
                $password = "123456";
            } else {
                $password = "123456"; // Fallback mặc định
                $tk = $taikhoan->taikhoan__Get_By_Email($email);
                if ($tk && $tk->id_phan_nhom == 3) {
                    $sv = $sinhvien->sinhvien__Get_By_Id($tk->id_nguoi_dung);
                    if ($sv) {
                        $lh = $lophoc->lophoc__Get_By_Id($sv->id_lop_hoc);
                        $ten_lop = $lh ? $lh->ten_lop_hoc : '';
                        $default_pass = vietTatChuCaiDau($sv->ten_sinh_vien) . "_" . vietTatChuCaiDau($ten_lop) . "#1234";
                        if (password_verify($default_pass, $pass)) {
                            $password = $default_pass;
                        }
                    }
                }
            }
        } else {
            $password = $hashpassword->Decryption($pass);
            if (!$password || strlen($password) > 100) {
                if (password_verify("123456", $pass)) {
                    $password = "123456";
                } else {
                    $password = "123456"; // Fallback mặc định
                    $tk = $taikhoan->taikhoan__Get_By_Email($email);
                    if ($tk && $tk->id_phan_nhom == 3) {
                        $sv = $sinhvien->sinhvien__Get_By_Id($tk->id_nguoi_dung);
                        if ($sv) {
                            $lh = $lophoc->lophoc__Get_By_Id($sv->id_lop_hoc);
                            $ten_lop = $lh ? $lh->ten_lop_hoc : '';
                            $default_pass = vietTatChuCaiDau($sv->ten_sinh_vien) . "_" . vietTatChuCaiDau($ten_lop) . "#1234";
                            if (password_verify($default_pass, $pass)) {
                                $password = $default_pass;
                            }
                        }
                    }
                }
            }
        }
        $mail->Body    =
            "
        <p>Thân chào bạn,</p>
        <p>Thông tin tài khoản sử dụng tại ứng dụng <b>TDU - DRL</b> là:</p>
        <p>Email: <b>$email</b></p>
        <p>Password: <b>$password</b></p>
        <hr/>
        <p>Hướng dẫn cài đặt</p>
        <p>Bước 1: Tải xuống ứng dụng tại đây  <a href='https://drive.google.com/file/d/13MW44mTnpcFP_I_-NIIVzQQwwBsn7d7V/view?usp=sharing'><b>Tải ứng dụng</b></a></p>
        <p>Bước 2: Nếu xuất hiện cảnh báo về nguồn cài đặt thì nhấn CHO PHÉP cài đặt ứng dụng</p>
        <p>Bước 3: Đăng nhập theo tài khoản đã được cấp</p>
    ";
        $status = $mail->send();
    }

    // if ($status != 0) {
    //     header("location: $href&status=success");
    // } else {
    //     header("location: $href&status=failed");
    // }
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    // header("location: $href&status=failed");
}
