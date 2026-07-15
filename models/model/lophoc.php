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

class lophoc extends Database {

    public function lophoc__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM lophoc");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function lophoc__Add($ten_lop_hoc, $ghi_chu, $id_khoa_hoc, $id_nganh_hoc) {
        $obj = $this->connect->prepare("INSERT INTO lophoc(ten_lop_hoc, ghi_chu, id_khoa_hoc, id_nganh_hoc) VALUES (?,?,?,?)");
        $obj->execute(array($ten_lop_hoc, $ghi_chu, $id_khoa_hoc, $id_nganh_hoc));
        return $obj->rowCount();
    }

    public function lophoc__Update($id_lop_hoc, $ten_lop_hoc, $ghi_chu, $id_khoa_hoc, $id_nganh_hoc) {
        $obj = $this->connect->prepare("UPDATE lophoc SET ten_lop_hoc=?, ghi_chu=?, id_khoa_hoc=?, id_nganh_hoc=? WHERE id_lop_hoc=?");
        $obj->execute(array($ten_lop_hoc, $ghi_chu, $id_khoa_hoc, $id_nganh_hoc, $id_lop_hoc));
        return $obj->rowCount();
    }
    

    public function lophoc__Delete($id_lop_hoc) {
        $obj = $this->connect->prepare("DELETE FROM lophoc WHERE id_lop_hoc = ?");
        $obj->execute(array($id_lop_hoc));
        return $obj->rowCount();
    }

  
    public function lophoc__Get_By_Id($id_lop_hoc) {
        $obj = $this->connect->prepare("SELECT * FROM lophoc WHERE id_lop_hoc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc));
        return $obj->fetch();
    }

    public function lophoc__Get_By_Id_Khoa_Hoc($id_khoa_hoc) {
        $obj = $this->connect->prepare("SELECT * FROM lophoc WHERE id_khoa_hoc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoa_hoc));
        return $obj->fetchAll();
    }

    public function lophoc__Get_By_Id_Khoa_Nganh($id_nganh_hoc) {
        $obj = $this->connect->prepare("SELECT * FROM lophoc WHERE id_nganh_hoc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_nganh_hoc));
        return $obj->fetchAll();
    }

    public function lophoc__Get_By_Id_Khoa($id_khoa) {
        $obj = $this->connect->prepare("SELECT * FROM nganhhoc, lophoc WHERE lophoc.id_nganh_hoc = nganhhoc.id_nganh_hoc AND nganhhoc.id_khoa = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoa));
        return $obj->fetchAll();

}

public function lophoc__Get_Last() {
    $obj = $this->connect->prepare("SELECT * FROM lophoc ORDER BY id_lop_hoc LIMIT 1");
    $obj->setFetchMode(PDO::FETCH_OBJ);
    $obj->execute(array());
    return $obj->fetch();

}
}
?>