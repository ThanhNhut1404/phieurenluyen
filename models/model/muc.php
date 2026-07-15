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

class muc extends Database {

    public function muc__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM muc");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function muc__Add($ten_muc, $ghi_chu, $thu_tu, $id_khoan) {
        $obj = $this->connect->prepare("INSERT INTO muc(ten_muc, ghi_chu, thu_tu, id_khoan) VALUES (?,?,?,?)");
        $obj->execute(array($ten_muc, $ghi_chu, $thu_tu, $id_khoan));
        return $obj->rowCount();
    }

    public function muc__Update($id_muc, $ten_muc, $ghi_chu, $thu_tu, $id_khoan) {
        $obj = $this->connect->prepare("UPDATE muc SET ten_muc=?, ghi_chu=?, thu_tu=?, id_khoan=? WHERE id_muc=?");
        $obj->execute(array($ten_muc, $ghi_chu, $thu_tu, $id_khoan, $id_muc));
        return $obj->rowCount();
    }
    

    public function muc__Delete($id_muc) {
        $obj = $this->connect->prepare("DELETE FROM muc WHERE id_muc = ?");
        $obj->execute(array($id_muc));
        return $obj->rowCount();
    }

  
    public function muc__Get_By_Id($id_muc) {
        $obj = $this->connect->prepare("SELECT * FROM muc WHERE id_muc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_muc));
        return $obj->fetch();
    }

    public function muc__Get_By_Id_Khoan($id_khoan) {
        $obj = $this->connect->prepare("SELECT * FROM muc WHERE id_khoan = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoan));
        return $obj->fetchAll();
    }
}
?>