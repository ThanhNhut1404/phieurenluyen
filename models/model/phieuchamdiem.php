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

class phieuchamdiem extends Database {

    public function phieuchamdiem__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function phieuchamdiem__Add($id_lop_ap_dung, $id_sinh_vien, $kq_sv, $kq_lt_bt, $kq_btdk, $ngay_thuc_hien, $kq_gv = "") {
        // Nhựt sửa lỗi: Bảng phieuchamdiem có cột kq_gv NOT NULL nên phải insert giá trị khởi tạo để tránh lỗi SQL strict mode.
        $obj = $this->connect->prepare("INSERT INTO phieuchamdiem(id_lop_ap_dung, id_sinh_vien, kq_sv, kq_lt_bt, kq_btdk, kq_gv, ngay_thuc_hien) VALUES (?,?,?,?,?,?,?)");
        $obj->execute(array($id_lop_ap_dung, $id_sinh_vien, $kq_sv, $kq_lt_bt, $kq_btdk, $kq_gv, $ngay_thuc_hien));
        return $obj->rowCount();
    }

    public function phieuchamdiem__Update($id_phieu, $id_sinh_vien, $kq_sv, $kq_lt_bt, $kq_btdk, $ngay_thuc_hien) {
        // Nhựt sửa lỗi: Sửa SQL Update cho khớp số lượng placeholder và tham số execute().
        $obj = $this->connect->prepare("UPDATE phieuchamdiem SET id_sinh_vien=?, kq_sv=?, kq_lt_bt=?, kq_btdk=?, ngay_thuc_hien=? WHERE id_phieu=?");
        $obj->execute(array($id_sinh_vien, $kq_sv, $kq_lt_bt, $kq_btdk, $ngay_thuc_hien, $id_phieu));
        return $obj->rowCount();
    }
    

    public function phieuchamdiem__Delete($id_phieu) {
        $obj = $this->connect->prepare("DELETE FROM phieuchamdiem WHERE id_phieu = ?");
        $obj->execute(array($id_phieu));
        return $obj->rowCount();
    }

    public function phieuchamdiem__Delete_By_Id_Dot($id_dot) {
        // Nhựt sửa lỗi: Khi xóa Đợt chưa có dữ liệu chấm thì phải xóa Phiếu chấm điểm trước Lớp áp dụng.
        $obj = $this->connect->prepare("DELETE phieuchamdiem FROM phieuchamdiem, lopapdung WHERE phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_dot = ?");
        $obj->execute(array($id_dot));
        return $obj->rowCount();
    }

    public function phieuchamdiem__Has_Scored_Data_By_Id_Dot($id_dot) {
        // Nhựt sửa lỗi: Chỉ khóa xóa Đợt chấm điểm khi Phiếu đã phát sinh dữ liệu chấm thật sự, không khóa vì phiếu khởi tạo.
        $obj = $this->connect->prepare("SELECT COUNT(*) FROM dotchamdiem, lopapdung, phieuchamdiem WHERE dotchamdiem.id_dot = lopapdung.id_dot AND lopapdung.id_lop_ap_dung = phieuchamdiem.id_lop_ap_dung AND dotchamdiem.id_dot = ? AND ((phieuchamdiem.kq_sv IS NOT NULL AND phieuchamdiem.kq_sv != '') OR (phieuchamdiem.kq_lt_bt IS NOT NULL AND phieuchamdiem.kq_lt_bt != '') OR (phieuchamdiem.kq_btdk IS NOT NULL AND phieuchamdiem.kq_btdk != '') OR (phieuchamdiem.kq_gv IS NOT NULL AND phieuchamdiem.kq_gv != ''))");
        $obj->execute(array($id_dot));
        return (int)$obj->fetchColumn() > 0;
    }

  
    public function phieuchamdiem__Has_Unscored_Gv_By_Id_Dot_And_Id_Lop_Hoc($id_dot, $id_lop_hoc) {
        // Nhựt sửa lỗi: Không cho sinh kết quả xếp loại khi còn Phiếu chưa có kết quả cố vấn.
        $obj = $this->connect->prepare("SELECT COUNT(*) FROM phieuchamdiem, lopapdung WHERE phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ? AND (phieuchamdiem.kq_gv IS NULL OR phieuchamdiem.kq_gv = '')");
        $obj->execute(array($id_dot, $id_lop_hoc));
        return (int)$obj->fetchColumn() > 0;
    }

    public function phieuchamdiem__Count_By_Id_Dot_And_Id_Lop_Hoc($id_dot, $id_lop_hoc) {
        // Nhựt sửa lỗi: Kiểm tra Đợt/Lớp có Phiếu chấm điểm trước khi tổng kết để tránh báo thành công rỗng.
        $obj = $this->connect->prepare("SELECT COUNT(*) FROM phieuchamdiem, lopapdung WHERE phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_dot = ? AND lopapdung.id_lop_hoc = ?");
        $obj->execute(array($id_dot, $id_lop_hoc));
        return (int)$obj->fetchColumn();
    }

    public function phieuchamdiem__Get_By_Id($id_phieu) {
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem WHERE id_phieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_phieu));
        return $obj->fetch();
    }

    public function phieuchamdiem__Get_By_Id_Sinh_Vien($id_sinh_vien, $id_dot) {
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem, lopapdung WHERE phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND id_sinh_vien = ? AND id_dot = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_sinh_vien, $id_dot));
        $res = $obj->fetch();
        if ($res) {
            return $res;
        }

        // Nhựt sửa: Tự động sinh phiếu chấm điểm nếu lớp của sinh viên đã được áp dụng trong đợt này
        // 1. Tìm thông tin lớp học của sinh viên
        $stmt_sv = $this->connect->prepare("SELECT id_lop_hoc FROM sinhvien WHERE id_sinh_vien = ?");
        $stmt_sv->execute(array($id_sinh_vien));
        $sv = $stmt_sv->fetch(PDO::FETCH_OBJ);
        if ($sv && isset($sv->id_lop_hoc)) {
            // 2. Tìm lớp áp dụng trong đợt này
            $stmt_lad = $this->connect->prepare("SELECT id_lop_ap_dung FROM lopapdung WHERE id_dot = ? AND id_lop_hoc = ?");
            $stmt_lad->execute(array($id_dot, $sv->id_lop_hoc));
            $lad = $stmt_lad->fetch(PDO::FETCH_OBJ);
            if ($lad) {
                // 3. Tạo phiếu chấm điểm mới cho sinh viên đó
                $this->phieuchamdiem__Add($lad->id_lop_ap_dung, $id_sinh_vien, "", "", "", date("Y-m-d"));
                // 4. Lấy lại phiếu vừa tạo
                $obj->execute(array($id_sinh_vien, $id_dot));
                return $obj->fetch();
            }
        }
        return false;
    }

    public function phieuchamdiem__Get_By_Id_Ap_Dung($id_lop_ap_dung) {
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem WHERE id_lop_ap_dung = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_ap_dung));
        return $obj->fetchAll();
    }

    public function phieuchamdiem__Get_By_Id_Dot_And_Id_Lop_Hoc($id_dot, $id_lop_hoc) {
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem, lopapdung  WHERE   phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung  AND id_dot = ? AND lopapdung.id_lop_hoc = ? GROUP BY phieuchamdiem.id_phieu");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_dot, $id_lop_hoc));
        return $obj->fetchAll();
    }

    public function phieuchamdiem__Get_Ket_Qua($req) {
        $kq = [];
        $res = explode('|', $req);
        foreach ($res as $item){
            $kq[] = $item;
        }
        return $kq;
    }
    public function phieuchamdiem__Get_Sum_Ket_Qua($req) {
        $kq = 0;
        foreach ($req as $item){
            $kq += intval($item);
        }
        return $kq;
    }
    
    public function phieuchamdiem__Update_Kq_Sv($id_phieu, $kq_sv) {
        // Ánh xạ điểm tự chấm của SV sang các role khác luôn
        $obj = $this->connect->prepare("UPDATE phieuchamdiem SET kq_sv=?, kq_lt_bt=?, kq_btdk=?, kq_gv=?, ngay_thuc_hien=? WHERE id_phieu=?");
        $obj->execute(array($kq_sv, $kq_sv, $kq_sv, $kq_sv, date('Y-m-d'), $id_phieu));
        return $obj->rowCount();
    }
    public function phieuchamdiem__Update_Kq_LTBT($id_phieu, $kq_lt_bt) {
        // Ánh xạ điểm của BCS sang các role cấp cao hơn
        $obj = $this->connect->prepare("UPDATE phieuchamdiem SET kq_lt_bt=?, kq_btdk=?, kq_gv=?, ngay_thuc_hien=? WHERE id_phieu=?");
        $obj->execute(array($kq_lt_bt, $kq_lt_bt, $kq_lt_bt, date('Y-m-d'), $id_phieu));
        return $obj->rowCount();
    }

    public function phieuchamdiem__Update_Kq_BTDK($id_phieu, $kq_btdk) {
        // Ánh xạ điểm của Đoàn khoa sang Giảng viên
        $obj = $this->connect->prepare("UPDATE phieuchamdiem SET kq_btdk=?, kq_gv=?, ngay_thuc_hien=? WHERE id_phieu=?");
        $obj->execute(array($kq_btdk, $kq_btdk, date('Y-m-d'), $id_phieu));
        return $obj->rowCount();
    }

    public function phieuchamdiem__Update_Kq_Gv($id_phieu, $kq_gv) {
        $obj = $this->connect->prepare("UPDATE phieuchamdiem SET kq_gv=?, ngay_thuc_hien=? WHERE id_phieu=?");
        $obj->execute(array($kq_gv, date('Y-m-d'), $id_phieu));
        return $obj->rowCount();
    }

    public function phieuchamdiem__Update_Trang_Thai($id_phieu, $trang_thai) {
        $obj = $this->connect->prepare("UPDATE phieuchamdiem SET trang_thai=? WHERE id_phieu=?");
        $obj->execute(array($trang_thai, $id_phieu));
        return $obj->rowCount();
    }


    public function phieuchamdiem__Get_By_Id_Lop($id_lop_hoc, $id_dot) {
        // Nhựt sửa lỗi: Hàm nhận id_lop_hoc nên phải lọc theo lopapdung.id_lop_hoc, không phải id_lop_ap_dung.
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem, lopapdung WHERE phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_lop_hoc = ? AND id_dot = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc, $id_dot));
        return $obj->fetchAll();
    }

    public function phieuchamdiem__Get_By_Id_Lop_Da_Cham($id_lop_hoc, $id_dot) {
        // Nhựt sửa lỗi: Hàm nhận id_lop_hoc nên phải lọc theo lopapdung.id_lop_hoc, không phải id_lop_ap_dung.
        // Nhựt sửa lỗi: So sánh rỗng bằng NULL || '' là sai, phải kiểm tra kq_gv khác NULL và khác chuỗi rỗng.
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem, lopapdung WHERE phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_lop_hoc = ? AND id_dot = ? AND kq_gv IS NOT NULL AND kq_gv != ''");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc, $id_dot));
        return $obj->fetchAll();
    }

    public function phieuchamdiem__Get_By_Id_Lop_Chua_Cham($id_lop_hoc, $id_dot) {
        // Nhựt sửa lỗi: Hàm nhận id_lop_hoc nên phải lọc theo lopapdung.id_lop_hoc, không phải id_lop_ap_dung.
        // Nhựt sửa lỗi: So sánh rỗng bằng NULL || '' là sai, phải kiểm tra kq_gv NULL hoặc chuỗi rỗng.
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem, lopapdung WHERE phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_lop_hoc = ? AND id_dot = ? AND (kq_gv IS NULL OR kq_gv = '')");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc, $id_dot));
        return $obj->fetchAll();
    }
    public function phieuchamdiem__Get_By_Id_Lop_All($id_lop_hoc, $id_dot) {
        // Nhựt sửa lỗi: Hàm nhận id_lop_hoc nên phải lọc theo lopapdung.id_lop_hoc, không phải id_lop_ap_dung.
        $obj = $this->connect->prepare("SELECT * FROM phieuchamdiem, lopapdung WHERE phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_lop_hoc = ? AND id_dot = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc, $id_dot));
        return $obj->fetchAll();
    }

    public function phieuchamdiem__Get_Lich_Su_By_Id_Sinh_Vien($id_sinh_vien) {
        $sql = "
            SELECT 
                phieuchamdiem.*, 
                dotchamdiem.ten_dot, 
                hocky.ten_hoc_ky, 
                namhoc.ten_nam_hoc,
                ketquaxeploai.ket_qua as tong_diem_xep_loai,
                ketquaxeploai.xep_loai
            FROM phieuchamdiem
            INNER JOIN lopapdung ON phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung
            INNER JOIN dotchamdiem ON lopapdung.id_dot = dotchamdiem.id_dot
            INNER JOIN hocky ON dotchamdiem.id_hoc_ky = hocky.id_hoc_ky
            INNER JOIN namhoc ON hocky.id_nam_hoc = namhoc.id_nam_hoc
            LEFT JOIN ketquaxeploai ON phieuchamdiem.id_phieu = ketquaxeploai.id_phieu
            WHERE phieuchamdiem.id_sinh_vien = ?
            ORDER BY namhoc.ngay_bat_dau DESC, hocky.ngay_bat_dau DESC
        ";
        $obj = $this->connect->prepare($sql);
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_sinh_vien));
        return $obj->fetchAll();
    }
}
?>
