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

class hocky extends Database {

    // Nhựt sửa lỗi: chuẩn hóa khoảng trắng tên học kỳ trước khi lưu và so trùng.
    public function hocky__Normalize_Name($ten_hoc_ky) {
        $ten_hoc_ky = trim((string)$ten_hoc_ky);
        $ten_hoc_ky = preg_replace('/\s+/u', ' ', $ten_hoc_ky);
        return $ten_hoc_ky === null ? '' : $ten_hoc_ky;
    }

    public function hocky__Get_All() {
        // Nhựt sửa lỗi: bỏ SELECT * và join sẵn năm học để tránh query lặp trong vòng lặp.
        $obj = $this->connect->prepare("
            SELECT hocky.id_hoc_ky, hocky.ten_hoc_ky, hocky.ghi_chu, hocky.id_nam_hoc, namhoc.ten_nam_hoc
            FROM hocky
            LEFT JOIN namhoc ON hocky.id_nam_hoc = namhoc.id_nam_hoc
            ORDER BY hocky.id_nam_hoc DESC, hocky.ten_hoc_ky ASC, hocky.id_hoc_ky DESC
        ");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }

    public function hocky__Add($ten_hoc_ky, $ghi_chu, $id_nam_hoc) {
        $obj = $this->connect->prepare("INSERT INTO hocky(ten_hoc_ky, ghi_chu, id_nam_hoc) VALUES (?,?,?)");
        $obj->execute(array($ten_hoc_ky, $ghi_chu, $id_nam_hoc));
        return $obj->rowCount();
    }

    public function hocky__Update($id_hoc_ky, $ten_hoc_ky, $ghi_chu, $id_nam_hoc) {
        $obj = $this->connect->prepare("UPDATE hocky SET ten_hoc_ky=?, ghi_chu=?, id_nam_hoc=? WHERE id_hoc_ky=?");
        // Nhựt sửa lỗi: trả về execute để update không đổi dữ liệu vẫn được xem là hợp lệ.
        return $obj->execute(array($ten_hoc_ky, $ghi_chu, $id_nam_hoc, $id_hoc_ky));
    }

    public function hocky__Delete($id_hoc_ky) {
        $obj = $this->connect->prepare("DELETE FROM hocky WHERE id_hoc_ky = ?");
        $obj->execute(array($id_hoc_ky));
        return $obj->rowCount();
    }

    public function hocky__Get_By_Id($id_hoc_ky) {
        // Nhựt sửa lỗi: lấy luôn tên năm học để form sửa không phải query lại.
        $obj = $this->connect->prepare("
            SELECT hocky.id_hoc_ky, hocky.ten_hoc_ky, hocky.ghi_chu, hocky.id_nam_hoc, namhoc.ten_nam_hoc
            FROM hocky
            LEFT JOIN namhoc ON hocky.id_nam_hoc = namhoc.id_nam_hoc
            WHERE hocky.id_hoc_ky = ?
        ");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_hoc_ky));
        return $obj->fetch();
    }

    public function hocky__Get_By_id_Nam_Hoc($id_nam_hoc) {
        $obj = $this->connect->prepare("
            SELECT hocky.id_hoc_ky, hocky.ten_hoc_ky, hocky.ghi_chu, hocky.id_nam_hoc, namhoc.ten_nam_hoc
            FROM hocky
            LEFT JOIN namhoc ON hocky.id_nam_hoc = namhoc.id_nam_hoc
            WHERE hocky.id_nam_hoc = ?
            ORDER BY hocky.ten_hoc_ky ASC, hocky.id_hoc_ky DESC
        ");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_nam_hoc));
        return $obj->fetchAll();
    }

    public function hocky__Name_Exists($ten_hoc_ky, $id_nam_hoc, $exclude_id_hoc_ky = null) {
        // Nhựt sửa lỗi: kiểm tra trùng tên học kỳ trong cùng năm học không phân biệt hoa thường.
        $ten_hoc_ky = $this->hocky__Normalize_Name($ten_hoc_ky);
        if ($exclude_id_hoc_ky === null) {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM hocky WHERE id_nam_hoc = ? AND ten_hoc_ky COLLATE utf8mb4_general_ci = ?");
            $obj->execute(array($id_nam_hoc, $ten_hoc_ky));
        } else {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM hocky WHERE id_nam_hoc = ? AND ten_hoc_ky COLLATE utf8mb4_general_ci = ? AND id_hoc_ky != ?");
            $obj->execute(array($id_nam_hoc, $ten_hoc_ky, $exclude_id_hoc_ky));
        }
        return (int)$obj->fetchColumn() > 0;
    }

    public function hocky__Has_Related_Data($id_hoc_ky) {
        // Nhựt sửa lỗi: chặn xóa học kỳ đang được đợt chấm điểm sử dụng.
        $obj = $this->connect->prepare("SELECT COUNT(*) FROM dotchamdiem WHERE id_hoc_ky = ?");
        $obj->execute(array($id_hoc_ky));
        return (int)$obj->fetchColumn() > 0;
    }

    public function hocky__Lock_By_Id($id_hoc_ky) {
        // Nhựt sửa lỗi: khóa bản ghi khi sửa/xóa để tránh thao tác đồng thời làm lệch dữ liệu.
        $obj = $this->connect->prepare("SELECT id_hoc_ky FROM hocky WHERE id_hoc_ky = ? FOR UPDATE");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_hoc_ky));
        return $obj->fetch();
    }
}
?>
