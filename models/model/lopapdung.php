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

class lopapdung extends Database {

    public function lopapdung__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM lopapdung");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function lopapdung__Add($id_dot, $id_mau_phieu, $id_lop_hoc) {
        $obj = $this->connect->prepare("INSERT INTO lopapdung(id_dot, id_mau_phieu, id_lop_hoc) VALUES (?,?,?)");
        $obj->execute(array($id_dot, $id_mau_phieu, $id_lop_hoc));
        return $this->connect->lastInsertId();
    }

    public function lopapdung__Update($id_lop_ap_dung, $id_dot, $id_mau_phieu, $id_lop_hoc) {
        $obj = $this->connect->prepare("UPDATE lopapdung SET id_dot=?, id_mau_phieu=?, id_lop_hoc=? WHERE id_lop_ap_dung=?");
        $obj->execute(array($id_dot, $id_mau_phieu, $id_lop_hoc, $id_lop_ap_dung));
        return $obj->rowCount();
    }
    

    public function lopapdung__Delete($id_lop_ap_dung) {
        $obj = $this->connect->prepare("DELETE FROM lopapdung WHERE id_lop_ap_dung = ?");
        $obj->execute(array($id_lop_ap_dung));
        return $obj->rowCount();
    }

  
    public function lopapdung__Get_By_Id($id_lop_ap_dung) {
        $obj = $this->connect->prepare("SELECT * FROM lopapdung WHERE id_lop_ap_dung = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_ap_dung));
        return $obj->fetch();
    }

    public function lopapdung__Get_By_Id_Dot($id_dot) {
        $obj = $this->connect->prepare("SELECT * FROM lopapdung WHERE id_dot = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot));
        return $obj->fetchAll();
    }

    public function lopapdung__Get_By_Id_Mau_Phieu($id_mau_phieu) {
        $obj = $this->connect->prepare("SELECT * FROM lopapdung WHERE id_mau_phieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_mau_phieu));
        return $obj->fetchAll();
    }
    public function lopapdung__Get_By_Id_Ap_Dung($id_lop_ap_dung) {
        $obj = $this->connect->prepare("SELECT * FROM lopapdung WHERE id_lop_ap_dung = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_ap_dung));
        return $obj->fetch();
    }

    public function lopapdung__Delete_By_Id_Dot($id_dot) {
        $obj = $this->connect->prepare("DELETE FROM lopapdung WHERE id_dot = ?");
        $obj->execute(array($id_dot));
        return $obj->rowCount();
    }

}
?>