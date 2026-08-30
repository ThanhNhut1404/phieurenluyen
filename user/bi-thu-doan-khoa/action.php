

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
                
                // Quân sửa: Kiểm tra quyền Bí thư đoàn khoa
                if (!isset($_SESSION["btdk"])) {
                    header("location: ../auth/");
                    exit();
                }
                $bithudoankhoa__Get_By_Id = $bithudoankhoa->bithudoankhoa__Get_By_Id($_SESSION["btdk"]->id_nguoi_dung);
                
                $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
                if (!$phieu) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                // Kiểm tra xem sinh viên có thuộc khoa của BTDK không
                $sv = $sinhvien->sinhvien__Get_By_Id($phieu->id_sinh_vien);
                $lop = $lophoc->lophoc__Get_By_Id($sv->id_lop_hoc);
                // Quân sửa: Lấy thông tin ngành học để kiểm tra id_khoa của lớp học
                $nganh = $nganhhoc->nganhhoc__Get_By_Id($lop->id_nganh_hoc);
                if ($nganh->id_khoa != $bithudoankhoa__Get_By_Id->id_khoa) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                // Nhựt sửa: Kiểm tra xem đợt phải đang mở ở backend
                $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($phieu->id_lop_ap_dung);
                if (!$lop_ap_dung) {
                    header("location: $href&status=failed");
                    exit();
                }
                $dot = $dotchamdiem->dotchamdiem__Get_By_Id($lop_ap_dung->id_dot);
                if (!$dot) {
                    header("location: $href&status=failed");
                    exit();
                }
                $is_ended = (strtotime(date('Y-m-d')) > strtotime($dot->thoi_gian_ket_thuc));
                if ($is_ended && $dot->trang_thai == 0) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                // Kiếm tra xem Lớp trưởng đã chấm chưa
                if (empty($phieu->kq_lt_bt)) {
                    header("location: $href&status=failed");
                    exit();
                }

                $kq_btdk = $_POST["kq_btdk"];
                $kq = "";
                foreach($kq_btdk as $item){
                    $val = ($item === "") ? "0" : $item; $kq .= $val."|";
                }
                
                // Quân sửa: Thực hiện cập nhật điểm và chuyển hướng thành công
                $phieuchamdiem->phieuchamdiem__Update_Kq_BTDK($id_phieu, rtrim($kq, "|"));
                header("location: $href&status=success&msg=" . urlencode("Cập nhật điểm đánh giá thành công!"));
                break; 
        }
    }
?>