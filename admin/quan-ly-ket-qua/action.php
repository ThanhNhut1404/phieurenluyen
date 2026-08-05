<?php
    // Nhựt sửa lỗi: Khởi động session và kiểm tra quyền Admin tránh bypass auth.
    session_start();
    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }

    require "../../models/getModel.php";
    $href = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "../index.php?page=quan-ly-ket-qua";
    if (strpos($href, "&status") !== false) {
        $href = explode("&status", $href)[0];
    }

    function ketqua__Is_Positive_Integer($value) {
        // Nhựt sửa lỗi: Validate id_ket_qua trước khi hạ bậc để tránh truy cập sai bản ghi.
        return preg_match('/^[1-9][0-9]*$/', $value);
    }

    if (isset($_GET["req"])){
        switch($_GET["req"]){
            
            case "update":
                // Nhựt sửa lỗi: Kiểm tra Token CSRF bảo mật hành động hạ bậc.
                if (!isset($_GET['csrf_token'], $_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
                    header("location: $href&status=csrf");
                    exit();
                }

                $id_ket_qua = isset($_GET["id_ket_qua"]) ? trim($_GET["id_ket_qua"]) : "";

                // Nhựt sửa lỗi: Không hạ bậc khi id_ket_qua sai kiểu hoặc không tồn tại.
                if (!ketqua__Is_Positive_Integer($id_ket_qua)) {
                    header("location: $href&status=not-found");
                    exit();
                }

                $ketquaxeploai__Get_By_Id = $ketquaxeploai->ketquaxeploai__Get_By_Id($id_ket_qua);
                if (!$ketquaxeploai__Get_By_Id) {
                    header("location: $href&status=not-found");
                    exit();
                }

                $xeploai__Get_By_Kq_Old = $xeploai->xeploai__Get_By_Kq($ketquaxeploai__Get_By_Id->ket_qua);
                if (!$xeploai__Get_By_Kq_Old) {
                    // Nhựt sửa lỗi: Nếu dữ liệu xếp loại cũ không còn hợp lệ thì không tính được mức hạ bậc.
                    header("location: $href&status=incomplete-xep-loai");
                    exit();
                }

                // Nhựt sửa lỗi: Giới hạn điểm tối thiểu sau khi hạ bậc là 0 để tránh điểm âm.
                $ha_bac = max(0, $ketquaxeploai__Get_By_Id->ket_qua - $xeploai__Get_By_Kq_Old->ha_bac);
                $xeploai__Get_By_Kq = $xeploai->xeploai__Get_By_Kq($ha_bac);
                if (!$xeploai__Get_By_Kq) {
                    // Nhựt sửa lỗi: Sau khi hạ bậc mà không còn xếp loại phù hợp thì không được cập nhật kết quả.
                    header("location: $href&status=incomplete-xep-loai");
                    exit();
                }

                $ket_qua_new = $ha_bac;
                $xep_loai = $xeploai__Get_By_Kq->ten_xep_loai;
                $ngay_xep_loai = date("Y-m-d");
                $ghi_chu = "Hạ bậc";

                $status = $ketquaxeploai->ketquaxeploai__Update_Ha_Bac($id_ket_qua, $ket_qua_new, $xep_loai, $ngay_xep_loai, $ghi_chu);
                if($status !=0 ){
                    header("location: $href&status=success");
                }else{
                    header("location: $href&status=failed");
                }
                
                break;

            case "print":

                $id_xep_loai = isset($_GET["id_xep_loai"]) ? trim($_GET["id_xep_loai"]) : "";

                if (!ketqua__Is_Positive_Integer($id_xep_loai)) {
                    header("location: $href&status=not-found");
                    exit();
                }

                header("location: $href&status=not-found");
                exit();
                
                break;
        }
    }

?>
