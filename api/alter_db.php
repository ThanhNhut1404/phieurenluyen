<?php
require_once 'core.php';

try {
    $db = new Database();
    
    // Check if column exists first
    $check = $db->connect->query("SHOW COLUMNS FROM sinhvien LIKE 'anh_dai_dien'")->fetchAll();
    if (count($check) == 0) {
        $db->connect->exec("ALTER TABLE sinhvien ADD COLUMN anh_dai_dien VARCHAR(255) NULL");
        echo "Column added successfully.\n";
    } else {
        echo "Column already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
