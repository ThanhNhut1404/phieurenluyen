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

class dotchamdiem extends Database {

    public function dotchamdiem__Get_All() {
        // Nhựt sửa lỗi: Không dùng SELECT * khi join nhiều bảng có cột trùng tên như ghi_chu làm sai dữ liệu Đợt.
        $obj = $this->connect->prepare("SELECT dotchamdiem.id_dot, dotchamdiem.ten_dot, dotchamdiem.ghi_chu, dotchamdiem.thoi_gian_bat_dau, dotchamdiem.thoi_gian_ket_thuc, dotchamdiem.id_hoc_ky, dotchamdiem.trang_thai, hocky.ten_hoc_ky, hocky.ngay_bat_dau AS ngay_hoc_ky_bat_dau, hocky.ngay_ket_thuc AS ngay_hoc_ky_ket_thuc, hocky.id_nam_hoc, namhoc.ten_nam_hoc, namhoc.ngay_bat_dau AS ngay_nam_hoc_bat_dau, namhoc.ngay_ket_thuc AS ngay_nam_hoc_ket_thuc FROM dotchamdiem, hocky, namhoc WHERE dotchamdiem.id_hoc_ky = hocky.id_hoc_ky AND hocky.id_nam_hoc = namhoc.id_nam_hoc ORDER BY dotchamdiem.id_dot DESC");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function dotchamdiem__Add($ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_hoc_ky) {
        $obj = $this->connect->prepare("INSERT INTO dotchamdiem(ten_dot, ghi_chu, thoi_gian_bat_dau, thoi_gian_ket_thuc, id_hoc_ky, trang_thai) VALUES (?,?,?,?,?, ?)");
        $obj->execute(array($ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_hoc_ky, 1));
        return $this->connect->lastInsertId();
    }

    public function dotchamdiem__Update($id_dot, $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_hoc_ky) {
        $obj = $this->connect->prepare("UPDATE dotchamdiem SET ten_dot=?, ghi_chu=?, thoi_gian_bat_dau=?, thoi_gian_ket_thuc=?, id_hoc_ky=? WHERE id_dot=?");
        // Nhựt sửa lỗi: Update không đổi dữ liệu vẫn là thao tác hợp lệ, không được dựa vào rowCount() để báo thất bại.
        return $obj->execute(array($ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_hoc_ky, $id_dot));
    }
    

    public function dotchamdiem__Delete($id_dot) {
        $obj = $this->connect->prepare("DELETE FROM dotchamdiem WHERE id_dot= ?");
        $obj->execute(array($id_dot));
        return $obj->rowCount();
    }

  
    public function dotchamdiem__Get_By_Id($id_dot) {
        // Nhựt sửa lỗi: Không dùng SELECT * khi join nhiều bảng có cột trùng tên như ghi_chu làm sai dữ liệu form cập nhật.
        $obj = $this->connect->prepare("SELECT dotchamdiem.id_dot, dotchamdiem.ten_dot, dotchamdiem.ghi_chu, dotchamdiem.thoi_gian_bat_dau, dotchamdiem.thoi_gian_ket_thuc, dotchamdiem.id_hoc_ky, dotchamdiem.trang_thai, hocky.ten_hoc_ky, hocky.ngay_bat_dau AS ngay_hoc_ky_bat_dau, hocky.ngay_ket_thuc AS ngay_hoc_ky_ket_thuc, hocky.id_nam_hoc, namhoc.ten_nam_hoc, namhoc.ngay_bat_dau AS ngay_nam_hoc_bat_dau, namhoc.ngay_ket_thuc AS ngay_nam_hoc_ket_thuc FROM dotchamdiem, hocky, namhoc WHERE dotchamdiem.id_hoc_ky = hocky.id_hoc_ky AND hocky.id_nam_hoc = namhoc.id_nam_hoc AND dotchamdiem.id_dot= ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot));
        return $obj->fetch();
    }

    public function dotchamdiem__Lock_By_Id($id_dot) {
        // Nhựt sửa lỗi: Khóa Đợt khi update/delete để giảm rủi ro thao tác đồng thời làm sai dữ liệu.
        $obj = $this->connect->prepare("SELECT id_dot FROM dotchamdiem WHERE id_dot = ? FOR UPDATE");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot));
        return $obj->fetch();
    }

    public function dotchamdiem__Get_By_Id_Hoc_Ky($id_hoc_ky) {
        $obj = $this->connect->prepare("SELECT * FROM dotchamdiem WHERE id_hoc_ky= ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_hoc_ky));
        return $obj->fetchAll();
    }

    public function dotchamdiem__Exists_By_Hoc_Ky($id_hoc_ky, $exclude_id_dot = 0) {
        // Nhựt sửa lỗi: Mỗi Học kỳ chỉ được phép có một Đợt chấm điểm, khi update thì loại trừ chính bản ghi đang sửa.
        if ($exclude_id_dot > 0) {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM dotchamdiem WHERE id_hoc_ky = ? AND id_dot != ?");
            $obj->execute(array($id_hoc_ky, $exclude_id_dot));
        } else {
            $obj = $this->connect->prepare("SELECT COUNT(*) FROM dotchamdiem WHERE id_hoc_ky = ?");
            $obj->execute(array($id_hoc_ky));
        }
        return (int)$obj->fetchColumn() > 0;
    }

    public function dotchamdiem__Get_Last() {
        // Nhựt sửa lỗi: Lấy đúng đợt chấm điểm mới nhất theo id giảm dần.
        $obj = $this->connect->prepare("SELECT * FROM dotchamdiem ORDER BY id_dot DESC LIMIT 1");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetch();
    }
    public function dotchamdiem__Update_Trang_Thai($id_dot, $trang_thai) {
        $obj = $this->connect->prepare("UPDATE dotchamdiem SET trang_thai=? WHERE id_dot=?");
        $obj->execute(array($trang_thai, $id_dot));
        return $obj->rowCount();
    }

    public function dotchamdiem__Get_Time($date) {
        $obj = $this->connect->prepare("SELECT * FROM dotchamdiem WHERE thoi_gian_ket_thuc <= DATE(?)");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($date));
        return $obj->fetchAll();
    }

    public function dotchamdiem__Has_Dang_Dien_Ra($date) {
        // Nhựt sửa lỗi: Kiểm tra có Đợt chấm điểm đang diễn ra để khóa thay đổi cấu hình Xếp loại trong thời gian chấm.
        $obj = $this->connect->prepare("SELECT COUNT(*) FROM dotchamdiem WHERE thoi_gian_bat_dau <= DATE(?) AND thoi_gian_ket_thuc >= DATE(?)");
        $obj->execute(array($date, $date));
        return (int)$obj->fetchColumn() > 0;
    }

    public function dotchamdiem__DeleteWithDependencies($id_dot) {
        // Quân sửa: Thêm logic xóa an toàn các dữ liệu phụ thuộc của đợt chấm điểm bằng transaction
        try {
            $this->connect->beginTransaction();

            // Khóa dòng đợt chấm điểm để tránh tranh chấp dữ liệu
            $stmtLock = $this->connect->prepare("SELECT id_dot FROM dotchamdiem WHERE id_dot = ? FOR UPDATE");
            $stmtLock->execute(array($id_dot));
            if (!$stmtLock->fetch()) {
                $this->connect->rollBack();
                return false;
            }

            // Xóa minh chứng của đợt
            $stmtMinhChung = $this->connect->prepare("DELETE minhchung FROM minhchung INNER JOIN phieuchamdiem ON minhchung.id_phieu = phieuchamdiem.id_phieu INNER JOIN lopapdung ON phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung WHERE lopapdung.id_dot = ?");
            $stmtMinhChung->execute(array($id_dot));

            // Xóa kết quả xếp loại của đợt
            $stmtKetQua = $this->connect->prepare("DELETE FROM ketquaxeploai WHERE id_dot = ?");
            $stmtKetQua->execute(array($id_dot));

            // Xóa phiếu chấm điểm của đợt
            $stmtPhieu = $this->connect->prepare("DELETE phieuchamdiem FROM phieuchamdiem INNER JOIN lopapdung ON phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung WHERE lopapdung.id_dot = ?");
            $stmtPhieu->execute(array($id_dot));

            // Xóa lớp áp dụng của đợt
            $stmtLop = $this->connect->prepare("DELETE FROM lopapdung WHERE id_dot = ?");
            $stmtLop->execute(array($id_dot));

            // Xóa đợt chấm điểm
            $stmtDot = $this->connect->prepare("DELETE FROM dotchamdiem WHERE id_dot = ?");
            $stmtDot->execute(array($id_dot));
            
            if ($stmtDot->rowCount() > 0) {
                $this->connect->commit();
                return true;
            } else {
                $this->connect->rollBack();
                return false;
            }
        } catch (Exception $e) {
            if ($this->connect->inTransaction()) {
                $this->connect->rollBack();
            }
            return false;
        }
    }
}
