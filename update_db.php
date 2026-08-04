<?php
require './models/configs/config.php';
$db = new Database();

$queries = [
    "ALTER TABLE `dieu` ADD COLUMN `is_deleted` TINYINT(1) NOT NULL DEFAULT 0",
    "ALTER TABLE `khoan` ADD COLUMN `is_deleted` TINYINT(1) NOT NULL DEFAULT 0",
    "ALTER TABLE `muc` ADD COLUMN `is_deleted` TINYINT(1) NOT NULL DEFAULT 0",
    "ALTER TABLE `muc` ADD COLUMN `co_minh_chung` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Yêu cầu minh chứng'",
    "ALTER TABLE `minhchung` ADD COLUMN `id_muc` INT NULL COMMENT 'Mục tương ứng với minh chứng, NULL nếu là minh chứng chung'",
    "ALTER TABLE `sinhvien` ADD UNIQUE KEY `unique_ma_sinh_vien` (`ma_sinh_vien`)",
    "ALTER TABLE `sinhvien` ADD UNIQUE KEY `unique_email` (`email`)",
    "ALTER TABLE `bithudoankhoa` ADD UNIQUE KEY `unique_email_bi_thu` (`email`)",
    "ALTER TABLE `giangvien` ADD UNIQUE KEY `unique_ma_giang_vien` (`ma_giang_vien`)",
    "ALTER TABLE `giangvien` ADD UNIQUE KEY `unique_email_giang_vien` (`email`)",
    "ALTER TABLE `phancong` ADD UNIQUE KEY `unique_phan_cong` (`id_giang_vien`, `id_lop_hoc`)",
    "ALTER TABLE `taikhoan` ADD UNIQUE KEY `unique_email_tai_khoan` (`email`)"
];

foreach ($queries as $q) {
    try {
        $db->connect->query($q);
        echo "Thành công: " . $q . "<br>";
    } catch (Exception $e) {
        // Bỏ qua lỗi (ví dụ cột đã tồn tại)
        echo "Bỏ qua (có thể đã tồn tại): " . $q . "<br>";
    }
}
echo 'Success';
?>
