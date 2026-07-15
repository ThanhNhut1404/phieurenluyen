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

class taikhoan extends Database
{

    public function taikhoan__Get_All()
    {
        $obj = $this->connect->prepare("SELECT * FROM taikhoan");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }

    public function taikhoan__Get_All_Phan_Nhom($cap_bac)
    {
        $obj = $this->connect->prepare("SELECT * FROM taikhoan, phannhom WHERE taikhoan.id_phan_nhom = phannhom.id_phan_nhom AND phannhom.cap_bac = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($cap_bac));
        return $obj->fetchAll();
    }

    public function taikhoan__Add($email, $mat_khau, $ghi_chu, $id_phan_quyen, $id_phan_nhom, $id_nguoi_dung)
    {
        $obj = $this->connect->prepare("INSERT INTO taikhoan(email, mat_khau, ghi_chu, id_phan_quyen, id_phan_nhom, id_nguoi_dung) VALUES (?,?,?,?,?,?)");
        $obj->execute(array($email, $mat_khau, $ghi_chu, $id_phan_quyen, $id_phan_nhom, $id_nguoi_dung));
        return $obj->rowCount();
    }

    public function taikhoan__Update($id_tai_khoan, $email, $mat_khau, $ghi_chu, $id_phan_quyen, $id_phan_nhom, $id_nguoi_dung)
    {
        $obj = $this->connect->prepare("UPDATE taikhoan SET email=?, mat_khau=?, ghi_chu=?, id_phan_quyen=?, id_phan_nhom=?, id_nguoi_dung=? WHERE id_tai_khoan=?");
        $obj->execute(array($email, $mat_khau, $ghi_chu, $id_phan_quyen, $id_phan_nhom, $id_nguoi_dung, $id_tai_khoan));
        return $obj->rowCount();
    }


    public function taikhoan__Delete($id_tai_khoan)
    {
        $obj = $this->connect->prepare("DELETE FROM taikhoan WHERE id_tai_khoan = ?");
        $obj->execute(array($id_tai_khoan));
        return $obj->rowCount();
    }


    public function taikhoan__Reset($id_tai_khoan, $mat_khau)
    {
        $obj = $this->connect->prepare("UPDATE taikhoan SET mat_khau=? WHERE id_tai_khoan=?");
        $obj->execute(array($mat_khau, $id_tai_khoan));
        return $obj->rowCount();
    }


    public function taikhoan__Active($id_tai_khoan, $trang_thai)
    {
        $obj = $this->connect->prepare("UPDATE taikhoan SET trang_thai=? WHERE id_tai_khoan=?");
        $obj->execute(array($trang_thai, $id_tai_khoan));
        return $obj->rowCount();
    }

    public function taikhoan__Get_By_Id($id_tai_khoan)
    {
        $obj = $this->connect->prepare("SELECT * FROM taikhoan WHERE id_tai_khoan = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_tai_khoan));
        return $obj->fetch();
    }



    public function taikhoan__Get_By_Id_Phan_Quyen($id_phan_quyen, $trang_thai = 1)
    {
        $obj = $this->connect->prepare("SELECT * FROM taikhoan WHERE id_phan_quyen = ? AND trang_thai=?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_phan_quyen, $trang_thai));
        return $obj->fetchAll();
    }


    public function taikhoan__Get_By_Id_Phan_Nhom($id_phan_nhom, $trang_thai = 1)
    {
        $obj = $this->connect->prepare("SELECT * FROM taikhoan WHERE id_phan_nhom = ? AND trang_thai =?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_phan_nhom, $trang_thai));
        return $obj->fetchAll();
    }

    public function taikhoan__Get_By_Id_Nguoi_Dung($id_nguoi_dung, $trang_thai = 1)
    {
        $obj = $this->connect->prepare("SELECT * FROM taikhoan WHERE id_nguoi_dung = ? AND trang_thai=?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_nguoi_dung, $trang_thai));
        return $obj->fetchAll();
    }

    public function taikhoan__Check_Login($email, $mat_khau)
    {
        $obj = $this->connect->prepare("SELECT * FROM taikhoan WHERE email =? AND mat_khau = ? AND trang_thai=1");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($email, $mat_khau));
        if ($obj->rowCount() > 0) {
            return $obj->fetch();
        } else {
            return 0;
        }
    }

    public function taikhoan__Get_By_Sinh_Vien($id_nguoi_dung)
    {
        $obj = $this->connect->prepare("SELECT taikhoan.email as email * FROM taikhoan INNER JOIN sinhvien ON taikhoan.id_nguoi_dung = sinhvien.id_sinh_vien WHERE taikhoan.id_phan_nhom = 3");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_nguoi_dung));
        return $obj->fetchAll();
    }

    public function taikhoan__Get_By_Bi_Thu($id_nguoi_dung)
    {
        $obj = $this->connect->prepare("SELECT * FROM taikhoan INNER JOIN sinhvien ON taikhoan.id_nguoi_dung = sinhvien.id_sinh_vien WHERE taikhoan.id_phan_nhom = 4");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_nguoi_dung));
        return $obj->fetchAll();
    }

    public function taikhoan__Get_By_Giang_Vien($id_nguoi_dung)
    {
        $obj = $this->connect->prepare("SELECT * FROM taikhoan INNER JOIN sinhvien ON taikhoan.id_nguoi_dung = sinhvien.id_sinh_vien WHERE taikhoan.id_phan_nhom = 5");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_nguoi_dung));
        return $obj->fetchAll();
    }

    public function taikhoan__Send($email, $password)
    {
        $to = "fdcvlinhff@gmail.com";
        $subject = "My subject";
        $txt = "Hello world!";
        $headers = "From: fdcvlinh6@gmail.com" . "\r\n" .
            "CC: fdcvlinhff@gmail.com";
        $res =  mail($to, $subject, $txt, $headers);
        return $res;
    }

    public function taikhoan__Get_By_Lop_Hoc($id_lop_hoc)
    {
        $obj = $this->connect->prepare("SELECT * FROM taikhoan INNER JOIN sinhvien ON taikhoan.id_nguoi_dung = sinhvien.id_sinh_vien WHERE taikhoan.id_phan_nhom = 3 AND sinhvien.id_lop_hoc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_lop_hoc));
        return $obj->fetchAll();
    }
}
