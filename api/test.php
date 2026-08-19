<?php
require 'core.php';

try {
    $sv = $sinhvien->sinhvien__Get_By_Id(6); // just testing
    print_r($sv);
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
