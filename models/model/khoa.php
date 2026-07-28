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

class khoa extends Database {

    // Nhựt sửa lỗi: chuẩn hóa tên khoa để so trùng ổn định hơn.
    public function khoa__Normalize_Name($ten_khoa) {
        $ten_khoa = trim((string)$ten_khoa);
        $ten_khoa = preg_replace('/\s+/u', ' ', $ten_khoa);
        return $ten_khoa ?? '';
    }

    public function khoa__Get_All() {
        // Nhựt sửa lỗi: chỉ lấy cột cần dùng và sắp xếp dữ liệu theo thứ tự xác định.
        $obj = $this->connect->prepare("SELECT id_khoa, ten_khoa, ghi_chu FROM khoa ORDER BY id_khoa DESC");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function khoa__Add($ten_khoa, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO khoa(ten_khoa, ghi_chu) VALUES (?,?)");
        $obj->execute(array($ten_khoa, $ghi_chu));
        return $obj->rowCount();
    }

    public function khoa__Update($id_khoa, $ten_khoa, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE khoa SET ten_khoa=?, ghi_chu=? WHERE id_khoa=?");
        // Nhựt sửa lỗi: trả về trạng thái execute để cập nhật không đổi dữ liệu vẫn được xem là thành công.
        return $obj->execute(array($ten_khoa, $ghi_chu, $id_khoa));
    }

    // Nhựt sửa lỗi: kiểm tra trùng tên khoa khi thêm/cập nhật.
    public function khoa__Name_Exists($ten_khoa, $exclude_id_khoa = null) {
        $ten_khoa = $this->khoa__Normalize_Name($ten_khoa);
        if ($exclude_id_khoa === null) {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM khoa WHERE ten_khoa = ?");
            $obj->execute(array($ten_khoa));
        } else {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM khoa WHERE ten_khoa = ? AND id_khoa != ?");
            $obj->execute(array($ten_khoa, $exclude_id_khoa));
        }
        return $obj->fetchColumn() > 0;
    }

    // Nhựt sửa lỗi: chặn xóa khoa khi còn dữ liệu liên quan để tránh mồ côi ngành học/bí thư đoàn khoa.
    public function khoa__Has_Related_Data($id_khoa) {
        $obj = $this->connect->prepare("
            SELECT
                (SELECT COUNT(*) FROM nganhhoc WHERE id_khoa = ?) +
                (SELECT COUNT(*) FROM bithudoankhoa WHERE id_khoa = ?)
        ");
        $obj->execute(array($id_khoa, $id_khoa));
        return $obj->fetchColumn() > 0;
    }
    

    public function khoa__Delete($id_khoa) {
        $obj = $this->connect->prepare("DELETE FROM khoa WHERE id_khoa = ?");
        $obj->execute(array($id_khoa));
        return $obj->rowCount();
    }

  
    public function khoa__Get_By_Id($id_khoa) {
        // Nhựt sửa lỗi: chỉ lấy cột cần dùng cho form sửa.
        $obj = $this->connect->prepare("SELECT id_khoa, ten_khoa, ghi_chu FROM khoa WHERE id_khoa = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoa));
        return $obj->fetch();
    }

    // Nhựt sửa lỗi: khóa bản ghi khoa khi cập nhật/xóa để giảm race condition.
    public function khoa__Lock_By_Id($id_khoa) {
        $obj = $this->connect->prepare("SELECT id_khoa FROM khoa WHERE id_khoa = ? FOR UPDATE");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoa));
        return $obj->fetch();
    }

}
?>
