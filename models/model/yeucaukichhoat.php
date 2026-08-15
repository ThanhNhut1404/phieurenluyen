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

class Yeucaukichhoat extends Database
{

    public function yeucaukichhoat__Get_All()
    {
        $obj = $this->connect->prepare("SELECT * FROM yeucaukichhoat ORDER BY thoi_gian DESC");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }

    public function yeucaukichhoat__Get_All_Pending()
    {
        $obj = $this->connect->prepare("SELECT * FROM yeucaukichhoat WHERE trang_thai = 0 ORDER BY thoi_gian ASC");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }

    public function yeucaukichhoat__Get_By_Id($id_yeu_cau)
    {
        $obj = $this->connect->prepare("SELECT * FROM yeucaukichhoat WHERE id_yeu_cau = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_yeu_cau));
        return $obj->fetch();
    }

    public function yeucaukichhoat__Add($email)
    {
        // Kiểm tra xem yêu cầu pending cho email này đã tồn tại chưa
        $check = $this->connect->prepare("SELECT id_yeu_cau FROM yeucaukichhoat WHERE email = ? AND trang_thai = 0");
        $check->setFetchMode(PDO::FETCH_OBJ);
        $check->execute(array($email));
        if ($check->rowCount() > 0) {
            return $check->fetch()->id_yeu_cau; // Đã tồn tại yêu cầu
        }

        $obj = $this->connect->prepare("INSERT INTO yeucaukichhoat(email, thoi_gian, trang_thai) VALUES (?, NOW(), 0)");
        $obj->execute(array($email));
        return $obj->rowCount();
    }

    public function yeucaukichhoat__Update_Status($id_yeu_cau, $trang_thai)
    {
        $obj = $this->connect->prepare("UPDATE yeucaukichhoat SET trang_thai = ? WHERE id_yeu_cau = ?");
        $obj->execute(array($trang_thai, $id_yeu_cau));
        return $obj->rowCount();
    }

    public function yeucaukichhoat__Delete($id_yeu_cau)
    {
        $obj = $this->connect->prepare("DELETE FROM yeucaukichhoat WHERE id_yeu_cau = ?");
        $obj->execute(array($id_yeu_cau));
        return $obj->rowCount();
    }
}
?>
