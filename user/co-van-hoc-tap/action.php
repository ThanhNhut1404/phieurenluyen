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
                
                // Quân sửa: Thêm ràng buộc bảo mật backend cho Cố vấn học tập
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                // Kiểm tra trạng thái phiếu phải là 4 (Cố vấn học tập chấm) và đợt phải đang mở
                $dot = $dotchamdiem->dotchamdiem__Get_By_Id($phieu->id_dot);
                if ($phieu->trang_thai != 4 || !$dot || $dot->trang_thai == 0) {
                    header("location: $href&status=failed");
                    exit();
                }

                // Kiểm tra giảng viên có được phân công lớp này không
                $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($phieu->id_lop_ap_dung);
                if (!$lop_ap_dung) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                $id_lop_hoc_target = $lop_ap_dung->id_lop_hoc;
                $phancong_list = $phancong->phancong__Get_By_Id_Giang_Vien_All($_SESSION['gv']->id_nguoi_dung);
                $is_assigned = false;
                foreach ($phancong_list as $pc) {
                    if ($pc->id_lop_hoc == $id_lop_hoc_target) {
                        $is_assigned = true;
                        break;
                    }
                }
                
                if (!$is_assigned) {
                    header("location: $href&status=failed");
                    exit();
                }

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