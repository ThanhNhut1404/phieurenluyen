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

class xeploai extends Database {

    public function xeploai__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM xeploai");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function xeploai__Add($ten_xep_loai, $can_tren, $can_duoi,$ha_bac, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO xeploai(ten_xep_loai, ghi_chu, can_tren, can_duoi, ha_bac, ghi_chu) VALUES (?,?,?,?,?)");
        $obj->execute(array($ten_xep_loai, $can_tren, $can_duoi,$ha_bac, $ghi_chu));
        return $obj->rowCount();
    }

    public function xeploai__Update($id_xep_loai, $ten_xep_loai, $can_tren, $can_duoi,$ha_bac, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE xeploai SET ten_xep_loai=?, ghi_chu=?, can_tren=?, can_duoi=?, ha_bac=?, ghi_chu=? WHERE id_xep_loai=?");
        $obj->execute(array($ten_xep_loai, $can_tren, $can_duoi,$ha_bac, $ghi_chu, $id_xep_loai));
        return $obj->rowCount();
    }
    

    public function xeploai__Delete($id_xep_loai) {
        $obj = $this->connect->prepare("DELETE FROM xeploai WHERE id_xep_loai = ?");
        $obj->execute(array($id_xep_loai));
        return $obj->rowCount();
    }

  
    public function xeploai__Get_By_Id($id_xep_loai) {
        $obj = $this->connect->prepare("SELECT * FROM xeploai WHERE id_xep_loai = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_xep_loai));
        return $obj->fetch();
    }

    public function xeploai__Get_By_Kq($kq) {
        $obj = $this->connect->prepare("SELECT * FROM xeploai WHERE can_duoi<=? AND can_tren >= ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($kq, $kq));
        return $obj->fetch();
    }
}
?>