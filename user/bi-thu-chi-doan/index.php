<?php
if (!isset($_SESSION['bt'])) {
    header('location: ../auth/');
    exit();
}
$id_dot = $dotchamdiem->dotchamdiem__Get_Last()->id_dot;

if (isset($_GET['id_dot'])) {
    $id_dot = $_GET['id_dot'];
}
$id_sinh_vien = $_SESSION['bt']->id_nguoi_dung;
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'lop';

if ($mode == 'ban_than') {
    $id_sinh_vien = $_SESSION['bt']->id_nguoi_dung;
} else {
    // Quân sửa: Mặc định không chọn sinh viên nào khi vào chế độ chấm cho lớp (tránh tự chọn bản thân)
    $id_sinh_vien = '';
    if (isset($_GET['id_sinh_vien']) && $_GET['id_sinh_vien'] != '') {
        // Quân sửa: Không cho phép chọn bản thân bí thư chi đoàn trong chế độ chấm cho lớp
        if ($_GET['id_sinh_vien'] != $_SESSION['bt']->id_nguoi_dung) {
            $id_sinh_vien = $_GET['id_sinh_vien'];
        }
    }
}
$phieuchamdiem__Get_By_Id_Sinh_Vien = $phieuchamdiem->phieuchamdiem__Get_By_Id_Sinh_Vien($id_sinh_vien, $id_dot);

// Quân sửa: Lấy danh sách lớp
$bt_info = $sinhvien->sinhvien__Get_By_Id($_SESSION['bt']->id_nguoi_dung);
$id_lop_bt = $bt_info ? $bt_info->id_lop_hoc : 0;

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sinhvien_list = [];
switch($filter) {
    case 'chua_tu_cham':
        $sinhvien_list = $sinhvien->sinhvien__Get_Chua_Tu_Cham($id_dot, $id_lop_bt);
        break;
    case 'da_tu_cham':
        $sinhvien_list = $sinhvien->sinhvien__Get_Da_Tu_Cham($id_dot, $id_lop_bt);
        break;
    case 'chua_bt_cham':
        $sinhvien_list = $sinhvien->sinhvien__Get_By_Id_Lop_Hoc_Kq_LTBT($id_dot, $id_lop_bt, -1);
        break;
    case 'da_bt_cham':
        $sinhvien_list = $sinhvien->sinhvien__Get_By_Id_Lop_Hoc_Kq_LTBT($id_dot, $id_lop_bt, null);
        break;
    default:
        $sinhvien_list = $sinhvien->sinhvien__Get_All_In_Lop($id_dot, $id_lop_bt);
        break;
}

// Quân sửa: Lọc bỏ tài khoản của bí thư chi đoàn khỏi danh sách chấm điểm lớp
if ($mode == 'lop') {
    $sinhvien_list = array_filter($sinhvien_list, function($sv) {
        return $sv->id_sinh_vien != $_SESSION['bt']->id_nguoi_dung;
    });
}
$sinhvien_list = array_values($sinhvien_list);

// Quân sửa: Tính toán phân trang cho danh sách sinh viên
$first_id = null;
$prev_id = null;
$next_id = null;
$last_id = null;
$current_index = false;

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

if (isset($phieuchamdiem__Get_By_Id_Sinh_Vien->id_lop_ap_dung)) {
    $sinhvien__Get_By_Id = $sinhvien->sinhvien__Get_By_Id($id_sinh_vien);
    $id_lop_ap_dung = isset($phieuchamdiem__Get_By_Id_Sinh_Vien->id_lop_ap_dung) ? $phieuchamdiem__Get_By_Id_Sinh_Vien->id_lop_ap_dung : 0;
    $lopapdung__Get_By_Id = $lopapdung->lopapdung__Get_By_Id($id_lop_ap_dung);
    $dotchamdiem__Get_By_Id = $dotchamdiem->dotchamdiem__Get_By_Id($id_dot);
    $id_hoc_ky = isset($dotchamdiem__Get_By_Id->id_hoc_ky) ? $dotchamdiem__Get_By_Id->id_hoc_ky : 0;
    $hocky__Get_By_Id = $hocky->hocky__Get_By_Id($id_hoc_ky);
    $id_nam_hoc = isset($hocky__Get_By_Id->id_nam_hoc) ? $hocky__Get_By_Id->id_nam_hoc : 0;

    $namhoc__Get_By_Id = $namhoc->namhoc__Get_By_Id($id_nam_hoc);
    $id_mau_phieu = isset($lopapdung__Get_By_Id->id_mau_phieu) ? $lopapdung__Get_By_Id->id_mau_phieu : 0;
    $mauphieu__Get_By_Id = $mauphieu->mauphieu__Get_By_Id($id_mau_phieu);
    $lophoc__Get_By_Id = $lophoc->lophoc__Get_By_Id($lopapdung__Get_By_Id->id_lop_hoc);
    $id_khoa_hoc = isset($lophoc__Get_By_Id->id_khoa_hoc) ? $lophoc__Get_By_Id->id_khoa_hoc : 0;

    $khoahoc__Get_By_Id = $khoahoc->khoahoc__Get_By_Id($id_khoa_hoc);
    
    // Quân sửa: Lấy thông tin Khoa/Bộ môn của sinh viên để hiển thị trên phiếu
    $id_nganh_hoc = isset($lophoc__Get_By_Id->id_nganh_hoc) ? $lophoc__Get_By_Id->id_nganh_hoc : 0;
    $nganhhoc__Get_By_Id = $nganhhoc->nganhhoc__Get_By_Id($id_nganh_hoc);
    $id_khoa = isset($nganhhoc__Get_By_Id->id_khoa) ? $nganhhoc__Get_By_Id->id_khoa : 0;
    $khoa__Get_By_Id = $khoa->khoa__Get_By_Id($id_khoa);

    $bocauhoi__Get_By_Id_Mau_Phieu = $bocauhoi->bocauhoi__Get_By_Id_Mau_Phieu($id_mau_phieu);

    $ketquaxeploai__Get_By_Id_Phieu = $ketquaxeploai->ketquaxeploai__Get_By_Id_Phieu($id_lop_ap_dung, $id_dot, $id_sinh_vien);
}



?>
<link rel="stylesheet" href="../assets/css/user.css">
<style>
    /* Override the global sidebar margin from main.css for this specific student page */
    body:not(.sidebar-collapse) .content-wrapper.student-evaluation-wrapper,
    body .content-wrapper.student-evaluation-wrapper {
        margin-left: 0 !important;
    }

    .evaluation-page {
        width: 100% !important;
        max-width: none !important;
        padding: 12px 16px !important;
        margin: 0 !important;
    }
</style>
<?php if (!isset($phieuchamdiem__Get_By_Id_Sinh_Vien->id_lop_ap_dung)) : ?>
<div class="content-wrapper student-evaluation-wrapper">
    <!-- Content Header (Page header) -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-body">
                        <form method="get" action="">
                            <input type="hidden" name="page" value="bi-thu-chi-doan">
                            <input type="hidden" name="id_dot" value="<?= $id_dot ?>">
                            
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label>Chế độ chấm</label>
                                    <select class="form-control" name="mode" onchange="this.form.submit()">
                                        <option value="ban_than" <?= $mode == 'ban_than' ? 'selected' : '' ?>>Chấm cho bản thân</option>
                                        <option value="lop" <?= $mode == 'lop' ? 'selected' : '' ?>>Chấm cho lớp</option>
                                    </select>
                                </div>
                                <?php if ($mode == 'lop'): ?>
                                <div class="col-md-4">
                                    <label>Lọc sinh viên</label>
                                    <select class="form-control" name="filter" onchange="this.form.submit()">
                                        <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Tất cả sinh viên</option>
                                        <option value="da_tu_cham" <?= $filter == 'da_tu_cham' ? 'selected' : '' ?>>Đã tự chấm</option>
                                        <option value="chua_tu_cham" <?= $filter == 'chua_tu_cham' ? 'selected' : '' ?>>Chưa tự chấm</option>
                                        <option value="da_bt_cham" <?= $filter == 'da_bt_cham' ? 'selected' : '' ?>>Đã được bí thư chi đoàn chấm</option>
                                        <option value="chua_bt_cham" <?= $filter == 'chua_bt_cham' ? 'selected' : '' ?>>Chưa được bí thư chi đoàn chấm</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label>Sinh viên (<?= count($sinhvien_list) ?>)</label>
                                    <select class="form-control" name="id_sinh_vien" onchange="this.form.submit()">
                                        <option value="">-- Chọn sinh viên --</option>
                                        <?php foreach ($sinhvien_list as $sv_item): ?>
                                            <option value="<?= $sv_item->id_sinh_vien ?>" <?= $id_sinh_vien == $sv_item->id_sinh_vien ? 'selected' : '' ?>>
                                                <?= $sv_item->ma_sinh_vien ?> - <?= $sv_item->ten_sinh_vien ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <div class="card-tools">

                        </div>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">

        <div class="card overflow-auto w-100">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h3 class="card-title text-center font-weight-bold w-100 mt-3 mb-3">
                            Bạn không có trong đợt này</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php else : ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper student-evaluation-wrapper">
    <!-- Content Header (Page header) -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-body">
                        <form method="get" action="">
                            <input type="hidden" name="page" value="bi-thu-chi-doan">
                            <input type="hidden" name="id_dot" value="<?= $id_dot ?>">
                            
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label>Chế độ chấm</label>
                                    <select class="form-control" name="mode" onchange="this.form.submit()">
                                        <option value="ban_than" <?= $mode == 'ban_than' ? 'selected' : '' ?>>Chấm cho bản thân</option>
                                        <option value="lop" <?= $mode == 'lop' ? 'selected' : '' ?>>Chấm cho lớp</option>
                                    </select>
                                </div>
                                <?php if ($mode == 'lop'): ?>
                                <div class="col-md-4">
                                    <label>Lọc sinh viên</label>
                                    <select class="form-control" name="filter" onchange="this.form.submit()">
                                        <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Tất cả sinh viên</option>
                                        <option value="da_tu_cham" <?= $filter == 'da_tu_cham' ? 'selected' : '' ?>>Đã tự chấm</option>
                                        <option value="chua_tu_cham" <?= $filter == 'chua_tu_cham' ? 'selected' : '' ?>>Chưa tự chấm</option>
                                        <option value="da_bt_cham" <?= $filter == 'da_bt_cham' ? 'selected' : '' ?>>Đã được bí thư chi đoàn chấm</option>
                                        <option value="chua_bt_cham" <?= $filter == 'chua_bt_cham' ? 'selected' : '' ?>>Chưa được bí thư chi đoàn chấm</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label>Sinh viên (<?= count($sinhvien_list) ?>)</label>
                                    <select class="form-control" name="id_sinh_vien" onchange="this.form.submit()">
                                        <option value="">-- Chọn sinh viên --</option>
                                        <?php foreach ($sinhvien_list as $index => $sv_item): ?>
                                            <option value="<?= $sv_item->id_sinh_vien ?>" <?= $id_sinh_vien == $sv_item->id_sinh_vien ? 'selected' : '' ?>>
                                                <?= ($index + 1) ?>. <?= $sv_item->ma_sinh_vien ?> - <?= $sv_item->ten_sinh_vien ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <div class="card-tools">

                        </div>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content evaluation-page">
        <!-- Quân sửa: Thêm nút phân trang chuyển nhanh giữa các sinh viên -->
        <?php if ($mode == 'lop' && count($sinhvien_list) > 0): ?>
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $first_id ?>" 
                       class="btn btn-outline-primary <?= ($current_index === 0 || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-double-left"></i> Đầu
                    </a>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $prev_id ?>" 
                       class="btn btn-outline-primary <?= ($prev_id === null || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-left"></i> Trước
                    </a>
                </div>
                <div class="text-muted font-weight-bold">
                    Sinh viên <?= ($current_index !== false) ? ($current_index + 1) : 0 ?> / <?= count($sinhvien_list) ?>
                </div>
                <div>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $next_id ?>" 
                       class="btn btn-outline-primary <?= ($next_id === null || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        Sau <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $last_id ?>" 
                       class="btn btn-outline-primary <?= ($current_index === count($sinhvien_list) - 1 || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        Cuối <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quân sửa: Định tuyến đúng hành động cập nhật điểm (Sinh viên tự chấm hoặc Bí thư chi đoàn chấm) -->
        <form class="form" action="bi-thu-chi-doan/action.php?req=<?= $mode == 'ban_than' ? 'add_sv' : 'add' ?>" method="post" enctype="multipart/form-data">

            <input type="hidden" name="id_phieu" value="<?= $phieuchamdiem__Get_By_Id_Sinh_Vien->id_phieu ?>">
            <div class="card overflow-auto w-100">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="card-title text-center font-weight-bold w-100 mt-3 mb-3" style="font-size: 1.65rem !important;">
                                <?= $mauphieu__Get_By_Id->ten_mau_phieu ?></h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <h3 class="card-title">Mã số sinh viên: <?= $sinhvien__Get_By_Id->ma_sinh_vien ?></h3>
                        </div>

                        <div class="col text-right">
                            <p class="card-title w-100">Lớp: <?= $lophoc__Get_By_Id->ten_lop_hoc ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <h3 class="card-title">Họ tên sinh viên: <?= $sinhvien__Get_By_Id->ten_sinh_vien ?></h3>
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
                <!-- /.card-header -->
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
                            <colgroup>
                                <col style="width:80px">
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
                                                <td rowspan="<?= $rowspan ?>" class="align-middle text-center" style="padding:6px 4px;">
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
                                                    <!-- Quân sửa: Chỉ cho phép bí thư chi đoàn sửa cột SV Tự Chấm khi chấm bản thân, và ngược lại -->
                                                    <input type="number" class="form-control kq_sv" name="kq_sv[]"
                                                        style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                                                        <?= ($mode == 'lop' || $dotchamdiem__Get_By_Id->trang_thai == 0 || $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1) ? 'readonly' : '' ?>
                                                        pattern="[-+]?[0-9]{1,2}"
                                                        title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0"
                                                        max="<?= $item_2->can_tren ?>" required
                                                        value="<?= isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_sv)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_sv)[$i] : 0 ?>">
                                                </td>
                                                <td class="text-center align-middle" style="padding:4px;">
                                                    <input type="number" class="form-control kq_lt_bt" name="kq_lt_bt[]"
                                                        style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                                                        pattern="[-+]?[0-9]{1,2}"
                                                        title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0"
                                                        max="<?= $item_2->can_tren ?>" required  <?= ($mode == 'ban_than' || $dotchamdiem__Get_By_Id->trang_thai == 0 || ($phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1 && $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 2)) ? 'readonly' : '' ?> value="<?= isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_lt_bt)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_lt_bt)[$i] : 0 ?>">
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
                                                    <input type="number" class="form-control kq_gv" name="kq_gv[]"
                                                        style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                                                        pattern="[-+]?[0-9]{1,2}"
                                                        title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0"
                                                        max="<?= $item_2->can_tren ?>" required disabled
                                                        value="<?= isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_gv)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_gv)[$i] : 0 ?>">
                                                </td>
                                                <td class="text-center align-middle" style="padding:4px;">
                                                    <?php if ($muc->muc__Get_By_Id($item_3->id_muc)->co_minh_chung == 1): ?>
                                                        <?php
                                                            $minh_chung_cua_muc = $minhchung->minhchung__Get_By_Id_Phieu_And_Muc($phieuchamdiem__Get_By_Id_Sinh_Vien->id_phieu, $item_3->id_muc);
                                                            $has_existing = count($minh_chung_cua_muc) > 0;
                                                            $existing_url = $has_existing ? 'sinh-vien/image.php?id_minh_chung=' . $minh_chung_cua_muc[0]->id_minh_chung : '';
                                                            $existing_name = $has_existing ? basename($minh_chung_cua_muc[0]->hinh_anh) : '';
                                                        ?>
                                                        <div class="evidence-upload" style="display: flex; align-items: center; justify-content: center; gap: 5px; flex-wrap: wrap; flex-direction: column;">
                                                            <input type="file" name="minh_chung_muc[<?= $item_3->id_muc ?>][]" accept="image/*,application/pdf"
                                                                <?= $dotchamdiem__Get_By_Id->trang_thai == 0 ? 'disabled' : '' ?>
                                                                <?= $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1 ? 'disabled' : '' ?>
                                                                class="evidence-file-input" <?= $mode == 'lop' ? 'disabled' : '' ?> style="display:none;">

                                                            <button type="button" class="btn btn-secondary btn-sm evidence-select-btn"
                                                                <?= $has_existing ? 'style="display:none;"' : '' ?>
                                                                <?= $mode == 'lop' ? 'disabled style="display:none;"' : '' ?>
                                                                <?= $dotchamdiem__Get_By_Id->trang_thai == 0 ? 'disabled' : '' ?>
                                                                <?= $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1 ? 'disabled' : '' ?>
                                                                style="font-size: 11px; padding: 3px 6px;">
                                                                Chọn tệp
                                                            </button>

                                                            <div class="evidence-selected-info" style="<?= !$has_existing ? 'display:none;' : 'display:flex;' ?> flex-direction: column; align-items: center; gap: 3px; width: 100%;">
                                                                <span class="evidence-file-name" title="<?= htmlspecialchars($existing_name) ?>" style="max-width: 90px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 11px;">
                                                                    <?= htmlspecialchars($existing_name) ?>
                                                                </span>
                                                                <div style="display:flex; justify-content:center; gap:3px; margin-top:2px;">
                                                                    <a class="btn btn-info btn-sm evidence-preview-link" target="_blank" rel="noopener"
                                                                       href="<?= $existing_url ?>" style="font-size: 11px; padding: 3px 6px;">Xem</a>
                                                                    <button type="button" class="btn btn-warning btn-sm evidence-change-btn"
                                                                        <?= $mode == 'lop' ? 'disabled style="display:none;"' : '' ?>
                                                                        <?= $dotchamdiem__Get_By_Id->trang_thai == 0 ? 'disabled' : '' ?>
                                                                        <?= $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1 ? 'disabled' : '' ?>
                                                                        style="font-size: 11px; padding: 3px 6px;">
                                                                        Thay tệp
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted d-block" style="font-size: 0.75rem;">(Không)</span>
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
                                        <input type="number" class="form-control font-weight-bold bg-success text-white" id="sum_sv" placeholder="0" min="0" max="100" readonly required style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                    </td>
                                    <td class="align-middle" style="padding:4px;">
                                        <input type="number" class="form-control font-weight-bold" id="sum_lt_bt" placeholder="0" min="0" max="100" readonly required style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                    </td>
                                    <td class="align-middle" style="padding:4px;">
                                        <input type="number" class="form-control font-weight-bold" id="sum_btdk" placeholder="0" min="0" max="100" readonly required style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                    </td>
                                    <td class="align-middle" style="padding:4px;">
                                        <input type="number" class="form-control font-weight-bold" id="sum_gv" placeholder="0" min="0" max="100" readonly required style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                    </td>
                                    <td style="padding:4px;"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <div class="card-footer">
                <input type="submit" value="Cập nhật" class="btn btn-danger float-right" id="submit"
                    <?= $dotchamdiem__Get_By_Id->trang_thai == 0 ? 'disabled' : '' ?>
                    <?= $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1 ? 'disabled' : '' ?>>
            </div>
        </form>

        <!-- Quân sửa: Thêm nút phân trang chuyển nhanh giữa các sinh viên (dưới form) -->
        <?php if ($mode == 'lop' && count($sinhvien_list) > 0): ?>
        <div class="row mt-3 mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $first_id ?>" 
                       class="btn btn-outline-primary <?= ($current_index === 0 || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-double-left"></i> Đầu
                    </a>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $prev_id ?>" 
                       class="btn btn-outline-primary <?= ($prev_id === null || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-left"></i> Trước
                    </a>
                </div>
                <div class="text-muted font-weight-bold">
                    Sinh viên <?= ($current_index !== false) ? ($current_index + 1) : 0 ?> / <?= count($sinhvien_list) ?>
                </div>
                <div>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $next_id ?>" 
                       class="btn btn-outline-primary <?= ($next_id === null || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        Sau <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $last_id ?>" 
                       class="btn btn-outline-primary <?= ($current_index === count($sinhvien_list) - 1 || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        Cuối <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>




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
</div>

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


    $('.kq_sv').change(function(event) {
        var kq = 0;
        var kq_sv = document.getElementsByClassName("kq_sv");
        for (i = 0; i < kq_sv.length; i++) {
            a = Number(kq_sv[i].value);
            console.log(typeof a);
            kq += a;
        }
        document.getElementById("sum_sv").value = kq;
        if (kq > 100) {
            document.getElementById("submit").setAttribute("disabled", true);
            document.getElementById("submit").setAttribute("value", "Điểm không hợp lệ");
            $("#sum_sv").addClass("bg-danger");
        } else {
            document.getElementById("submit").removeAttribute("disabled");
            document.getElementById("submit").setAttribute("value", "Cập nhật");
            $("#sum_sv").removeClass("bg-danger");
        }
    });

    // Quân sửa: Thêm sự kiện change để tính tổng điểm Bí thư chi đoàn/Bí thư động
    $('.kq_lt_bt').change(function(event) {
        var kq = 0;
        var kq_lt_bt = document.getElementsByClassName("kq_lt_bt");
        for (i = 0; i < kq_lt_bt.length; i++) {
            a = Number(kq_lt_bt[i].value);
            console.log(typeof a);
            kq += a;
        }
        document.getElementById("sum_lt_bt").value = kq;
        if (kq > 100) {
            document.getElementById("submit").setAttribute("disabled", true);
            document.getElementById("submit").setAttribute("value", "Điểm không hợp lệ");
            $("#sum_lt_bt").addClass("bg-danger");
        } else {
            document.getElementById("submit").removeAttribute("disabled");
            document.getElementById("submit").setAttribute("value", "Cập nhật");
            $("#sum_lt_bt").removeClass("bg-danger");
        }
    });

    document.querySelectorAll('.evidence-upload').forEach(function(container) {
        var fileInput = container.querySelector('.evidence-file-input');
        var selectBtn = container.querySelector('.evidence-select-btn');
        var changeBtn = container.querySelector('.evidence-change-btn');
        var infoDiv = container.querySelector('.evidence-selected-info');
        var nameSpan = container.querySelector('.evidence-file-name');
        var previewLink = container.querySelector('.evidence-preview-link');
        var currentObjectUrl = null;

        if (selectBtn) {
            selectBtn.addEventListener('click', function() {
                fileInput.click();
            });
        }

        if (changeBtn) {
            changeBtn.addEventListener('click', function() {
                fileInput.click();
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    var file = this.files[0];
                    nameSpan.textContent = file.name;
                    nameSpan.title = file.name;

                    if (currentObjectUrl) {
                        URL.revokeObjectURL(currentObjectUrl);
                    }
                    currentObjectUrl = URL.createObjectURL(file);
                    previewLink.href = currentObjectUrl;

                    if (selectBtn) selectBtn.style.display = 'none';
                    if (infoDiv) infoDiv.style.display = 'flex';
                }
            });
        }
    });

})
</script>


<?php endif ?>
