<?php

$a = "./models/configs/config.php";
$b = "../models/configs/config.php";
$c = "../../models/configs/config.php";
$d = "../../../models/configs/config.php";
$e = "../../../../models/configs/config.php";

if (file_exists($a)) {
    $des = $a;
}
if (file_exists($b)) {
    $des = $b;
}
if (file_exists($c)) {
    $des = $c;
} 
if (file_exists($d)) {
    $des = $d;
} 

if (file_exists($e)) {
    $des = $e;
} 
include_once($des);

class mauphieu extends Database {

    public function mauphieu__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM mauphieu");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function mauphieu__Add($ten_mau_phieu, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO mauphieu(ten_mau_phieu, ghi_chu) VALUES (?,?)");
        $obj->execute(array($ten_mau_phieu, $ghi_chu));
        return $this->connect->lastInsertId();
    }

    public function mauphieu__Update($id_mau_phieu, $ten_mau_phieu, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE mauphieu SET ten_mau_phieu=?, ghi_chu=? WHERE id_mau_phieu=?");
        $obj->execute(array($ten_mau_phieu, $ghi_chu, $id_mau_phieu));
        return $obj->rowCount();
    }
    

    public function mauphieu__Delete($id_mau_phieu) {
        $obj = $this->connect->prepare("DELETE FROM mauphieu WHERE id_mau_phieu = ?");
        $obj->execute(array($id_mau_phieu));
        return $obj->rowCount();
    }

  
    public function mauphieu__Get_By_Id($id_mau_phieu) {
        $obj = $this->connect->prepare("SELECT * FROM mauphieu WHERE id_mau_phieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_mau_phieu));
        return $obj->fetch();
    }
}
?>