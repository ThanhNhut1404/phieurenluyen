<?php

    require '../../models/getModel.php';
    $href = $_SERVER["HTTP_REFERER"];
    if(strlen(strpos($href, "&status")) > 0){
        $href = explode("&status", $href)[0];
    }
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':
                $status  = 0;
                $id_dot = $_POST['id_dot'];
                $id_lop_hoc = $_POST['id_lop_hoc'];

                $phieuchamdiem__Get_By_Id_Dot_And_Id_Lop_Hoc = $phieuchamdiem->phieuchamdiem__Get_By_Id_Dot_And_Id_Lop_Hoc($id_dot, $id_lop_hoc);

                foreach($phieuchamdiem__Get_By_Id_Dot_And_Id_Lop_Hoc as $item){
                    $arr = $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($item->kq_gv);
                    $sum = $phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua($arr);
                    $xeploai__Get_By_Kq = $xeploai->xeploai__Get_By_Kq($sum);

                    $sinhvien__Get_By_Id = $sinhvien->sinhvien__Get_By_Id($item->id_sinh_vien);
                    $lophoc__Get_By_Id = $lophoc->lophoc__Get_By_Id($item->id_lop_hoc);
                    

                    $status += $ketquaxeploai->ketquaxeploai__Add($item->id_phieu, $xeploai__Get_By_Kq->id_xep_loai, $item->id_sinh_vien, $item->id_lop_hoc, $item->id_dot, $sinhvien__Get_By_Id->ma_sinh_vien, $sinhvien__Get_By_Id->ten_sinh_vien, $lophoc__Get_By_Id->ten_lop_hoc, $sum, $xeploai__Get_By_Kq->ten_xep_loai, date('Y-m-d'));

                }
                
                if($status != "0" ){
                    header("location: ../index.php?page=quan-ly-ket-qua&id_dot=$id_dot&id_lop_hoc=$id_lop_hoc&status=success");
                }else{
                    header("location: $href&status=failed");
                }
                
                break;
        }
    }

?>