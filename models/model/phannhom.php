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

class phannhom extends Database {

    public function phannhom__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM phannhom");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function phannhom__Add($ten_phan_nhom, $cap_bac, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO phannhom(ten_phan_nhom, cap_bac, ghi_chu) VALUES (?,?,?)");
        $obj->execute(array($ten_phan_nhom, $cap_bac, $ghi_chu));
        return $obj->rowCount();
    }

    public function phannhom__Update($id_phan_nhom, $ten_phan_nhom, $cap_bac, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE phannhom SET ten_phan_nhom=?, cap_bac=?, ghi_chu=? WHERE id_phan_nhom=?");
        $obj->execute(array($ten_phan_nhom, $cap_bac, $ghi_chu, $id_phan_nhom));
        return $obj->rowCount();
    }
    

    public function phannhom__Delete($id_phan_nhom) {
        $obj = $this->connect->prepare("DELETE FROM phannhom WHERE id_phan_nhom = ?");
        $obj->execute(array($id_phan_nhom));
        return $obj->rowCount();
    }

  
    public function phannhom__Get_By_Id($id_phan_nhom) {
        $obj = $this->connect->prepare("SELECT * FROM phannhom WHERE id_phan_nhom = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_phan_nhom));
        return $obj->fetch();
    }
}
?>