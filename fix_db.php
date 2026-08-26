<?php
require_once './models/configs/config.php';
$db = new Database();
$conn = $db->connect;
try {
    $conn->exec("ALTER TABLE xeploai ADD COLUMN is_deleted TINYINT(1) DEFAULT 0");
    echo "Thêm cột is_deleted thành công!\n";
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Cột is_deleted đã tồn tại.\n";
    } else {
        echo "Lỗi: " . $e->getMessage() . "\n";
    }
}
?>
