<?php
require './models/configs/config.php';
$db = new Database();
$db->connect->query("ALTER TABLE `muc` ADD COLUMN `diem_toi_da` INT NOT NULL DEFAULT 0 COMMENT 'Điểm tối đa của mục'");
echo 'Success';
?>
