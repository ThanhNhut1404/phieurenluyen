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

class phieuchamdiem extends Database {

    public function phieuchamdiem__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function phieuchamdiem__Add($id_lop_ap_dung, $id_sinh_vien, $kq_sv, $kq_lt_bt, $kq_btdk, $ngay_thuc_hien) {
        $obj = $this->connect->prepare("INSERT INTO phieuchamdiem(id_lop_ap_dung, id_sinh_vien, kq_sv, kq_lt_bt, kq_btdk, ngay_thuc_hien) VALUES (?,?,?,?,?,?)");
        $obj->execute(array($id_lop_ap_dung, $id_sinh_vien, $kq_sv, $kq_lt_bt, $kq_btdk, $ngay_thuc_hien));
        return $obj->rowCount();
    }

    public function phieuchamdiem__Update($id_phieu, $id_sinh_vien, $kq_sv, $kq_lt_bt, $kq_btdk, $ngay_thuc_hien) {
        $obj = $this->connect->prepare("UPDATE phieuchamdiem SET id_lop_ap_dung=?, id_sinh_vien=?, kq_sv=?, kq_lt_bt=?, kq_btdk=?, kq_gv=?, ngay_thuc_hien=? WHERE id_phieu=?");
        $obj->execute(array($id_sinh_vien, $kq_sv, $kq_lt_bt, $kq_btdk, $ngay_thuc_hien, $id_phieu));
        return $obj->rowCount();
    }
    

    public function phieuchamdiem__Delete($id_phieu) {
        $obj = $this->connect->prepare("DELETE FROM phieuchamdiem WHERE id_phieu = ?");
        $obj->execute(array($id_phieu));
        return $obj->rowCount();
    }

  
    public function phieuchamdiem__Get_By_Id($id_phieu) {
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem WHERE id_phieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_phieu));
        return $obj->fetch();
    }

    
    public function phieuchamdiem__Get_By_Id_Sinh_Vien($id_sinh_vien, $id_dot) {
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem, lopapdung WHERE phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND id_sinh_vien = ? AND id_dot = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_sinh_vien, $id_dot));
        return $obj->fetch();
    }

    public function phieuchamdiem__Get_By_Id_Ap_Dung($id_lop_ap_dung) {
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem WHERE id_lop_ap_dung = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_ap_dung));
        return $obj->fetchAll();
    }

    public function phieuchamdiem__Get_By_Id_Dot_And_Id_Lop_Hoc($id_dot, $id_lop_hoc) {
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem, lopapdung  WHERE   phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung  AND id_dot = ? AND lopapdung.id_lop_hoc = ? GROUP BY phieuchamdiem.id_phieu");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc));
        return $obj->fetchAll();
    }

    public function phieuchamdiem__Get_Ket_Qua($req) {
        $kq = [];
        $res = explode('|', $req);
        foreach ($res as $item){
            $kq[] = $item;
        }
        return $kq;
    }
    public function phieuchamdiem__Get_Sum_Ket_Qua($req) {
        $kq = 0;
        foreach ($req as $item){
            $kq += intval($item);
        }
        return $kq;
    }
    
    public function phieuchamdiem__Update_Kq_Sv($id_phieu, $kq_sv) {
        $obj = $this->connect->prepare("UPDATE phieuchamdiem SET kq_sv=?, ngay_thuc_hien=? WHERE id_phieu=?");
        $obj->execute(array($kq_sv, date('Y-m-d'), $id_phieu));
        return $obj->rowCount();
    }
    public function phieuchamdiem__Update_Kq_LTBT($id_phieu, $kq_lt_bt) {
        $obj = $this->connect->prepare("UPDATE phieuchamdiem SET kq_lt_bt=?, ngay_thuc_hien=? WHERE id_phieu=?");
        $obj->execute(array($kq_lt_bt, date('Y-m-d'), $id_phieu));
        return $obj->rowCount();
    }

    public function phieuchamdiem__Update_Kq_BTDK($id_phieu, $kq_btdk) {
        $obj = $this->connect->prepare("UPDATE phieuchamdiem SET kq_btdk=?, ngay_thuc_hien=? WHERE id_phieu=?");
        $obj->execute(array($kq_btdk, date('Y-m-d'), $id_phieu));
        return $obj->rowCount();
    }

    public function phieuchamdiem__Update_Kq_Gv($id_phieu, $kq_gv) {
        $obj = $this->connect->prepare("UPDATE phieuchamdiem SET kq_gv=?, ngay_thuc_hien=? WHERE id_phieu=?");
        $obj->execute(array($kq_gv, date('Y-m-d'), $id_phieu));
        return $obj->rowCount();
    }

    public function phieuchamdiem__Get_By_Id_Lop($id_lop_hoc, $id_dot) {
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem, lopapdung WHERE phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_lop_ap_dung = ? AND id_dot = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc, $id_dot));
        return $obj->fetchAll();
    }

    public function phieuchamdiem__Get_By_Id_Lop_Da_Cham($id_lop_hoc, $id_dot) {
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem, lopapdung WHERE phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_lop_ap_dung = ? AND id_dot = ? AND kq_gv !=?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc, $id_dot, NULL || ""));
        return $obj->fetchAll();
    }

    public function phieuchamdiem__Get_By_Id_Lop_Chua_Cham($id_lop_hoc, $id_dot) {
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem, lopapdung WHERE phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_lop_ap_dung = ? AND id_dot = ? AND kq_gv =?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc, $id_dot, NULL || ""));
        return $obj->fetchAll();
    }
    public function phieuchamdiem__Get_By_Id_Lop_All($id_lop_hoc, $id_dot) {
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem, lopapdung WHERE phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_lop_ap_dung = ? AND id_dot = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc, $id_dot));
        return $obj->fetchAll();
    }

    
}
?>