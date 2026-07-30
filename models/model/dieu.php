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

class dieu extends Database {

    public function dieu__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM dieu");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function dieu__Add($ten_dieu, $ghi_chu, $thu_tu) {
        $obj = $this->connect->prepare("INSERT INTO dieu(ten_dieu, ghi_chu, thu_tu) VALUES (?,?,?)");
        $obj->execute(array($ten_dieu, $ghi_chu, $thu_tu));
        return $obj->rowCount();
    }

    public function dieu__Update($id_dieu, $ten_dieu, $ghi_chu, $thu_tu) {
        $obj = $this->connect->prepare("UPDATE dieu SET ten_dieu=?, ghi_chu=?, thu_tu=? WHERE id_dieu=?");
        $obj->execute(array($ten_dieu, $ghi_chu, $thu_tu, $id_dieu));
        return $obj->rowCount();
    }
    

    public function dieu__Delete($id_dieu) {
        $obj = $this->connect->prepare("DELETE FROM dieu WHERE id_dieu = ?");
        $obj->execute(array($id_dieu));
        return $obj->rowCount();
    }

  
    public function dieu__Get_By_Id($id_dieu) {
        $obj = $this->connect->prepare("SELECT * FROM dieu WHERE id_dieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dieu));
        return $obj->fetch();
    }

    // quân sửa: Hàm lấy thứ tự lớn nhất
    public function dieu__Get_Max_Thu_Tu() {
        $obj = $this->connect->prepare("SELECT MAX(thu_tu) as max_thu_tu FROM dieu");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        $result = $obj->fetch();
        return $result ? $result->max_thu_tu : 0;
    }

    // quân sửa: Hàm lấy Điều theo thứ tự cụ thể (để check trùng lặp Swap)
    public function dieu__Get_By_Thu_Tu($thu_tu) {
        $obj = $this->connect->prepare("SELECT * FROM dieu WHERE thu_tu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($thu_tu));
        return $obj->fetch();
    }

    // quân sửa: Hàm chỉ cập nhật thứ tự
    public function dieu__Update_Thu_Tu($id_dieu, $thu_tu) {
        $obj = $this->connect->prepare("UPDATE dieu SET thu_tu=? WHERE id_dieu=?");
        $obj->execute(array($thu_tu, $id_dieu));
        return $obj->rowCount();
    }

    public function dieu__Get_By_Id_Mau_Phieu($id_mau_phieu) {
        $obj = $this->connect->prepare("SELECT * FROM dieu WHERE id_mau_phieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_mau_phieu));
        return $obj->fetchAll();
    }

    public function dieu__Get_All_Selected($id_mau_phieu) {
        $obj = $this->connect->prepare("SELECT * FROM dieu, bocauhoi WHERE dieu.id_dieu = bocauhoi.id_dieu AND bocauhoi.id_mau_phieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_mau_phieu));
        return $obj->fetchAll();
    }

    public function dieu__Get_All_Unselected($id_mau_phieu) {
        $obj = $this->connect->prepare("SELECT * FROM dieu, bocauhoi WHERE dieu.id_dieu != bocauhoi.id_dieu AND bocauhoi.id_mau_phieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_mau_phieu));
        return $obj->fetchAll();
    }
}
?>