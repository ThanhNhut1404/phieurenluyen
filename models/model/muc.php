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

class muc extends Database {

    public function muc__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM muc");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    // quân sửa: Bổ sung tham số diem_toi_da
    public function muc__Add($ten_muc, $ghi_chu, $thu_tu, $id_khoan, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $diem_toi_da) {
        $obj = $this->connect->prepare("INSERT INTO muc(ten_muc, ghi_chu, thu_tu, id_khoan, quyen_sv, quyen_lt, quyen_btdk, quyen_gv, diem_toi_da) VALUES (?,?,?,?,?,?,?,?,?)");
        $obj->execute(array($ten_muc, $ghi_chu, $thu_tu, $id_khoan, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $diem_toi_da));
        return $obj->rowCount();
    }

    // quân sửa: Bổ sung tham số diem_toi_da
    public function muc__Update($id_muc, $ten_muc, $ghi_chu, $thu_tu, $id_khoan, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $diem_toi_da) {
        $obj = $this->connect->prepare("UPDATE muc SET ten_muc=?, ghi_chu=?, thu_tu=?, id_khoan=?, quyen_sv=?, quyen_lt=?, quyen_btdk=?, quyen_gv=?, diem_toi_da=? WHERE id_muc=?");
        $obj->execute(array($ten_muc, $ghi_chu, $thu_tu, $id_khoan, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $diem_toi_da, $id_muc));
        return $obj->rowCount();
    }
    

    public function muc__Delete($id_muc) {
        $obj = $this->connect->prepare("DELETE FROM muc WHERE id_muc = ?");
        $obj->execute(array($id_muc));
        return $obj->rowCount();
    }

  
    public function muc__Get_By_Id($id_muc) {
        $obj = $this->connect->prepare("SELECT * FROM muc WHERE id_muc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_muc));
        return $obj->fetch();
    }

    public function muc__Get_By_Id_Khoan($id_khoan) {
        $obj = $this->connect->prepare("SELECT * FROM muc WHERE id_khoan = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoan));
        return $obj->fetchAll();
    }

    // quân sửa: Hàm tính tổng điểm tối đa của các Mục trong một Khoản
    public function muc__Get_Total_Diem_By_Khoan($id_khoan) {
        $obj = $this->connect->prepare("SELECT SUM(diem_toi_da) as total_diem FROM muc WHERE id_khoan = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoan));
        $result = $obj->fetch();
        return $result ? (int)$result->total_diem : 0;
    }

    // quân sửa: Hàm lấy thứ tự lớn nhất trong khoản
    public function muc__Get_Max_Thu_Tu($id_khoan) {
        $obj = $this->connect->prepare("SELECT MAX(thu_tu) as max_thu_tu FROM muc WHERE id_khoan = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoan));
        $result = $obj->fetch();
        return $result ? $result->max_thu_tu : 0;
    }

    // quân sửa: Hàm lấy mục theo thứ tự cụ thể (để check trùng lặp)
    public function muc__Get_By_Thu_Tu($id_khoan, $thu_tu) {
        $obj = $this->connect->prepare("SELECT * FROM muc WHERE id_khoan = ? AND thu_tu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoan, $thu_tu));
        return $obj->fetch();
    }

    // quân sửa: Hàm chỉ cập nhật thứ tự (dùng để swap)
    public function muc__Update_Thu_Tu($id_muc, $thu_tu) {
        $obj = $this->connect->prepare("UPDATE muc SET thu_tu=? WHERE id_muc=?");
        $obj->execute(array($thu_tu, $id_muc));
        return $obj->rowCount();
    }
}
?>