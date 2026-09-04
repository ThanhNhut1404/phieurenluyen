<?php
$list_thong_bao = [];

$id_nguoi_dung = "";
if(isset($_SESSION['gv'])) {
    $id_nguoi_dung = $_SESSION['gv']->id_nguoi_dung;
} else if(isset($_SESSION['btdk'])) {
    $id_nguoi_dung = $_SESSION['btdk']->id_nguoi_dung;
}

try {
    if (isset($thongbao)) {
        // GV cũng có thể nhận thông báo qua ID người dùng (từ DB)
        $db_thong_bao = $thongbao->thongbao__Get_By_Sinh_Vien($id_nguoi_dung);
        if ($db_thong_bao) {
            foreach ($db_thong_bao as $tb) {
                $list_thong_bao[] = $tb;
            }
        }
    }
} catch (PDOException $e) {}

usort($list_thong_bao, function($a, $b) {
    return strtotime($b->ngay_tao) - strtotime($a->ngay_tao);
});
?>

