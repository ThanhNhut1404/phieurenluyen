<?php
require './models/configs/config.php';
$db = new Database();
$db->connect->query("ALTER TABLE `dieu` ADD COLUMN `is_deleted` TINYINT(1) NOT NULL DEFAULT 0");
$db->connect->query("ALTER TABLE `khoan` ADD COLUMN `is_deleted` TINYINT(1) NOT NULL DEFAULT 0");
$db->connect->query("ALTER TABLE `muc` ADD COLUMN `is_deleted` TINYINT(1) NOT NULL DEFAULT 0");
$db->connect->query("ALTER TABLE `muc` ADD COLUMN `co_minh_chung` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Yêu cầu minh chứng'");
echo 'Success';
?>
