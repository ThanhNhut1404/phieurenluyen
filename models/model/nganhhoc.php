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

class nganhhoc extends Database {

    public function nganhhoc__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM nganhhoc");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function nganhhoc__Add($ten_nganh_hoc, $ghi_chu, $id_khoa) {
        $obj = $this->connect->prepare("INSERT INTO nganhhoc(ten_nganh_hoc, ghi_chu, id_khoa) VALUES (?,?,?)");
        $obj->execute(array($ten_nganh_hoc, $ghi_chu, $id_khoa));
        return $obj->rowCount();
    }

    public function nganhhoc__Update($id_nganh_hoc, $ten_nganh_hoc, $ghi_chu, $id_khoa) {
        $obj = $this->connect->prepare("UPDATE nganhhoc SET ten_nganh_hoc=?, ghi_chu=?, id_khoa=? WHERE id_nganh_hoc=?");
        $obj->execute(array($ten_nganh_hoc, $ghi_chu, $id_khoa, $id_nganh_hoc));
        return $obj->rowCount();
    }
    

    public function nganhhoc__Delete($id_nganh_hoc) {
        $obj = $this->connect->prepare("DELETE FROM nganhhoc WHERE id_nganh_hoc = ?");
        $obj->execute(array($id_nganh_hoc));
        return $obj->rowCount();
    }

  
    public function nganhhoc__Get_By_Id($id_nganh_hoc) {
        $obj = $this->connect->prepare("SELECT * FROM nganhhoc WHERE id_nganh_hoc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_nganh_hoc));
        return $obj->fetch();
    }

    public function nganhhoc__Get_By_Id_Khoa($id_khoa) {
        $obj = $this->connect->prepare("SELECT * FROM nganhhoc WHERE id_khoa = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoa));
        return $obj->fetch();
    }
}
?>