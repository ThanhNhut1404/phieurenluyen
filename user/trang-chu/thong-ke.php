<?php
    $id_dot = $dotchamdiem->dotchamdiem__Get_Last()->id_dot;

    if(isset($_GET['id_dot'])){
        $id_dot = $_GET['id_dot'];
    }
    if($phannhom->phannhom__Get_By_Id($_SESSION["user"]->id_phan_nhom)->cap_bac == 2){
        $sinhvien__Get_By_Id = $sinhvien->sinhvien__Get_By_Id($_SESSION["user"]->id_nguoi_dung);
        if($sinhvien__Get_By_Id->chuc_vu == 0){
            header("location: ../user/index.php?page=sinh-vien&id_dot=$id_dot");
            exit();
        }
        if($sinhvien__Get_By_Id->chuc_vu == 1){
            header("location: ../user/index.php?page=lop-truong&id_dot=$id_dot");
            exit();
        }
        if($sinhvien__Get_By_Id->chuc_vu == 2){
            header("location: ../user/index.php?page=bi-thu-chi-doan&id_dot=$id_dot");
            exit();
        }
    }
   
    if($phannhom->phannhom__Get_By_Id($_SESSION["user"]->id_phan_nhom)->cap_bac == 3){
        header("location: ../user/index.php?page=bi-thu-doan-khoa&id_dot=$id_dot");
        exit();
    }
    if($phannhom->phannhom__Get_By_Id($_SESSION["user"]->id_phan_nhom)->cap_bac == 4){
        header("location: ../user/index.php?page=co-van-hoc-tap&id_dot=$id_dot");
        exit();
    }
?>