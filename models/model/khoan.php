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

class khoan extends Database {

    public function khoan__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM khoan");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    // quân sửa: Bổ sung tham số so_luong_muc vào hàm Thêm
    public function khoan__Add($ten_khoan, $ghi_chu, $can_tren, $thu_tu, $id_dieu, $so_luong_muc) {
        $obj = $this->connect->prepare("INSERT INTO khoan(ten_khoan, ghi_chu, can_tren, thu_tu, id_dieu, so_luong_muc) VALUES (?,?,?,?,?,?)");
        $obj->execute(array($ten_khoan, $ghi_chu, $can_tren, $thu_tu, $id_dieu, $so_luong_muc));
        return $obj->rowCount();
    }

    // quân sửa: Bổ sung tham số so_luong_muc vào hàm Sửa
    public function khoan__Update($id_khoan, $ten_khoan, $ghi_chu, $can_tren, $thu_tu, $id_dieu, $so_luong_muc) {
        $obj = $this->connect->prepare("UPDATE khoan SET ten_khoan=?, ghi_chu=?, can_tren=?, thu_tu=?, id_dieu=?, so_luong_muc=? WHERE id_khoan=?");
        $obj->execute(array($ten_khoan, $ghi_chu, $can_tren, $thu_tu, $id_dieu, $so_luong_muc, $id_khoan));
        return $obj->rowCount();
    }
    

    public function khoan__Delete($id_khoan) {
        $obj = $this->connect->prepare("DELETE FROM khoan WHERE id_khoan = ?");
        $obj->execute(array($id_khoan));
        return $obj->rowCount();
    }

  
    public function khoan__Get_By_Id($id_khoan) {
        $obj = $this->connect->prepare("SELECT * FROM khoan WHERE id_khoan = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoan));
        return $obj->fetch();
    }

    public function khoan__Get_By_Id_Dieu($id_dieu) {
        $obj = $this->connect->prepare("SELECT * FROM khoan WHERE id_dieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dieu));
        return $obj->fetchAll();
    }

}
?>