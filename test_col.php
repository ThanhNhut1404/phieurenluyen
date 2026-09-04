<?php require "models/configs/config.php"; $db = new Database(); $stmt = $db->connect->prepare("DESCRIBE ketquaxeploai"); $stmt->execute(); print_r($stmt->fetchAll(PDO::FETCH_ASSOC)); ?>
