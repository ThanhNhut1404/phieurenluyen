<?php
require './models/configs/config.php';
$db = new Database();
$res = $db->connect->query("SELECT kq_sv FROM phieuchamdiem LIMIT 1");
print_r($res->fetchColumn());
?>
