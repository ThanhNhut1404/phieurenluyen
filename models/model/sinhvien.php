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
        $rowCount = $obj->rowCount();
        
        // Đồng bộ email sang bảng taikhoan
        $obj_tk = $this->connect->prepare("UPDATE taikhoan SET email=? WHERE id_nguoi_dung=? AND id_phan_nhom=3");
        $obj_tk->execute(array($email, $id_sinh_vien));
        
        return $rowCount;
    }
    

    public function sinhvien__Delete($id_sinh_vien) {
        // 1. Delete associated user account (id_phan_nhom = 3 is Student)
        $stmt_tk = $this->connect->prepare("DELETE FROM taikhoan WHERE id_nguoi_dung = ? AND id_phan_nhom = 3");
        $stmt_tk->execute(array($id_sinh_vien));

        // 2. Delete associated grading sheets
        $stmt_pcd = $this->connect->prepare("DELETE FROM phieuchamdiem WHERE id_sinh_vien = ?");
        $stmt_pcd->execute(array($id_sinh_vien));

        // 3. Delete associated classification results
        $stmt_kq = $this->connect->prepare("DELETE FROM ketquaxeploai WHERE id_sinh_vien = ?");
        $stmt_kq->execute(array($id_sinh_vien));

        // 4. Delete student record
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
    // Quân sửa: Sửa lại các hàm Get theo kq_lt_bt bị lỗi NULL || "" và bổ sung các hàm lọc trạng thái
    public function sinhvien__Get_All_In_Lop($id_dot, $id_lop_hoc) {
        $obj = $this->connect->prepare("SELECT sinhvien.* FROM sinhvien, phieuchamdiem, lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ? AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc));
        return $obj->fetchAll();
    }

    public function sinhvien__Get_Chua_Tu_Cham($id_dot, $id_lop_hoc) {
        $obj = $this->connect->prepare("SELECT sinhvien.* FROM sinhvien, phieuchamdiem, lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ? AND (phieuchamdiem.kq_sv IS NULL OR phieuchamdiem.kq_sv = '') AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc));
        return $obj->fetchAll();
    }

    public function sinhvien__Get_Da_Tu_Cham($id_dot, $id_lop_hoc) {
        $obj = $this->connect->prepare("SELECT sinhvien.* FROM sinhvien, phieuchamdiem, lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ? AND phieuchamdiem.kq_sv IS NOT NULL AND phieuchamdiem.kq_sv != '' AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc));
        return $obj->fetchAll();
    }

    public function sinhvien__Get_By_Id_Lop_Hoc_Kq_LTBT($id_dot, $id_lop_hoc, $kq_lt_bt) {
       if($kq_lt_bt == -1){ // Chưa chấm
        $obj = $this->connect->prepare("SELECT sinhvien.* FROM sinhvien, phieuchamdiem, lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ? AND (phieuchamdiem.kq_lt_bt IS NULL OR phieuchamdiem.kq_lt_bt = '') AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc));
        return $obj->fetchAll();
       } else { // Đã chấm
        $obj = $this->connect->prepare("SELECT sinhvien.* FROM sinhvien, phieuchamdiem, lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ? AND phieuchamdiem.kq_lt_bt IS NOT NULL AND phieuchamdiem.kq_lt_bt != '' AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc));
        return $obj->fetchAll();
       }
    }

    public function sinhvien__Get_By_Id_Lop_Hoc_Kq_BTDK($id_dot, $id_lop_hoc, $kq_btdk) {
        if($kq_btdk == -1){
         $obj = $this->connect->prepare("SELECT sinhvien.* FROM sinhvien, phieuchamdiem, lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ? AND (phieuchamdiem.kq_btdk IS NULL OR phieuchamdiem.kq_btdk = '') AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
         $obj->setFetchMode(PDO::FETCH_OBJ);
         $obj->execute(array($id_dot, $id_lop_hoc));
         return $obj->fetchAll();
        } else {
         $obj = $this->connect->prepare("SELECT sinhvien.* FROM sinhvien, phieuchamdiem, lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ? AND phieuchamdiem.kq_btdk IS NOT NULL AND phieuchamdiem.kq_btdk != '' AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
         $obj->setFetchMode(PDO::FETCH_OBJ);
         $obj->execute(array($id_dot, $id_lop_hoc));
         return $obj->fetchAll();
        }
     }

    // Quân sửa: Lọc cho Bí thư đoàn khoa
    public function sinhvien__Get_Chua_LT_Cham($id_dot, $id_lop_hoc) {
        $obj = $this->connect->prepare("SELECT sinhvien.* FROM sinhvien, phieuchamdiem, lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ? AND (phieuchamdiem.kq_lt_bt IS NULL OR phieuchamdiem.kq_lt_bt = '') AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc));
        return $obj->fetchAll();
    }

    public function sinhvien__Get_Da_LT_Cham($id_dot, $id_lop_hoc) {
        $obj = $this->connect->prepare("SELECT sinhvien.* FROM sinhvien, phieuchamdiem, lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ? AND phieuchamdiem.kq_lt_bt IS NOT NULL AND phieuchamdiem.kq_lt_bt != '' AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc));
        return $obj->fetchAll();
    }

    public function sinhvien__Get_Chua_BTDK_Cham($id_dot, $id_lop_hoc) {
        $obj = $this->connect->prepare("SELECT sinhvien.* FROM sinhvien, phieuchamdiem, lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ? AND phieuchamdiem.kq_lt_bt IS NOT NULL AND phieuchamdiem.kq_lt_bt != '' AND (phieuchamdiem.kq_btdk IS NULL OR phieuchamdiem.kq_btdk = '') AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc));
        return $obj->fetchAll();
    }

    public function sinhvien__Get_Da_BTDK_Cham($id_dot, $id_lop_hoc) {
        $obj = $this->connect->prepare("SELECT sinhvien.* FROM sinhvien, phieuchamdiem, lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ? AND phieuchamdiem.kq_lt_bt IS NOT NULL AND phieuchamdiem.kq_lt_bt != '' AND phieuchamdiem.kq_btdk IS NOT NULL AND phieuchamdiem.kq_btdk != '' AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc));
        return $obj->fetchAll();
    }

     public function sinhvien__Get_By_Id_Lop_Hoc_Kq_CVHT($id_dot, $id_lop_hoc, $kq_gv) {
        if($kq_gv == -1){
         $obj = $this->connect->prepare("SELECT sinhvien.* FROM sinhvien, phieuchamdiem, lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ? AND (phieuchamdiem.kq_gv IS NULL OR phieuchamdiem.kq_gv = '') AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
         $obj->setFetchMode(PDO::FETCH_OBJ);
         $obj->execute(array($id_dot, $id_lop_hoc));
         return $obj->fetchAll();
        } else {
         $obj = $this->connect->prepare("SELECT sinhvien.* FROM sinhvien, phieuchamdiem, lopapdung WHERE lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ? AND phieuchamdiem.kq_gv IS NOT NULL AND phieuchamdiem.kq_gv != '' AND phieuchamdiem.id_sinh_vien = sinhvien.id_sinh_vien");
         $obj->setFetchMode(PDO::FETCH_OBJ);
         $obj->execute(array($id_dot, $id_lop_hoc));
         return $obj->fetchAll();
        }
     }

    public function sinhvien__Exists_Ma_Sinh_Vien($ma_sinh_vien, $exclude_id = null) {
        if ($exclude_id !== null) {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM sinhvien WHERE ma_sinh_vien = ? AND id_sinh_vien != ?");
            $obj->execute(array($ma_sinh_vien, $exclude_id));
        } else {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM sinhvien WHERE ma_sinh_vien = ?");
            $obj->execute(array($ma_sinh_vien));
        }
        return $obj->fetchColumn() > 0;
    }

    public function sinhvien__Exists_Email($email, $exclude_id = null) {
        if ($exclude_id !== null) {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM sinhvien WHERE email = ? AND id_sinh_vien != ?");
            $obj->execute(array($email, $exclude_id));
        } else {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM sinhvien WHERE email = ?");
            $obj->execute(array($email));
        }
        return $obj->fetchColumn() > 0;
    }

    public function sinhvien__Get_By_Email($email) {
        $obj = $this->connect->prepare("SELECT * FROM sinhvien WHERE email = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($email));
        return $obj->fetch();
    }
}
?>