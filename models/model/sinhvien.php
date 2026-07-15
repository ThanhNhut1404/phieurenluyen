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

class sinhvien extends Database {

    public function sinhvien__Get_All($trang_thai = 1) {
        $obj = $this->connect->prepare("SELECT * FROM sinhvien WHERE trang_thai = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($trang_thai));
        return $obj->fetchAll();
    }
    
    public function sinhvien__Get_All_Not_Exits($id_lop_hoc ,$trang_thai = 1) {
        $obj = $this->connect->prepare("SELECT * FROM sinhvien WHERE id_lop_hoc =? AND trang_thai = ? AND id_sinh_vien NOT IN (SELECT id_nguoi_dung FROM taikhoan, phannhom WHERE taikhoan.id_phan_nhom = phannhom.id_phan_nhom AND cap_bac = ?)");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc, $trang_thai, 2));
        return $obj->fetchAll();
    }

    public function sinhvien__Add($ma_sinh_vien, $ten_sinh_vien, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $chuc_vu, $id_lop_hoc) {
        $obj = $this->connect->prepare("INSERT INTO sinhvien(ma_sinh_vien, ten_sinh_vien, gioi_tinh, ngay_sinh, email, so_dien_thoai_1, so_dien_thoai_2, dia_chi_lien_lac, dia_chi_thuong_tru, chuc_vu, id_lop_hoc) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $obj->execute(array($ma_sinh_vien, $ten_sinh_vien, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $chuc_vu, $id_lop_hoc));
        return $obj->rowCount();
    }

    public function sinhvien__Update($id_sinh_vien, $ma_sinh_vien, $ten_sinh_vien, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $chuc_vu, $id_lop_hoc) {
        $obj = $this->connect->prepare("UPDATE sinhvien SET ma_sinh_vien=?, ten_sinh_vien=?, gioi_tinh=?, ngay_sinh=?, email=?, so_dien_thoai_1=?, so_dien_thoai_2=?, dia_chi_lien_lac=?, dia_chi_thuong_tru=?, chuc_vu=?, id_lop_hoc=? WHERE id_sinh_vien=?");
        $obj->execute(array($ma_sinh_vien, $ten_sinh_vien, $gioi_tinh, $ngay_sinh, $email, $so_dien_thoai_1, $so_dien_thoai_2, $dia_chi_lien_lac, $dia_chi_thuong_tru, $chuc_vu, $id_lop_hoc, $id_sinh_vien));
        return $obj->rowCount();
    }
    

    public function sinhvien__Delete($id_sinh_vien) {
        $obj = $this->connect->prepare("DELETE FROM sinhvien WHERE id_sinh_vien = ?");
        $obj->execute(array($id_sinh_vien));
        return $obj->rowCount();
    }

  
    public function sinhvien__Get_By_Id($id_sinh_vien) {
        $obj = $this->connect->prepare("SELECT * FROM sinhvien WHERE id_sinh_vien = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_sinh_vien));
        return $obj->fetch();
    }

    public function sinhvien__Get_By_Chuc_Vu($chuc_vu) {
        $obj = $this->connect->prepare("SELECT * FROM sinhvien WHERE chuc_vu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($chuc_vu));
        return $obj->fetchAll();
    }
    public function sinhvien__Update_Trang_Thai($id_sinh_vien, $trang_thai) {
        $obj = $this->connect->prepare("UPDATE sinhvien SET trang_thai=? WHERE id_sinh_vien=?");
        $obj->execute(array($trang_thai, $id_sinh_vien));
        return $obj->rowCount();
    }
    
    public function sinhvien__Get_By_Id_Lop_Hoc($id_lop_hoc) {
        $obj = $this->connect->prepare("SELECT * FROM sinhvien WHERE id_lop_hoc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc));
        return $obj->fetchAll();
    }
    public function sinhvien__Get_By_Id_Lop_Hoc_Kq_LTBT($id_dot,$id_lop_hoc, $kq_lt_bt) {
       if($kq_lt_bt == -1){
        $obj = $this->connect->prepare("SELECT * FROM sinhvien,phieuchamdiem,lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot=? AND lopapdung.id_lop_hoc = ? AND kq_lt_bt = ? AND  phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc, NULL || ""));
        return $obj->fetchAll();
       }else{
        $obj = $this->connect->prepare("SELECT * FROM sinhvien,phieuchamdiem,lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot=? AND lopapdung.id_lop_hoc = ? AND kq_lt_bt != ? AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc, NULL || ""));
        return $obj->fetchAll();
       }
    }
    public function sinhvien__Get_By_Id_Lop_Hoc_Kq_BTDK($id_dot,$id_lop_hoc, $kq_btdk) {
        if($kq_btdk == -1){
         $obj = $this->connect->prepare("SELECT * FROM sinhvien,phieuchamdiem,lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot=? AND lopapdung.id_lop_hoc = ? AND kq_btdk = ? AND  phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
         $obj->setFetchMode(PDO::FETCH_OBJ);
         $obj->execute(array($id_dot, $id_lop_hoc, NULL || ""));
         return $obj->fetchAll();
        }else{
         $obj = $this->connect->prepare("SELECT * FROM sinhvien,phieuchamdiem,lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot=? AND lopapdung.id_lop_hoc = ? AND kq_btdk != ? AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
         $obj->setFetchMode(PDO::FETCH_OBJ);
         $obj->execute(array($id_dot, $id_lop_hoc, NULL || ""));
         return $obj->fetchAll();
        }
     }

     public function sinhvien__Get_By_Id_Lop_Hoc_Kq_CVHT($id_dot,$id_lop_hoc, $kq_gv) {
        if($kq_gv == -1){
         $obj = $this->connect->prepare("SELECT * FROM sinhvien,phieuchamdiem,lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot=? AND lopapdung.id_lop_hoc = ? AND kq_gv = ? AND  phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
         $obj->setFetchMode(PDO::FETCH_OBJ);
         $obj->execute(array($id_dot, $id_lop_hoc, NULL || ""));
         return $obj->fetchAll();
        }else{
         $obj = $this->connect->prepare("SELECT * FROM sinhvien,phieuchamdiem,lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot=? AND lopapdung.id_lop_hoc = ? AND kq_gv != ? AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
         $obj->setFetchMode(PDO::FETCH_OBJ);
         $obj->execute(array($id_dot, $id_lop_hoc, NULL || ""));
         return $obj->fetchAll();
        }
     }    }
?>