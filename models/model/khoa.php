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

class khoa extends Database {

    public function khoa__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM khoa");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function khoa__Add($ten_khoa, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO khoa(ten_khoa, ghi_chu) VALUES (?,?)");
        $obj->execute(array($ten_khoa, $ghi_chu));
        return $obj->rowCount();
    }

    public function khoa__Update($id_khoa, $ten_khoa, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE khoa SET ten_khoa=?, ghi_chu=? WHERE id_khoa=?");
        $obj->execute(array($ten_khoa, $ghi_chu, $id_khoa));
        return $obj->rowCount();
    }
    

    public function khoa__Delete($id_khoa) {
        $obj = $this->connect->prepare("DELETE FROM khoa WHERE id_khoa = ?");
        $obj->execute(array($id_khoa));
        return $obj->rowCount();
    }

  
    public function khoa__Get_By_Id($id_khoa) {
        $obj = $this->connect->prepare("SELECT * FROM khoa WHERE id_khoa = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoa));
        return $obj->fetch();
    }

}
?>