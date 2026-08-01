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

class xeploai extends Database {

    public function xeploai__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM xeploai ORDER BY can_tren DESC");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function xeploai__Add($ten_xep_loai, $can_tren, $can_duoi, $ha_bac, $ghi_chu) {
        $obj = $this->connect->prepare("INSERT INTO xeploai(ten_xep_loai, can_tren, can_duoi, ha_bac, ghi_chu) VALUES (?,?,?,?,?)");
        $obj->execute(array($ten_xep_loai, $can_tren, $can_duoi, $ha_bac, $ghi_chu));
        return $obj->rowCount();
    }

    public function xeploai__Update($id_xep_loai, $ten_xep_loai, $can_tren, $can_duoi, $ha_bac, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE xeploai SET ten_xep_loai=?, can_tren=?, can_duoi=?, ha_bac=?, ghi_chu=? WHERE id_xep_loai=?");
        // Nhựt sửa lỗi: Update không đổi dữ liệu vẫn là thao tác hợp lệ, không được dựa vào rowCount() để báo thất bại.
        return $obj->execute(array($ten_xep_loai, $can_tren, $can_duoi, $ha_bac, $ghi_chu, $id_xep_loai));
    }

    public function xeploai__Lock_All() {
        // Nhựt sửa lỗi: Khóa danh sách xếp loại trong transaction để giảm rủi ro thêm/cập nhật đồng thời gây trùng hoặc chồng khoảng điểm.
        $obj = $this->connect->prepare("SELECT id_xep_loai FROM xeploai ORDER BY id_xep_loai FOR UPDATE");
        $obj->execute();
        return $obj->fetchAll();
    }

    public function xeploai__Normalize_Name($ten_xep_loai) {
        // Nhựt sửa lỗi: Chuẩn hóa tên xếp loại trước khi kiểm tra trùng tên.
        return preg_replace('/\s+/', ' ', trim($ten_xep_loai));
    }

    public function xeploai__Check_Name($ten_xep_loai) {
        // Nhựt sửa lỗi: Không cho thêm xếp loại trùng tên sau khi đã chuẩn hóa khoảng trắng.
        $ten_xep_loai = $this->xeploai__Normalize_Name($ten_xep_loai);
        $obj = $this->connect->prepare("SELECT id_xep_loai, ten_xep_loai FROM xeploai");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        $list = $obj->fetchAll();
        foreach ($list as $item) {
            if ($this->xeploai__Normalize_Name($item->ten_xep_loai) == $ten_xep_loai) {
                return $item;
            }
        }
        return false;
    }

    public function xeploai__Check_Name_Update($id_xep_loai, $ten_xep_loai) {
        // Nhựt sửa lỗi: Khi cập nhật được giữ nguyên tên của chính nó nhưng không được trùng xếp loại khác.
        $ten_xep_loai = $this->xeploai__Normalize_Name($ten_xep_loai);
        $obj = $this->connect->prepare("SELECT id_xep_loai, ten_xep_loai FROM xeploai WHERE id_xep_loai != ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_xep_loai));
        $list = $obj->fetchAll();
        foreach ($list as $item) {
            if ($this->xeploai__Normalize_Name($item->ten_xep_loai) == $ten_xep_loai) {
                return $item;
            }
        }
        return false;
    }
    

    public function xeploai__Delete($id_xep_loai) {
        $obj = $this->connect->prepare("DELETE FROM xeploai WHERE id_xep_loai = ?");
        $obj->execute(array($id_xep_loai));
        return $obj->rowCount();
    }

    public function xeploai__Check_Khoang_Diem_Ton_Tai($can_duoi, $can_tren, $id_xep_loai = 0) {
        // Nhựt sửa lỗi: Kiểm tra khoảng điểm chồng nhau, khi update thì loại trừ chính bản ghi đang sửa.
        if ($id_xep_loai > 0) {
            $obj = $this->connect->prepare("SELECT * FROM xeploai WHERE can_duoi <= ? AND can_tren >= ? AND id_xep_loai != ? LIMIT 1");
            $obj->setFetchMode(PDO::FETCH_OBJ);
            $obj->execute(array($can_tren, $can_duoi, $id_xep_loai));
        } else {
            $obj = $this->connect->prepare("SELECT * FROM xeploai WHERE can_duoi <= ? AND can_tren >= ? LIMIT 1");
            $obj->setFetchMode(PDO::FETCH_OBJ);
            $obj->execute(array($can_tren, $can_duoi));
        }
        return $obj->fetch();
    }

    public function xeploai__Is_Used_In_Ketquaxeploai($id_xep_loai) {
        // Nhựt sửa lỗi: Không cho xóa xếp loại đang được dùng trong ketquaxeploai.
        $obj = $this->connect->prepare("SELECT COUNT(*) as total FROM ketquaxeploai WHERE id_xep_loai = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_xep_loai));
        $result = $obj->fetch();
        return $result && $result->total > 0;
    }

    public function xeploai__Check_Du_Khoang_Diem() {
        // Nhựt sửa lỗi: Trước khi tạo kết quả phải đảm bảo các khoảng điểm phủ kín từ 0 đến 100.
        $obj = $this->connect->prepare("SELECT can_duoi, can_tren FROM xeploai ORDER BY can_duoi ASC");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        $list = $obj->fetchAll();

        if (empty($list)) {
            return false;
        }

        $diem_hien_tai = 0;
        foreach ($list as $item) {
            if ($item->can_duoi < 0 || $item->can_tren > 100 || $item->can_duoi > $item->can_tren) {
                return false;
            }
            if ($item->can_duoi < $diem_hien_tai) {
                // Nhựt sửa lỗi: Phòng trường hợp dữ liệu DB bị import/sửa trực tiếp làm các khoảng điểm bị chồng nhau.
                return false;
            }
            if ($item->can_duoi > $diem_hien_tai) {
                return false;
            }
            if ($item->can_tren >= $diem_hien_tai) {
                // Nhựt sửa lỗi: Quy tắc +1 áp dụng vì hệ thống dùng điểm rèn luyện là số nguyên từ 0 đến 100.
                $diem_hien_tai = $item->can_tren + 1;
            }
            if ($diem_hien_tai > 100) {
                return true;
            }
        }

        return $diem_hien_tai > 100;
    }

  
    public function xeploai__Get_By_Id($id_xep_loai) {
        $obj = $this->connect->prepare("SELECT * FROM xeploai WHERE id_xep_loai = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_xep_loai));
        return $obj->fetch();
    }

    public function xeploai__Get_By_Kq($kq) {
        $obj = $this->connect->prepare("SELECT * FROM xeploai WHERE can_duoi<=? AND can_tren >= ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($kq, $kq));
        return $obj->fetch();
    }
}
?>
