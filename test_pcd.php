<?php require "models/configs/config.php"; $db = new Database(); $stmt = $db->connect->prepare("DESCRIBE phieuchamdiem"); $stmt->execute(); print_r($stmt->fetchAll(PDO::FETCH_ASSOC)); ?>
