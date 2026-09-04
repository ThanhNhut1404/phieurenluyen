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
                
                // Nhựt sửa: Thêm ràng buộc bảo mật backend cho Cố vấn học tập
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                // Kiểm tra lớp áp dụng của phiếu
                $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($phieu->id_lop_ap_dung);
                if (!$lop_ap_dung) {
                    header("location: $href&status=failed");
                    exit();
                }

                // Kiểm tra xem đợt phải đang mở
                // Nhựt sửa: Sửa lỗi lấy id_dot từ phiếu (phiếu không có id_dot, phải lấy từ lớp áp dụng) và bỏ kiểm tra trang_thai == 4
                $dot = $dotchamdiem->dotchamdiem__Get_By_Id($lop_ap_dung->id_dot);
                if (!$dot) {
                    header("location: $href&status=failed");
                    exit();
                }

                // Nhất sửa: Kiểm tra xem Ban chấp hành Đoàn khoa đã chấm điểm chưa
                // Cho phép GV chấm nếu đợt chấm đã kết thúc
                $dot_expired = false;
                if (isset($dot->thoi_gian_ket_thuc)) {
                    if ($dot->thoi_gian_ket_thuc <= date('Y-m-d') || $dot->trang_thai == 0) {
                        $dot_expired = true;
                    }
                }
                
                if (empty($phieu->kq_btdk) && !$dot_expired) {
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
                    $val = ($item === "") ? "0" : $item; $kq .= $val."|";
                }
                
                // Quân sửa: Thực hiện cập nhật điểm của cố vấn học tập và chuyển hướng thành công
                $phieuchamdiem->phieuchamdiem__Update_Kq_Gv($id_phieu, rtrim($kq, "|"));
                header("location: $href&status=success&msg=" . urlencode("Cập nhật điểm đánh giá thành công!"));
                break; 
        }
    }
?>