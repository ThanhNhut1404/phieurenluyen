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
    // QuÃ¢n sá»­a: Máº·c Ä‘á»‹nh khÃ´ng chá»n sinh viÃªn nÃ o khi vÃ o cháº¿ Ä‘á»™ cháº¥m cho lá»›p (trÃ¡nh tá»± chá»n báº£n thÃ¢n)
    $id_sinh_vien = '';
    if (isset($_GET['id_sinh_vien']) && $_GET['id_sinh_vien'] != '') {
        // QuÃ¢n sá»­a: KhÃ´ng cho phÃ©p chá»n báº£n thÃ¢n bÃ­ thÆ° chi Ä‘oÃ n trong cháº¿ Ä‘á»™ cháº¥m cho lá»›p
        if ($_GET['id_sinh_vien'] != $_SESSION['bt']->id_nguoi_dung) {
            $id_sinh_vien = $_GET['id_sinh_vien'];
        }
    }
}
$phieuchamdiem__Get_By_Id_Sinh_Vien = $phieuchamdiem->phieuchamdiem__Get_By_Id_Sinh_Vien($id_sinh_vien, $id_dot);

// QuÃ¢n sá»­a: Láº¥y danh sÃ¡ch lá»›p
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

// QuÃ¢n sá»­a: Lá»c bá» tÃ i khoáº£n cá»§a bÃ­ thÆ° chi Ä‘oÃ n khá»i danh sÃ¡ch cháº¥m Ä‘iá»ƒm lá»›p
if ($mode == 'lop') {
    $sinhvien_list = array_filter($sinhvien_list, function($sv) {
        return $sv->id_sinh_vien != $_SESSION['bt']->id_nguoi_dung;
    });
}
$sinhvien_list = array_values($sinhvien_list);

// QuÃ¢n sá»­a: TÃ­nh toÃ¡n phÃ¢n trang cho danh sÃ¡ch sinh viÃªn
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
    
    // QuÃ¢n sá»­a: Láº¥y thÃ´ng tin Khoa/Bá»™ mÃ´n cá»§a sinh viÃªn Ä‘á»ƒ hiá»ƒn thá»‹ trÃªn phiáº¿u
    $id_nganh_hoc = isset($lophoc__Get_By_Id->id_nganh_hoc) ? $lophoc__Get_By_Id->id_nganh_hoc : 0;
    $nganhhoc__Get_By_Id = $nganhhoc->nganhhoc__Get_By_Id($id_nganh_hoc);
    $id_khoa = isset($nganhhoc__Get_By_Id->id_khoa) ? $nganhhoc__Get_By_Id->id_khoa : 0;
    $khoa__Get_By_Id = $khoa->khoa__Get_By_Id($id_khoa);

    $bocauhoi__Get_By_Id_Mau_Phieu = $bocauhoi->bocauhoi__Get_By_Id_Mau_Phieu($id_mau_phieu);

    $ketquaxeploai__Get_By_Id_Phieu = $ketquaxeploai->ketquaxeploai__Get_By_Id_Phieu($id_lop_ap_dung, $id_dot, $id_sinh_vien);
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
                            <input type="hidden" name="page" value="bi-thu-chi-doan">
                            <input type="hidden" name="id_dot" value="<?= $id_dot ?>">
                            
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label>Cháº¿ Ä‘á»™ cháº¥m</label>
                                    <select class="form-control" name="mode" onchange="this.form.submit()">
                                        <option value="ban_than" <?= $mode == 'ban_than' ? 'selected' : '' ?>>Cháº¥m cho báº£n thÃ¢n</option>
                                        <option value="lop" <?= $mode == 'lop' ? 'selected' : '' ?>>Cháº¥m cho lá»›p</option>
                                    </select>
                                </div>
                                <?php if ($mode == 'lop'): ?>
                                <div class="col-md-4">
                                    <label>Lá»c sinh viÃªn</label>
                                    <select class="form-control" name="filter" onchange="this.form.submit()">
                                        <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Táº¥t cáº£ sinh viÃªn</option>
                                        <option value="da_tu_cham" <?= $filter == 'da_tu_cham' ? 'selected' : '' ?>>ÄÃ£ tá»± cháº¥m</option>
                                        <option value="chua_tu_cham" <?= $filter == 'chua_tu_cham' ? 'selected' : '' ?>>ChÆ°a tá»± cháº¥m</option>
                                        <option value="da_bt_cham" <?= $filter == 'da_bt_cham' ? 'selected' : '' ?>>ÄÃ£ Ä‘Æ°á»£c bÃ­ thÆ° chi Ä‘oÃ n cháº¥m</option>
                                        <option value="chua_bt_cham" <?= $filter == 'chua_bt_cham' ? 'selected' : '' ?>>ChÆ°a Ä‘Æ°á»£c bÃ­ thÆ° chi Ä‘oÃ n cháº¥m</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label>Sinh viÃªn (<?= count($sinhvien_list) ?>)</label>
                                    <select class="form-control" name="id_sinh_vien" onchange="this.form.submit()">
                                        <option value="" <?= ($id_sinh_vien == '' || $id_sinh_vien == -2) ? 'selected' : '' ?>>-- Chá»n sinh viÃªn --</option>
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

        <div class="card overflow-auto w-100">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h3 class="card-title text-center font-weight-bold w-100 mt-3 mb-3">
                            <?php if ($mode == 'lop' && ($id_sinh_vien == '' || $id_sinh_vien == -2)): ?>Vui lÃ²ng chá»n má»™t sinh viÃªn trong danh sÃ¡ch Ä‘á»ƒ báº¯t Ä‘áº§u cháº¥m Ä‘iá»ƒm<?php else: ?>Báº¡n khÃ´ng cÃ³ trong Ä‘á»£t nÃ y<?php endif; ?></h3>
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
                                    <label>Cháº¿ Ä‘á»™ cháº¥m</label>
                                    <select class="form-control" name="mode" onchange="this.form.submit()">
                                        <option value="ban_than" <?= $mode == 'ban_than' ? 'selected' : '' ?>>Cháº¥m cho báº£n thÃ¢n</option>
                                        <option value="lop" <?= $mode == 'lop' ? 'selected' : '' ?>>Cháº¥m cho lá»›p</option>
                                    </select>
                                </div>
                                <?php if ($mode == 'lop'): ?>
                                <div class="col-md-4">
                                    <label>Lá»c sinh viÃªn</label>
                                    <select class="form-control" name="filter" onchange="this.form.submit()">
                                        <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Táº¥t cáº£ sinh viÃªn</option>
                                        <option value="da_tu_cham" <?= $filter == 'da_tu_cham' ? 'selected' : '' ?>>ÄÃ£ tá»± cháº¥m</option>
                                        <option value="chua_tu_cham" <?= $filter == 'chua_tu_cham' ? 'selected' : '' ?>>ChÆ°a tá»± cháº¥m</option>
                                        <option value="da_bt_cham" <?= $filter == 'da_bt_cham' ? 'selected' : '' ?>>ÄÃ£ Ä‘Æ°á»£c bÃ­ thÆ° chi Ä‘oÃ n cháº¥m</option>
                                        <option value="chua_bt_cham" <?= $filter == 'chua_bt_cham' ? 'selected' : '' ?>>ChÆ°a Ä‘Æ°á»£c bÃ­ thÆ° chi Ä‘oÃ n cháº¥m</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label>Sinh viÃªn (<?= count($sinhvien_list) ?>)</label>
                                    <select class="form-control" name="id_sinh_vien" onchange="this.form.submit()">
                                        <option value="" <?= ($id_sinh_vien == '' || $id_sinh_vien == -2) ? 'selected' : '' ?>>-- Chá»n sinh viÃªn --</option>
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
        <!-- QuÃ¢n sá»­a: ThÃªm nÃºt phÃ¢n trang chuyá»ƒn nhanh giá»¯a cÃ¡c sinh viÃªn -->
        <?php if ($mode == 'lop' && count($sinhvien_list) > 0): ?>
        <div class="row mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $first_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($current_index === 0 || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-double-left"></i> Äáº§u
                    </a>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $prev_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($prev_id === null || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-left"></i> TrÆ°á»›c
                    </a>
                </div>
                <div class="text-muted font-weight-bold">
                    Sinh viÃªn <?= ($current_index !== false) ? ($current_index + 1) : 0 ?> / <?= count($sinhvien_list) ?><?= ($current_index !== false && isset($sinhvien_list[$current_index])) ? ' - ' . htmlspecialchars($sinhvien_list[$current_index]->ten_sinh_vien) : '' ?>
                </div>
                <div>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $next_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($next_id === null || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        Sau <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $last_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($current_index === count($sinhvien_list) - 1 || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        Cuá»‘i <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- QuÃ¢n sá»­a: Äá»‹nh tuyáº¿n Ä‘Ãºng hÃ nh Ä‘á»™ng cáº­p nháº­t Ä‘iá»ƒm (Sinh viÃªn tá»± cháº¥m hoáº·c BÃ­ thÆ° chi Ä‘oÃ n cháº¥m) -->
        <form class="form" action="bi-thu-chi-doan/action.php?req=<?= $mode == 'ban_than' ? 'add_sv' : 'add' ?>" method="post" enctype="multipart/form-data">

            <input type="hidden" name="id_phieu" value="<?= $phieuchamdiem__Get_By_Id_Sinh_Vien->id_phieu ?>">
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
                                <span class="info-label">MÃ£ sá»‘ sinh viÃªn</span>
                                <span class="info-value"><?= $sinhvien__Get_By_Id->ma_sinh_vien ?></span>
                            </div>
                            <div class="info-item" style="flex: 1.2;">
                                <span class="info-label">Há» tÃªn sinh viÃªn</span>
                                <span class="info-value"><?= $sinhvien__Get_By_Id->ten_sinh_vien ?></span>
                            </div>
                            <div class="info-item" style="flex: 0.5; min-width: 60px;">
                                <span class="info-label">KhÃ³a há»c</span>
                                <span class="info-value"><?= $khoahoc__Get_By_Id->ten_khoa_hoc ?></span>
                            </div>
                            <div class="info-item" style="flex: 2.2;">
                                <span class="info-label">Khoa/ Bá»™ mÃ´n</span>
                                <span class="info-value"><?php 
                                    $nganhhoc_info = $nganhhoc->nganhhoc__Get_By_Id($lophoc__Get_By_Id->id_nganh_hoc);
                                    echo $khoa->khoa__Get_By_Id($nganhhoc_info->id_khoa)->ten_khoa;
                                ?></span>
                            </div>
                            <div class="info-item" style="flex: 1.6;">
                                <span class="info-label">Lá»›p</span>
                                <span class="info-value"><?= $lophoc__Get_By_Id->ten_lop_hoc ?></span>
                            </div>
                            <div class="info-item" style="flex: 0.5; min-width: 60px;">
                                <span class="info-label">NÄƒm há»c</span>
                                <span class="info-value"><?= $namhoc__Get_By_Id->ten_nam_hoc ?></span>
                            </div>
                            <div class="info-item" style="flex: 0.4; min-width: 50px;">
                                <span class="info-label">Há»c ká»³</span>
                                <span class="info-value"><?= $hocky__Get_By_Id->ten_hoc_ky ?></span>
                            </div>
                            <div class="info-item" style="flex: 1.2;">
                                <span class="info-label">Thá»i gian thá»±c hiá»‡n Ä‘Ã¡nh giÃ¡</span>
                                <span class="info-value">
                                    <?= isset($dotchamdiem__Get_By_Id->thoi_gian_bat_dau) ? date('d/m/Y', strtotime($dotchamdiem__Get_By_Id->thoi_gian_bat_dau)) : '' ?> - 
                                    <?= isset($dotchamdiem__Get_By_Id->thoi_gian_ket_thuc) ? date('d/m/Y', strtotime($dotchamdiem__Get_By_Id->thoi_gian_ket_thuc)) : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="results-summary">
                        <div><strong>Äiá»ƒm rÃ¨n luyá»‡n:</strong> <?= isset($ketquaxeploai__Get_By_Id_Phieu->ket_qua) ? $ketquaxeploai__Get_By_Id_Phieu->ket_qua : "ChÆ°a tá»•ng káº¿t" ?></div>
                        <div><strong>Xáº¿p loáº¡i:</strong> <?= isset($ketquaxeploai__Get_By_Id_Phieu->xep_loai) ? $ketquaxeploai__Get_By_Id_Phieu->xep_loai : "ChÆ°a tá»•ng káº¿t" ?></div>
                        <div><strong>NgÃ y xáº¿p loáº¡i:</strong> <?= isset($ketquaxeploai__Get_By_Id_Phieu->ngay_xep_loai) ? $ketquaxeploai__Get_By_Id_Phieu->ngay_xep_loai : "ChÆ°a tá»•ng káº¿t" ?></div>
                        <?php if (isset($ketquaxeploai__Get_By_Id_Phieu->ghi_chu) && $ketquaxeploai__Get_By_Id_Phieu->ghi_chu != ""): ?>
                        <div><strong>Ghi chÃº:</strong> <?= $ketquaxeploai__Get_By_Id_Phieu->ghi_chu ?></div>
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
                                    <th class="align-middle" style="padding:6px 4px; font-size: 1.15rem; background-color: #e9ecef !important;">ÄIá»€U</th>
                                    <th class="align-middle" style="padding:6px 4px; font-size: 1.4rem; background-color: #e9ecef !important;">Ná»˜I DUNG ÄÃNH GIÃ</th>
                                    <th class="align-middle" style="padding:6px 4px; background-color: #e9ecef !important;">SV Tá»°<br>CHáº¤M</th>
                                    <th class="align-middle" style="padding:6px 4px; background-color: #e9ecef !important;">Lá»šP TRÆ¯á»žNG<br>BÃ THÆ¯</th>
                                    <th class="align-middle" style="padding:6px 4px; background-color: #e9ecef !important;">BCH ÄOÃ€N<br>KHOA</th>
                                    <th class="align-middle" style="padding:6px 4px; background-color: #e9ecef !important;">Cá» Váº¤N<br>Há»ŒC Táº¬P</th>
                                    <th class="align-middle" style="padding:6px 4px; background-color: #e9ecef !important;">MINH<br>CHá»¨NG</th>
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
                                                <td rowspan="<?= $rowspan ?>" class="align-middle text-center" style="padding:6px 4px; max-width: 60px; overflow-wrap: anywhere !important; word-break: break-word !important; white-space: normal !important;">
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
                                                    <!-- QuÃ¢n sá»­a: Chá»‰ cho phÃ©p bÃ­ thÆ° chi Ä‘oÃ n sá»­a cá»™t SV Tá»± Cháº¥m khi cháº¥m báº£n thÃ¢n, vÃ  ngÆ°á»£c láº¡i -->
                                                    <input type="number" class="form-control kq_sv" name="kq_sv[]"
                title="Äiá»ƒm tá»‘i Ä‘a: <?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" max="<?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" data-id-khoan="<?= $item_2->id_khoan ?>" data-khoan-max="<?= $item_2->can_tren ?>"
                pattern="[-+]?[0-9]{1,2}" placeholder="0" min="0"
                style="<?= $quyen_sv == 0 ? 'background: linear-gradient(to bottom right, transparent 48%, #ccc 49%, #ccc 51%, transparent 52%) #e9ecef; pointer-events: none; opacity: 0.8;' . ($val_sv == 0 ? ' color: transparent !important; -webkit-text-fill-color: transparent !important;' : '') : '' ?> width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                
                <?= ($quyen_sv == 0 || $mode == 'lop' || $dotchamdiem__Get_By_Id->trang_thai == 0 || $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1) ? 'readonly tabindex="-1"' : '' ?>
                value="<?= $val_sv == 0 ? '' : $val_sv ?>">
</td>
                                                <td class="text-center align-middle" style="padding:4px;">
                                                    <input type="number" class="form-control kq_lt_bt" name="kq_lt_bt[]"
                title="Äiá»ƒm tá»‘i Ä‘a: <?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" max="<?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" data-id-khoan="<?= $item_2->id_khoan ?>" data-khoan-max="<?= $item_2->can_tren ?>"
                pattern="[-+]?[0-9]{1,2}" placeholder="0" min="0"
                style="<?= $quyen_lt == 0 ? 'background: linear-gradient(to bottom right, transparent 48%, #ccc 49%, #ccc 51%, transparent 52%) #e9ecef; pointer-events: none; opacity: 0.8;' . ($val_lt == 0 ? ' color: transparent !important; -webkit-text-fill-color: transparent !important;' : '') : '' ?> width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                
                <?= ($quyen_lt == 0 || $mode == 'ban_than' || $dotchamdiem__Get_By_Id->trang_thai == 0 || ($phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1 && $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 2)) ? 'readonly tabindex="-1"' : '' ?>
                value="<?= $val_lt == 0 ? '' : $val_lt ?>">
</td>
                                                <td class="text-center align-middle" style="padding:4px;">
                                                    <input type="number" class="form-control kq_btdk" name="kq_btdk[]"
                title="Äiá»ƒm tá»‘i Ä‘a: <?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" max="<?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" data-id-khoan="<?= $item_2->id_khoan ?>" data-khoan-max="<?= $item_2->can_tren ?>"
                pattern="[-+]?[0-9]{1,2}" placeholder="0" min="0"
                style="<?= $quyen_btdk == 0 ? 'background: linear-gradient(to bottom right, transparent 48%, #ccc 49%, #ccc 51%, transparent 52%) #e9ecef; pointer-events: none; opacity: 0.8;' . ($val_btdk == 0 ? ' color: transparent !important; -webkit-text-fill-color: transparent !important;' : '') : '' ?> width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                
                readonly tabindex="-1"
                value="<?= $val_btdk == 0 ? '' : $val_btdk ?>">
</td>
                                                <td class="text-center align-middle" style="padding:4px;">
                                                    <input type="number" class="form-control kq_gv" name="kq_gv[]"
                title="Äiá»ƒm tá»‘i Ä‘a: <?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" max="<?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" data-id-khoan="<?= $item_2->id_khoan ?>" data-khoan-max="<?= $item_2->can_tren ?>"
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
                                                            // Bi-thu-chi-doan mode logic
                                                            $readonly = ($mode == 'lop' || $dotchamdiem__Get_By_Id->trang_thai == 0) ? 'true' : 'false';
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
                                                            <!-- File Input -->
                                                            <input type="file" name="minh_chung_muc[<?= $item_3->id_muc ?>][]" multiple accept="image/*,application/pdf"
                                                                class="evidence-file-input-hidden" style="display:none;" <?= $readonly == 'true' ? 'disabled' : '' ?>>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted d-block" style="font-size: 0.75rem;">(KhÃ´ng)</span>
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
                                    <td colspan="2" class="align-middle text-right font-weight-bold" style="padding:6px 8px; font-size: 1.15rem; color: #003366; background-color: #e9ecef !important;">Tá»”NG ÄIá»‚M:</td>
                                    <td class="align-middle" style="padding:4px; background-color: #e9ecef !important;">
                                        <input type="number" class="form-control font-weight-bold " id="sum_sv" placeholder="" min="0" max="100" readonly style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;; color: #003366 !important;">
                                    </td>
                                    <td class="align-middle" style="padding:4px; background-color: #e9ecef !important;">
                                        <input type="number" class="form-control font-weight-bold" id="sum_lt_bt" placeholder="" min="0" max="100" readonly style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;; color: #003366 !important;">
                                    </td>
                                    <td class="align-middle" style="padding:4px; background-color: #e9ecef !important;">
                                        <input type="number" class="form-control font-weight-bold" id="sum_btdk" placeholder="" min="0" max="100" readonly style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;; color: #003366 !important;">
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
                <input type="submit" value="Cáº­p nháº­t" class="btn btn-success btn-lg float-right font-weight-bold" id="submit"
                    <?= $dotchamdiem__Get_By_Id->trang_thai == 0 ? 'disabled' : '' ?>>
            </div>
        </div>
        </form>

        <!-- QuÃ¢n sá»­a: ThÃªm nÃºt phÃ¢n trang chuyá»ƒn nhanh giá»¯a cÃ¡c sinh viÃªn (dÆ°á»›i form) -->
        <?php if ($mode == 'lop' && count($sinhvien_list) > 0): ?>
        <div class="row mt-3 mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $first_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($current_index === 0 || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-double-left"></i> Äáº§u
                    </a>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $prev_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($prev_id === null || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        <i class="fas fa-angle-left"></i> TrÆ°á»›c
                    </a>
                </div>
                <div class="text-muted font-weight-bold">
                    Sinh viÃªn <?= ($current_index !== false) ? ($current_index + 1) : 0 ?> / <?= count($sinhvien_list) ?><?= ($current_index !== false && isset($sinhvien_list[$current_index])) ? ' - ' . htmlspecialchars($sinhvien_list[$current_index]->ten_sinh_vien) : '' ?>
                </div>
                <div>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $next_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($next_id === null || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        Sau <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="?page=bi-thu-chi-doan&id_dot=<?= $id_dot ?>&mode=lop&filter=<?= $filter ?>&id_sinh_vien=<?= $last_id ?>" 
                       class="btn btn-outline-custom-blue <?= ($current_index === count($sinhvien_list) - 1 || $id_sinh_vien == '') ? 'disabled' : '' ?>">
                        Cuá»‘i <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>




<div class="card mt-3">
<div class="card-header">
    <div class="row">
        <div class="col">
            <p class="mb-0" style="color: red;"><b>â€¢ Quy Ä‘á»‹nh:</b></p>
            <div style="padding-left: 15px;">
                <p>
                    - Xáº¿p loáº¡i káº¿t quáº£ rÃ¨n luyá»‡n: <b>xuáº¥t sáº¯c</b> (90 - 100 Ä‘iá»ƒm), <b>tá»‘t</b> (80 - 89 Ä‘iá»ƒm), <b>khÃ¡</b> (65 - 79 Ä‘iá»ƒm), <b>trung bÃ¬nh</b> (50 - 64 Ä‘iá»ƒm), <b>yáº¿u</b> (35 - 49 Ä‘iá»ƒm), <b>kÃ©m</b> (dÆ°á»›i 35 Ä‘iá»ƒm).<br>
                    - Káº¿t quáº£ rÃ¨n luyá»‡n nÄƒm há»c <b>xuáº¥t sáº¯c</b> vÃ  <b>tá»‘t</b> Ä‘Æ°á»£c nhÃ  trÆ°á»ng xÃ©t khen thÆ°á»Ÿng.<br>
                    - Káº¿t quáº£ rÃ¨n luyá»‡n <b>yáº¿u</b>, <b>kÃ©m</b> 2 há»c ká»³ liÃªn tiáº¿p pháº£i táº¡m ngá»«ng há»c Ã­t nháº¥t 1 há»c ká»³ á»Ÿ há»c ká»³ tiáº¿p theo.<br>
                    - Sinh viÃªn bá»‹ ká»· luáº­t má»©c khiá»ƒn trÃ¡ch trong há»c ká»³ thÃ¬ má»©c xáº¿p loáº¡i khÃ´ng Ä‘Æ°á»£c vÆ°á»£t quÃ¡ loáº¡i <b>khÃ¡</b>, bá»‹ ká»· luáº­t má»©c cáº£nh cÃ¡o thÃ¬ khÃ´ng Ä‘Æ°á»£c vÆ°á»£t quÃ¡ loáº¡i <b>trung bÃ¬nh</b>.<br>
                    - Sinh viÃªn khÃ´ng ná»™p phiáº¿u Ä‘Ã¡nh giÃ¡ káº¿t quáº£ rÃ¨n luyá»‡n mÃ  khÃ´ng cÃ³ lÃ½ do chÃ­nh Ä‘Ã¡ng, cá»‘ váº¥n há»c táº­p vÃ  táº­p thá»ƒ lá»›p Ä‘Ã¡nh giÃ¡ káº¿t quáº£ rÃ¨n luyá»‡n cho sinh viÃªn khÃ´ng ná»™p phiáº¿u vÃ  trá»« Ä‘iá»ƒm Ä‘á»ƒ háº¡ má»™t báº­c xáº¿p loáº¡i (<b>xuáº¥t sáº¯c</b>: trá»« 11 Ä‘iá»ƒm, <b>tá»‘t</b>: trá»« 11 Ä‘iá»ƒm, <b>khÃ¡</b>: trá»« 15 Ä‘iá»ƒm, <b>trung bÃ¬nh</b>: trá»« 15 Ä‘iá»ƒm, <b>yáº¿u</b>: trá»« 15 Ä‘iá»ƒm).
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
        <h5 class="modal-title font-weight-bold">Minh chá»©ng <span style="font-size: 0.9rem; font-weight: normal;">(Tá»‘i Ä‘a 5MB)</span></h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 1; font-size: 1.75rem;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="row">
              <!-- Left column: List -->
              <div class="col-md-5 border-right">
                  <h6 class="font-weight-bold mb-3">Danh sÃ¡ch tá»‡p</h6>
                  
                  <!-- NÃºt táº£i lÃªn -->
                  <div class="mb-3 text-center" id="managerUploadBtnContainer">
                      <button type="button" class="btn btn-upload-custom btn-block" style="border-style: dashed; padding: 10px; transition: all 0.2s; color: #003366 !important; border-color: #003366 !important;" id="managerUploadBtn">
                          <i class="fas fa-cloud-upload-alt fa-2x mb-2 d-block"></i>
                          Nháº¥n hoáº·c kÃ©o tháº£ tá»‡p táº£i lÃªn vÃ o Ä‘Ã¢y
                      </button>
                  </div>

                  <!-- Danh sÃ¡ch Existing -->
                  <div id="managerExistingList" class="mb-3">
                      <!-- Populated by JS -->
                  </div>

                  <!-- Danh sÃ¡ch New -->
                  <div id="managerNewList">
                      <!-- Populated by JS -->
                  </div>

              </div>
              
              <!-- Right column: Preview -->
              <div class="col-md-7 d-flex flex-column align-items-center justify-content-center bg-light rounded" style="min-height: 450px; padding: 10px;">
                  <div id="managerPreviewEmpty" class="text-muted text-center">
                      <i class="fas fa-image fa-3x mb-2 d-block"></i>
                      Chá»n má»™t tá»‡p á»Ÿ danh sÃ¡ch bÃªn trÃ¡i Ä‘á»ƒ xem trÆ°á»›c
                  </div>
                  <img id="managerPreviewImage" src="" class="img-fluid rounded d-none" style="max-height: 500px; object-fit: contain;">
                  <iframe id="managerPreviewPdf" src="" class="d-none w-100" style="height: 500px; border: 1px solid #ddd; border-radius: 4px;"></iframe>
              </div>
          </div>
      </div>
      <div class="modal-footer bg-light py-2">
        <button type="button" class="btn btn-cancel-custom font-weight-bold" style="font-size: 1.15rem; padding: 6px 24px; font-weight: bold !important;" data-dismiss="modal">ÄÃ³ng</button>
      </div>
    </div>
  </div>
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
    document.getElementById("sum_sv").value = kq > 0 ? kq : '';

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


    $('.kq_sv').change(function(event) {
        var kq = 0;
        var kq_sv = document.getElementsByClassName("kq_sv");
        for (i = 0; i < kq_sv.length; i++) {
            a = Number(kq_sv[i].value);
            console.log(typeof a);
            kq += a;
        }
        document.getElementById("sum_sv").value = kq > 0 ? kq : '';
        if (kq > 100) {
            document.getElementById("submit").setAttribute("disabled", true);
            document.getElementById("submit").setAttribute("value", "Äiá»ƒm khÃ´ng há»£p lá»‡");
            $("#sum_sv").addClass("bg-danger");
        } else {
            document.getElementById("submit").removeAttribute("disabled");
            document.getElementById("submit").setAttribute("value", "Cáº­p nháº­t");
            $("#sum_sv").removeClass("bg-danger");
        }
    });

    // QuÃ¢n sá»­a: ThÃªm sá»± kiá»‡n change Ä‘á»ƒ tÃ­nh tá»•ng Ä‘iá»ƒm BÃ­ thÆ° chi Ä‘oÃ n/BÃ­ thÆ° Ä‘á»™ng
    $('.kq_lt_bt').change(function(event) {
        var kq = 0;
        var kq_lt_bt = document.getElementsByClassName("kq_lt_bt");
        for (i = 0; i < kq_lt_bt.length; i++) {
            a = Number(kq_lt_bt[i].value);
            console.log(typeof a);
            kq += a;
        }
        document.getElementById("sum_lt_bt").value = kq > 0 ? kq : '';
        if (kq > 100) {
            document.getElementById("submit").setAttribute("disabled", true);
            document.getElementById("submit").setAttribute("value", "Äiá»ƒm khÃ´ng há»£p lá»‡");
            $("#sum_lt_bt").addClass("bg-danger");
        } else {
            document.getElementById("submit").removeAttribute("disabled");
            document.getElementById("submit").setAttribute("value", "Cáº­p nháº­t");
            $("#sum_lt_bt").removeClass("bg-danger");
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
            let deleteBtn = isCurrentReadonly ? '' : `<button type="button" class="btn btn-sm text-danger btn-delete-existing" data-id="${id}" title="XÃ³a"><i class="fas fa-trash"></i></button>`;
            
            existingListHtml += `
                <div class="d-flex justify-content-between align-items-center p-2 mb-1 border rounded bg-white" style="font-size: 13px;">
                    <div class="text-truncate btn-view-evidence" data-url="${url}" data-type="${isPdf ? 'pdf' : 'image'}" style="max-width: 250px; cursor: pointer;" title="Nháº¥n Ä‘á»ƒ xem trÆ°á»›c: ${name}">
                        <i class="${iconClass} mr-1"></i> ${name}
                    </div>
                    <div>
                        ${deleteBtn}
                    </div>
                </div>
            `;
        });
        
        if(existingListHtml === '') {
            existingListHtml = '<p class="text-muted small text-center italic" style="font-style: italic;">ChÆ°a cÃ³ tá»‡p nÃ o trÃªn há»‡ thá»‘ng.</p>';
        }
        $('#managerExistingList').html('<h6 class="small font-weight-bold text-uppercase text-muted mb-2">ÄÃ£ táº£i lÃªn</h6>' + existingListHtml);
        
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
                        <div class="text-truncate btn-view-evidence" data-url="${objUrl}" data-type="${isPdf ? 'pdf' : 'image'}" style="max-width: 250px; cursor: pointer;" title="Nháº¥n Ä‘á»ƒ xem trÆ°á»›c: ${file.name}">
                            <i class="${iconClass} mr-1"></i> <span class="text-success">${file.name}</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm text-danger btn-remove-new" data-name="${file.name}" title="XÃ³a"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                `;
            }
            newListHtml = '<h6 class="small font-weight-bold text-uppercase text-success mb-2 mt-3">ÄÃ£ chá»n má»›i (Chá» lÆ°u)</h6>' + newListHtml + 
                '<div class="text-center mt-2"><button type="button" class="btn btn-sm btn-link text-danger btn-clear-new">Há»§y toÃ n bá»™ tá»‡p chá»n má»›i</button></div>';
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
        if(confirm('XÃ¡c nháº­n xÃ³a tá»‡p nÃ y? Thay Ä‘á»•i chá»‰ Ä‘Æ°á»£c Ã¡p dá»¥ng khi báº¡n nháº¥n "Cáº­p nháº­t" form.')) {
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
                Swal.fire({
                    icon: 'error',
                    title: 'Lá»—i',
                    text: 'Tá»‡p "' + file.name + '" vÆ°á»£t quÃ¡ dung lÆ°á»£ng tá»‘i Ä‘a 5MB!'
                });
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
                Swal.fire({icon: 'warning', title: 'VÆ°á»£t quÃ¡ Ä‘iá»ƒm tá»‘i Ä‘a', text: 'Tá»•ng Ä‘iá»ƒm cá»§a Khoáº£n nÃ y khÃ´ng Ä‘Æ°á»£c vÆ°á»£t quÃ¡ ' + khoan_max + ' Ä‘iá»ƒm!'});
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

