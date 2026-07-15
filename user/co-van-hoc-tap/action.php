<?php
    session_start();
    $href = $_SERVER["HTTP_REFERER"];
    if(strlen(strpos($href, "&status")) > 0){
        $href = explode("&status", $href)[0];
    }
    require "../../models/getModel.php";
    
    if (isset($_GET["req"])){
        switch($_GET["req"]){
            case "add":
                $status = 0;
                $id_phieu = $_POST["id_phieu"];
                $kq_gv = $_POST["kq_gv"];
                $kq = "";
                foreach($kq_gv as $item){
                    $kq .= $item."|";
                }
                
                $status .= $phieuchamdiem->phieuchamdiem__Update_Kq_Gv($id_phieu, rtrim($kq, "|"));

                if($status != "0" ){
                    header("location: $href&status=success");
                }else{
                    header("location: $href&status=failed");
                }
                break; 
        }
    }
?>