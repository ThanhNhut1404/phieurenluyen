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

class namhoc extends Database {

    // Nhựt sửa lỗi: chuẩn hóa khoảng trắng tên năm học trước khi lưu và so trùng.
    public function namhoc__Normalize_Name($ten_nam_hoc) {
        $ten_nam_hoc = trim((string)$ten_nam_hoc);
        $ten_nam_hoc = preg_replace('/\s+/u', ' ', $ten_nam_hoc);
        return $ten_nam_hoc === null ? '' : $ten_nam_hoc;
    }

    public function namhoc__Get_All() {
        // Nhựt sửa lỗi: bỏ SELECT * và thêm ngày bắt đầu/kết thúc để quản lý đúng khoảng năm học.
        $obj = $this->connect->prepare("SELECT id_nam_hoc, ten_nam_hoc, ngay_bat_dau, ngay_ket_thuc, ghi_chu FROM namhoc ORDER BY ngay_bat_dau DESC, id_nam_hoc DESC");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }

    public function namhoc__Add($ten_nam_hoc, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO namhoc(ten_nam_hoc, ngay_bat_dau, ngay_ket_thuc, ghi_chu) VALUES (?,?,?,?)");
        $obj->execute(array($ten_nam_hoc, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu));
        return $obj->rowCount();
    }

    public function namhoc__Update($id_nam_hoc, $ten_nam_hoc, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE namhoc SET ten_nam_hoc=?, ngay_bat_dau=?, ngay_ket_thuc=?, ghi_chu=? WHERE id_nam_hoc=?");
        // Nhựt sửa lỗi: trả về execute để update không đổi dữ liệu vẫn là hợp lệ.
        return $obj->execute(array($ten_nam_hoc, $ngay_bat_dau, $ngay_ket_thuc, $ghi_chu, $id_nam_hoc));
    }

    public function namhoc__Name_Exists($ten_nam_hoc, $exclude_id_nam_hoc = null) {
        // Nhựt sửa lỗi: kiểm tra trùng tên năm học không phân biệt hoa thường theo collation.
        $ten_nam_hoc = $this->namhoc__Normalize_Name($ten_nam_hoc);
        if ($exclude_id_nam_hoc === null) {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM namhoc WHERE ten_nam_hoc COLLATE utf8mb4_general_ci = ?");
            $obj->execute(array($ten_nam_hoc));
        } else {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM namhoc WHERE ten_nam_hoc COLLATE utf8mb4_general_ci = ? AND id_nam_hoc != ?");
            $obj->execute(array($ten_nam_hoc, $exclude_id_nam_hoc));
        }
        return (int)$obj->fetchColumn() > 0;
    }

    public function namhoc__Date_Range_Overlaps($ngay_bat_dau, $ngay_ket_thuc, $exclude_id_nam_hoc = null) {
        // Nhựt sửa lỗi: không cho khoảng thời gian năm học chồng lên năm học khác.
        if ($exclude_id_nam_hoc === null) {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM namhoc WHERE ngay_bat_dau <= ? AND ngay_ket_thuc >= ?");
            $obj->execute(array($ngay_ket_thuc, $ngay_bat_dau));
        } else {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM namhoc WHERE ngay_bat_dau <= ? AND ngay_ket_thuc >= ? AND id_nam_hoc != ?");
            $obj->execute(array($ngay_ket_thuc, $ngay_bat_dau, $exclude_id_nam_hoc));
        }
        return (int)$obj->fetchColumn() > 0;
    }

    public function namhoc__Has_Related_Data($id_nam_hoc) {
        // Nhựt sửa lỗi: chặn xóa năm học đang được học kỳ sử dụng.
        $obj = $this->connect->prepare("SELECT COUNT(*) FROM hocky WHERE id_nam_hoc = ?");
        $obj->execute(array($id_nam_hoc));
        return (int)$obj->fetchColumn() > 0;
    }

    public function namhoc__Delete($id_nam_hoc) {
        $obj = $this->connect->prepare("DELETE FROM namhoc WHERE id_nam_hoc = ?");
        $obj->execute(array($id_nam_hoc));
        return $obj->rowCount();
    }

    public function namhoc__Get_By_Id($id_nam_hoc) {
        // Nhựt sửa lỗi: bỏ SELECT * và chỉ lấy cột cần dùng.
        $obj = $this->connect->prepare("SELECT id_nam_hoc, ten_nam_hoc, ngay_bat_dau, ngay_ket_thuc, ghi_chu FROM namhoc WHERE id_nam_hoc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_nam_hoc));
        return $obj->fetch();
    }

    public function namhoc__Lock_By_Id($id_nam_hoc) {
        // Nhựt sửa lỗi: khóa bản ghi khi sửa/xóa để tránh thao tác đồng thời làm lệch dữ liệu.
        $obj = $this->connect->prepare("SELECT id_nam_hoc FROM namhoc WHERE id_nam_hoc = ? FOR UPDATE");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_nam_hoc));
        return $obj->fetch();
    }
}
?>
