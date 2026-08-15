<?php
require_once 'core.php';

// Find a student account
$sql = "SELECT * FROM taikhoan WHERE vai_tro = 0 LIMIT 1";
$stmt = $taikhoan->connect->prepare($sql);
$stmt->execute();
$tk = $stmt->fetch(PDO::FETCH_OBJ);

if (!$tk) {
    die("No student account found");
}

// Generate a token or use an existing one
// Actually, let's just bypass the HTTP request and test the logic directly in a web context.
// But we want to see if ANY PHP warning is generated.
// We can just call get_phieu_cham_diem.php via HTTP locally!

$email = $tk->email;
$password = "123456"; // usually default password? We don't know the password.
// Just inject the token into the database
$token = "TEST_TOKEN_" . time();
$sql = "UPDATE taikhoan SET token = ? WHERE email = ?";
$stmt = $taikhoan->connect->prepare($sql);
$stmt->execute([$token, $email]);

// Now call get_phieu_cham_diem.php via cURL
$ch = curl_init('http://localhost/phieurenluyen/api/get_phieu_cham_diem.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
$response = curl_exec($ch);
curl_close($ch);

echo "RESPONSE:\n" . $response . "\n";
