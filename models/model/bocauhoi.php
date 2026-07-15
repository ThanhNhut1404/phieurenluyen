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

class bocauhoi extends Database {

    public function bocauhoi__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM bocauhoi");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function bocauhoi__Add($id_mau_phieu, $id_dieu) {
        $obj = $this->connect->prepare("INSERT INTO bocauhoi(id_mau_phieu, id_dieu) VALUES (?,?)");
        $obj->execute(array($id_mau_phieu, $id_dieu));
        return $obj->rowCount();
    }

    public function bocauhoi__Update($id_bo_cau_hoi, $id_mau_phieu, $id_dieu) {
        $obj = $this->connect->prepare("UPDATE bocauhoi SET id_mau_phieu=?, id_dieuu=? WHERE id_bo_cau_hoi=?");
        $obj->execute(array($id_mau_phieu, $id_dieu, $id_bo_cau_hoi));
        return $obj->rowCount();
    }
    

    public function bocauhoi__Delete($id_bo_cau_hoi) {
        $obj = $this->connect->prepare("DELETE FROM bocauhoi WHERE id_bo_cau_hoi = ?");
        $obj->execute(array($id_bo_cau_hoi));
        return $obj->rowCount();
    }

  
    public function bocauhoi__Get_By_Id($id_bo_cau_hoi) {
        $obj = $this->connect->prepare("SELECT * FROM bocauhoi WHERE id_bo_cau_hoi = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_bo_cau_hoi));
        return $obj->fetch();
    }

    public function bocauhoi__Get_By_Id_Mau_Phieu($id_mau_phieu) {
        $obj = $this->connect->prepare("SELECT * FROM bocauhoi WHERE id_mau_phieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_mau_phieu));
        return $obj->fetchAll();
    }
    
    public function bocauhoi__Delete_Mau_Phieu($id_mau_phieu) {
        $obj = $this->connect->prepare("DELETE FROM bocauhoi WHERE id_mau_phieu=?");
        $obj->execute(array($id_mau_phieu));
        return $obj->rowCount();
    }

}
?>