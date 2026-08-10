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

                // Nhựt sửa: Kiểm tra quyền bí thư chi đoàn chỉ được chấm cho sinh viên cùng lớp & đợt đang mở
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                $sv_target = $sinhvien->sinhvien__Get_By_Id($phieu->id_sinh_vien);
                $bt_info = $sinhvien->sinhvien__Get_By_Id($_SESSION['bt']->id_nguoi_dung);
                if (!$sv_target || !$bt_info || $sv_target->id_lop_hoc != $bt_info->id_lop_hoc) {
                    header("location: $href&status=failed");
                    exit();
                }
                $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($phieu->id_lop_ap_dung);
                if (!$lop_ap_dung) {
                    header("location: $href&status=failed");
                    exit();
                }
                $dot = $dotchamdiem->dotchamdiem__Get_By_Id($lop_ap_dung->id_dot);
                if (!$dot || $dot->trang_thai == 0) {
                    header("location: $href&status=failed");
                    exit();
                }

                $kq_lt_bt = $_POST["kq_lt_bt"];
                $kq = "";
                foreach($kq_lt_bt as $item){
                    $kq .= $item."|";
                }
                
                // Nhựt sửa: Thực hiện cập nhật điểm của bí thư chi đoàn và chuyển hướng thành công
                $phieuchamdiem->phieuchamdiem__Update_Kq_LTBT($id_phieu, rtrim($kq, "|"));
                header("location: $href&status=success");
                break; 

            case "add_sv":
                $status = 0;
                $id_phieu = $_POST["id_phieu"];

                // Nhựt sửa: Kiểm tra quyền chấm hộ của bí thư chi đoàn và đợt đang mở
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                $sv_target = $sinhvien->sinhvien__Get_By_Id($phieu->id_sinh_vien);
                $bt_info = $sinhvien->sinhvien__Get_By_Id($_SESSION['bt']->id_nguoi_dung);
                if (!$sv_target || !$bt_info || $sv_target->id_lop_hoc != $bt_info->id_lop_hoc) {
                    header("location: $href&status=failed");
                    exit();
                }
                $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($phieu->id_lop_ap_dung);
                if (!$lop_ap_dung) {
                    header("location: $href&status=failed");
                    exit();
                }
                $dot = $dotchamdiem->dotchamdiem__Get_By_Id($lop_ap_dung->id_dot);
                if (!$dot || $dot->trang_thai == 0) {
                    header("location: $href&status=failed");
                    exit();
                }

                $kq_sv = $_POST["kq_sv"];
                $kq = "";
                foreach($kq_sv as $item){
                    $kq .= $item."|";
                }
                
                // Nhựt sửa: Thực hiện cập nhật điểm tự chấm của sinh viên (do bí thư chi đoàn chấm hộ) và chuyển hướng thành công
                $phieuchamdiem->phieuchamdiem__Update_Kq_Sv($id_phieu, rtrim($kq, "|"));
                header("location: $href&status=success");
                break; 
        }
    }
?>