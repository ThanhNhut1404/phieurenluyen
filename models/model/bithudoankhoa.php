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

class bithudoankhoa extends Database {

    public function bithudoankhoa__Get_All($trang_thai = 1) {
        $obj = $this->connect->prepare("SELECT * FROM bithudoankhoa WHERE trang_thai = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($trang_thai));
        return $obj->fetchAll();
    }

    public function bithudoankhoa__Get_All_Not_Exits($id_khoa ,$trang_thai = 1) {
        $obj = $this->connect->prepare("SELECT * FROM bithudoankhoa WHERE id_khoa =? AND trang_thai = ? AND id_bi_thu NOT IN (SELECT id_nguoi_dung FROM taikhoan, phannhom WHERE taikhoan.id_phan_nhom = phannhom.id_phan_nhom AND phannhom.cap_bac =?)");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoa, $trang_thai, 3));
        return $obj->fetchAll();
    }
    
    public function bithudoankhoa__Add($ten_bi_thu, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_khoa) {
        $obj = $this->connect->prepare("INSERT INTO bithudoankhoa (ten_bi_thu, gioi_tinh, ngay_sinh, email, so_dien_thoai_1, so_dien_thoai_2, dia_chi_lien_lac, dia_chi_thuong_tru, id_khoa) VALUES (?,?,?,?,?,?,?,?,?)");
        $obj->execute(array($ten_bi_thu, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_khoa));
        return $obj->rowCount();
    }

    public function bithudoankhoa__Update($id_bi_thu, $ten_bi_thu, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_khoa) {
        $obj = $this->connect->prepare("UPDATE bithudoankhoa SET ten_bi_thu=?, gioi_tinh=?, ngay_sinh=?, email=?, so_dien_thoai_1=?, so_dien_thoai_2=?, dia_chi_lien_lac=?, dia_chi_thuong_tru=?, id_khoa=? WHERE id_bi_thu=?");
        $obj->execute(array($ten_bi_thu, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $id_khoa, $id_bi_thu));
        return $obj->rowCount();
    }
    

    public function bithudoankhoa__Delete($id_bi_thu) {
        $obj = $this->connect->prepare("DELETE FROM bithudoankhoa WHERE id_bi_thu = ?");
        $obj->execute(array($id_bi_thu));
        return $obj->rowCount();
    }

  
    public function bithudoankhoa__Get_By_Id($id_bi_thu) {
        $obj = $this->connect->prepare("SELECT * FROM bithudoankhoa WHERE id_bi_thu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_bi_thu));
        return $obj->fetch();
    }

    public function bithudoankhoa__Get_By_Id_Khoa($id_khoa) {
        $obj = $this->connect->prepare("SELECT * FROM bithudoankhoa WHERE id_khoa = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoa));
        return $obj->fetchAll();
    }

    public function bithudoankhoa__Update_Trang_Thai($id_bi_thu, $trang_thai) {
        $obj = $this->connect->prepare("UPDATE bithudoankhoa SET trang_thai=? WHERE id_bi_thu=?");
        $obj->execute(array($trang_thai, $id_bi_thu));
        return $obj->rowCount();
    }
    // quân sửa: Hàm kiểm tra trùng lặp Tên hoặc Email (Dùng cho cả Thêm và Sửa)
    public function bithudoankhoa__Check_Duplicate($ten_bi_thu, $email, $id_bi_thu = 0) {
        if ($id_bi_thu == 0) {
            $obj = $this->connect->prepare("SELECT * FROM bithudoankhoa WHERE ten_bi_thu = ? OR email = ?");
            $obj->execute(array($ten_bi_thu, $email));
        } else {
            $obj = $this->connect->prepare("SELECT * FROM bithudoankhoa WHERE (ten_bi_thu = ? OR email = ?) AND id_bi_thu != ?");
            $obj->execute(array($ten_bi_thu, $email, $id_bi_thu));
        }
        return $obj->rowCount();
    }
}
?>