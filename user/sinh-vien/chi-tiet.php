<?php
// Nhựt sửa lỗi: Khởi động session và kiểm tra đăng nhập tránh bypass auth.
session_start();
if (!isset($_SESSION['admin']) && !isset($_SESSION['user'])) {
    header('location: ../../auth/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chấm Điểm Rèn Luyện</title>
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/gif" sizes="16x16">
    <meta name="description" content="Chấm Điểm Rèn Luyện">
    <!-- CSS Files -->
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="../../assets/css/source-sans-pro.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../assets/theme/plugins/fontawesome-free/css/all.min.css">

    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="../../assets/theme/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
    <!-- Ekko Lightbox -->
    <link rel="stylesheet" href="../../assets/theme/plugins/ekko-lightbox/ekko-lightbox.css">
    <link rel="stylesheet" href="../../assets/theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../assets/theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../../assets/theme/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../assets/theme/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../assets/vendor/dropzone/dropzone.min.css">
    <link rel="stylesheet" href="../../assets/css/main.css?v=8">

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php

        require "../../models/getModel.php";

        $id_lop_hoc = isset($_GET['id_lop_hoc']) ? $_GET['id_lop_hoc'] : 0;
        $id_dot = isset($_GET['id_dot']) ? $_GET['id_dot'] : 0;
        $id_sinh_vien = isset($_GET['id_sinh_vien']) ? $_GET['id_sinh_vien'] : 0;

        // Nhựt sửa lỗi: Phân quyền truy cập chi tiết phiếu chấm (IDOR).
        // 1. Sinh viên thường chỉ được xem phiếu của chính mình
        if (isset($_SESSION['sv']) && (int)$_SESSION['sv']->id_nguoi_dung !== (int)$id_sinh_vien) {
            header('location: ../../auth/');
            exit();
        }

        // 2. Lớp trưởng / Bí thư chi đoàn chỉ được xem sinh viên thuộc lớp mình quản lý
        if (isset($_SESSION['lt']) || isset($_SESSION['bt'])) {
            $session_user = isset($_SESSION['lt']) ? $_SESSION['lt'] : $_SESSION['bt'];
            $sinhvien_sender = $sinhvien->sinhvien__Get_By_Id($session_user->id_nguoi_dung);
            if ($sinhvien_sender && (int)$sinhvien_sender->id_lop_hoc !== (int)$id_lop_hoc) {
                header('location: ../../auth/');
                exit();
            }
        }

        // 3. Giảng viên cố vấn chỉ được xem lớp mình được phân công cố vấn
        if (isset($_SESSION['gv'])) {
            $is_assigned = false;
            $gv_id = $_SESSION['gv']->id_nguoi_dung;
            $db = $phancong->connect;
            $stmt_pc = $db->prepare("SELECT COUNT(*) FROM phancong WHERE id_giang_vien = ? AND id_lop_hoc = ?");
            $stmt_pc->execute(array($gv_id, $id_lop_hoc));
            if ((int)$stmt_pc->fetchColumn() > 0) {
                $is_assigned = true;
            }
            if (!$is_assigned) {
                header('location: ../../auth/');
                exit();
            }
        }

        // 4. Bí thư đoàn khoa chỉ được xem sinh viên thuộc khoa của mình
        if (isset($_SESSION['btdk'])) {
            $is_same_khoa = false;
            $btdk_id = $_SESSION['btdk']->id_nguoi_dung;
            $btdk_info = $bithudoankhoa->bithudoankhoa__Get_By_Id($btdk_id);
            if ($btdk_info) {
                // Lấy khoa của lớp học cần xem
                $lop_hoc = $lophoc->lophoc__Get_By_Id($id_lop_hoc);
                if ($lop_hoc && isset($lop_hoc->id_nganh_hoc)) {
                    $nganh = $nganhhoc->nganhhoc__Get_By_Id($lop_hoc->id_nganh_hoc);
                    if ($nganh && (int)$nganh->id_khoa === (int)$btdk_info->id_khoa) {
                        $is_same_khoa = true;
                    }
                }
            }
            if (!$is_same_khoa) {
                header('location: ../../auth/');
                exit();
            }
        }

        $phieuchamdiem__Get_By_Id_Sinh_Vien = $phieuchamdiem->phieuchamdiem__Get_By_Id_Sinh_Vien($id_sinh_vien, $id_dot);

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
        }
        $ketquaxeploai__Get_By_Id_Phieu = $ketquaxeploai->ketquaxeploai__Get_By_Id_Phieu($id_lop_hoc, $id_dot, $id_sinh_vien);




        ?>
        <link rel="stylesheet" href="../../assets/css/user.css?v=<?=time()?>">
        <?php if (isset($phieuchamdiem__Get_By_Id_Sinh_Vien->id_lop_ap_dung)) : ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper student-evaluation-wrapper">
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
                <!-- Content Header (Page header) -->
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
                                <span class="info-label">Thời gian thực hiện</span>
                                <span class="info-value">
                                    <?= isset($dotchamdiem__Get_By_Id->thoi_gian_bat_dau) ? date('d/m/Y', strtotime($dotchamdiem__Get_By_Id->thoi_gian_bat_dau)) : '' ?> - 
                                    <?= isset($dotchamdiem__Get_By_Id->thoi_gian_ket_thuc) ? date('d/m/Y', strtotime($dotchamdiem__Get_By_Id->thoi_gian_ket_thuc)) : '' ?>
                                </span>
                            </div>
                        </div>
                        <div class="results-summary">
                            <div><strong>Ngày xếp loại:</strong> <?= $ketquaxeploai__Get_By_Id_Phieu ? $ketquaxeploai__Get_By_Id_Phieu->ngay_xep_loai : "Chưa tổng kết" ?></div>
                        <div><strong>Điểm rèn luyện:</strong> <?= $ketquaxeploai__Get_By_Id_Phieu ? $ketquaxeploai__Get_By_Id_Phieu->ket_qua : "Chưa tổng kết" ?></div>
                        <div><strong>Xếp loại:</strong> <?= $ketquaxeploai__Get_By_Id_Phieu ? $ketquaxeploai__Get_By_Id_Phieu->xep_loai : "Chưa tổng kết" ?></div>
                            <div><strong>Ghi chú:</strong> <?= $ketquaxeploai__Get_By_Id_Phieu ? $ketquaxeploai__Get_By_Id_Phieu->ghi_chu : "" ?></div>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                        <div class="card-body responsive">
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
                                
                            </colgroup>
                            <thead class="thead-light text-center">
                                <tr>
                                    <th class="align-middle" style="padding:6px 4px; font-size: 1.15rem; background-color: #e9ecef !important;">ĐIỀU</th>
                                    <th class="align-middle" style="padding:6px 4px; font-size: 1.4rem; background-color: #e9ecef !important;">NỘI DUNG</th>
                                    <th class="align-middle" style="padding:6px 4px; background-color: #e9ecef !important;">SV TỰ<br>CHẤM</th>
                                    <th class="align-middle" style="padding:6px 4px; background-color: #e9ecef !important;">LỚP TRƯỞNG<br>BÍ THƯ</th>
                                    <th class="align-middle" style="padding:6px 4px; background-color: #e9ecef !important;">BCH ĐOÀN<br>KHOA</th>
                                    <th class="align-middle" style="padding:6px 4px; background-color: #e9ecef !important;">CỐ VẤN<br>HỌC TẬP</th>
                                    
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
                                                    <div class="font-weight-bold" style="white-space: normal !important; word-wrap: break-word;"><?= $dieu->dieu__Get_By_Id($item_1->id_dieu)->ten_dieu ?></div>
                                                    <div class="text-muted small mt-1" style="white-space: normal !important; word-wrap: break-word;"><?= $dieu->dieu__Get_By_Id($item_1->id_dieu)->ghi_chu ?></div>
                                                </td>
                                                <?php $is_first_row_dieu = false; ?>
                                            <?php endif; ?>
                                            <td class="font-weight-bold text-left criterion-text-cell" style="padding:6px 8px;">
                                                <?= $khoan->khoan__Get_By_Id($item_2->id_khoan)->ten_khoan ?>
                                            </td>
                                            <td class="text-center align-middle"><span class="khoan-total-kq_sv font-weight-bold" style="color: #003366;" data-id-khoan="<?= $item_2->id_khoan ?>"></span></td>
<td class="text-center align-middle"><span class="khoan-total-kq_lt_bt font-weight-bold" style="color: #003366;" data-id-khoan="<?= $item_2->id_khoan ?>"></span></td>
<td class="text-center align-middle"><span class="khoan-total-kq_btdk font-weight-bold" style="color: #003366;" data-id-khoan="<?= $item_2->id_khoan ?>"></span></td>
<td class="text-center align-middle"><span class="khoan-total-kq_gv font-weight-bold" style="color: #003366;" data-id-khoan="<?= $item_2->id_khoan ?>"></span></td>
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
                                                    <input type="number" class="form-control kq_sv" name="kq_sv[]"
                title="Điểm tối đa: <?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" max="<?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" data-id-khoan="<?= $item_2->id_khoan ?>" data-khoan-max="<?= $item_2->can_tren ?>"
                pattern="[-+]?[0-9]{1,2}" placeholder="0" min="0"
                style="<?= $quyen_sv == 0 ? 'background: linear-gradient(to bottom right, transparent 48%, #ccc 49%, #ccc 51%, transparent 52%) #e9ecef; pointer-events: none; opacity: 0.8;' . ($val_sv == 0 ? ' color: transparent !important; -webkit-text-fill-color: transparent !important;' : '') : '' ?> width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                
                <?= ($quyen_sv == 0 || $dotchamdiem__Get_By_Id->trang_thai == 0 || $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1) ? 'disabled' : '' ?>
                value="<?= $val_sv == 0 ? '' : $val_sv ?>">
</td>
                                                <td class="text-center align-middle" style="padding:4px;">
                                                    <input type="number" class="form-control kq_lt_bt" name="kq_lt_bt[]"
                title="Điểm tối đa: <?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" max="<?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" data-id-khoan="<?= $item_2->id_khoan ?>" data-khoan-max="<?= $item_2->can_tren ?>"
                pattern="[-+]?[0-9]{1,2}" placeholder="0" min="0"
                style="<?= $quyen_lt == 0 ? 'background: linear-gradient(to bottom right, transparent 48%, #ccc 49%, #ccc 51%, transparent 52%) #e9ecef; pointer-events: none; opacity: 0.8;' . ($val_lt == 0 ? ' color: transparent !important; -webkit-text-fill-color: transparent !important;' : '') : '' ?> width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                
                disabled
                value="<?= $val_lt == 0 ? '' : $val_lt ?>">
</td>
                                                <td class="text-center align-middle" style="padding:4px;">
                                                    <input type="number" class="form-control kq_btdk" name="kq_btdk[]"
                title="Điểm tối đa: <?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" max="<?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" data-id-khoan="<?= $item_2->id_khoan ?>" data-khoan-max="<?= $item_2->can_tren ?>"
                pattern="[-+]?[0-9]{1,2}" placeholder="0" min="0"
                style="<?= $quyen_btdk == 0 ? 'background: linear-gradient(to bottom right, transparent 48%, #ccc 49%, #ccc 51%, transparent 52%) #e9ecef; pointer-events: none; opacity: 0.8;' . ($val_btdk == 0 ? ' color: transparent !important; -webkit-text-fill-color: transparent !important;' : '') : '' ?> width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                
                disabled
                value="<?= $val_btdk == 0 ? '' : $val_btdk ?>">
</td>
                                                <td class="text-center align-middle" style="padding:4px;">
                                                    <input type="number" class="form-control kq_gv" name="kq_gv[]"
                title="Điểm tối đa: <?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" max="<?= $muc->muc__Get_By_Id($item_3->id_muc)->diem_toi_da ?>" data-id-khoan="<?= $item_2->id_khoan ?>" data-khoan-max="<?= $item_2->can_tren ?>"
                pattern="[-+]?[0-9]{1,2}" placeholder="0" min="0"
                style="<?= $quyen_gv == 0 ? 'background: linear-gradient(to bottom right, transparent 48%, #ccc 49%, #ccc 51%, transparent 52%) #e9ecef; pointer-events: none; opacity: 0.8;' . ($val_gv == 0 ? ' color: transparent !important; -webkit-text-fill-color: transparent !important;' : '') : '' ?> width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                
                disabled
                value="<?= $val_gv == 0 ? '' : $val_gv ?>">
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
                                        <input type="number" class="form-control font-weight-bold bg-success text-white" id="sum_sv" placeholder="0" min="0" max="100" readonly style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                    </td>
                                    <td class="align-middle" style="padding:4px; background-color: #e9ecef !important;">
                                        <input type="number" class="form-control font-weight-bold" id="sum_lt_bt" placeholder="0" min="0" max="100" readonly style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                    </td>
                                    <td class="align-middle" style="padding:4px; background-color: #e9ecef !important;">
                                        <input type="number" class="form-control font-weight-bold" id="sum_btdk" placeholder="0" min="0" max="100" readonly style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                    </td>
                                    <td class="align-middle" style="padding:4px; background-color: #e9ecef !important;">
                                        <input type="number" class="form-control font-weight-bold" id="sum_gv" placeholder="0" min="0" max="100" readonly style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                    </td>
                                    
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                        </div>
                    </div>
                    <!-- Main content -->

                    <div class="row">
                        <div class="col-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h4 class="card-title">Minh chứng đã thêm</h4>
                                </div>
                                <div class="card-body">

                                    <div>
                                        <div class="filter-container p-0 d-flex flex-wrap">
                                            <?php foreach ($minhchung->minhchung__Get_By_Id_Phieu($phieuchamdiem__Get_By_Id_Sinh_Vien->id_phieu) as $item) : 
                                                $isPdf = strpos($item->hinh_anh, 'application/pdf') !== false;
                                            ?>
                                                <div class="card mr-3 mb-3 shadow-sm" style="width: 200px; height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center; border-radius: 8px; background: #f8f9fa;">
                                                    <div class="filtr-item w-100 h-100" data-category="1">
                                                        <?php if($isPdf): ?>
                                                            <a href="image.php?id_minh_chung=<?= $item->id_minh_chung ?>" target="_blank" class="d-flex flex-column align-items-center justify-content-center w-100 h-100" style="text-decoration: none; color: #dc3545;">
                                                                <i class="fas fa-file-pdf fa-4x mb-2"></i>
                                                                <span class="font-weight-bold">Xem tệp PDF</span>
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="image.php?id_minh_chung=<?= $item->id_minh_chung ?>" data-toggle="lightbox" class="d-block w-100 h-100">
                                                                <img src="<?= $item->hinh_anh ?>" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;" />
                                                            </a>
                                                        <?php endif; ?>
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

        })
    </script>

<?php endif ?>
</div>

<!-- Js Files -->
<script src="../../assets/vendor/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../assets/theme/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="../../assets/theme/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>



<script src="../../assets/theme/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../assets/theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../assets/theme/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../../assets/theme/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../../assets/theme/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../../assets/theme/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../../assets/theme/plugins/jszip/jszip.min.js"></script>
<script src="../../assets/theme/plugins/pdfmake/pdfmake.min.js"></script>
<script src="../../assets/theme/plugins/pdfmake/vfs_fonts.js"></script>
<script src="../../assets/theme/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../../assets/theme/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../../assets/theme/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Ekko Lightbox -->
<script src="../../assets/theme/plugins/ekko-lightbox/ekko-lightbox.min.js"></script>
<!-- Filterizr-->
<script src="../../assets/theme/plugins/filterizr/jquery.filterizr.min.js"></script>
<!-- sweetalert2  -->
<script src="../../assets/vendor/sweetalert2@11.js"></script>
<!-- dropzonejs -->
<script src="../../assets/vendor/dropzone/dropzone.min.js"></script>

<!-- AdminLTE App -->
<script src="../../assets/theme/dist/js/adminlte.min.js"></script>


<script>
    //Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox({
        filterTextClear: 'Hiện tất cả',
        filterPlaceHolder: 'Tìm kiếm',
        infoText: 'Hiển thị tất cả ({0})',
        infoTextFiltered: '<span class="badge badge-warning">Tìm kiếm</span> {0} từ {1}',
        infoTextEmpty: 'Danh sách trống'
    });
    $('.duallistbox_sv').bootstrapDualListbox({
        filterTextClear: 'Hiện tất cả',
        filterPlaceHolder: 'Tìm kiếm',
        infoText: 'Hiển thị tất cả ({0})',
        infoTextFiltered: '<span class="badge badge-warning">Tìm kiếm</span> {0} từ {1}',
        infoTextEmpty: 'Danh sách trống'
    });


    Dropzone.options.uploadForm = { // The camelized version of the ID of the form element

        dictDefaultMessage: 'Kéo thả hình ảnh hoặc file PDF vào đây',
        paramName: "hinh_anh",
        acceptedFiles: "image/jpeg,image/png,image/jpg,application/pdf",
        autoProcessQueue: false,
        thumbnailWidth: 400,
        thumbnailHeight: 400,
        maxFilesize: 5,
        addRemoveLinks: true,
        dictFileTooBig: 'File vượt quá dung lượng cho phép ({{filesize}}MB). Tối đa: {{maxFilesize}}MB.',
        dictInvalidFileType: 'Chỉ chấp nhận hình ảnh hoặc file PDF.',

        init: function() {
            var myDropzone = this;
            document.getElementById("btn_upload").removeAttribute("disabled");
            this.element.querySelector("button[type=submit]").addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                myDropzone.processQueue();
            });
        },
        queuecomplete: function() {
            this.removeAllFiles();
            Toast.fire({
                title: 'Đã thêm minh chứng thành công',
                icon: 'success'
            }).then(() => {
                location.reload();
            })
        },

    }
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox({
            alwaysShowClose: true
        });
    });

    $('.filter-container').filterizr({
        gutterPixels: 3
    });
    $('.btn[data-filter]').on('click', function() {
        $('.btn[data-filter]').removeClass('active');
        $(this).addClass('active');
    });


    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
</script>
<style>
    /* CSS cho thẻ Toast: Giảm khoảng cách giữa tiêu đề và nội dung */
    .swal2-popup.swal2-toast {
        padding: 8px 12px !important;
    }
    .swal2-popup.swal2-toast:has(.swal2-success) {
        border: 1px solid #28a745 !important;
        border-radius: 6px !important;
    }
    .swal2-popup.swal2-toast:has(.swal2-error) {
        border: 1px solid #dc3545 !important;
        border-radius: 6px !important;
    }
    .swal2-toast .swal2-title {
        margin: 0.1em 0 0 0 !important;
        font-size: 15px !important;
    }
    .swal2-toast.swal2-icon-success .swal2-title,
    .swal2-toast .swal2-success ~ .swal2-title {
        color: #28a745 !important;
        font-weight: bold !important;
    }
    .swal2-toast.swal2-icon-error .swal2-title,
    .swal2-toast .swal2-error ~ .swal2-title {
        color: #dc3545 !important;
        font-weight: bold !important;
    }
    .swal2-toast .swal2-html-container {
        margin: 0.2em 0 0.2em 0 !important;
        font-size: 14px !important;
    }
</style>
<?php
if (isset($_GET['status'])) {
    if ($_GET['status'] == "success") {
        echo "<script>
               Toast.fire(
                   'Thành công!',
                   'Thao tác thành công!',
                   'success'
                 );
                 if (window.history.replaceState) {
                     const url = new URL(window.location.href);
                     url.searchParams.delete('status');
                     window.history.replaceState({ path: url.href }, '', url.href);
                 }
                 </script>";
    }
    if ($_GET['status'] == "failed") {
        echo "<script>
               Toast.fire(
                   'Thất bại!',
                   'Thao tác không thành công!',
                   'error'
                 );
                 if (window.history.replaceState) {
                     const url = new URL(window.location.href);
                     url.searchParams.delete('status');
                     window.history.replaceState({ path: url.href }, '', url.href);
                 }
                 </script>";
    }
}
?>
</body>

</html>
