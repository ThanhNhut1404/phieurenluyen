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

class phanquyen extends Database {

    public function phanquyen__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM phanquyen");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function phanquyen__Add($ten_phan_quyen, $cap_bac, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO phanquyen(ten_phan_quyen,cap_bac, ghi_chu) VALUES (?,?,?)");
        $obj->execute(array($ten_phan_quyen, $cap_bac, $ghi_chu));
        return $obj->rowCount();
    }

    public function phanquyen__Update($id_phan_quyen, $ten_phan_quyen,$cap_bac, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE phanquyen SET ten_phan_quyen=?,cap_bac=?, ghi_chu=? WHERE id_phan_quyen=?");
        $obj->execute(array($ten_phan_quyen,$cap_bac, $ghi_chu, $id_phan_quyen));
        return $obj->rowCount();
    }
    

    public function phanquyen__Delete($id_phan_quyen) {
        $obj = $this->connect->prepare("DELETE FROM phanquyen WHERE id_phan_quyen = ?");
        $obj->execute(array($id_phan_quyen));
        return $obj->rowCount();
    }

  
    public function phanquyen__Get_By_Id($id_phan_quyen) {
        $obj = $this->connect->prepare("SELECT * FROM phanquyen WHERE id_phan_quyen = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_phan_quyen));
        return $obj->fetch();
    }

    public function phanquyen__Get_By_Cap_Bac($cap_bac) {
        $obj = $this->connect->prepare("SELECT * FROM phanquyen WHERE cap_bac = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($cap_bac));
        return $obj->fetch();
    }
}
?>