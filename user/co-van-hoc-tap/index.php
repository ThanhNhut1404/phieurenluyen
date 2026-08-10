<?php
if (!isset($_SESSION['gv'])) {
    header('location: ../auth/');
    exit();
}
$phancong__Get_By_Id_Giang_Vien_All = $phancong->phancong__Get_By_Id_Giang_Vien_All($_SESSION['gv']->id_nguoi_dung);

$id_dot = $dotchamdiem->dotchamdiem__Get_Last()->id_dot;

$id_lop_hoc  = -2;
$id_sinh_vien  = -2;
if (isset($_GET['id_lop_hoc'])) {
    $id_lop_hoc = $_GET['id_lop_hoc'];
}

if (isset($_GET['id_dot'])) {
    $id_dot = $_GET['id_dot'];
}
if (isset($_GET['id_sinh_vien'])) {
    $id_sinh_vien = $_GET['id_sinh_vien'];
}

$phieuchamdiem__Get_By_Id_Sinh_Vien = $phieuchamdiem->phieuchamdiem__Get_By_Id_Sinh_Vien($id_sinh_vien, $id_dot);
$sinhvien__Get_By_Id = $sinhvien->sinhvien__Get_By_Id($id_sinh_vien);
// Quân sửa: Lấy đúng id_lop_ap_dung để tránh lỗi lấy thuộc tính trên biến bool
$id_lop_ap_dung = isset($phieuchamdiem__Get_By_Id_Sinh_Vien->id_lop_ap_dung) ? $phieuchamdiem__Get_By_Id_Sinh_Vien->id_lop_ap_dung : 0;
$lopapdung__Get_By_Id = $lopapdung->lopapdung__Get_By_Id($id_lop_ap_dung);
$dotchamdiem__Get_By_Id = $dotchamdiem->dotchamdiem__Get_By_Id($id_dot);
$id_hoc_ky = isset($dotchamdiem__Get_By_Id->id_hoc_ky) ? $dotchamdiem__Get_By_Id->id_hoc_ky : 0;
$hocky__Get_By_Id = $hocky->hocky__Get_By_Id($id_hoc_ky);
$id_nam_hoc = isset($hocky__Get_By_Id->id_nam_hoc) ? $hocky__Get_By_Id->id_nam_hoc : 0;

$namhoc__Get_By_Id = $namhoc->namhoc__Get_By_Id($id_nam_hoc);
$id_mau_phieu = isset($lopapdung__Get_By_Id->id_mau_phieu) ? $lopapdung__Get_By_Id->id_mau_phieu : 0;
$mauphieu__Get_By_Id = $mauphieu->mauphieu__Get_By_Id($id_mau_phieu);
$lophoc__Get_By_Id = $lophoc->lophoc__Get_By_Id($id_lop_hoc);
$id_khoa_hoc = isset($lophoc__Get_By_Id->id_khoa_hoc) ? $lophoc__Get_By_Id->id_khoa_hoc : 0;

$khoahoc__Get_By_Id = $khoahoc->khoahoc__Get_By_Id($id_khoa_hoc);
$bocauhoi__Get_By_Id_Mau_Phieu = $bocauhoi->bocauhoi__Get_By_Id_Mau_Phieu($id_mau_phieu);

$sinhvien__Get_By_Id_Lop_Hoc_Chua_Cham = $sinhvien->sinhvien__Get_By_Id_Lop_Hoc_Kq_CVHT($id_dot, $id_lop_hoc, -1);
$sinhvien__Get_By_Id_Lop_Hoc_Da_Cham = $sinhvien->sinhvien__Get_By_Id_Lop_Hoc_Kq_CVHT($id_dot, $id_lop_hoc, null);
// Quân sửa: Định nghĩa các biến bị thiếu và đổi sang hàm Get_By_Id_Lop_Hoc_Kq_CVHT cho Cố vấn học tập
$dw = "";

// Quân sửa: Hợp nhất danh sách để tính toán phân trang ổn định theo mã sinh viên
$sinhvien_list = array_merge($sinhvien__Get_By_Id_Lop_Hoc_Chua_Cham, $sinhvien__Get_By_Id_Lop_Hoc_Da_Cham);
usort($sinhvien_list, function($a, $b) {
    return strcmp($a->ma_sinh_vien, $b->ma_sinh_vien);
});
$sinhvien_list = array_values($sinhvien_list);

$first_id = null;
$prev_id = null;
$next_id = null;
$last_id = null;
$current_index = false;
$student_ids = [];

if (count($sinhvien_list) > 0) {
    $student_ids = array_map(function($sv) {
        return $sv->id_sinh_vien;
    }, $sinhvien_list);
    
    $current_index = array_search($id_sinh_vien, $student_ids);
    
    $first_id = $student_ids[0];
    $last_id = $student_ids[count($student_ids) - 1];
    
    if ($current_index !== false) {
        if ($current_index > 0) {
            $prev_id = $student_ids[$current_index - 1];
        }
        if ($current_index < count($student_ids) - 1) {
            $next_id = $student_ids[$current_index + 1];
        }
    }
}

$ketquaxeploai__Get_By_Id_Phieu = $ketquaxeploai->ketquaxeploai__Get_By_Id_Phieu($id_lop_hoc, $id_dot, $id_sinh_vien);
$btdk_has_scored = false;
if (isset($phieuchamdiem__Get_By_Id_Sinh_Vien->id_lop_ap_dung)) {
    $btdk_has_scored = (!empty($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_btdk));
}
?>
<link rel="stylesheet" href="../assets/css/user.css">
<!-- Quân sửa: Thêm CSS override và class wrapper để loại bỏ khoảng trắng bên trái do ảnh hưởng sidebar -->
<style>
    body:not(.sidebar-collapse) .content-wrapper.student-evaluation-wrapper,
    body .content-wrapper.student-evaluation-wrapper {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper student-evaluation-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col">
                    <label for="">Chọn lớp (<?= count($phancong__Get_By_Id_Giang_Vien_All) ?>)</label>
                    <select class="form-control" name="id_lop_hoc" required onchange="location.href=this.value">
                        <option value="">Chọn lớp</option>
                        <?php foreach ($phancong__Get_By_Id_Giang_Vien_All as $item) : ?>
                            <option value="?page=co-van-hoc-tap&id_lop_hoc=<?= $item->id_lop_hoc ?>&id_dot=<?= $id_dot ?>" <?= $id_lop_hoc == $item->id_lop_hoc ? "selected" : "" ?>>
                                <?= $item->ten_lop_hoc ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <label for="">Đã chấm điểm (<?= count($sinhvien__Get_By_Id_Lop_Hoc_Da_Cham) ?>)</label>
                    <select class="form-control" name="id_sinh_vien" required onchange="location.href=this.value">
                        <option value="">Chọn sinh viên</option>
                        <?php foreach ($sinhvien__Get_By_Id_Lop_Hoc_Da_Cham as $item) : ?>
                            <?php
                            $abs_index = array_search($item->id_sinh_vien, $student_ids);
                            $num_prefix = ($abs_index !== false) ? ($abs_index + 1) . '. ' : '';
                            ?>
                            <option value="?page=co-van-hoc-tap&id_lop_hoc=<?= $id_lop_hoc ?>&id_dot=<?= $id_dot ?>&id_sinh_vien=<?= $item->id_sinh_vien ?>" <?= $id_sinh_vien == $item->id_sinh_vien ? "selected" : "" ?>>
                                <?= $num_prefix ?><?= $item->ma_sinh_vien ?> - <?= $item->ten_sinh_vien ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <label for="">Chưa chấm điểm (<?= count($sinhvien__Get_By_Id_Lop_Hoc_Chua_Cham) ?>)</label>
                    <select class="form-control" name="id_sinh_vien" required onchange="location.href=this.value">
                        <option value="">Chọn sinh viên</option>

                        <?php foreach ($sinhvien__Get_By_Id_Lop_Hoc_Chua_Cham as $item) : ?>
                            <?php
                            $abs_index = array_search($item->id_sinh_vien, $student_ids);
                            $num_prefix = ($abs_index !== false) ? ($abs_index + 1) . '. ' : '';
                            ?>
                            <option value="?page=co-van-hoc-tap&id_lop_hoc=<?= $id_lop_hoc ?>&id_dot=<?= $id_dot ?>&id_sinh_vien=<?= $item->id_sinh_vien ?>" <?= $id_sinh_vien == $item->id_sinh_vien ? "selected" : "" ?>>
                                <?= $num_prefix ?><?= $item->ma_sinh_vien ?> - <?= $item->ten_sinh_vien ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </section>
    <?php if (isset($phieuchamdiem__Get_By_Id_Sinh_Vien->id_lop_ap_dung)) : ?>

        <section class="content">
            <!-- Quân sửa: Thêm nút phân trang chuyển nhanh giữa các sinh viên -->
            <?php if (count($sinhvien_list) > 0): ?>
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <a href="?page=co-van-hoc-tap&id_lop_hoc=<?= $id_lop_hoc ?>&id_dot=<?= $id_dot ?>&id_sinh_vien=<?= $first_id ?>" 
                           class="btn btn-outline-primary <?= ($current_index === 0 || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                            <i class="fas fa-angle-double-left"></i> Đầu
                        </a>
                        <a href="?page=co-van-hoc-tap&id_lop_hoc=<?= $id_lop_hoc ?>&id_dot=<?= $id_dot ?>&id_sinh_vien=<?= $prev_id ?>" 
                           class="btn btn-outline-primary <?= ($prev_id === null || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                            <i class="fas fa-angle-left"></i> Trước
                        </a>
                    </div>
                    <div class="text-muted font-weight-bold">
                        Sinh viên <?= ($current_index !== false) ? ($current_index + 1) : 0 ?> / <?= count($sinhvien_list) ?>
                    </div>
                    <div>
                        <a href="?page=co-van-hoc-tap&id_lop_hoc=<?= $id_lop_hoc ?>&id_dot=<?= $id_dot ?>&id_sinh_vien=<?= $next_id ?>" 
                           class="btn btn-outline-primary <?= ($next_id === null || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                            Sau <i class="fas fa-angle-right"></i>
                        </a>
                        <a href="?page=co-van-hoc-tap&id_lop_hoc=<?= $id_lop_hoc ?>&id_dot=<?= $id_dot ?>&id_sinh_vien=<?= $last_id ?>" 
                           class="btn btn-outline-primary <?= ($current_index === count($sinhvien_list) - 1 || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                            Cuối <i class="fas fa-angle-double-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <form class="form" action="co-van-hoc-tap/action.php?req=add" method="post" enctype="multipart/form-data">

                <input type="hidden" name="id_phieu" value="<?= $phieuchamdiem__Get_By_Id_Sinh_Vien->id_phieu ?>">
                <!-- Quân sửa: Thêm cảnh báo nếu sinh viên chưa được BCH Đoàn khoa chấm điểm -->
                <?php if (!$btdk_has_scored): ?>
                    <div class="alert alert-warning text-center">
                        <strong>Sinh viên này chưa được Ban chấp hành Đoàn khoa chấm điểm.</strong> Bạn không thể chấm điểm lúc này.
                    </div>
                <?php endif; ?>
                <div class="card overflow-auto w-100">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <h3 class="card-title text-center font-weight-bold w-100 mt-3 mb-3">
                                    <?= $mauphieu__Get_By_Id->ten_mau_phieu ?></h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <h3 class="card-title">Mã sinh viên: <?= $sinhvien__Get_By_Id->ma_sinh_vien ?></h3>
                            </div>

                            <div class="col text-right">
                                <p class="card-title w-100">Lớp: <?= $lophoc__Get_By_Id->ten_lop_hoc ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <h3 class="card-title">Tên sinh viên: <?= $sinhvien__Get_By_Id->ten_sinh_vien ?></h3>
                            </div>
                            <div class="col text-right">
                                <p class="card-title w-100">Học kỳ: <?= $hocky__Get_By_Id->ten_hoc_ky ?></p>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col">
                                <p class="card-title">Khóa học: <?= $khoahoc__Get_By_Id->ten_khoa_hoc ?></p>
                            </div>

                            <div class="col text-right">
                                <p class="card-title w-100">Năm học: <?= $namhoc__Get_By_Id->ten_nam_hoc ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col">
                                <p class="card-title">Điểm rèn luyện:
                                    <?= isset($ketquaxeploai__Get_By_Id_Phieu->ket_qua) ? $ketquaxeploai__Get_By_Id_Phieu->ket_qua : "Chưa tổng kết" ?>
                                </p>
                            </div>

                            <div class="col text-right">
                                <p class="card-title w-100">Ngày xếp loại:
                                    <?= isset($ketquaxeploai__Get_By_Id_Phieu->ngay_xep_loai) ? $ketquaxeploai__Get_By_Id_Phieu->ngay_xep_loai : "Chưa tổng kết" ?>
                                </p>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <p class="card-title w-100">Xếp loại:
                                    <?= isset($ketquaxeploai__Get_By_Id_Phieu->xep_loai) ? $ketquaxeploai__Get_By_Id_Phieu->xep_loai : "Chưa tổng kết" ?>
                                </p>

                            </div>

                            <div class="col text-right">
                                <p class="card-title w-100">Ghi chú:
                                    <?= isset($ketquaxeploai__Get_By_Id_Phieu->ghi_chu) ? $ketquaxeploai__Get_By_Id_Phieu->ghi_chu : "" ?>
                                </p>

                            </div>
                        </div>
                    </div>
                    <!-- Quân sửa: Cập nhật giao diện bảng chấm điểm giống sinh viên -->
                    <style>
                        table.table tbody tr td.criterion-text-cell {
                            text-align: left !important;
                            white-space: normal !important;
                            overflow-wrap: break-word !important;
                            word-break: normal !important;
                            line-height: 1.45;
                            padding: 8px 10px !important;
                            vertical-align: middle;
                        }
                    </style>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" style="width: 100%; table-layout: fixed; white-space: normal; overflow-wrap: break-word; word-break: normal;">
                                <!-- Quân sửa: Tăng chiều rộng cột Điều và sửa lỗi tràn chữ gây lệch cột -->
                                <colgroup>
                                    <col style="width:110px">
                                    <col>
                                    <col style="width:65px">
                                    <col style="width:75px">
                                    <col style="width:75px">
                                    <col style="width:75px">
                                    <col style="width:85px">
                                </colgroup>
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th class="align-middle" style="padding:6px 4px;">Điều</th>
                                        <th class="align-middle" style="padding:6px 4px;">Nội dung</th>
                                        <th class="align-middle" style="padding:6px 4px;">SV TỰ<br>CHẤM</th>
                                        <th class="align-middle" style="padding:6px 4px;">LỚP TRƯỞNG<br>BÍ THƯ</th>
                                        <th class="align-middle" style="padding:6px 4px;">BCH ĐOÀN<br>KHOA</th>
                                        <th class="align-middle" style="padding:6px 4px;">CỐ VẤN<br>HỌC TẬP</th>
                                        <th class="align-middle" style="padding:6px 4px;">MINH<br>CHỨNG</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0; ?>
                                    <?php foreach ($bocauhoi__Get_By_Id_Mau_Phieu as $item_1) : ?>
                                        <?php
                                        $rowspan = 0;
                                        $khoan_list = $khoan->khoan__Get_All_By_Id_Dieu($item_1->id_dieu);
                                        foreach ($khoan_list as $k) {
                                            $rowspan += 1;
                                            $rowspan += count($muc->muc__Get_All_By_Id_Khoan($k->id_khoan));
                                        }
                                        $is_first_row_dieu = true;
                                        ?>
                                        <?php foreach ($khoan_list as $item_2) : ?>
                                            <tr class="table-info">
                                                <?php if ($is_first_row_dieu): ?>
                                                    <!-- Quân sửa: Thêm word-break và white-space normal để text không tràn làm lệch cột -->
                                                    <td rowspan="<?= $rowspan ?>" class="align-middle text-center" style="padding:6px 4px; word-break: break-word; overflow-wrap: break-word; white-space: normal;">
                                                        <div class="font-weight-bold"><?= $dieu->dieu__Get_By_Id($item_1->id_dieu)->ten_dieu ?></div>
                                                        <div class="text-muted small mt-1"><?= $dieu->dieu__Get_By_Id($item_1->id_dieu)->ghi_chu ?></div>
                                                    </td>
                                                    <?php $is_first_row_dieu = false; ?>
                                                <?php endif; ?>
                                                <td colspan="6" class="font-weight-bold" style="padding:6px 8px;">
                                                    <?= $khoan->khoan__Get_By_Id($item_2->id_khoan)->ten_khoan ?>
                                                </td>
                                            </tr>
                                            <?php foreach ($muc->muc__Get_All_By_Id_Khoan($item_2->id_khoan) as $item_3) : ?>
                                                <tr>
                                                    <td class="criterion-text-cell">
                                                        - <?= $muc->muc__Get_By_Id($item_3->id_muc)->ten_muc ?>
                                                    </td>
                                                    <td class="text-center align-middle" style="padding:4px;">
                                                        <input type="number" class="form-control kq_sv" name="kq_sv[]"
                                                            style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                                                            pattern="[-+]?[0-9]{1,2}"
                                                            title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0"
                                                            max="<?= $item_2->can_tren ?>" required disabled
                                                            value="<?= isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_sv)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_sv)[$i] : 0 ?>">
                                                    </td>
                                                    <td class="text-center align-middle" style="padding:4px;">
                                                        <input type="number" class="form-control kq_lt_bt" name="kq_lt_bt[]"
                                                            style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                                                            pattern="[-+]?[0-9]{1,2}"
                                                            title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0"
                                                            max="<?= $item_2->can_tren ?>" required disabled
                                                            value="<?= isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_lt_bt)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_lt_bt)[$i] : 0 ?>">
                                                    </td>
                                                    <td class="text-center align-middle" style="padding:4px;">
                                                        <input type="number" class="form-control kq_btdk" name="kq_btdk[]"
                                                            style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                                                            pattern="[-+]?[0-9]{1,2}"
                                                            title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0"
                                                            max="<?= $item_2->can_tren ?>" required disabled
                                                            value="<?= isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_btdk)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_btdk)[$i] : 0 ?>">
                                                    </td>
                                                    <td class="text-center align-middle" style="padding:4px;">
                                                        <!-- Quân sửa: Sửa lỗi hiển thị và thừa kế điểm từ BCH Đoàn khoa cho Cố vấn học tập -->
                                                        <input type="number" class="form-control kq_gv" name="kq_gv[]"
                                                            style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                                                            <?= $dotchamdiem__Get_By_Id->trang_thai == 0 ? 'readonly' : '' ?>
                                                            <?php // Quân sửa: Bỏ kiểm tra trang_thai != 4 của phiếu chấm điểm ?>
                                                            <?= !$btdk_has_scored ? 'readonly' : '' ?>
                                                            pattern="[-+]?[0-9]{1,2}"
                                                            title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0"
                                                            max="<?= $item_2->can_tren ?>" required
                                                            value="<?= (isset($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_gv) && !empty($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_gv) && isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_gv)[$i])) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_gv)[$i] : 0 ?>">
                                                    </td>
                                                    <td class="text-center align-middle" style="padding:4px;">
                                                        <?php if ($muc->muc__Get_By_Id($item_3->id_muc)->co_minh_chung == 1): ?>
                                                            <?php
                                                                $minh_chung_cua_muc = $minhchung->minhchung__Get_By_Id_Phieu_And_Muc($phieuchamdiem__Get_By_Id_Sinh_Vien->id_phieu, $item_3->id_muc);
                                                                $has_existing = count($minh_chung_cua_muc) > 0;
                                                            ?>
                                                            <?php if ($has_existing): ?>
                                                                <?php
                                                                    $existing_url = 'co-van-hoc-tap/image.php?id_minh_chung=' . $minh_chung_cua_muc[0]->id_minh_chung;
                                                                    $existing_name = basename($minh_chung_cua_muc[0]->hinh_anh);
                                                                ?>
                                                                <div style="display:flex; flex-direction:column; align-items:center; gap:2px;">
                                                                    <span class="text-truncate d-inline-block" title="<?= htmlspecialchars($existing_name) ?>" style="max-width:80px; font-size:11px;">
                                                                        <?= htmlspecialchars($existing_name) ?>
                                                                    </span>
                                                                    <a class="btn btn-info btn-xs" target="_blank" rel="noopener"
                                                                       href="<?= $existing_url ?>" style="font-size: 10px; padding: 2px 4px;">Xem</a>
                                                                </div>
                                                            <?php else: ?>
                                                                <span class="text-muted" style="font-size:11px;">Chưa nộp</span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted" style="font-size:11px;">(Không)</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php $i++ ?>
                                            <?php endforeach ?>
                                        <?php endforeach ?>
                                    <?php endforeach ?>
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold table-secondary text-center">
                                        <td colspan="2" class="align-middle text-right" style="padding:6px 8px;">Tổng điểm:</td>
                                        <td class="align-middle" style="padding:4px;">
                                            <input type="number" class="form-control font-weight-bold" id="sum_sv" placeholder="0" min="0" max="100" readonly required style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                        </td>
                                        <td class="align-middle" style="padding:4px;">
                                            <input type="number" class="form-control font-weight-bold" id="sum_lt_bt" placeholder="0" min="0" max="100" readonly required style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                        </td>
                                        <td class="align-middle" style="padding:4px;">
                                            <input type="number" class="form-control font-weight-bold" id="sum_btdk" placeholder="0" min="0" max="100" readonly required style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                        </td>
                                        <td class="align-middle" style="padding:4px;">
                                            <input type="number" class="form-control font-weight-bold bg-success text-white" id="sum_gv" placeholder="0" min="0" max="100" readonly required style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                        </td>
                                        <td style="padding:4px;"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <label for="" class="text-muted text-crimson">Vui lòng thêm minh chứng trước khi nhấn cập
                            nhật</label>
                        <input type="submit" value="Cập nhật" class="btn btn-danger float-right" id="submit"
                            <?php // Quân sửa: Bỏ kiểm tra trang_thai != 4 của phiếu chấm điểm trên nút submit ?>
                            <?= ($dotchamdiem__Get_By_Id->trang_thai == 0 || !$btdk_has_scored) ? 'disabled' : '' ?>>
                    </div>
                </div>
            </form>

            <!-- Quân sửa: Thêm nút phân trang chuyển nhanh giữa các sinh viên (dưới form) -->
            <?php if (count($sinhvien_list) > 0): ?>
            <div class="row mt-3 mb-3">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <a href="?page=co-van-hoc-tap&id_lop_hoc=<?= $id_lop_hoc ?>&id_dot=<?= $id_dot ?>&id_sinh_vien=<?= $first_id ?>" 
                           class="btn btn-outline-primary <?= ($current_index === 0 || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                            <i class="fas fa-angle-double-left"></i> Đầu
                        </a>
                        <a href="?page=co-van-hoc-tap&id_lop_hoc=<?= $id_lop_hoc ?>&id_dot=<?= $id_dot ?>&id_sinh_vien=<?= $prev_id ?>" 
                           class="btn btn-outline-primary <?= ($prev_id === null || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                            <i class="fas fa-angle-left"></i> Trước
                        </a>
                    </div>
                    <div class="text-muted font-weight-bold">
                        Sinh viên <?= ($current_index !== false) ? ($current_index + 1) : 0 ?> / <?= count($sinhvien_list) ?>
                    </div>
                    <div>
                        <a href="?page=co-van-hoc-tap&id_lop_hoc=<?= $id_lop_hoc ?>&id_dot=<?= $id_dot ?>&id_sinh_vien=<?= $next_id ?>" 
                           class="btn btn-outline-primary <?= ($next_id === null || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                            Sau <i class="fas fa-angle-right"></i>
                        </a>
                        <a href="?page=co-van-hoc-tap&id_lop_hoc=<?= $id_lop_hoc ?>&id_dot=<?= $id_dot ?>&id_sinh_vien=<?= $last_id ?>" 
                           class="btn btn-outline-primary <?= ($current_index === count($sinhvien_list) - 1 || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                            Cuối <i class="fas fa-angle-double-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!-- Main content -->

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4 class="card-title">Minh chứng đã thêm</h4>
                        </div>
                        <div class="card-body">

                            <div>
                                <div class="filter-container p-0 row">
                                    <?php foreach ($minhchung->minhchung__Get_By_Id_Phieu($phieuchamdiem__Get_By_Id_Sinh_Vien->id_phieu) as $item) : ?>
                                        <div class="card">
                                            <div class="filtr-item no-flexed " data-category="1">
                                                <a href="co-van-hoc-tap/image.php?id_minh_chung=<?= $item->id_minh_chung ?>" data-toggle="lightbox">
                                                    <img src="<?= $item->hinh_anh ?>" class="img-fluid img-50" />
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

</div>


<div class="card-header">
    <div class="row">
        <div class="col">
            <h3 class="card-title font-weight-bold">
                • Quy định:
            </h3>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <h3 class="card-title">
                - Xếp loại kết quả rèn luyện: xuất sắc (90 - 100 điểm), tốt (90 - 89 điểm), khá (65 - 79
                điểm), trung bình (50 - 64 điểm), yếu (35 - 49 điểm), kém (dưới 35 điểm).
            </h3>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <h3 class="card-title">
                - Kết quả rèn luyện năm học xuất sắc và tốt được nhà trường xét khen thưởng.
            </h3>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <h3 class="card-title">
                - Kết quả rèn luyện yếu, kém 2 học kỳ liên tiếp phải tạm ngừng học ít nhất 1 học kỳ ở
                học kỳ
                tiếp theo.
            </h3>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <h3 class="card-title">
                - Sinh viên bị kỷ luật mức khiển trách trong học kỳ thì mức xếp loại không được vượt quá
                loại
                khá, bị kỷ luật mức cảnh cáo thì không được vượt quá loại trung bình.
            </h3>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="col">
                <h3 class="card-title">
                    - Sinh viên không nộp phiếu đánh giá kết quả rèn luyện mà không có lý do chính
                    đáng, cố
                    vấn học
                    tập và tập thể lớp đánh giá kết quả rèn luyện cho sinh viên không nộp phiếu và
                    trừ điểm
                    để hạ
                    một bậc xếp loại (xuất sắc: trừ 10 điểm, tốt: trừ 10 điểm, khá: trừ 15 điểm,
                    trung bình:
                    trừ 15
                    điểm, yếu: trừ 15 điểm).
                </h3>
            </div>
        </div>
    </div>
</div>
</section>
<?php endif; ?>

<script>
    window.addEventListener('load', function() {
        var kq = 0;
        var kq_sv = document.getElementsByClassName("kq_sv");
        for (i = 0; i < kq_sv.length; i++) {
            a = Number(kq_sv[i].value);
            console.log(typeof a);
            kq += a;
        }
        document.getElementById("sum_sv").value = kq;

        var kq_lt_bt_load = 0;
        var kq_lt_bt = document.getElementsByClassName("kq_lt_bt");
        for (i = 0; i < kq_lt_bt.length; i++) {
            a = Number(kq_lt_bt[i].value);
            console.log(typeof a);
            kq_lt_bt_load += a;
        }
        document.getElementById("sum_lt_bt").value = kq_lt_bt_load;


        var kq_btdk_load = 0;
        var kq_btdk = document.getElementsByClassName("kq_btdk");
        for (i = 0; i < kq_btdk.length; i++) {
            a = Number(kq_btdk[i].value);
            console.log(typeof a);
            kq_btdk_load += a;
        }
        document.getElementById("sum_btdk").value = kq_btdk_load;


        var kq_gv_load = 0;
        var kq_gv = document.getElementsByClassName("kq_gv");
        for (i = 0; i < kq_gv.length; i++) {
            a = Number(kq_gv[i].value);
            console.log(typeof a);
            kq_gv_load += a;
        }
        document.getElementById("sum_gv").value = kq_gv_load;



        $('.kq_gv').change(function(event) {
            var kq = 0;
            var kq_gv = document.getElementsByClassName("kq_gv");
            for (i = 0; i < kq_gv.length; i++) {
                a = Number(kq_gv[i].value);
                console.log(typeof a);
                kq += a;
            }
            document.getElementById("sum_gv").value = kq;
            document.getElementById("sum_gv").value = kq;
            if (kq > 100) {
                document.getElementById("submit").setAttribute("disabled", true);
                document.getElementById("submit").setAttribute("value", "Điểm không hợp lệ");
                $("#sum_gv").addClass("bg-danger");
            } else {
                document.getElementById("submit").removeAttribute("disabled");
                document.getElementById("submit").setAttribute("value", "Cập nhật");
                $("#sum_gv").removeClass("bg-danger");
            }
        });



    })
</script>
