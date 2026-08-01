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

class minhchung extends Database {

    public function minhchung__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM minhchung");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function minhchung__Add($id_phieu, $hinh_anh, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO minhchung(id_phieu, hinh_anh, ghi_chu) VALUES (?,?,?)");
        $obj->execute(array($id_phieu, $hinh_anh, $ghi_chu));
        return $obj->rowCount();
    }

    public function minhchung__Update($id_minh_chung, $id_phieu, $hinh_anh, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE minhchung SET id_phieu=?, hinh_anh=?, ghi_chu=? WHERE id_minh_chung=?");
        $obj->execute(array($id_phieu, $hinh_anh, $ghi_chu, $id_minh_chung));
        return $obj->rowCount();
    }
    

    public function minhchung__Delete($id_minh_chung) {
        $obj = $this->connect->prepare("DELETE FROM minhchung WHERE id_minh_chung = ?");
        $obj->execute(array($id_minh_chung));
        return $obj->rowCount();
    }

  
    public function minhchung__Get_By_Id($id_minh_chung) {
        $obj = $this->connect->prepare("SELECT * FROM minhchung WHERE id_minh_chung = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_minh_chung));
        return $obj->fetch();
    }

    public function minhchung__Get_By_Id_Phieu($id_phieu) {
        $obj = $this->connect->prepare("SELECT * FROM minhchung WHERE id_phieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_phieu));
        return $obj->fetchAll();
    }

    public function minhchung__Has_By_Id_Dot($id_dot) {
        // Nhựt sửa lỗi: Không cho xóa Đợt chấm điểm nếu Phiếu thuộc Đợt đã có Minh chứng.
        $obj = $this->connect->prepare("SELECT COUNT(*) FROM minhchung, phieuchamdiem, lopapdung WHERE minhchung.id_phieu = phieuchamdiem.id_phieu AND phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_dot = ?");
        $obj->execute(array($id_dot));
        return (int)$obj->fetchColumn() > 0;
    }

    public function minhchung__Delete_By_Id_Dot($id_dot) {
        // Nhựt sửa lỗi: Khi cho phép xóa Đợt thì phải xóa Minh chứng thuộc các Phiếu của Đợt trước để tránh dữ liệu mồ côi.
        $obj = $this->connect->prepare("DELETE minhchung FROM minhchung, phieuchamdiem, lopapdung WHERE minhchung.id_phieu = phieuchamdiem.id_phieu AND phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_dot = ?");
        $obj->execute(array($id_dot));
        return $obj->rowCount();
    }

}
?>
