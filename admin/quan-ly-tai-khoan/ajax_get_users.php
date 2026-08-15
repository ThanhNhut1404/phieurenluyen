<?php
require "../../models/getModel.php";

header('Content-Type: application/json');

$req = isset($_GET['req']) ? $_GET['req'] : '';

if ($req == 'get_sv') {
    $id_lop_hoc = isset($_GET['id_lop_hoc']) ? $_GET['id_lop_hoc'] : 0;
    $list = $sinhvien->sinhvien__Get_All_Not_Exits($id_lop_hoc);
    $result = [];
    foreach ($list as $item) {
        $result[] = [
            'id' => $item->id_sinh_vien,
            'name' => $item->ten_sinh_vien
        ];
    }
    echo json_encode($result);
    exit();
}

if ($req == 'get_gv') {
    $id_lop_hoc = isset($_GET['id_lop_hoc']) ? $_GET['id_lop_hoc'] : 0;
    $list = $giangvien->giangvien__Get_All_Not_Exits($id_lop_hoc);
    $result = [];
    foreach ($list as $item) {
        $result[] = [
            'id' => $item->id_giang_vien,
            'name' => $item->ten_giang_vien
        ];
    }
    echo json_encode($result);
    exit();
}

if ($req == 'get_btdk') {
    $id_khoa = isset($_GET['id_khoa']) ? $_GET['id_khoa'] : 0;
    $list = $bithudoankhoa->bithudoankhoa__Get_All_Not_Exits($id_khoa);
    $result = [];
    foreach ($list as $item) {
        $result[] = [
            'id' => $item->id_bi_thu,
            'name' => $item->ten_bi_thu
        ];
    }
    echo json_encode($result);
    exit();
}

echo json_encode([]);
exit();
