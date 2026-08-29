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
                if (!$dot || $dot->trang_thai == 0) {
                    header("location: $href&status=failed");
                    exit();
                }

                // Nhựt sửa: Kiểm tra xem Ban chấp hành Đoàn khoa đã chấm điểm chưa
                if (empty($phieu->kq_btdk)) {
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

            case "ha_bac":
                $id_ket_qua = $_POST["id_ket_qua"];
                $ly_do = isset($_POST["ly_do"]) ? $_POST["ly_do"] : "Không nộp phiếu đánh giá (Bị hạ bậc)";
                
                $kq_xl = $ketquaxeploai->ketquaxeploai__Get_By_Id($id_ket_qua);
                if (!$kq_xl) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($kq_xl->id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                // Điểm gốc dựa vào điểm của Cố vấn
                $kq_gv_arr = $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieu->kq_gv);
                $base_score = $phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua($kq_gv_arr);
                
                $original_xeploai = $xeploai->xeploai__Get_By_Kq($base_score);
                if (!$original_xeploai) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                // Xử lý trừ điểm hạ bậc
                $deduction = $original_xeploai->ha_bac;
                if ($base_score == 100) {
                    $deduction = 11; // Xử lý đặc biệt theo quy chế (100 điểm thì trừ 11)
                }
                
                $new_score = $base_score - $deduction;
                if ($new_score < 0) $new_score = 0;
                
                $new_xeploai = $xeploai->xeploai__Get_By_Kq($new_score);
                
                $ketquaxeploai->ketquaxeploai__Update_Ha_Bac($id_ket_qua, $new_score, $new_xeploai->ten_xep_loai, date('Y-m-d'), $ly_do);
                header("location: $href&status=success&msg=" . urlencode("Hạ bậc thành công!"));
                break;
                
            case "huy_ha_bac":
                $id_ket_qua = $_POST["id_ket_qua"];
                
                $kq_xl = $ketquaxeploai->ketquaxeploai__Get_By_Id($id_ket_qua);
                if (!$kq_xl) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($kq_xl->id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                // Khôi phục lại điểm gốc
                $kq_gv_arr = $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieu->kq_gv);
                $base_score = $phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua($kq_gv_arr);
                
                $original_xeploai = $xeploai->xeploai__Get_By_Kq($base_score);
                
                $ketquaxeploai->ketquaxeploai__Update_Ha_Bac($id_ket_qua, $base_score, $original_xeploai->ten_xep_loai, date('Y-m-d'), "");
                header("location: $href&status=success&msg=" . urlencode("Hủy hạ bậc thành công!"));
                break;
        }
    }
?>