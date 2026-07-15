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

    public function trinhdo__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM trinhdo");
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
        $obj->execute(array($ten_trinh_do, $ghi_chu, $id_trinh_do));
        return $obj->rowCount();
    }
    

    public function trinhdo__Delete($id_trinh_do) {
        $obj = $this->connect->prepare("DELETE FROM trinhdo WHERE id_trinh_do = ?");
        $obj->execute(array($id_trinh_do));
        return $obj->rowCount();
    }

  
    public function trinhdo__Get_By_Id($id_trinh_do) {
        $obj = $this->connect->prepare("SELECT * FROM trinhdo WHERE id_trinh_do = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_trinh_do));
        return $obj->fetch();
    }

}
?>