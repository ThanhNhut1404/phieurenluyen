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

class lophoc extends Database {

    // Nhựt sửa lỗi: chuẩn hóa tên lớp học để validate và kiểm tra trùng ổn định hơn.
    public function lophoc__Normalize_Name($ten_lop_hoc) {
        $ten_lop_hoc = trim((string)$ten_lop_hoc);
        $ten_lop_hoc = preg_replace('/\s+/u', ' ', $ten_lop_hoc);
        return $ten_lop_hoc ?? '';
    }

    public function lophoc__Get_All() {
        // Nhựt sửa lỗi: join khoa học/ngành học để tránh query trong vòng lặp và sắp xếp dữ liệu rõ ràng.
        $obj = $this->connect->prepare("
            SELECT
                lophoc.id_lop_hoc,
                lophoc.ten_lop_hoc,
                lophoc.ghi_chu,
                lophoc.id_khoa_hoc,
                lophoc.id_nganh_hoc,
                khoahoc.ten_khoa_hoc,
                nganhhoc.ten_nganh_hoc
            FROM lophoc
            INNER JOIN khoahoc ON lophoc.id_khoa_hoc = khoahoc.id_khoa_hoc
            INNER JOIN nganhhoc ON lophoc.id_nganh_hoc = nganhhoc.id_nganh_hoc
            ORDER BY khoahoc.ten_khoa_hoc ASC, nganhhoc.ten_nganh_hoc ASC, lophoc.ten_lop_hoc ASC, lophoc.id_lop_hoc DESC
        ");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }

    public function lophoc__Add($ten_lop_hoc, $ghi_chu, $id_khoa_hoc, $id_nganh_hoc) {
        $obj = $this->connect->prepare("INSERT INTO lophoc(ten_lop_hoc, ghi_chu, id_khoa_hoc, id_nganh_hoc) VALUES (?,?,?,?)");
        $obj->execute(array($ten_lop_hoc, $ghi_chu, $id_khoa_hoc, $id_nganh_hoc));
        return $obj->rowCount();
    }

    public function lophoc__Update($id_lop_hoc, $ten_lop_hoc, $ghi_chu, $id_khoa_hoc, $id_nganh_hoc) {
        $obj = $this->connect->prepare("UPDATE lophoc SET ten_lop_hoc=?, ghi_chu=?, id_khoa_hoc=?, id_nganh_hoc=? WHERE id_lop_hoc=?");
        // Nhựt sửa lỗi: trả về kết quả execute để cập nhật không đổi dữ liệu vẫn tính là thành công.
        return $obj->execute(array($ten_lop_hoc, $ghi_chu, $id_khoa_hoc, $id_nganh_hoc, $id_lop_hoc));
    }

    // Nhựt sửa lỗi: kiểm tra trùng tên lớp trong cùng khoa học/ngành học khi thêm/cập nhật.
    public function lophoc__Name_Exists($ten_lop_hoc, $id_khoa_hoc, $id_nganh_hoc, $exclude_id_lop_hoc = null) {
        $ten_lop_hoc = $this->lophoc__Normalize_Name($ten_lop_hoc);
        if ($exclude_id_lop_hoc === null) {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM lophoc WHERE ten_lop_hoc COLLATE utf8mb4_general_ci = ? AND id_khoa_hoc = ? AND id_nganh_hoc = ?");
            $obj->execute(array($ten_lop_hoc, $id_khoa_hoc, $id_nganh_hoc));
        } else {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM lophoc WHERE ten_lop_hoc COLLATE utf8mb4_general_ci = ? AND id_khoa_hoc = ? AND id_nganh_hoc = ? AND id_lop_hoc != ?");
            $obj->execute(array($ten_lop_hoc, $id_khoa_hoc, $id_nganh_hoc, $exclude_id_lop_hoc));
        }
        return $obj->fetchColumn() > 0;
    }

    // Nhựt sửa lỗi: kiểm tra lớp học còn dữ liệu liên quan trước khi xóa để tránh mồ côi.
    public function lophoc__Has_Related_Data($id_lop_hoc) {
        $obj = $this->connect->prepare("
            SELECT
                (SELECT COUNT(*) FROM sinhvien WHERE id_lop_hoc = ?) +
                (SELECT COUNT(*) FROM lopapdung WHERE id_lop_hoc = ?) +
                (SELECT COUNT(*) FROM phancong WHERE id_lop_hoc = ?) +
                (SELECT COUNT(*) FROM ketquaxeploai WHERE id_lop_hoc = ?)
        ");
        $obj->execute(array($id_lop_hoc, $id_lop_hoc, $id_lop_hoc, $id_lop_hoc));
        return $obj->fetchColumn() > 0;
    }

    public function lophoc__Delete($id_lop_hoc) {
        $obj = $this->connect->prepare("DELETE FROM lophoc WHERE id_lop_hoc = ?");
        $obj->execute(array($id_lop_hoc));
        return $obj->rowCount();
    }

    public function lophoc__Get_By_Id($id_lop_hoc) {
        // Nhựt sửa lỗi: chỉ lấy cột cần dùng cho form sửa.
        $obj = $this->connect->prepare("SELECT id_lop_hoc, ten_lop_hoc, ghi_chu, id_khoa_hoc, id_nganh_hoc FROM lophoc WHERE id_lop_hoc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc));
        return $obj->fetch();
    }

    public function lophoc__Get_By_Id_Khoa_Hoc($id_khoa_hoc) {
        // Nhựt sửa lỗi: chỉ lấy cột cần dùng và sắp xếp lớp học theo tên.
        $obj = $this->connect->prepare("SELECT id_lop_hoc, ten_lop_hoc, ghi_chu, id_khoa_hoc, id_nganh_hoc FROM lophoc WHERE id_khoa_hoc = ? ORDER BY ten_lop_hoc ASC, id_lop_hoc DESC");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoa_hoc));
        return $obj->fetchAll();
    }

    public function lophoc__Get_By_Id_Khoa_Nganh($id_nganh_hoc) {
        // Nhựt sửa lỗi: chỉ lấy cột cần dùng và sắp xếp lớp học theo tên.
        $obj = $this->connect->prepare("SELECT id_lop_hoc, ten_lop_hoc, ghi_chu, id_khoa_hoc, id_nganh_hoc FROM lophoc WHERE id_nganh_hoc = ? ORDER BY ten_lop_hoc ASC, id_lop_hoc DESC");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_nganh_hoc));
        return $obj->fetchAll();
    }

    public function lophoc__Get_By_Id_Khoa($id_khoa) {
        // Nhựt sửa lỗi: lọc lớp theo khoa thông qua ngành học, tránh SELECT * và sắp xếp dữ liệu ổn định.
        $obj = $this->connect->prepare("
            SELECT
                lophoc.id_lop_hoc,
                lophoc.ten_lop_hoc,
                lophoc.ghi_chu,
                lophoc.id_khoa_hoc,
                lophoc.id_nganh_hoc,
                nganhhoc.ten_nganh_hoc,
                khoahoc.ten_khoa_hoc
            FROM lophoc
            INNER JOIN nganhhoc ON lophoc.id_nganh_hoc = nganhhoc.id_nganh_hoc
            INNER JOIN khoahoc ON lophoc.id_khoa_hoc = khoahoc.id_khoa_hoc
            WHERE nganhhoc.id_khoa = ?
            ORDER BY khoahoc.ten_khoa_hoc ASC, nganhhoc.ten_nganh_hoc ASC, lophoc.ten_lop_hoc ASC, lophoc.id_lop_hoc DESC
        ");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_khoa));
        return $obj->fetchAll();
    }

    // Nhựt sửa lỗi: lấy bản ghi cuối theo đúng thứ tự id giảm dần.
    public function lophoc__Get_Last() {
        $obj = $this->connect->prepare("SELECT id_lop_hoc, ten_lop_hoc, ghi_chu, id_khoa_hoc, id_nganh_hoc FROM lophoc ORDER BY id_lop_hoc DESC LIMIT 1");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array());
        return $obj->fetch();
    }

    // Nhựt sửa lỗi: khóa bản ghi lớp học khi cập nhật/xóa để giảm race condition.
    public function lophoc__Lock_By_Id($id_lop_hoc) {
        $obj = $this->connect->prepare("SELECT id_lop_hoc FROM lophoc WHERE id_lop_hoc = ? FOR UPDATE");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc));
        return $obj->fetch();
    }
}
?>
