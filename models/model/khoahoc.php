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

class khoahoc extends Database {

    // Nhựt sửa lỗi: chuẩn hóa khoảng trắng tên khóa học trước khi lưu và so trùng.
    public function khoahoc__Normalize_Name($ten_khoa_hoc) {
        $ten_khoa_hoc = trim((string)$ten_khoa_hoc);
        $ten_khoa_hoc = preg_replace('/\s+/u', ' ', $ten_khoa_hoc);
        return $ten_khoa_hoc === null ? '' : $ten_khoa_hoc;
    }

    public function khoahoc__Get_All() {
        // Nhựt sửa lỗi: bỏ SELECT * và thêm ORDER BY để danh sách ổn định.
        $obj = $this->connect->prepare("
            SELECT id_khoa_hoc, ten_khoa_hoc, nam_nhap_hoc, he_dao_tao, ghi_chu
            FROM khoahoc
            ORDER BY CAST(nam_nhap_hoc AS UNSIGNED) DESC, ten_khoa_hoc ASC, id_khoa_hoc DESC
        ");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }

    public function khoahoc__Add($ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO khoahoc(ten_khoa_hoc, nam_nhap_hoc, he_dao_tao, ghi_chu) VALUES (?,?,?,?)");
        $obj->execute(array($ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu));
        return $obj->rowCount();
    }

    public function khoahoc__Update($id_khoa_hoc, $ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE khoahoc SET ten_khoa_hoc=?, nam_nhap_hoc=?, he_dao_tao=?, ghi_chu=? WHERE id_khoa_hoc=?");
        // Nhựt sửa lỗi: trả về kết quả execute để update không đổi dữ liệu vẫn là hợp lệ.
        return $obj->execute(array($ten_khoa_hoc, $nam_nhap_hoc, $he_dao_tao, $ghi_chu, $id_khoa_hoc));
    }

    public function khoahoc__Name_Exists($ten_khoa_hoc, $exclude_id_khoa_hoc = null) {
        // Nhựt sửa lỗi: kiểm tra trùng tên khóa học không phân biệt hoa thường theo collation.
        $ten_khoa_hoc = $this->khoahoc__Normalize_Name($ten_khoa_hoc);
        if ($exclude_id_khoa_hoc === null) {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM khoahoc WHERE ten_khoa_hoc COLLATE utf8mb4_general_ci = ?");
            $obj->execute(array($ten_khoa_hoc));
        } else {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM khoahoc WHERE ten_khoa_hoc COLLATE utf8mb4_general_ci = ? AND id_khoa_hoc != ?");
            $obj->execute(array($ten_khoa_hoc, $exclude_id_khoa_hoc));
        }
        return (int)$obj->fetchColumn() > 0;
    }

    public function khoahoc__Has_Related_Data($id_khoa_hoc) {
        // Nhựt sửa lỗi: chặn xóa khóa học đang được lớp học sử dụng.
        $checks = array(
            "SELECT COUNT(*) FROM lophoc WHERE id_khoa_hoc = ?",
        );
        foreach ($checks as $sql) {
            $obj = $this->connect->prepare($sql);
            $obj->execute(array($id_khoa_hoc));
            if ((int)$obj->fetchColumn() > 0) {
                return true;
            }
        }
        return false;
    }

    public function khoahoc__Delete($id_khoa_hoc) {
        $obj = $this->connect->prepare("DELETE FROM khoahoc WHERE id_khoa_hoc = ?");
        $obj->execute(array($id_khoa_hoc));
        return $obj->rowCount();
    }

    public function khoahoc__Get_By_Id($id_khoa_hoc) {
        // Nhựt sửa lỗi: bỏ SELECT * và chỉ lấy cột cần dùng.
        $obj = $this->connect->prepare("SELECT id_khoa_hoc, ten_khoa_hoc, nam_nhap_hoc, he_dao_tao, ghi_chu FROM khoahoc WHERE id_khoa_hoc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoa_hoc));
        return $obj->fetch();
    }

    public function khoahoc__Lock_By_Id($id_khoa_hoc) {
        // Nhựt sửa lỗi: khóa bản ghi khi sửa/xóa để tránh thao tác đồng thời làm lệch dữ liệu.
        $obj = $this->connect->prepare("SELECT id_khoa_hoc FROM khoahoc WHERE id_khoa_hoc = ? FOR UPDATE");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoa_hoc));
        return $obj->fetch();
    }

}
?>
