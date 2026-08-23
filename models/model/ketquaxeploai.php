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

class ketquaxeploai extends Database {

    public function ketquaxeploai__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM ketquaxeploai");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function ketquaxeploai__Add($id_phieu, $id_xep_loai, $id_sinh_vien, $id_lop_hoc, $id_dot, $ma_sinh_vien, $ten_sinh_vien, $ten_lop_hoc, $ket_qua, $xep_loai, $ngay_xep_loai) {
        $obj = $this->connect->prepare("INSERT INTO ketquaxeploai(id_phieu, id_xep_loai, id_sinh_vien, id_lop_hoc, id_dot, ma_sinh_vien, ten_sinh_vien, ten_lop_hoc, ket_qua, xep_loai, ngay_xep_loai) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $obj->execute(array($id_phieu, $id_xep_loai, $id_sinh_vien, $id_lop_hoc, $id_dot, $ma_sinh_vien, $ten_sinh_vien, $ten_lop_hoc, $ket_qua, $xep_loai, $ngay_xep_loai));
        return $obj->rowCount();
    }

    public function ketquaxeploai__Update($id_ket_qua, $id_phieu, $id_xep_loai, $id_sinh_vien, $id_lop_hoc, $id_dot, $ma_sinh_vien, $ten_sinh_vien, $ten_lop_hoc, $ket_qua, $xep_loai, $ngay_xep_loai) {
        // Nhựt sửa lỗi: Update kết quả xếp loại phải theo khóa chính id_ket_qua, không phải id_xep_loai.
        $obj = $this->connect->prepare("UPDATE ketquaxeploai SET id_phieu=?, id_xep_loai=?, id_sinh_vien=?, id_lop_hoc=?, id_dot=?, ma_sinh_vien=?, ten_sinh_vien=?, ten_lop_hoc=?, ket_qua=?,  xep_loai=?, ngay_xep_loai=? WHERE id_ket_qua=?");
        $obj->execute(array($id_phieu, $id_xep_loai, $id_sinh_vien, $id_lop_hoc, $id_dot, $ma_sinh_vien, $ten_sinh_vien, $ten_lop_hoc, $ket_qua, $xep_loai, $ngay_xep_loai, $id_ket_qua));
        return $obj->rowCount();
    }

    public function ketquaxeploai__Exists_By_Id_Phieu($id_phieu) {
        // Nhựt sửa lỗi: Một Phiếu chấm điểm chỉ được có một Kết quả xếp loại.
        $obj = $this->connect->prepare("SELECT COUNT(*) FROM ketquaxeploai WHERE id_phieu = ?");
        $obj->execute(array($id_phieu));
        return (int)$obj->fetchColumn() > 0;
    }
    

    public function ketquaxeploai__Delete($id_ket_qua) {
        $obj = $this->connect->prepare("DELETE FROM ketquaxeploai WHERE id_ket_qua = ?");
        $obj->execute(array($id_ket_qua));
        return $obj->rowCount();
    }

  
    public function ketquaxeploai__Get_By_Id($id_ket_qua) {
        $obj = $this->connect->prepare("SELECT * FROM ketquaxeploai WHERE id_ket_qua = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_ket_qua));
        return $obj->fetch();
    }

    public function ketquaxeploai__Update_Ha_Bac($id_ket_qua, $ket_qua, $xep_loai, $ngay_xep_loai, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE ketquaxeploai SET ket_qua=?,  xep_loai=?, ngay_xep_loai=?, ghi_chu=? WHERE id_ket_qua=?");
        $obj->execute(array($ket_qua, $xep_loai, $ngay_xep_loai, $ghi_chu, $id_ket_qua));
        return $obj->rowCount();
    }

    public function ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot($id_lop_hoc, $id_dot) {
        $obj = $this->connect->prepare("SELECT ketquaxeploai.*, ten_hoc_ky, ten_khoa FROM ketquaxeploai, dotchamdiem, nganhhoc, khoa, lophoc, hocky WHERE dotchamdiem.id_hoc_ky = hocky.id_hoc_ky AND ketquaxeploai.id_dot = dotchamdiem.id_dot AND ketquaxeploai.id_lop_hoc = lophoc.id_lop_hoc AND lophoc.id_nganh_hoc = nganhhoc.id_nganh_hoc AND nganhhoc.id_khoa = khoa.id_khoa AND ketquaxeploai.id_lop_hoc=? AND ketquaxeploai.id_dot = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc, $id_dot));
        return $obj->fetchAll();
    }

    public function ketquaxeploai__Get_By_Id_Phieu($id_lop_hoc, $id_dot, $id_sinh_vien) {
        $obj = $this->connect->prepare("SELECT * FROM ketquaxeploai WHERE id_lop_hoc=? AND id_dot=? AND id_sinh_vien=?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc, $id_dot, $id_sinh_vien));
        return $obj->fetch();
    }

    public function ketquaxeploai__Get_By_Id_Dot($id_dot, $id_lop_hoc) {
        $obj = $this->connect->prepare("SELECT count(*) as sum_so_luong, xep_loai FROM ketquaxeploai WHERE id_dot=? AND id_lop_hoc=? GROUP BY xep_loai");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc));
        return $obj->fetchAll();
    }

    public function ketquaxeploai__Get_By_Id_Dot_All($id_dot, $id_lop_hoc) {
        $obj = $this->connect->prepare("SELECT COUNT(*) as sum FROM ketquaxeploai WHERE id_dot=? AND id_lop_hoc=?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc));
        return $obj->fetch();
    }

    public function ketquaxeploai__Has_By_Id_Dot($id_dot) {
        // Nhựt sửa lỗi: Không cho xóa Đợt chấm điểm nếu đã phát sinh Kết quả xếp loại.
        $obj = $this->connect->prepare("SELECT COUNT(*) FROM ketquaxeploai WHERE id_dot = ?");
        $obj->execute(array($id_dot));
        return (int)$obj->fetchColumn() > 0;
    }

    public function ketquaxeploai__Delete_By_Id_Dot($id_dot) {
        // Nhựt sửa lỗi: Khi cho phép xóa Đợt thì phải xóa Kết quả xếp loại thuộc Đợt trước để tránh dữ liệu mồ côi.
        $obj = $this->connect->prepare("DELETE FROM ketquaxeploai WHERE id_dot = ?");
        $obj->execute(array($id_dot));
        return $obj->rowCount();
    }

    public function ketquaxeploai__Get_By_Id_Sinh_Vien_With_HocKy_NamHoc($id_sinh_vien) {
        $obj = $this->connect->prepare("
            SELECT 
                ketquaxeploai.*, 
                hocky.ten_hoc_ky, hocky.ngay_bat_dau as hocky_ngay_bat_dau,
                namhoc.ten_nam_hoc, namhoc.ngay_bat_dau as namhoc_ngay_bat_dau
            FROM ketquaxeploai
            JOIN dotchamdiem ON ketquaxeploai.id_dot = dotchamdiem.id_dot
            JOIN hocky ON dotchamdiem.id_hoc_ky = hocky.id_hoc_ky
            JOIN namhoc ON hocky.id_nam_hoc = namhoc.id_nam_hoc
            WHERE ketquaxeploai.id_sinh_vien = ?
            ORDER BY namhoc.ngay_bat_dau DESC, hocky.ngay_bat_dau ASC
        ");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_sinh_vien));
        return $obj->fetchAll();
    }
}
?>
