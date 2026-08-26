<?php
require_once 'core.php';

try {
    $db = new Database();
    
    // Add ai_trang_thai
    $check1 = $db->connect->query("SHOW COLUMNS FROM minhchung LIKE 'ai_trang_thai'")->fetchAll();
    if (count($check1) == 0) {
        $db->connect->exec("ALTER TABLE minhchung ADD COLUMN ai_trang_thai INT DEFAULT 0");
        echo "Column ai_trang_thai added.\n";
    }
    
    // Add ai_nhan_xet
    $check2 = $db->connect->query("SHOW COLUMNS FROM minhchung LIKE 'ai_nhan_xet'")->fetchAll();
    if (count($check2) == 0) {
        $db->connect->exec("ALTER TABLE minhchung ADD COLUMN ai_nhan_xet TEXT NULL");
        echo "Column ai_nhan_xet added.\n";
    }
    echo "Database updated successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
