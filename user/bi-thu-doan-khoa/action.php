

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
                if ($lop->id_khoa != $bithudoankhoa__Get_By_Id->id_khoa) {
                    header("location: $href&status=failed");
                    exit();
                }
                
                // Kiểm tra xem Lớp trưởng đã chấm chưa
                if (empty($phieu->kq_lt_bt)) {
                    header("location: $href&status=failed");
                    exit();
                }

                $kq_btdk = $_POST["kq_btdk"];
                $kq = "";
                foreach($kq_btdk as $item){
                    $kq .= $item."|";
                }
                
                $status .= $phieuchamdiem->phieuchamdiem__Update_Kq_BTDK($id_phieu, rtrim($kq, "|"));


                if($status != "0" ){
                    header("location: $href&status=success");
                }else{
                    header("location: $href&status=failed");
                }
                break; 
        }
    }
?>