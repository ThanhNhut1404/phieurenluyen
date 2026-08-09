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

class dieu extends Database {

    public function dieu__Get_All() {
        // Nhựt sửa lỗi: Danh sách Điều phải hiển thị theo thứ tự nghiệp vụ, không phụ thuộc id_dieu.
        // quân sửa: Chỉ hiển thị các Điều chưa bị xoá mềm
        $obj = $this->connect->prepare("SELECT * FROM dieu WHERE is_deleted = 0 ORDER BY thu_tu ASC, id_dieu ASC");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function dieu__Add($ten_dieu, $ghi_chu, $thu_tu) {
        $obj = $this->connect->prepare("INSERT INTO dieu(ten_dieu, ghi_chu, thu_tu) VALUES (?,?,?)");
        $obj->execute(array($ten_dieu, $ghi_chu, $thu_tu));
        return $obj->rowCount();
    }

    // Nhựt sửa lỗi: Dùng chung cho Add/Update để kiểm tra tên Điều đã tồn tại sau khi trim.
    public function dieu__Get_By_Ten($ten_dieu, $id_dieu = 0) {
        $ten_dieu = trim($ten_dieu);
        if ($id_dieu > 0) {
            $obj = $this->connect->prepare("SELECT * FROM dieu WHERE TRIM(ten_dieu) = ? AND id_dieu != ? LIMIT 1");
            $obj->setFetchMode(PDO::FETCH_OBJ);
            $obj->execute(array($ten_dieu, $id_dieu));
        } else {
            $obj = $this->connect->prepare("SELECT * FROM dieu WHERE TRIM(ten_dieu) = ? LIMIT 1");
            $obj->setFetchMode(PDO::FETCH_OBJ);
            $obj->execute(array($ten_dieu));
        }
        return $obj->fetch();
    }

    public function dieu__Update($id_dieu, $ten_dieu, $ghi_chu, $thu_tu) {
        $obj = $this->connect->prepare("UPDATE dieu SET ten_dieu=?, ghi_chu=?, thu_tu=? WHERE id_dieu=?");
        $obj->execute(array($ten_dieu, $ghi_chu, $thu_tu, $id_dieu));
        return $obj->rowCount();
    }
    

    // quân sửa: Cập nhật hàm Xoá (Soft Delete kết hợp Hard Delete)
    public function dieu__Delete($id_dieu) {
        // Nếu Điều đã từng được đưa vào Bộ câu hỏi (có lịch sử), thì chỉ xoá mềm
        if ($this->dieu__Is_Used_In_Bocauhoi($id_dieu)) {
            $obj = $this->connect->prepare("UPDATE dieu SET is_deleted = 1 WHERE id_dieu = ?");
            $obj->execute(array($id_dieu));
            return $obj->rowCount();
        } else {
            // Nếu chưa từng có lịch sử, xoá vĩnh viễn cho nhẹ DB
            $obj = $this->connect->prepare("DELETE FROM dieu WHERE id_dieu = ?");
            $obj->execute(array($id_dieu));
            return $obj->rowCount();
        }
    }

  
    public function dieu__Get_By_Id($id_dieu) {
        $obj = $this->connect->prepare("SELECT * FROM dieu WHERE id_dieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dieu));
        return $obj->fetch();
    }

    // quân sửa: Hàm lấy thứ tự lớn nhất
    public function dieu__Get_Max_Thu_Tu() {
        $obj = $this->connect->prepare("SELECT MAX(thu_tu) as max_thu_tu FROM dieu WHERE is_deleted = 0");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        $result = $obj->fetch();
        // Nhựt sửa lỗi: Bảng rỗng hoặc SQL trả về NULL phải xem max thứ tự là 0.
        return ($result && $result->max_thu_tu != null) ? (int)$result->max_thu_tu : 0;
    }

    // quân sửa: Hàm lấy Điều theo thứ tự cụ thể (để check trùng lặp Swap)
    public function dieu__Get_By_Thu_Tu($thu_tu) {
        $obj = $this->connect->prepare("SELECT * FROM dieu WHERE thu_tu = ? AND is_deleted = 0");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($thu_tu));
        return $obj->fetch();
    }

    // quân sửa: Hàm chỉ cập nhật thứ tự
    public function dieu__Update_Thu_Tu($id_dieu, $thu_tu) {
        $obj = $this->connect->prepare("UPDATE dieu SET thu_tu=? WHERE id_dieu=?");
        $obj->execute(array($thu_tu, $id_dieu));
        return $obj->rowCount();
    }

    // Nhựt sửa lỗi: Sau khi xóa một Điều thì dồn thứ tự của các Điều phía sau xuống 1.
    public function dieu__Giam_Thu_Tu_Sau_Khi_Xoa($thu_tu) {
        $obj = $this->connect->prepare("UPDATE dieu SET thu_tu = thu_tu - 1 WHERE thu_tu > ? AND is_deleted = 0");
        return $obj->execute(array($thu_tu));
    }

    public function dieu__Is_Used_In_Bocauhoi($id_dieu) {
        // Nhựt sửa lỗi: Trước khi xóa phải kiểm tra Điều có đang dùng trong Mẫu phiếu hay không.
        $obj = $this->connect->prepare("SELECT COUNT(*) as total FROM bocauhoi WHERE id_dieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dieu));
        $result = $obj->fetch();
        return $result && $result->total > 0;
    }

    public function dieu__Is_Used_In_Khoan($id_dieu) {
        // Nhựt sửa lỗi: Không xóa Điều đang được Khoản tham chiếu để tránh dữ liệu mồ côi.
        // quân sửa: Chỉ tính các Khoản chưa bị xoá mềm
        $obj = $this->connect->prepare("SELECT COUNT(*) as total FROM khoan WHERE id_dieu = ? AND is_deleted = 0");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dieu));
        $result = $obj->fetch();
        return $result && $result->total > 0;
    }

    public function dieu__Check_Thu_Tu_Hop_Le() {
        // Nhựt sửa lỗi: Kiểm tra toàn vẹn thứ tự, không cho trùng/hở/NULL/âm/0 sau thao tác.
        // quân sửa: Chỉ check với is_deleted = 0
        $obj = $this->connect->prepare("SELECT COUNT(*) as total, COUNT(DISTINCT thu_tu) as total_thu_tu, MIN(thu_tu) as min_thu_tu, MAX(thu_tu) as max_thu_tu FROM dieu WHERE is_deleted = 0");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        $result = $obj->fetch();

        if (!$result || $result->total == 0) {
            return true;
        }

        return $result->total == $result->total_thu_tu
            && $result->min_thu_tu == 1
            && $result->max_thu_tu == $result->total;
    }

    public function dieu__Get_By_Id_Mau_Phieu($id_mau_phieu) {
        $obj = $this->connect->prepare("SELECT * FROM dieu WHERE id_mau_phieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_mau_phieu));
        return $obj->fetchAll();
    }

    public function dieu__Get_All_Selected($id_mau_phieu) {
        $obj = $this->connect->prepare("SELECT * FROM dieu, bocauhoi WHERE dieu.id_dieu = bocauhoi.id_dieu AND bocauhoi.id_mau_phieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_mau_phieu));
        return $obj->fetchAll();
    }

    public function dieu__Get_All_Unselected($id_mau_phieu) {
        // quân sửa: Lọc is_deleted = 0
        $obj = $this->connect->prepare("SELECT * FROM dieu WHERE is_deleted = 0 AND id_dieu NOT IN (SELECT id_dieu FROM bocauhoi WHERE id_mau_phieu = ?)");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_mau_phieu));
        return $obj->fetchAll();
    }

    // quân sửa: Kiểm tra Điều có nằm trong Đợt chấm điểm Active không
    public function dieu__Is_In_Active_DotChamDiem($id_dieu) {
        $obj = $this->connect->prepare("
            SELECT COUNT(*) as total 
            FROM bocauhoi b
            JOIN lopapdung l ON b.id_mau_phieu = l.id_mau_phieu
            JOIN dotchamdiem d ON l.id_dot = d.id_dot
            WHERE b.id_dieu = ? 
              AND d.trang_thai = 1 
              AND NOW() >= d.thoi_gian_bat_dau 
              AND NOW() <= d.thoi_gian_ket_thuc
        ");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dieu));
        $result = $obj->fetch();
        return $result && $result->total > 0;
    }

    public function dieu__DeleteWithChildren($id_dieu) {
        // Quân sửa: Thực hiện xóa cascade toàn bộ Khoản, Mục, BoCauHoi và Điều theo yêu cầu mới. Transaction được quản lý ở Controller.
        try {
            // 1 & 2 & 3. Tìm Khoản của Điều và Xóa Mục thuộc các Khoản đó
            $stmtMuc = $this->connect->prepare("DELETE FROM muc WHERE id_khoan IN (SELECT id_khoan FROM khoan WHERE id_dieu = ?)");
            $stmtMuc->execute(array($id_dieu));

            // 4. Xóa Khoản thuộc Điều
            $stmtKhoan = $this->connect->prepare("DELETE FROM khoan WHERE id_dieu = ?");
            $stmtKhoan->execute(array($id_dieu));

            // 5. Xóa quan hệ bocauhoi tham chiếu đến Điều
            $stmtBoCauHoi = $this->connect->prepare("DELETE FROM bocauhoi WHERE id_dieu = ?");
            $stmtBoCauHoi->execute(array($id_dieu));

            // 6. Xóa Điều
            $stmtDieu = $this->connect->prepare("DELETE FROM dieu WHERE id_dieu = ?");
            $stmtDieu->execute(array($id_dieu));

            return $stmtDieu->rowCount() > 0;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>
