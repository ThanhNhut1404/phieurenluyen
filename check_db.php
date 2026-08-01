<?php
require './models/configs/config.php';
$db = new Database();
$res = $db->connect->query('SELECT id_muc, co_minh_chung FROM muc')->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
?>
