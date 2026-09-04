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

class thongbao extends Database {

    public function thongbao__Add($tieu_de, $noi_dung, $loai_thong_bao = 1, $nguoi_gui = 'Hệ thống', $nguoi_nhan = 0) {
        $obj = $this->connect->prepare("INSERT INTO thongbao (tieu_de, noi_dung, loai_thong_bao, nguoi_gui, nguoi_nhan) VALUES (?, ?, ?, ?, ?)");
        $obj->execute(array($tieu_de, $noi_dung, $loai_thong_bao, $nguoi_gui, $nguoi_nhan));
        return $obj->rowCount();
    }

    public function thongbao__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM thongbao ORDER BY ngay_tao DESC");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }

    public function thongbao__Get_By_Sinh_Vien($id_sinh_vien) {
        // Lấy tất cả thông báo gửi chung (0) hoặc gửi riêng cho SV này
        // và left join với thongbao_dathu để biết trạng thái đọc
        $obj = $this->connect->prepare("
            SELECT t.*, 
                   IF(d.id_sinh_vien IS NOT NULL, 1, 0) as is_read,
                   d.ngay_doc
            FROM thongbao t
            LEFT JOIN thongbao_dathu d ON t.id_thong_bao = d.id_thong_bao AND d.id_sinh_vien = ?
            WHERE t.nguoi_nhan = 0 OR t.nguoi_nhan = ?
            ORDER BY t.ngay_tao DESC
        ");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_sinh_vien, $id_sinh_vien));
        return $obj->fetchAll();
    }

    public function thongbao__Mark_As_Read($id_thong_bao, $id_sinh_vien) {
        // Kiểm tra xem đã mark chưa
        $check = $this->connect->prepare("SELECT * FROM thongbao_dathu WHERE id_thong_bao = ? AND id_sinh_vien = ?");
        $check->execute(array($id_thong_bao, $id_sinh_vien));
        if ($check->rowCount() == 0) {
            $obj = $this->connect->prepare("INSERT INTO thongbao_dathu (id_thong_bao, id_sinh_vien) VALUES (?, ?)");
            $obj->execute(array($id_thong_bao, $id_sinh_vien));
            return $obj->rowCount();
        }
        return 0;
    }
    
    public function thongbao__Delete($id_thong_bao) {
        $obj = $this->connect->prepare("DELETE FROM thongbao WHERE id_thong_bao = ?");
        $obj->execute(array($id_thong_bao));
        // Xoá các log đã đọc
        $obj2 = $this->connect->prepare("DELETE FROM thongbao_dathu WHERE id_thong_bao = ?");
        $obj2->execute(array($id_thong_bao));
        return $obj->rowCount();
    }
}
?>
