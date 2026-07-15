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

class namhoc extends Database {

    public function namhoc__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM namhoc");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function namhoc__Add($ten_nam_hoc, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO namhoc(ten_nam_hoc, ghi_chu) VALUES (?,?)");
        $obj->execute(array($ten_nam_hoc, $ghi_chu));
        return $obj->rowCount();
    }

    public function namhoc__Update($id_nam_hoc, $ten_nam_hoc, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE namhoc SET ten_nam_hoc=?, ghi_chu=? WHERE id_nam_hoc=?");
        $obj->execute(array($ten_nam_hoc, $ghi_chu, $id_nam_hoc));
        return $obj->rowCount();
    }
    

    public function namhoc__Delete($id_nam_hoc) {
        $obj = $this->connect->prepare("DELETE FROM namhoc WHERE id_nam_hoc = ?");
        $obj->execute(array($id_nam_hoc));
        return $obj->rowCount();
    }

  
    public function namhoc__Get_By_Id($id_nam_hoc) {
        $obj = $this->connect->prepare("SELECT * FROM namhoc WHERE id_nam_hoc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_nam_hoc));
        return $obj->fetch();
    }
}
?>