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
        $password = $hashpassword->Decryption($pass);
        $mail->Body    =
            "
        <p>Thân chào bạn,</p>
        <p>Thông tin tài khoản sử dụng tại ứng dụng <b>TDU - PRL</b> là:</p>
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
