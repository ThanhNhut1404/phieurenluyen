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

class khoahoc extends Database {

    public function khoahoc__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM khoahoc");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function khoahoc__Add($ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO khoahoc(ten_khoa_hoc, nam_nhap_hoc, he_dao_tao, ghi_chu) VALUES (?,?,?,?)");
        $obj->execute(array($ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu));
        return $obj->rowCount();
    }

    public function khoahoc__Update($id_khoa_hoc, $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE khoahoc SET ten_khoa_hoc=?, nam_nhap_hoc=?, he_dao_tao=?, ghi_chu=? WHERE id_khoa_hoc=?");
        $obj->execute(array($ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu, $id_khoa_hoc));
        return $obj->rowCount();
    }
    

    public function khoahoc__Delete($id_khoa_hoc) {
        $obj = $this->connect->prepare("DELETE FROM khoahoc WHERE id_khoa_hoc = ?");
        $obj->execute(array($id_khoa_hoc));
        return $obj->rowCount();
    }

  
    public function khoahoc__Get_By_Id($id_khoa_hoc) {
        $obj = $this->connect->prepare("SELECT * FROM khoahoc WHERE id_khoa_hoc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoa_hoc));
        return $obj->fetch();
    }

}
?>