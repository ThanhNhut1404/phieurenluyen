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
        // quân sửa: Chỉ lấy Mục chưa bị xoá mềm
        $obj = $this->connect->prepare("SELECT * FROM muc WHERE is_deleted = 0");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    // quân sửa: Bổ sung tham số diem_toi_da và co_minh_chung
    public function muc__Add($ten_muc, $ghi_chu, $thu_tu, $id_khoan, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $diem_toi_da, $co_minh_chung = 0) {
        $obj = $this->connect->prepare("INSERT INTO muc(ten_muc, ghi_chu, thu_tu, id_khoan, quyen_sv, quyen_lt, quyen_btdk, quyen_gv, diem_toi_da, co_minh_chung) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $obj->execute(array($ten_muc, $ghi_chu, $thu_tu, $id_khoan, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $diem_toi_da, $co_minh_chung));
        return $obj->rowCount();
    }

    // quân sửa: Bổ sung tham số diem_toi_da và co_minh_chung
    public function muc__Update($id_muc, $ten_muc, $ghi_chu, $thu_tu, $id_khoan, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $diem_toi_da, $co_minh_chung = 0) {
        $obj = $this->connect->prepare("UPDATE muc SET ten_muc=?, ghi_chu=?, thu_tu=?, id_khoan=?, quyen_sv=?, quyen_lt=?, quyen_btdk=?, quyen_gv=?, diem_toi_da=?, co_minh_chung=? WHERE id_muc=?");
        $obj->execute(array($ten_muc, $ghi_chu, $thu_tu, $id_khoan, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $diem_toi_da, $co_minh_chung, $id_muc));
        return $obj->rowCount();
    }
    

    public function muc__Delete($id_muc) {
        // quân sửa: Soft delete kết hợp Hard delete
        if ($this->muc__Is_Used_In_Bocauhoi($id_muc)) {
            $obj = $this->connect->prepare("UPDATE muc SET is_deleted = 1 WHERE id_muc = ?");
            $obj->execute(array($id_muc));
            return $obj->rowCount();
        } else {
            $obj = $this->connect->prepare("DELETE FROM muc WHERE id_muc = ?");
            $obj->execute(array($id_muc));
            return $obj->rowCount();
        }
    }

  
    public function muc__Get_By_Id($id_muc) {
        $obj = $this->connect->prepare("SELECT * FROM muc WHERE id_muc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_muc));
        return $obj->fetch();
    }

    public function muc__Get_By_Id_Khoan($id_khoan) {
        // quân sửa: Lọc is_deleted = 0 (dùng cho Admin và xử lý logic)
        $obj = $this->connect->prepare("SELECT * FROM muc WHERE id_khoan = ? AND is_deleted = 0");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoan));
        return $obj->fetchAll();
    }

    // quân sửa: Hàm lấy tất cả Mục (bao gồm cả bị xoá mềm) để hiển thị lịch sử Mẫu phiếu
    public function muc__Get_All_By_Id_Khoan($id_khoan) {
        $obj = $this->connect->prepare("SELECT * FROM muc WHERE id_khoan = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoan));
        return $obj->fetchAll();
    }

    // quân sửa: Hàm tính tổng điểm tối đa của các Mục trong một Khoản
    public function muc__Get_Total_Diem_By_Khoan($id_khoan) {
        $obj = $this->connect->prepare("SELECT SUM(diem_toi_da) as total_diem FROM muc WHERE id_khoan = ? AND is_deleted = 0");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoan));
        $result = $obj->fetch();
        return $result ? (int)$result->total_diem : 0;
    }

    // quân sửa: Hàm lấy thứ tự lớn nhất trong khoản
    public function muc__Get_Max_Thu_Tu($id_khoan) {
        $obj = $this->connect->prepare("SELECT MAX(thu_tu) as max_thu_tu FROM muc WHERE id_khoan = ? AND is_deleted = 0");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoan));
        $result = $obj->fetch();
        return $result ? $result->max_thu_tu : 0;
    }

    // quân sửa: Hàm lấy mục theo thứ tự cụ thể (để check trùng lặp)
    public function muc__Get_By_Thu_Tu($id_khoan, $thu_tu) {
        $obj = $this->connect->prepare("SELECT * FROM muc WHERE id_khoan = ? AND thu_tu = ? AND is_deleted = 0");
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

    // quân sửa: Kiểm tra Mục có nằm trong lịch sử Mẫu phiếu không
    public function muc__Is_Used_In_Bocauhoi($id_muc) {
        $obj = $this->connect->prepare("SELECT COUNT(*) as total FROM bocauhoi b JOIN khoan k ON b.id_dieu = k.id_dieu JOIN muc m ON m.id_khoan = k.id_khoan WHERE m.id_muc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_muc));
        $result = $obj->fetch();
        return $result && $result->total > 0;
    }

    // Quân sửa: Kiểm tra Mục có nằm trong Mẫu phiếu đang bị khóa Sửa không
    public function muc__Is_Edit_Locked($id_muc) {
        $sql1 = "SELECT COUNT(*) FROM bocauhoi b
                 JOIN khoan k ON b.id_dieu = k.id_dieu
                 JOIN muc m ON m.id_khoan = k.id_khoan
                 JOIN lopapdung l ON b.id_mau_phieu = l.id_mau_phieu
                 JOIN phieuchamdiem p ON p.id_lop_ap_dung = l.id_lop_ap_dung
                 WHERE m.id_muc = ?";
        $stmt1 = $this->connect->prepare($sql1);
        $stmt1->execute(array($id_muc));
        if ($stmt1->fetchColumn() > 0) return true;

        $sql2 = "SELECT COUNT(*) FROM bocauhoi b
                 JOIN khoan k ON b.id_dieu = k.id_dieu
                 JOIN muc m ON m.id_khoan = k.id_khoan
                 JOIN lopapdung l ON b.id_mau_phieu = l.id_mau_phieu
                 JOIN dotchamdiem d ON d.id_dot = l.id_dot
                 WHERE m.id_muc = ? AND NOW() >= d.thoi_gian_bat_dau";
        $stmt2 = $this->connect->prepare($sql2);
        $stmt2->execute(array($id_muc));
        if ($stmt2->fetchColumn() > 0) return true;

        return false;
    }

    // quân sửa: Kiểm tra Mục có nằm trong Đợt chấm điểm Active không
    public function muc__Is_In_Active_DotChamDiem($id_muc) {
        $obj = $this->connect->prepare("
            SELECT COUNT(*) as total 
            FROM bocauhoi b
            JOIN khoan k ON b.id_dieu = k.id_dieu
            JOIN muc m ON m.id_khoan = k.id_khoan
            JOIN lopapdung l ON b.id_mau_phieu = l.id_mau_phieu
            JOIN dotchamdiem d ON l.id_dot = d.id_dot
            WHERE m.id_muc = ? 
              AND d.trang_thai = 1 
              AND NOW() >= d.thoi_gian_bat_dau 
              AND NOW() <= d.thoi_gian_ket_thuc
        ");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_muc));
        $result = $obj->fetch();
        return $result && $result->total > 0;
    }
}
?>