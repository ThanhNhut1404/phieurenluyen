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

class hocky extends Database {

    public function hocky__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM hocky");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function hocky__Add($ten_hoc_ky, $ghi_chu, $id_nam_hoc) {
        $obj = $this->connect->prepare("INSERT INTO hocky(ten_hoc_ky, ghi_chu, id_nam_hoc) VALUES (?,?,?)");
        $obj->execute(array($ten_hoc_ky, $ghi_chu, $id_nam_hoc));
        return $obj->rowCount();
    }

    public function hocky__Update($id_hoc_ky, $ten_hoc_ky, $ghi_chu, $id_nam_hoc) {
        $obj = $this->connect->prepare("UPDATE hocky SET ten_hoc_ky=?, ghi_chu=?, id_nam_hoc=? WHERE id_hoc_ky=?");
        $obj->execute(array($ten_hoc_ky, $ghi_chu, $id_nam_hoc, $id_hoc_ky));
        return $obj->rowCount();
    }
    

    public function hocky__Delete($id_hoc_ky) {
        $obj = $this->connect->prepare("DELETE FROM hocky WHERE id_hoc_ky = ?");
        $obj->execute(array($id_hoc_ky));
        return $obj->rowCount();
    }

  
    public function hocky__Get_By_Id($id_hoc_ky) {
        $obj = $this->connect->prepare("SELECT * FROM hocky WHERE id_hoc_ky = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_hoc_ky));
        return $obj->fetch();
    }

    public function hocky__Get_By_id_Nam_Hoc($id_nam_hoc) {
        $obj = $this->connect->prepare("SELECT * FROM hocky WHERE id_nam_hoc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_nam_hoc));
        return $obj->fetchAll();
    }
}
?>