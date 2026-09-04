<?php
require 'models/configs/config.php';
$db = new Database();
$stmt = $db->connect->query('SELECT thoi_gian_ket_thuc, trang_thai FROM dotchamdiem ORDER BY id_dot DESC LIMIT 1');
print_r($stmt->fetch());
