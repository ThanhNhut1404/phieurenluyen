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

class nganhhoc extends Database {

    // Nhựt sửa lỗi: chuẩn hóa tên ngành học để validate và so trùng ổn định hơn.
    public function nganhhoc__Normalize_Name($ten_nganh_hoc) {
        $ten_nganh_hoc = trim((string)$ten_nganh_hoc);
        $ten_nganh_hoc = preg_replace('/\s+/u', ' ', $ten_nganh_hoc);
        return $ten_nganh_hoc ?? '';
    }

    public function nganhhoc__Get_All() {
        // Nhựt sửa lỗi: join bảng khoa để tránh query trong vòng lặp, chỉ hiển thị ngành có khoa hợp lệ và sắp xếp dữ liệu.
        $obj = $this->connect->prepare("
            SELECT nganhhoc.id_nganh_hoc, nganhhoc.ten_nganh_hoc, nganhhoc.ghi_chu, nganhhoc.id_khoa, khoa.ten_khoa
            FROM nganhhoc
            INNER JOIN khoa ON nganhhoc.id_khoa = khoa.id_khoa
            ORDER BY khoa.ten_khoa ASC, nganhhoc.ten_nganh_hoc ASC, nganhhoc.id_nganh_hoc DESC
        ");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function nganhhoc__Add($ten_nganh_hoc, $ghi_chu, $id_khoa) {
        $obj = $this->connect->prepare("INSERT INTO nganhhoc(ten_nganh_hoc, ghi_chu, id_khoa) VALUES (?,?,?)");
        $obj->execute(array($ten_nganh_hoc, $ghi_chu, $id_khoa));
        return $obj->rowCount();
    }

    public function nganhhoc__Update($id_nganh_hoc, $ten_nganh_hoc, $ghi_chu, $id_khoa) {
        $obj = $this->connect->prepare("UPDATE nganhhoc SET ten_nganh_hoc=?, ghi_chu=?, id_khoa=? WHERE id_nganh_hoc=?");
        // Nhựt sửa lỗi: trả về trạng thái execute để cập nhật không đổi dữ liệu vẫn được xem là thành công.
        return $obj->execute(array($ten_nganh_hoc, $ghi_chu, $id_khoa, $id_nganh_hoc));
    }

    // Nhựt sửa lỗi: kiểm tra trùng tên ngành trong cùng khoa, không phân biệt hoa thường theo collation của DB.
    public function nganhhoc__Name_Exists($ten_nganh_hoc, $id_khoa, $exclude_id_nganh_hoc = null) {
        $ten_nganh_hoc = $this->nganhhoc__Normalize_Name($ten_nganh_hoc);
        if ($exclude_id_nganh_hoc === null) {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM nganhhoc WHERE ten_nganh_hoc COLLATE utf8mb4_general_ci = ? AND id_khoa = ?");
            $obj->execute(array($ten_nganh_hoc, $id_khoa));
        } else {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM nganhhoc WHERE ten_nganh_hoc COLLATE utf8mb4_general_ci = ? AND id_khoa = ? AND id_nganh_hoc != ?");
            $obj->execute(array($ten_nganh_hoc, $id_khoa, $exclude_id_nganh_hoc));
        }
        return $obj->fetchColumn() > 0;
    }

    // Nhựt sửa lỗi: kiểm tra ngành học còn lớp học liên quan trước khi xóa để tránh dữ liệu mồ côi.
    public function nganhhoc__Has_Related_Data($id_nganh_hoc) {
        $obj = $this->connect->prepare("SELECT COUNT(*) FROM lophoc WHERE id_nganh_hoc = ?");
        $obj->execute(array($id_nganh_hoc));
        return $obj->fetchColumn() > 0;
    }
    

    public function nganhhoc__Delete($id_nganh_hoc) {
        $obj = $this->connect->prepare("DELETE FROM nganhhoc WHERE id_nganh_hoc = ?");
        $obj->execute(array($id_nganh_hoc));
        return $obj->rowCount();
    }

  
    public function nganhhoc__Get_By_Id($id_nganh_hoc) {
        // Nhựt sửa lỗi: chỉ lấy các cột cần dùng cho form sửa.
        $obj = $this->connect->prepare("SELECT id_nganh_hoc, ten_nganh_hoc, ghi_chu, id_khoa FROM nganhhoc WHERE id_nganh_hoc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_nganh_hoc));
        return $obj->fetch();
    }

    public function nganhhoc__Get_By_Id_Khoa($id_khoa) {
        // Nhựt sửa lỗi: chỉ lấy cột cần dùng và sắp xếp ngành học theo tên khi lọc theo khoa.
        $obj = $this->connect->prepare("SELECT id_nganh_hoc, ten_nganh_hoc, ghi_chu, id_khoa FROM nganhhoc WHERE id_khoa = ? ORDER BY ten_nganh_hoc ASC, id_nganh_hoc DESC");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoa));
        return $obj->fetchAll();
    }

    // Nhựt sửa lỗi: khóa bản ghi ngành học khi cập nhật/xóa để giảm race condition.
    public function nganhhoc__Lock_By_Id($id_nganh_hoc) {
        $obj = $this->connect->prepare("SELECT id_nganh_hoc FROM nganhhoc WHERE id_nganh_hoc = ? FOR UPDATE");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_nganh_hoc));
        return $obj->fetch();
    }
}
?>
