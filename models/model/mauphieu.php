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

class mauphieu extends Database {

    public function mauphieu__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM mauphieu");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function mauphieu__Add($ten_mau_phieu, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO mauphieu(ten_mau_phieu, ghi_chu) VALUES (?,?)");
        $obj->execute(array($ten_mau_phieu, $ghi_chu));
        return $this->connect->lastInsertId();
    }

    public function mauphieu__Update($id_mau_phieu, $ten_mau_phieu, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE mauphieu SET ten_mau_phieu=?, ghi_chu=? WHERE id_mau_phieu=?");
        return $obj->execute(array($ten_mau_phieu, $ghi_chu, $id_mau_phieu));
    }
    

    public function mauphieu__Delete($id_mau_phieu) {
        $obj = $this->connect->prepare("DELETE FROM mauphieu WHERE id_mau_phieu = ?");
        $obj->execute(array($id_mau_phieu));
        return $obj->rowCount();
    }

  
    public function mauphieu__Get_By_Id($id_mau_phieu) {
        $obj = $this->connect->prepare("SELECT * FROM mauphieu WHERE id_mau_phieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_mau_phieu));
        return $obj->fetch();
    }

    // Quân sửa: Kiểm tra điều kiện khóa Sửa (Đã phát sinh phiếu chấm HOẶC đợt chấm đang diễn ra)
    public function mauphieu__Is_Edit_Locked($id_mau_phieu) {
        // 1. Kiểm tra xem đã phát sinh phiếu chấm điểm chưa
        $stmt1 = $this->connect->prepare("SELECT COUNT(*) FROM phieuchamdiem p JOIN lopapdung l ON p.id_lop_ap_dung = l.id_lop_ap_dung WHERE l.id_mau_phieu = ?");
        $stmt1->execute(array($id_mau_phieu));
        if ($stmt1->fetchColumn() > 0) return true;

        // 2. Kiểm tra xem đợt chấm điểm đã bắt đầu chưa
        $stmt2 = $this->connect->prepare("SELECT COUNT(*) FROM dotchamdiem d JOIN lopapdung l ON d.id_dot = l.id_dot WHERE l.id_mau_phieu = ? AND NOW() >= d.thoi_gian_bat_dau");
        $stmt2->execute(array($id_mau_phieu));
        if ($stmt2->fetchColumn() > 0) return true;

        return false;
    }
}
?>