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

class phancong extends Database {

     // Phân công cố vấn

     public function phancong__Get_All($trang_thai = 1) {
        $obj = $this->connect->prepare("SELECT * FROM phancong WHERE trang_thai=?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($trang_thai));
        return $obj->fetchAll();
    }


    public function phancong__Add($id_giang_vien, $id_lop_hoc, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO phancong (id_giang_vien, id_lop_hoc, ghi_chu) VALUES (?,?,?)");
        $obj->execute(array($id_giang_vien, $id_lop_hoc, $ghi_chu));
        return $obj->rowCount();
    }

    public function phancong__Update($id_phan_cong, $id_giang_vien, $id_lop_hoc, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE phancong SET id_giang_vien = ?, id_lop_hoc =? , ghi_chu =? WHERE id_phan_cong =?");
        $obj->execute(array($id_giang_vien, $id_lop_hoc, $ghi_chu, $id_phan_cong));
        return $obj->rowCount();
    }

    public function phancong__Delete($id_phan_cong) {
        $obj = $this->connect->prepare("DELETE FROM phancong WHERE id_phan_cong =?");
        $obj->execute(array($id_phan_cong));
        return $obj->rowCount();
    }

    public function phancong__Get_By_Id($id_phan_cong) {
        $obj = $this->connect->prepare("SELECT * FROM phancong WHERE id_phan_cong = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_phan_cong));
        return $obj->fetch();
    }

    public function phancong__Get_By_Id_Giang_Vien($id_giang_vien, $trang_thai=1) {
        $obj = $this->connect->prepare("SELECT * FROM phancong WHERE id_giang_vien = ? AND trang_thai=?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_giang_vien, $trang_thai));
        return $obj->fetch();
    }

    public function phancong__Get_By_id_lop_hoc_Hoc($id_lop_hoc_hoc, $trang_thai=1) {
        $obj = $this->connect->prepare("SELECT * FROM phancong WHERE id_lop_hoc_hoc = ? AND trang_thai=?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc_hoc, $trang_thai));
        return $obj->fetch();
    }

    public function phancong__Get_By_Id_Giang_Vien_And_id_lop_hoc_Hoc($id_giang_vien, $id_lop_hoc_hoc, $trang_thai=1) {
        $obj = $this->connect->prepare("SELECT * FROM phancong WHERE id_giang_vien=? AND id_lop_hoc_hoc = ? AND trang_thai=?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_giang_vien, $id_lop_hoc_hoc, $trang_thai));
        return $obj->fetch();
    }

    public function phancong__Update_Trang_Thai($id_phan_cong, $trang_thai) {
        $obj = $this->connect->prepare("UPDATE phancong SET trang_thai=? WHERE id_phan_cong=?");
        $obj->execute(array($trang_thai, $id_phan_cong));
        return $obj->rowCount();
    }

    public function phannhom__Get_By_Cap_Bac($cap_bac) {
        $obj = $this->connect->prepare("SELECT * FROM phancong WHERE cap_bac = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($cap_bac));
        return $obj->fetch();
    }
    
    public function phancong__Get_By_Id_Giang_Vien_All($id_giang_vien, $trang_thai=1) {
        $obj = $this->connect->prepare("SELECT * FROM phancong, lophoc WHERE phancong.id_lop_hoc = lophoc.id_lop_hoc AND  phancong.id_giang_vien = ? AND trang_thai=?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_giang_vien, $trang_thai));
        return $obj->fetchAll();
    }
}
?>