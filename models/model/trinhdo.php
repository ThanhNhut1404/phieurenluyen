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

class trinhdo extends Database {

    // Nhựt sửa lỗi: chuẩn hóa khoảng trắng tên trình độ trước khi lưu và so trùng.
    public function trinhdo__Normalize_Name($ten_trinh_do) {
        $ten_trinh_do = trim((string)$ten_trinh_do);
        $ten_trinh_do = preg_replace('/\s+/u', ' ', $ten_trinh_do);
        return $ten_trinh_do === null ? '' : $ten_trinh_do;
    }

    public function trinhdo__Get_All() {
        // Nhựt sửa lỗi: bỏ SELECT * và thêm ORDER BY để danh sách ổn định.
        $obj = $this->connect->prepare("SELECT id_trinh_do, ten_trinh_do, ghi_chu FROM trinhdo ORDER BY ten_trinh_do ASC, id_trinh_do DESC");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }

    public function trinhdo__Add($ten_trinh_do, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO trinhdo(ten_trinh_do, ghi_chu) VALUES (?,?)");
        $obj->execute(array($ten_trinh_do, $ghi_chu));
        return $obj->rowCount();
    }

    public function trinhdo__Update($id_trinh_do, $ten_trinh_do, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE trinhdo SET ten_trinh_do=?, ghi_chu=? WHERE id_trinh_do=?");
        // Nhựt sửa lỗi: trả về execute để update không đổi dữ liệu vẫn được xem là hợp lệ.
        return $obj->execute(array($ten_trinh_do, $ghi_chu, $id_trinh_do));
    }

    public function trinhdo__Name_Exists($ten_trinh_do, $exclude_id_trinh_do = null) {
        // Nhựt sửa lỗi: kiểm tra trùng tên trình độ không phân biệt hoa thường theo collation.
        $ten_trinh_do = $this->trinhdo__Normalize_Name($ten_trinh_do);
        if ($exclude_id_trinh_do === null) {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM trinhdo WHERE ten_trinh_do COLLATE utf8mb4_general_ci = ?");
            $obj->execute(array($ten_trinh_do));
        } else {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM trinhdo WHERE ten_trinh_do COLLATE utf8mb4_general_ci = ? AND id_trinh_do != ?");
            $obj->execute(array($ten_trinh_do, $exclude_id_trinh_do));
        }
        return (int)$obj->fetchColumn() > 0;
    }

    public function trinhdo__Has_Related_Data($id_trinh_do) {
        // Nhựt sửa lỗi: chặn xóa trình độ đang được giảng viên sử dụng.
        $obj = $this->connect->prepare("SELECT COUNT(*) FROM giangvien WHERE id_trinh_do = ?");
        $obj->execute(array($id_trinh_do));
        return (int)$obj->fetchColumn() > 0;
    }

    public function trinhdo__Delete($id_trinh_do) {
        $obj = $this->connect->prepare("DELETE FROM trinhdo WHERE id_trinh_do = ?");
        $obj->execute(array($id_trinh_do));
        return $obj->rowCount();
    }

    public function trinhdo__Get_By_Id($id_trinh_do) {
        // Nhựt sửa lỗi: bỏ SELECT * và chỉ lấy cột cần dùng.
        $obj = $this->connect->prepare("SELECT id_trinh_do, ten_trinh_do, ghi_chu FROM trinhdo WHERE id_trinh_do = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_trinh_do));
        return $obj->fetch();
    }

    public function trinhdo__Lock_By_Id($id_trinh_do) {
        // Nhựt sửa lỗi: khóa bản ghi khi sửa/xóa để tránh thao tác đồng thời làm lệch dữ liệu.
        $obj = $this->connect->prepare("SELECT id_trinh_do FROM trinhdo WHERE id_trinh_do = ? FOR UPDATE");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_trinh_do));
        return $obj->fetch();
    }

}
?>
