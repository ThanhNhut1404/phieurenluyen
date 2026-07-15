<?php

    require "../../models/getModel.php";
     $href = $_SERVER["HTTP_REFERER"];
    if(strlen(strpos($href, "&status")) > 0){
        $href = explode("&status", $href)[0];
    } 
    if (isset($_GET["req"])){
        switch($_GET["req"]){
            
            case "update":
               echo $id_ket_qua = $_GET["id_ket_qua"];
               $ketquaxeploai__Get_By_Id = $ketquaxeploai->ketquaxeploai__Get_By_Id($id_ket_qua);


               $xeploai__Get_By_Kq_Old = $xeploai->xeploai__Get_By_Kq($ketquaxeploai__Get_By_Id->ket_qua);
               $ha_bac = $ketquaxeploai__Get_By_Id->ket_qua - $xeploai__Get_By_Kq_Old->ha_bac;
               $xeploai__Get_By_Kq = $xeploai->xeploai__Get_By_Kq($ha_bac);

               echo $ket_qua_new = $ha_bac;
               echo $xep_loai = $xeploai__Get_By_Kq->ten_xep_loai;
               echo $ngay_xep_loai = date("Y-m-d");
               echo  $ghi_chu = "Hạ bậc";

               echo $status = $ketquaxeploai->ketquaxeploai__Update_Ha_Bac($id_ket_qua, $ket_qua_new, $xep_loai, $ngay_xep_loai, $ghi_chu);
                if($status !=0 ){
                    header("location: $href&status=success");
                }else{
                    header("location: $href&status=failed");
                }
                
                break;

            case "print":

                $id_xep_loai = $_GET["id_xep_loai"];

                if($status !=0 ){
                    header("location: $href&status=success");
                }else{
                    header("location: $href&status=failed");
                }
                
                break;
        }
    }

?>