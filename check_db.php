<?php
require "models/getModel.php";
$stmt = $ketquaxeploai->connect->query("SELECT * FROM ketquaxeploai LIMIT 10");
$rows = $stmt->fetchAll();
print_r($rows);

$id_sinh_vien = 22;
$id_dot = 1;
$id_lop_hoc = 2; // whatever is passed
// Let's print the ketquaxeploai__Get_By_Id_Phieu query result
print_r("Result:\n");
print_r($ketquaxeploai->ketquaxeploai__Get_By_Id_Phieu(1, 1, 1));
?>
