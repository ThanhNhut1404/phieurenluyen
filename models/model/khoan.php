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

class khoan extends Database {

    public function khoan__Get_All() {
        // quân sửa: Chỉ hiển thị Khoản chưa xoá mềm
        $obj = $this->connect->prepare("SELECT * FROM khoan WHERE is_deleted = 0");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    // quân sửa: Bổ sung tham số so_luong_muc vào hàm Thêm
    public function khoan__Add($ten_khoan, $ghi_chu, $can_tren, $thu_tu, $id_dieu, $so_luong_muc) {
        $obj = $this->connect->prepare("INSERT INTO khoan(ten_khoan, ghi_chu, can_tren, thu_tu, id_dieu, so_luong_muc) VALUES (?,?,?,?,?,?)");
        $obj->execute(array($ten_khoan, $ghi_chu, $can_tren, $thu_tu, $id_dieu, $so_luong_muc));
        return $obj->rowCount();
    }

    // quân sửa: Bổ sung tham số so_luong_muc vào hàm Sửa
    public function khoan__Update($id_khoan, $ten_khoan, $ghi_chu, $can_tren, $thu_tu, $id_dieu, $so_luong_muc) {
        $obj = $this->connect->prepare("UPDATE khoan SET ten_khoan=?, ghi_chu=?, can_tren=?, thu_tu=?, id_dieu=?, so_luong_muc=? WHERE id_khoan=?");
        return $obj->execute(array($ten_khoan, $ghi_chu, $can_tren, $thu_tu, $id_dieu, $so_luong_muc, $id_khoan));
    }
    

    public function khoan__Delete($id_khoan) {
        // quân sửa: Soft delete kết hợp Hard delete
        if ($this->khoan__Is_Used_In_Bocauhoi($id_khoan)) {
            $stmtMuc = $this->connect->prepare("UPDATE muc SET is_deleted = 1 WHERE id_khoan = ?");
            $stmtMuc->execute(array($id_khoan));

            $obj = $this->connect->prepare("UPDATE khoan SET is_deleted = 1 WHERE id_khoan = ?");
            $obj->execute(array($id_khoan));
            return $obj->rowCount();
        } else {
            $stmtMuc = $this->connect->prepare("DELETE FROM muc WHERE id_khoan = ?");
            $stmtMuc->execute(array($id_khoan));

            $obj = $this->connect->prepare("DELETE FROM khoan WHERE id_khoan = ?");
            $obj->execute(array($id_khoan));
            return $obj->rowCount();
        }
    }

  
    public function khoan__Get_By_Id($id_khoan) {
        $obj = $this->connect->prepare("SELECT * FROM khoan WHERE id_khoan = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoan));
        return $obj->fetch();
    }

    public function khoan__Get_By_Id_Dieu($id_dieu) {
        // quân sửa: Lọc Khoản chưa bị xoá mềm (dùng cho Admin và xử lý logic)
        $obj = $this->connect->prepare("SELECT * FROM khoan WHERE id_dieu = ? AND is_deleted = 0");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dieu));
        return $obj->fetchAll();
    }

    // quân sửa: Hàm lấy tất cả Khoản (bao gồm cả bị xoá mềm) để hiển thị lịch sử Mẫu phiếu
    public function khoan__Get_All_By_Id_Dieu($id_dieu) {
        $obj = $this->connect->prepare("SELECT * FROM khoan WHERE id_dieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dieu));
        return $obj->fetchAll();
    }

    // quân sửa: Kiểm tra Khoản có nằm trong lịch sử Mẫu phiếu không
    public function khoan__Is_Used_In_Bocauhoi($id_khoan) {
        $obj = $this->connect->prepare("SELECT COUNT(*) as total FROM bocauhoi b JOIN khoan k ON b.id_dieu = k.id_dieu WHERE k.id_khoan = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoan));
        $result = $obj->fetch();
        return $result && $result->total > 0;
    }

    // Quân sửa: Kiểm tra Khoản có nằm trong Mẫu phiếu đang bị khóa Sửa không
    public function khoan__Is_Edit_Locked($id_khoan) {
        $sql1 = "SELECT COUNT(*) FROM bocauhoi b
                 JOIN khoan k ON b.id_dieu = k.id_dieu
                 JOIN lopapdung l ON b.id_mau_phieu = l.id_mau_phieu
                 JOIN phieuchamdiem p ON p.id_lop_ap_dung = l.id_lop_ap_dung
                 WHERE k.id_khoan = ?";
        $stmt1 = $this->connect->prepare($sql1);
        $stmt1->execute(array($id_khoan));
        if ($stmt1->fetchColumn() > 0) return true;

        $sql2 = "SELECT COUNT(*) FROM bocauhoi b
                 JOIN khoan k ON b.id_dieu = k.id_dieu
                 JOIN lopapdung l ON b.id_mau_phieu = l.id_mau_phieu
                 JOIN dotchamdiem d ON d.id_dot = l.id_dot
                 WHERE k.id_khoan = ? AND NOW() >= d.thoi_gian_bat_dau";
        $stmt2 = $this->connect->prepare($sql2);
        $stmt2->execute(array($id_khoan));
        if ($stmt2->fetchColumn() > 0) return true;

        return false;
    }

    // quân sửa: Kiểm tra Khoản có nằm trong Đợt chấm điểm Active không
    public function khoan__Is_In_Active_DotChamDiem($id_khoan) {
        $obj = $this->connect->prepare("
            SELECT COUNT(*) as total 
            FROM bocauhoi b
            JOIN khoan k ON b.id_dieu = k.id_dieu
            JOIN lopapdung l ON b.id_mau_phieu = l.id_mau_phieu
            JOIN dotchamdiem d ON l.id_dot = d.id_dot
            WHERE k.id_khoan = ? 
              AND d.trang_thai = 1 
              AND NOW() >= d.thoi_gian_bat_dau 
              AND NOW() <= d.thoi_gian_ket_thuc
        ");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoan));
        $result = $obj->fetch();
        return $result && $result->total > 0;
    }

}
?>