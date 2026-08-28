<?php
if (!isset($_SESSION['btdk'])) {
    header('location: ../auth/');
    exit();
}
$id_dot = $dotchamdiem->dotchamdiem__Get_Last()->id_dot;

if (isset($_GET['id_dot'])) {
    $id_dot = $_GET['id_dot'];
}
$bithudoankhoa__Get_By_Id = null;
if (isset($_SESSION['btdk']) && isset($_SESSION['btdk']->id_nguoi_dung)) {
    $bithudoankhoa__Get_By_Id = $bithudoankhoa->bithudoankhoa__Get_By_Id($_SESSION['btdk']->id_nguoi_dung);
}

$lophoc__Get_By_Id_Khoa = [];
if ($bithudoankhoa__Get_By_Id && isset($bithudoankhoa__Get_By_Id->id_khoa) && $bithudoankhoa__Get_By_Id->id_khoa > 0) {
    $res = $lophoc->lophoc__Get_By_Id_Khoa($bithudoankhoa__Get_By_Id->id_khoa);
    $lophoc__Get_By_Id_Khoa = is_array($res) ? $res : [];
}

$id_lop_hoc = isset($_GET['id_lop_hoc']) ? (int) $_GET['id_lop_hoc'] : -2;
$id_sinh_vien = isset($_GET['id_sinh_vien']) ? (int) $_GET['id_sinh_vien'] : -2;

// Check if selected class belongs to this Khoa
$class_belongs_to_khoa = false;
foreach ($lophoc__Get_By_Id_Khoa as $lop) {
    if ($lop->id_lop_hoc == $id_lop_hoc) {
        $class_belongs_to_khoa = true;
        break;
    }
}
if (!$class_belongs_to_khoa) {
    $id_lop_hoc = -2;
}

$phieuchamdiem__Get_By_Id_Sinh_Vien = $phieuchamdiem->phieuchamdiem__Get_By_Id_Sinh_Vien($id_sinh_vien, $id_dot);

$lt_has_scored = false;
if (isset($phieuchamdiem__Get_By_Id_Sinh_Vien->id_lop_ap_dung)) {
    $lt_has_scored = (!empty($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_lt_bt));
}


$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sinhvien_list = [];
if ($id_lop_hoc != -2) {
    switch($filter) {
        case 'chua_lt_cham':
            $sinhvien_list = $sinhvien->sinhvien__Get_Chua_LT_Cham($id_dot, $id_lop_hoc);
            break;
        case 'da_lt_cham':
            $sinhvien_list = $sinhvien->sinhvien__Get_Da_LT_Cham($id_dot, $id_lop_hoc);
            break;
        case 'chua_btdk_cham':
            $sinhvien_list = $sinhvien->sinhvien__Get_Chua_BTDK_Cham($id_dot, $id_lop_hoc);
            break;
        case 'da_btdk_cham':
            $sinhvien_list = $sinhvien->sinhvien__Get_Da_BTDK_Cham($id_dot, $id_lop_hoc);
            break;
        default:
            $sinhvien_list = $sinhvien->sinhvien__Get_All_In_Lop($id_dot, $id_lop_hoc);
            break;
    }
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
    $bocauhoi__Get_By_Id_Mau_Phieu = $bocauhoi->bocauhoi__Get_By_Id_Mau_Phieu($id_mau_phieu);

    // Quân sửa: Lấy đúng kết quả xếp loại theo id_lop_hoc của Bí thư Đoàn khoa
    $ketquaxeploai__Get_By_Id_Phieu = $ketquaxeploai->ketquaxeploai__Get_By_Id_Phieu($id_lop_hoc, $id_dot, $id_sinh_vien);
}



?>
<link rel="stylesheet" href="../assets/css/user.css?v=<?=time()?>">
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
                            <input type="hidden" name="page" value="bi-thu-doan-khoa">
                            <input type="hidden" name="id_dot" value="<?= $id_dot ?>">
                            
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label>Lớp (<?= count($lophoc__Get_By_Id_Khoa) ?>)</label>
                                    <select class="form-control" name="id_lop_hoc" onchange="this.form.submit()">
                                        <option value="-2">-- Chọn lớp --</option>
                                        <?php foreach ($lophoc__Get_By_Id_Khoa as $item) : ?>
                                            <option value="<?= $item->id_lop_hoc ?>" <?= $id_lop_hoc == $item->id_lop_hoc ? "selected" : "" ?>>
                                                <?= $item->ten_lop_hoc ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php if ($id_lop_hoc != -2): ?>
                                <div class="col-md-4">
                                    <label>Lọc sinh viên</label>
                                    <select class="form-control" name="filter" onchange="this.form.submit()">
                                        <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Tất cả sinh viên</option>
                                        <option value="da_lt_cham" <?= $filter == 'da_lt_cham' ? 'selected' : '' ?>>Đã được lớp trưởng chấm</option>
                                        <option value="chua_lt_cham" <?= $filter == 'chua_lt_cham' ? 'selected' : '' ?>>Chưa được lớp trưởng chấm</option>
                                        <option value="da_btdk_cham" <?= $filter == 'da_btdk_cham' ? 'selected' : '' ?>>Đã được Đoàn khoa chấm</option>
                                        <option value="chua_btdk_cham" <?= $filter == 'chua_btdk_cham' ? 'selected' : '' ?>>Chưa được Đoàn khoa chấm</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label>Sinh viên (<?= count($sinhvien_list) ?>)</label>
                                    <select class="form-control" name="id_sinh_vien" onchange="this.form.submit()">
                                        <option value="-2">-- Chọn sinh viên --</option>
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
    <section class="content-header d-none">
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

        
<div class="card overflow-auto w-100 mt-3">
            <div class="card-header p-3">
                <h3 class="card-title text-center text-danger font-weight-bold m-0" style="float: none;">
                    <?php if ($id_sinh_vien == '' || $id_sinh_vien == -2): ?>Vui lòng chọn một sinh viên trong danh sách để bắt đầu chấm điểm<?php else: ?>Sinh viên này không có phiếu đánh giá trong đợt này<?php endif; ?>
                </h3>
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
                            <input type="hidden" name="page" value="bi-thu-doan-khoa">
                            <input type="hidden" name="id_dot" value="<?= $id_dot ?>">
                            
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label>Lớp (<?= count($lophoc__Get_By_Id_Khoa) ?>)</label>
                                    <select class="form-control" name="id_lop_hoc" onchange="this.form.submit()">
                                        <option value="-2">-- Chọn lớp --</option>
                                        <?php foreach ($lophoc__Get_By_Id_Khoa as $item) : ?>
                                            <option value="<?= $item->id_lop_hoc ?>" <?= $id_lop_hoc == $item->id_lop_hoc ? "selected" : "" ?>>
                                                <?= $item->ten_lop_hoc ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php if ($id_lop_hoc != -2): ?>
                                <div class="col-md-4">
                                    <label>Lọc sinh viên</label>
                                    <select class="form-control" name="filter" onchange="this.form.submit()">
                                        <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Tất cả sinh viên</option>
                                        <option value="da_lt_cham" <?= $filter == 'da_lt_cham' ? 'selected' : '' ?>>Đã được lớp trưởng chấm</option>
                                        <option value="chua_lt_cham" <?= $filter == 'chua_lt_cham' ? 'selected' : '' ?>>Chưa được lớp trưởng chấm</option>
                                        <option value="da_btdk_cham" <?= $filter == 'da_btdk_cham' ? 'selected' : '' ?>>Đã được Đoàn khoa chấm</option>
                                        <option value="chua_btdk_cham" <?= $filter == 'chua_btdk_cham' ? 'selected' : '' ?>>Chưa được Đoàn khoa chấm</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label>Sinh viên (<?= count($sinhvien_list) ?>)</label>
                                    <select class="form-control" name="id_sinh_vien" onchange="this.form.submit()">
                                        <option value="-2">-- Chọn sinh viên --</option>
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
    <section class="content-header d-none">
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
        <?php if ($id_lop_hoc != -2 && count($sinhvien_list) > 0): ?>
        <div class="row mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="?page=bi-thu-doan-khoa&id_dot=<?= $id_dot ?>&id_lop_hoc=<?= $id_lop_hoc ?>&filter=<?= $filter ?>&id_sinh_vien=<?= $first_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($current_index === 0 || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-double-left"></i> Đầu
                    </a>
                    <a href="?page=bi-thu-doan-khoa&id_dot=<?= $id_dot ?>&id_lop_hoc=<?= $id_lop_hoc ?>&filter=<?= $filter ?>&id_sinh_vien=<?= $prev_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($prev_id === null || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-left"></i> Trước
                    </a>
                </div>
                <div class="text-muted font-weight-bold">
                    Sinh viên <?= ($current_index !== false) ? ($current_index + 1) : 0 ?> / <?= count($sinhvien_list) ?><?= ($current_index !== false && isset($sinhvien_list[$current_index])) ? ' - ' . htmlspecialchars($sinhvien_list[$current_index]->ten_sinh_vien) : '' ?>
                </div>
                <div>
                    <a href="?page=bi-thu-doan-khoa&id_dot=<?= $id_dot ?>&id_lop_hoc=<?= $id_lop_hoc ?>&filter=<?= $filter ?>&id_sinh_vien=<?= $next_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($next_id === null || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                        Sau <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="?page=bi-thu-doan-khoa&id_dot=<?= $id_dot ?>&id_lop_hoc=<?= $id_lop_hoc ?>&filter=<?= $filter ?>&id_sinh_vien=<?= $last_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($current_index === count($sinhvien_list) - 1 || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                        Cuối <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        
<form class="form" action="bi-thu-doan-khoa/action.php?req=add" method="post" enctype="multipart/form-data">

            <input type="hidden" name="id_phieu" value="<?= $phieuchamdiem__Get_By_Id_Sinh_Vien->id_phieu ?>">
<?php if (!$lt_has_scored): ?>
    <div class="card overflow-auto w-100 mt-3">
    <div class="card-header p-3">
        <h3 class="card-title text-center text-danger font-weight-bold m-0" style="float: none;">
            Sinh viên này chưa được Lớp trưởng/Bí thư chấm điểm. Bạn không thể chấm điểm lúc này.
        </h3>
    </div>
</div>
<?php endif; ?>
            <div class="card overflow-auto w-100">
                <div class="card-header p-0" style="border-bottom: none;">
                    <style>
                        .evaluation-header {
                            border: 1px solid #dee2e6;
                            background: #fff;
                        }
                        .evaluation-header .title {
                            text-align: center;
                            font-weight: 700;
                            color: #003366;
                            padding: 15px;
                            font-size: 1.75rem;
                            text-transform: uppercase;
                            border-bottom: 1px solid #dee2e6;
                        }
                        .evaluation-header .info-row {
                            display: flex;
                            flex-wrap: wrap;
                            background: #e9ecef;
                        }
                        .evaluation-header .info-item {
                            flex: 1;
                            min-width: 130px;
                            padding: 12px 10px;
                            text-align: center;
                            border-right: 1px solid #dee2e6;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                        }
                        .evaluation-header .info-item:last-child {
                            border-right: none;
                        }
                        .evaluation-header .info-label {
                            font-weight: normal;
                            font-size: 1rem;
                            color: #6c757d;
                            margin-bottom: 5px;
                        }
                        .evaluation-header .info-value {
                            font-weight: 700;
                            font-size: 1.15rem;
                            color: #212529;
                        }
                        @media (max-width: 768px) {
                            .evaluation-header .info-item {
                                border-right: none;
                                border-bottom: 1px solid #dee2e6;
                            }
                        }
                        .results-summary {
                            background: #f8f9fa;
                            border: 1px solid #dee2e6;
                            border-top: none;
                            padding: 10px 15px;
                            display: flex;
                            justify-content: space-between;
                            flex-wrap: wrap;
                        }
                        .results-summary div {
                            margin-right: 15px;
                            font-size: 1.15rem;
                            font-weight: normal;
                        }
                    </style>
                    <div class="evaluation-header">
                        <div class="title">
                            <?= $mauphieu__Get_By_Id->ten_mau_phieu ?>
                        </div>
                        <div class="info-row">
                            <div class="info-item" style="flex: 0.6;">
                                <span class="info-label">Mã số sinh viên</span>
                                <span class="info-value"><?= $sinhvien__Get_By_Id->ma_sinh_vien ?></span>
                            </div>
                            <div class="info-item" style="flex: 1.2;">
                                <span class="info-label">Họ tên sinh viên</span>
                                <span class="info-value"><?= $sinhvien__Get_By_Id->ten_sinh_vien ?></span>
                            </div>
                            <div class="info-item" style="flex: 0.5; min-width: 60px;">
                                <span class="info-label">Khóa học</span>
                                <span class="info-value"><?= $khoahoc__Get_By_Id->ten_khoa_hoc ?></span>
                            </div>
                            <div class="info-item" style="flex: 2.2;">
                                <span class="info-label">Khoa/ Bộ môn</span>
                                <span class="info-value"><?php 
                                    $nganhhoc_info = $nganhhoc->nganhhoc__Get_By_Id($lophoc__Get_By_Id->id_nganh_hoc);
                                    echo $khoa->khoa__Get_By_Id($nganhhoc_info->id_khoa)->ten_khoa;
                                ?></span>
                            </div>
                            <div class="info-item" style="flex: 1.6;">
                                <span class="info-label">Lớp</span>
                                <span class="info-value"><?= $lophoc__Get_By_Id->ten_lop_hoc ?></span>
                            </div>
                            <div class="info-item" style="flex: 0.5; min-width: 60px;">
                                <span class="info-label">Năm học</span>
                                <span class="info-value"><?= $namhoc__Get_By_Id->ten_nam_hoc ?></span>
                            </div>
                            <div class="info-item" style="flex: 0.4; min-width: 50px;">
                                <span class="info-label">Học kỳ</span>
                                <span class="info-value"><?= $hocky__Get_By_Id->ten_hoc_ky ?></span>
                            </div>
                            <div class="info-item" style="flex: 1.2;">
                                <span class="info-label">Thời gian thực hiện đánh giá</span>
                                <span class="info-value">
                                    <?= isset($dotchamdiem__Get_By_Id->thoi_gian_bat_dau) ? date('d/m/Y', strtotime($dotchamdiem__Get_By_Id->thoi_gian_bat_dau)) : '' ?> - 
                                    <?= isset($dotchamdiem__Get_By_Id->thoi_gian_ket_thuc) ? date('d/m/Y', strtotime($dotchamdiem__Get_By_Id->thoi_gian_ket_thuc)) : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="results-summary">
                        <div><strong>Ngày xếp loại:</strong> <?= isset($ketquaxeploai__Get_By_Id_Phieu->ngay_xep_loai) ? $ketquaxeploai__Get_By_Id_Phieu->ngay_xep_loai : "Chưa tổng kết" ?></div>
                        <div><strong>Điểm rèn luyện:</strong> <?= isset($ketquaxeploai__Get_By_Id_Phieu->ket_qua) ? $ketquaxeploai__Get_By_Id_Phieu->ket_qua : "Chưa tổng kết" ?></div>
                        <div><strong>Xếp loại:</strong> <?= isset($ketquaxeploai__Get_By_Id_Phieu->xep_loai) ? $ketquaxeploai__Get_By_Id_Phieu->xep_loai : "Chưa tổng kết" ?></div>
                        <?php if (isset($ketquaxeploai__Get_By_Id_Phieu->ghi_chu) && $ketquaxeploai__Get_By_Id_Phieu->ghi_chu != ""): ?>
                        <div><strong>Ghi chú:</strong> <?= $ketquaxeploai__Get_By_Id_Phieu->ghi_chu ?></div>
                        <?php endif; ?>
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
                                <col style="width:60px">
                                <col>
                                <col style="width:75px">
                                <col style="width:90px">
                                <col style="width:85px">
                                <col style="width:85px">
                                <col style="width:80px">
                            </colgroup>
                            <thead class="thead-light text-center">
                                <tr>
                                    <th class="align-middle" style="padding:6px 4px; font-size: 1.15rem; background-color: #e9ecef !important;">ĐIỀU</th>
                                    <th class="align-middle" style="padding:6px 4px; font-size: 1.4rem; background-color: #e9ecef !important;">NỘI DUNG ĐÁNH GIÁ</th>
                                    <th class="align-middle" style="padding:6px 4px; background-color: #e9ecef !important;">SV TỰ<br>CHẤM</th>
                                    <th class="align-middle" style="padding:6px 4px; background-color: #e9ecef !important;">LỚP TRƯỞNG<br>BÍ THƯ</th>
                                    <th class="align-middle" style="padding:6px 4px; background-color: #e9ecef !important;">BCH ĐOÀN<br>KHOA</th>
                                    <th class="align-middle" style="padding:6px 4px; background-color: #e9ecef !important;">CỐ VẤN<br>HỌC TẬP</th>
                                    <th class="align-middle" style="padding:6px 4px; background-color: #e9ecef !important;">MINH<br>CHỨNG</th>
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
                                        <tr style="background-color: #e9ecef !important;">
                                            <?php if ($is_first_row_dieu): ?>
                                                <td rowspan="<?= $rowspan ?>" class="align-middle text-center" style="padding:6px 4px; word-break: break-word; overflow-wrap: break-word; white-space: normal;">
                                                    <div class="font-weight-bold" style="white-space: normal !important; word-wrap: break-word; font-size: 1.15em;"><?= $dieu->dieu__Get_By_Id($item_1->id_dieu)->ten_dieu ?></div>
                                                    <div class="text-muted small mt-1" style="white-space: normal !important; word-wrap: break-word;"><?= $dieu->dieu__Get_By_Id($item_1->id_dieu)->ghi_chu ?></div>
                                                </td>
                                                <?php $is_first_row_dieu = false; ?>
                                            <?php endif; ?>
                                            <td class="font-weight-bold text-left criterion-text-cell" style="padding:6px 8px;">
                                                <?= $khoan->khoan__Get_By_Id($item_2->id_khoan)->ten_khoan ?>
                                            </td>
                                            <td class="text-center align-middle font-weight-bold" style="padding:4px;">
                                                <span class="khoan-total-kq_sv font-weight-bold" style="color: #003366;" data-id-khoan="<?= $item_2->id_khoan ?>"></span>
                                            </td>
                                            <td class="text-center align-middle font-weight-bold" style="padding:4px;">
                                                <span class="khoan-total-kq_lt_bt font-weight-bold" style="color: #003366;" data-id-khoan="<?= $item_2->id_khoan ?>"></span>
                                            </td>
                                            <td class="text-center align-middle font-weight-bold" style="padding:4px;">
                                                <span class="khoan-total-kq_btdk font-weight-bold" style="color: #003366;" data-id-khoan="<?= $item_2->id_khoan ?>"></span>
                                            </td>
                                            <td class="text-center align-middle font-weight-bold" style="padding:4px;">
                                                <span class="khoan-total-kq_gv font-weight-bold" style="color: #003366;" data-id-khoan="<?= $item_2->id_khoan ?>"></span>
                                            </td>
                                            <td style="padding:4px;"></td>
                                        </tr>
                                        <?php foreach ($muc->muc__Get_All_By_Id_Khoan($item_2->id_khoan) as $item_3) : ?>
                                            <tr>
                                                <td class="criterion-text-cell">
                                                    <?= $muc->muc__Get_By_Id($item_3->id_muc)->ten_muc ?>
                                                    <?php $ghi_chu_muc = $muc->muc__Get_By_Id($item_3->id_muc)->ghi_chu; ?>
                                                    <?php if(!empty(trim(strip_tags($ghi_chu_muc)))): ?>
                                                        <div class="mt-1 ml-2 text-muted text-sm"><?= $ghi_chu_muc ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                    <?php 
                                                        $val_sv = isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_sv)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_sv)[$i] : 0;
                                                        $val_lt = isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_lt_bt)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_lt_bt)[$i] : 0;
                                                        $val_btdk = isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_btdk)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_btdk)[$i] : 0;
                                                        $val_gv = isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_gv)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_gv)[$i] : 0;
                                                        
                                                        $muc_info = $muc->muc__Get_By_Id($item_3->id_muc);
                                                        $quyen_sv = $muc_info->quyen_sv;
                                                        $quyen_lt = $muc_info->quyen_lt;
                                                        $quyen_btdk = $muc_info->quyen_btdk;
                                                        $quyen_gv = $muc_info->quyen_gv;
                                                        if ($quyen_sv == 0) { $val_sv = 0; }
                                                        if ($quyen_lt == 0) { $val_lt = $val_sv; }
                                                        if ($quyen_btdk == 0) { $val_btdk = $val_lt; }
                                                        if ($quyen_gv == 0) { $val_gv = $val_btdk; }

                                                    ?>

<td class="text-center align-middle" style="padding:4px;">
                                                    <!-- Quân sửa: Khóa cột điểm SV Tự chấm trên trang Bí thư Đoàn khoa bằng thuộc tính readonly -->
                                                    <input type="number" class="form-control kq_sv" name="kq_sv[]"
                title="Điểm tối đa: <?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" max="<?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" data-id-khoan="<?= $item_2->id_khoan ?>" data-khoan-max="<?= $item_2->can_tren ?>"
                pattern="[-+]?[0-9]{1,2}" placeholder="0" min="0"
                style="<?= $quyen_sv == 0 ? 'background: linear-gradient(to bottom right, transparent 48%, #ccc 49%, #ccc 51%, transparent 52%) #e9ecef; pointer-events: none; opacity: 0.8;' . ($val_sv == 0 ? ' color: transparent !important; -webkit-text-fill-color: transparent !important;' : '') : '' ?> width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                
                readonly tabindex="-1"
                value="<?= $val_sv == 0 ? '' : $val_sv ?>">
</td>
                                                <td class="text-center align-middle" style="padding:4px;">
                                                    <input type="number" class="form-control kq_lt_bt" name="kq_lt_bt[]"
                title="Điểm tối đa: <?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" max="<?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" data-id-khoan="<?= $item_2->id_khoan ?>" data-khoan-max="<?= $item_2->can_tren ?>"
                pattern="[-+]?[0-9]{1,2}" placeholder="0" min="0"
                style="<?= $quyen_lt == 0 ? 'background: linear-gradient(to bottom right, transparent 48%, #ccc 49%, #ccc 51%, transparent 52%) #e9ecef; pointer-events: none; opacity: 0.8;' . ($val_lt == 0 ? ' color: transparent !important; -webkit-text-fill-color: transparent !important;' : '') : '' ?> width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                
                readonly tabindex="-1"
                value="<?= $val_lt == 0 ? '' : $val_lt ?>">
</td>
                                                <td class="text-center align-middle" style="padding:4px;">
                                                    <!-- Quân sửa: Khóa cột điểm SV Tự chấm trên trang Bí thư Đoàn khoa bằng thuộc tính readonly -->
                                                    <input type="number" class="form-control kq_btdk" name="kq_btdk[]"
                title="Điểm tối đa: <?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" max="<?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" data-id-khoan="<?= $item_2->id_khoan ?>" data-khoan-max="<?= $item_2->can_tren ?>"
                pattern="[-+]?[0-9]{1,2}" placeholder="0" min="0"
                style="<?= $quyen_btdk == 0 ? 'background: linear-gradient(to bottom right, transparent 48%, #ccc 49%, #ccc 51%, transparent 52%) #e9ecef; pointer-events: none; opacity: 0.8;' . ($val_btdk == 0 ? ' color: transparent !important; -webkit-text-fill-color: transparent !important;' : '') : '' ?> width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                
                <?= ($quyen_btdk == 0 || $dotchamdiem__Get_By_Id->trang_thai == 0 || $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 2) ? 'readonly tabindex="-1"' : '' ?>
                value="<?= empty($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_btdk) ? (!empty($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_lt_bt) ? ($val_lt == 0 ? '' : $val_lt) : ($val_sv == 0 ? '' : $val_sv)) : ($val_btdk == 0 ? '' : $val_btdk) ?>">
</td>
                                                <td class="text-center align-middle" style="padding:4px;">
                                                    <input type="number" class="form-control kq_gv" name="kq_gv[]"
                title="Điểm tối đa: <?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" max="<?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" data-id-khoan="<?= $item_2->id_khoan ?>" data-khoan-max="<?= $item_2->can_tren ?>"
                pattern="[-+]?[0-9]{1,2}" placeholder="0" min="0"
                style="<?= $quyen_gv == 0 ? 'background: linear-gradient(to bottom right, transparent 48%, #ccc 49%, #ccc 51%, transparent 52%) #e9ecef; pointer-events: none; opacity: 0.8;' . ($val_gv == 0 ? ' color: transparent !important; -webkit-text-fill-color: transparent !important;' : '') : '' ?> width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                
                readonly tabindex="-1"
                value="<?= $val_gv == 0 ? '' : $val_gv ?>">
</td>
                                                <td class="text-center align-middle" style="padding:4px;">
                                                    <?php if ($muc->muc__Get_By_Id($item_3->id_muc)->co_minh_chung == 1): ?>
                                                        <?php
                                                            $minh_chung_cua_muc = $minhchung->minhchung__Get_By_Id_Phieu_And_Muc($phieuchamdiem__Get_By_Id_Sinh_Vien->id_phieu, $item_3->id_muc);
                                                            $has_existing = count($minh_chung_cua_muc) > 0;
                                                            $readonly = 'true';
                                                        ?>
                                                        <button type="button" class="btn btn-evidence-custom evidence-manager-trigger"
                                                            data-id-muc="<?= $item_3->id_muc ?>"
                                                            data-readonly="<?= $readonly ?>">
                                                            <i class="fas fa-folder-open"></i> (<span class="evidence-count-<?= $item_3->id_muc ?>"><?= count($minh_chung_cua_muc) ?></span>)
                                                        </button>

                                                        <!-- Hidden State Container for this Muc -->
                                                        <div id="evidence-state-<?= $item_3->id_muc ?>" class="d-none">
                                                            <!-- Existing Evidences -->
                                                            <?php foreach($minh_chung_cua_muc as $mc): ?>
                                                                <?php
                                                                    $isPdf = strpos($mc->hinh_anh, 'application/pdf') !== false;
                                                                    $ext = $isPdf ? '.pdf' : '.jpg';
                                                                    $displayName = "Minh_chung_" . $mc->id_minh_chung . "_" . date("d-m-Y", strtotime($mc->ghi_chu)) . $ext;
                                                                ?>
                                                                <div class="existing-evidence" data-id="<?= $mc->id_minh_chung ?>" data-url="<?= $mc->hinh_anh ?>" data-name="<?= htmlspecialchars($displayName) ?>"></div>
                                                            <?php endforeach; ?>
                                                            <!-- File Input (disabled for readonly) -->
                                                            <input type="file" name="minh_chung_muc[<?= $item_3->id_muc ?>][]" multiple accept="image/*,application/pdf"
                                                                class="evidence-file-input-hidden" style="display:none;" disabled>
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
                                    <td colspan="2" class="align-middle text-right font-weight-bold" style="padding:6px 8px; font-size: 1.15rem; color: #003366; background-color: #e9ecef !important;">TỔNG ĐIỂM:</td>
                                    <td class="align-middle" style="padding:4px; background-color: #e9ecef !important;">
                                        <input type="number" class="form-control font-weight-bold" id="sum_sv" placeholder="" min="0" max="100" readonly style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;; color: #003366 !important;">
                                    </td>
                                    <td class="align-middle" style="padding:4px; background-color: #e9ecef !important;">
                                        <input type="number" class="form-control font-weight-bold" id="sum_lt_bt" placeholder="" min="0" max="100" readonly style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;; color: #003366 !important;">
                                    </td>
                                    <td class="align-middle" style="padding:4px; background-color: #e9ecef !important;">
                                        <input type="number" class="form-control font-weight-bold " id="sum_btdk" placeholder="" min="0" max="100" readonly style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;; color: #003366 !important;">
                                    </td>
                                    <td class="align-middle" style="padding:4px; background-color: #e9ecef !important;">
                                        <input type="number" class="form-control font-weight-bold" id="sum_gv" placeholder="" min="0" max="100" readonly style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;; color: #003366 !important;">
                                    </td>
                                    <td style="padding:4px; background-color: #e9ecef !important;"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <div class="card-footer">
                
                <input type="submit" value="Cập nhật" class="btn btn-success btn-lg float-right font-weight-bold" id="submit"
                    <?= ($dotchamdiem__Get_By_Id->trang_thai == 0 || !$lt_has_scored) ? 'disabled' : '' ?>>
            </div>

        </form>

        <!-- Quân sửa: Thêm nút phân trang chuyển nhanh giữa các sinh viên (dưới form) -->
        <?php if ($id_lop_hoc != -2 && count($sinhvien_list) > 0): ?>
        <div class="row mt-3 mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="?page=bi-thu-doan-khoa&id_dot=<?= $id_dot ?>&id_lop_hoc=<?= $id_lop_hoc ?>&filter=<?= $filter ?>&id_sinh_vien=<?= $first_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($current_index === 0 || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-double-left"></i> Đầu
                    </a>
                    <a href="?page=bi-thu-doan-khoa&id_dot=<?= $id_dot ?>&id_lop_hoc=<?= $id_lop_hoc ?>&filter=<?= $filter ?>&id_sinh_vien=<?= $prev_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($prev_id === null || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-left"></i> Trước
                    </a>
                </div>
                <div class="text-muted font-weight-bold">
                    Sinh viên <?= ($current_index !== false) ? ($current_index + 1) : 0 ?> / <?= count($sinhvien_list) ?><?= ($current_index !== false && isset($sinhvien_list[$current_index])) ? ' - ' . htmlspecialchars($sinhvien_list[$current_index]->ten_sinh_vien) : '' ?>
                </div>
                <div>
                    <a href="?page=bi-thu-doan-khoa&id_dot=<?= $id_dot ?>&id_lop_hoc=<?= $id_lop_hoc ?>&filter=<?= $filter ?>&id_sinh_vien=<?= $next_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($next_id === null || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                        Sau <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="?page=bi-thu-doan-khoa&id_dot=<?= $id_dot ?>&id_lop_hoc=<?= $id_lop_hoc ?>&filter=<?= $filter ?>&id_sinh_vien=<?= $last_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($current_index === count($sinhvien_list) - 1 || $id_sinh_vien == -2) ? 'disabled' : '' ?>">
                        Cuối <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>




<div class="card mt-3">
<div class="card-header">
    <div class="row">
        <div class="col">
            <p class="mb-0" style="color: red;"><b>• Quy định:</b></p>
            <div style="padding-left: 15px;">
                <p>
                    - Xếp loại kết quả rèn luyện: <b>xuất sắc</b> (90 - 100 điểm), <b>tốt</b> (80 - 89 điểm), <b>khá</b> (65 - 79 điểm), <b>trung bình</b> (50 - 64 điểm), <b>yếu</b> (35 - 49 điểm), <b>kém</b> (dưới 35 điểm).<br>
                    - Kết quả rèn luyện năm học <b>xuất sắc</b> và <b>tốt</b> được nhà trường xét khen thưởng.<br>
                    - Kết quả rèn luyện <b>yếu</b>, <b>kém</b> 2 học kỳ liên tiếp phải tạm ngừng học ít nhất 1 học kỳ ở học kỳ tiếp theo.<br>
                    - Sinh viên bị kỷ luật mức khiển trách trong học kỳ thì mức xếp loại không được vượt quá loại <b>khá</b>, bị kỷ luật mức cảnh cáo thì không được vượt quá loại <b>trung bình</b>.<br>
                    - Sinh viên không nộp phiếu đánh giá kết quả rèn luyện mà không có lý do chính đáng, cố vấn học tập và tập thể lớp đánh giá kết quả rèn luyện cho sinh viên không nộp phiếu và trừ điểm để hạ một bậc xếp loại (<b>xuất sắc</b>: trừ 11 điểm, <b>tốt</b>: trừ 11 điểm, <b>khá</b>: trừ 15 điểm, <b>trung bình</b>: trừ 15 điểm, <b>yếu</b>: trừ 15 điểm).
                </p>
            </div>
        </div>
    </div>
</div>
</div>

</section>
</div>

<!-- Modal Manager -->
<div class="modal fade" id="evidenceManagerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 1000px;">
    <div class="modal-content" style="border-radius: 0.75rem; overflow: hidden;">
      <div class="modal-header bg-custom-dark text-white py-2">
        <h5 class="modal-title font-weight-bold">Minh chứng</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 1; font-size: 1.75rem;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="row">
              <!-- Left column: List -->
              <div class="col-md-5 border-right">
                  <h6 class="font-weight-bold mb-3">Danh sách tệp</h6>
                  
                  <!-- Nút tải lên -->
                  <div class="mb-3 text-center" id="managerUploadBtnContainer">
                      <button type="button" class="btn btn-upload-custom btn-block" style="border-style: dashed; padding: 10px; transition: all 0.2s; color: #003366 !important; border-color: #003366 !important;" id="managerUploadBtn">
                          <i class="fas fa-cloud-upload-alt fa-2x mb-2 d-block"></i>
                          Nhấn hoặc kéo thả tệp tải lên vào đây
                      </button>
                  </div>

                  <!-- Danh sách Existing -->
                  <div id="managerExistingList" class="mb-3">
                      <!-- Populated by JS -->
                  </div>

                  <!-- Danh sách New -->
                  <div id="managerNewList">
                      <!-- Populated by JS -->
                  </div>

              </div>
              
              <!-- Right column: Preview -->
              <div class="col-md-7 d-flex flex-column align-items-center justify-content-center bg-light rounded" style="min-height: 450px; padding: 10px;">
                  <div id="managerPreviewEmpty" class="text-muted text-center">
                      <i class="fas fa-image fa-3x mb-2 d-block"></i>
                      Chọn một tệp ở danh sách bên trái để xem trước
                  </div>
                  <img id="managerPreviewImage" src="" class="img-fluid rounded d-none" style="max-height: 500px; object-fit: contain;">
                  <iframe id="managerPreviewPdf" src="" class="d-none w-100" style="height: 500px; border: 1px solid #ddd; border-radius: 4px;"></iframe>
              </div>
          </div>
      </div>
      <div class="modal-footer bg-light py-2">
        <button type="button" class="btn btn-cancel-custom font-weight-bold" style="font-size: 1.15rem; padding: 6px 24px; font-weight: bold !important;" data-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>

<script>
window.addEventListener('load', function() {
    // Quân sửa: Tính tổng điểm tự chấm của Sinh viên trên giao diện Bí thư Đoàn khoa
    var kq_sv_load = 0;
    var kq_sv = document.getElementsByClassName("kq_sv");
    for (i = 0; i < kq_sv.length; i++) {
        a = Number(kq_sv[i].value);
        console.log(typeof a);
        kq_sv_load += a;
    }
    document.getElementById("sum_sv").value = kq_sv_load > 0 ? kq_sv_load : '';

    var kq_lt_bt_load = 0;
    var kq_lt_bt = document.getElementsByClassName("kq_lt_bt");
    for (i = 0; i < kq_lt_bt.length; i++) {
        a = Number(kq_lt_bt[i].value);
        console.log(typeof a);
        kq_lt_bt_load += a;
    }
    document.getElementById("sum_lt_bt").value = kq_lt_bt_load > 0 ? kq_lt_bt_load : '';


    var kq_btdk_load = 0;
    var kq_btdk = document.getElementsByClassName("kq_btdk");
    for (i = 0; i < kq_btdk.length; i++) {
        a = Number(kq_btdk[i].value);
        console.log(typeof a);
        kq_btdk_load += a;
    }
    document.getElementById("sum_btdk").value = kq_btdk_load > 0 ? kq_btdk_load : '';


    var kq_gv_load = 0;
    var kq_gv = document.getElementsByClassName("kq_gv");
    for (i = 0; i < kq_gv.length; i++) {
        a = Number(kq_gv[i].value);
        console.log(typeof a);
        kq_gv_load += a;
    }
    document.getElementById("sum_gv").value = kq_gv_load > 0 ? kq_gv_load : '';


    $('.kq_btdk').change(function(event) {
        var kq = 0;
        var kq_btdk = document.getElementsByClassName("kq_btdk");
        // Quân sửa: Sửa biến kq_sv thành kq_btdk tránh lỗi Javascript ReferenceError
        for (i = 0; i < kq_btdk.length; i++) {
            a = Number(kq_btdk[i].value);
            console.log(typeof a);
            kq += a;
        }
        document.getElementById("sum_btdk").value = kq > 0 ? kq : '';
        if (kq > 100) {
            document.getElementById("submit").setAttribute("disabled", true);
            document.getElementById("submit").setAttribute("value", "Điểm không hợp lệ");
            $("#sum_btdk").addClass("bg-danger");
        } else {
            document.getElementById("submit").removeAttribute("disabled");
            document.getElementById("submit").setAttribute("value", "Cập nhật");
            $("#sum_btdk").removeClass("bg-danger");
        }
    });

});
</script>


<script>
window.addEventListener('load', function () {
    let currentMucId = null;
    let isCurrentReadonly = false;
    let accumulatedFiles = {}; // { mucId: DataTransfer }

    $('.evidence-manager-trigger').click(function(e) {
        e.preventDefault();
        currentMucId = $(this).data('id-muc');
        isCurrentReadonly = $(this).data('readonly') == true || $(this).data('readonly') == 'true';
        
        // Reset preview
        $('#managerPreviewImage').addClass('d-none').attr('src', '');
        $('#managerPreviewPdf').addClass('d-none').attr('src', '');
        $('#managerPreviewEmpty').removeClass('d-none');
        
        // Manage upload button visibility
        if (isCurrentReadonly) {
            $('#managerUploadBtnContainer').hide();
        } else {
            $('#managerUploadBtnContainer').show();
        }
        
        refreshManagerList();
        
        $('#evidenceManagerModal').modal('show');
    });

    function refreshManagerList() {
        if (!currentMucId) return;
        let stateDiv = $('#evidence-state-' + currentMucId);
        let existingListHtml = '';
        
        // Existing Items
        stateDiv.find('.existing-evidence').each(function() {
            if ($(this).hasClass('deleted')) return; // skip if already marked as deleted
            
            let id = $(this).data('id');
            let name = $(this).data('name');
            let url = $(this).data('url');
            let isPdf = url.startsWith('data:application/pdf');
            let iconClass = isPdf ? 'fas fa-file-pdf text-danger' : 'fas fa-file-image text-info';
            let deleteBtn = isCurrentReadonly ? '' : `<button type="button" class="btn btn-sm text-danger btn-delete-existing" data-id="${id}" title="Xóa"><i class="fas fa-trash"></i></button>`;
            
            existingListHtml += `
                <div class="d-flex justify-content-between align-items-center p-2 mb-1 border rounded bg-white" style="font-size: 13px;">
                    <div class="text-truncate btn-view-evidence" data-url="${url}" data-type="${isPdf ? 'pdf' : 'image'}" style="max-width: 250px; cursor: pointer;" title="Nhấn để xem trước: ${name}">
                        <i class="${iconClass} mr-1"></i> ${name}
                    </div>
                    <div>
                        ${deleteBtn}
                    </div>
                </div>
            `;
        });
        
        if(existingListHtml === '') {
            existingListHtml = '<p class="text-muted small text-center italic" style="font-style: italic;">Chưa có tệp nào trên hệ thống.</p>';
        }
        $('#managerExistingList').html('<h6 class="small font-weight-bold text-uppercase text-muted mb-2">Đã tải lên</h6>' + existingListHtml);
        
        // New Items
        let fileInput = stateDiv.find('input[type="file"]')[0];
        let newListHtml = '';
        if (fileInput && fileInput.files && fileInput.files.length > 0) {
            for(let i=0; i<fileInput.files.length; i++) {
                let file = fileInput.files[i];
                let objUrl = URL.createObjectURL(file);
                let isPdf = file.type === 'application/pdf';
                let iconClass = isPdf ? 'fas fa-file-pdf text-danger' : 'fas fa-file-image text-success';
                
                newListHtml += `
                    <div class="d-flex justify-content-between align-items-center p-2 mb-1 border rounded bg-light" style="font-size: 13px;">
                        <div class="text-truncate btn-view-evidence" data-url="${objUrl}" data-type="${isPdf ? 'pdf' : 'image'}" style="max-width: 250px; cursor: pointer;" title="Nhấn để xem trước: ${file.name}">
                            <i class="${iconClass} mr-1"></i> <span class="text-success">${file.name}</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm text-danger btn-remove-new" data-name="${file.name}" title="Xóa"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                `;
            }
            newListHtml = '<h6 class="small font-weight-bold text-uppercase text-success mb-2 mt-3">Đã chọn mới (Chờ lưu)</h6>' + newListHtml + 
                '<div class="text-center mt-2"><button type="button" class="btn btn-sm btn-link text-danger btn-clear-new">Hủy toàn bộ tệp chọn mới</button></div>';
        }
        $('#managerNewList').html(newListHtml);
        
        updateCountButton(currentMucId);
    }

    // Bind events for dynamically generated buttons in the modal
    $(document).on('click', '.btn-view-evidence', function() {
        let url = $(this).data('url');
        let type = $(this).data('type');
        $('#managerPreviewEmpty').addClass('d-none');
        
        if (type === 'pdf') {
            $('#managerPreviewImage').addClass('d-none');
            $('#managerPreviewPdf').removeClass('d-none').attr('src', url);
        } else {
            $('#managerPreviewPdf').addClass('d-none');
            $('#managerPreviewImage').removeClass('d-none').attr('src', url);
        }
    });

    $(document).on('click', '.btn-delete-existing', function() {
        let id = $(this).data('id');
        if(confirm('Xác nhận xóa tệp này? Thay đổi chỉ được áp dụng khi bạn nhấn "Cập nhật" form.')) {
            // Mark as deleted in state
            let item = $('#evidence-state-' + currentMucId).find('.existing-evidence[data-id="'+id+'"]');
            item.addClass('deleted');
            
            // Append hidden input to form
            $('<input>').attr({
                type: 'hidden',
                name: 'delete_minhchung[]',
                value: id
            }).appendTo('form');
            
            refreshManagerList();
        }
    });

    $(document).on('click', '#managerUploadBtn', function() {
        $('#evidence-state-' + currentMucId).find('input[type="file"]').click();
    });

    // Drag and Drop support
    $(document).on('dragover', '#managerUploadBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('bg-custom-dark').removeClass('btn-upload-custom');
    });

    $(document).on('dragleave', '#managerUploadBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('bg-custom-dark').addClass('btn-upload-custom');
    });

    $(document).on('drop', '#managerUploadBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('bg-custom-dark').addClass('btn-upload-custom');
        
        let files = e.originalEvent.dataTransfer.files;
        if (files && files.length > 0) {
            let fileInput = $('#evidence-state-' + currentMucId).find('input[type="file"]')[0];
            if (fileInput) {
                fileInput.files = files;
                $(fileInput).trigger('change');
            }
        }
    });

    $(document).on('click', '.btn-remove-new', function() {
        let nameToRemove = $(this).data('name');
        if (accumulatedFiles[currentMucId]) {
            let dt = accumulatedFiles[currentMucId];
            let newDt = new DataTransfer();
            for (let i = 0; i < dt.files.length; i++) {
                if (dt.files[i].name !== nameToRemove) {
                    newDt.items.add(dt.files[i]);
                }
            }
            accumulatedFiles[currentMucId] = newDt;
            let fileInput = $('#evidence-state-' + currentMucId).find('input[type="file"]')[0];
            if(fileInput) fileInput.files = newDt.files;
            refreshManagerList();
        }
    });

    $(document).on('click', '.btn-clear-new', function() {
        if(accumulatedFiles[currentMucId]) {
            accumulatedFiles[currentMucId] = new DataTransfer();
        }
        let fileInput = $('#evidence-state-' + currentMucId).find('input[type="file"]')[0];
        if(fileInput) {
            fileInput.value = ''; // clear files
            if (accumulatedFiles[currentMucId]) fileInput.files = accumulatedFiles[currentMucId].files;
        }
        refreshManagerList();
    });

    // We must also bind the change event of the hidden file inputs to refresh the modal if it's open
    $(document).on('change', '.evidence-file-input-hidden', function() {
        let changedMucId = $(this).closest('.d-none').attr('id').replace('evidence-state-', '');
        
        if (!accumulatedFiles[changedMucId]) {
            accumulatedFiles[changedMucId] = new DataTransfer();
        }
        
        let dt = accumulatedFiles[changedMucId];
        let existingNames = new Set();
        for (let i = 0; i < dt.files.length; i++) {
            existingNames.add(dt.files[i].name);
        }
        
        // Add new files to DataTransfer if not duplicate
        for (let i = 0; i < this.files.length; i++) {
            let file = this.files[i];
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Kích thước tệp ' + file.name + ' vượt quá 5MB. Vui lòng chọn tệp nhỏ hơn.' });
                continue;
            }
            if (!existingNames.has(file.name)) {
                dt.items.add(file);
            }
        }
        
        // Reassign accumulated files back to input
        this.files = dt.files;
        
        // If the modal is currently open for this muc, refresh it
        if ($('#evidenceManagerModal').is(':visible') && changedMucId == currentMucId) {
            refreshManagerList();
        } else {
            // Just update count button if modal not open (e.g., edge case)
            updateCountButton(changedMucId);
        }
    });

    function updateCountButton(mucId) {
        let stateDiv = $('#evidence-state-' + mucId);
        let existingCount = stateDiv.find('.existing-evidence:not(.deleted)').length;
        let fileInput = stateDiv.find('input[type="file"]')[0];
        let newCount = (fileInput && fileInput.files) ? fileInput.files.length : 0;
        let total = existingCount + newCount;
        
        let btn = $('.evidence-manager-trigger[data-id-muc="'+mucId+'"]');
        btn.find('span.evidence-count-'+mucId).text(total);
          // Removed dynamic toggling of btn-outline-custom-blue because we use inline styling
    }
});
</script>



<script>
function calculateKhoanTotals(targetClass) {
    let khoanSums = {};
    $('.' + targetClass).each(function() {
        let id_khoan = $(this).data('id-khoan');
        let val = Number($(this).val()) || 0;
        
        if (id_khoan) {
            if (!khoanSums[id_khoan]) khoanSums[id_khoan] = 0;
            khoanSums[id_khoan] += val;
        }
    });

    for (let id_khoan in khoanSums) {
        let displayElem = $('.khoan-total-' + targetClass + '[data-id-khoan="' + id_khoan + '"]');
        if (displayElem.length > 0) {
            let sum = khoanSums[id_khoan];
            displayElem.text(sum === 0 ? '' : sum);
        }
    }
}

window.addEventListener('load', function() {
    ['kq_sv', 'kq_lt_bt', 'kq_btdk', 'kq_gv'].forEach(function(cls) {
        calculateKhoanTotals(cls);
        $('.' + cls).on('input change', function() {
            let max_muc = parseInt($(this).attr('max')) || 0;
            if (Number($(this).val()) > max_muc) {
                $(this).val(max_muc);
            }
            
            let id_khoan = $(this).data('id-khoan');
            let khoan_max = parseInt($(this).data('khoan-max')) || 0;
            let sum = 0;
            $('.' + cls + '[data-id-khoan="' + id_khoan + '"]').each(function() {
                sum += Number($(this).val()) || 0;
            });

            if (sum > khoan_max) {
                Swal.fire({icon: 'warning', title: 'Vượt quá điểm tối đa', text: 'Tổng điểm của Khoản này không được vượt quá ' + khoan_max + ' điểm!'});
                $(this).val(0);
                $(this).trigger('change');
            } else {
                calculateKhoanTotals(cls);
            }
        });
    });
});
</script>

<?php endif ?>
