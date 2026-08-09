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
                
                // Quân sửa: Kiểm tra quyền lớp trưởng chỉ được chấm cho sinh viên cùng lớp
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                $sv_target = $sinhvien->sinhvien__Get_By_Id($phieu->id_sinh_vien);
                $lt_info = $sinhvien->sinhvien__Get_By_Id($_SESSION['lt']->id_nguoi_dung);
                if (!$sv_target || !$lt_info || $sv_target->id_lop_hoc != $lt_info->id_lop_hoc) {
                    header("location: $href&status=failed");
                    exit();
                }

                $kq_lt_bt = $_POST["kq_lt_bt"];
                $kq = "";
                foreach($kq_lt_bt as $item){
                    $kq .= $item."|";
                }
                
                $status .= $phieuchamdiem->phieuchamdiem__Update_Kq_LTBT($id_phieu, rtrim($kq, "|"));

                if($status != "0" ){
                    header("location: $href&status=success");
                }else{
                    header("location: $href&status=failed");
                }
                break; 

            case "add_sv":
                $status = 0;
                $id_phieu = $_POST["id_phieu"];
                $kq_sv = $_POST["kq_sv"];
                $kq = "";
                foreach($kq_sv as $item){
                    $kq .= $item."|";
                }
                
                $status .= $phieuchamdiem->phieuchamdiem__Update_Kq_Sv($id_phieu, rtrim($kq, "|"));

                if($status != "0" ){
                    header("location: $href&status=success");
                }else{
                    header("location: $href&status=failed");
                }
                break; 
        }
    }
?>