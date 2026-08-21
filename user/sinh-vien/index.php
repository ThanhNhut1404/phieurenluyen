<?php
if (!isset($_SESSION['sv'])) {
    header('location: ../auth/');
    exit();
}
$id_dot = $dotchamdiem->dotchamdiem__Get_Last()->id_dot;

if (isset($_GET['id_dot'])) {
    $id_dot = $_GET['id_dot'];
}
$id_sinh_vien = $_SESSION['sv']->id_nguoi_dung;
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

    $ketquaxeploai__Get_By_Id_Phieu = $ketquaxeploai->ketquaxeploai__Get_By_Id_Phieu($id_lop_ap_dung, $id_dot, $id_sinh_vien);
}



?>
<link rel="stylesheet" href="../assets/css/user.css">
<?php if (!isset($phieuchamdiem__Get_By_Id_Sinh_Vien->id_lop_ap_dung)) : ?>


<div class="content-wrapper student-evaluation-wrapper">
    <!-- Content Header (Page header) -->
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
        <form class="form" action="sinh-vien/action.php?req=add" method="post" enctype="multipart/form-data">

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
                                <span class="info-label">Thời gian thực hiện đánh giá</span>
                                <span class="info-value">
                                    <?= isset($dotchamdiem__Get_By_Id->thoi_gian_bat_dau) ? date('d/m/Y', strtotime($dotchamdiem__Get_By_Id->thoi_gian_bat_dau)) : '' ?> - 
                                    <?= isset($dotchamdiem__Get_By_Id->thoi_gian_ket_thuc) ? date('d/m/Y', strtotime($dotchamdiem__Get_By_Id->thoi_gian_ket_thuc)) : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="results-summary">
                        <div><strong>Điểm rèn luyện:</strong> <?= isset($ketquaxeploai__Get_By_Id_Phieu->ket_qua) ? $ketquaxeploai__Get_By_Id_Phieu->ket_qua : "Chưa tổng kết" ?></div>
                        <div><strong>Xếp loại:</strong> <?= isset($ketquaxeploai__Get_By_Id_Phieu->xep_loai) ? $ketquaxeploai__Get_By_Id_Phieu->xep_loai : "Chưa tổng kết" ?></div>
                        <div><strong>Ngày xếp loại:</strong> <?= isset($ketquaxeploai__Get_By_Id_Phieu->ngay_xep_loai) ? $ketquaxeploai__Get_By_Id_Phieu->ngay_xep_loai : "Chưa tổng kết" ?></div>
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
                                    <th class="align-middle" style="padding:6px 4px; font-size: 1.4rem; background-color: #e9ecef !important;">NỘI DUNG</th>
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
                                                <td rowspan="<?= $rowspan ?>" class="align-middle text-center" style="padding:6px 4px; max-width: 60px; overflow-wrap: anywhere !important; word-break: break-word !important; white-space: normal !important;">
                                                    <div class="font-weight-bold" style="white-space: normal !important; word-wrap: break-word;"><?= $dieu->dieu__Get_By_Id($item_1->id_dieu)->ten_dieu ?></div>
                                                    <div class="text-muted small mt-1" style="white-space: normal !important; word-wrap: break-word;"><?= $dieu->dieu__Get_By_Id($item_1->id_dieu)->ghi_chu ?></div>
                                                </td>
                                                <?php $is_first_row_dieu = false; ?>
                                            <?php endif; ?>
                                            <td class="font-weight-bold text-left criterion-text-cell" style="padding:6px 8px;">
                                                <?= $khoan->khoan__Get_By_Id($item_2->id_khoan)->ten_khoan ?>
                                            </td>
                                            <td colspan="5"></td>
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
                                                <td class="text-center align-middle" style="padding:4px;">
                                                    <input type="number" class="form-control kq_sv" name="kq_sv[]"
                                                        style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;"
                                                        <?= $dotchamdiem__Get_By_Id->trang_thai == 0 ? 'readonly' : '' ?>
                                                        <?= $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1 ? 'readonly' : '' ?>
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
                                                            $readonly = ($dotchamdiem__Get_By_Id->trang_thai == 0) ? 'true' : 'false';
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
                                        <input type="number" class="form-control font-weight-bold bg-success text-white" id="sum_sv" placeholder="0" min="0" max="100" readonly required style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                    </td>
                                    <td class="align-middle" style="padding:4px; background-color: #e9ecef !important;">
                                        <input type="number" class="form-control font-weight-bold" id="sum_lt_bt" placeholder="0" min="0" max="100" readonly required style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                    </td>
                                    <td class="align-middle" style="padding:4px; background-color: #e9ecef !important;">
                                        <input type="number" class="form-control font-weight-bold" id="sum_btdk" placeholder="0" min="0" max="100" readonly required style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                    </td>
                                    <td class="align-middle" style="padding:4px; background-color: #e9ecef !important;">
                                        <input type="number" class="form-control font-weight-bold" id="sum_gv" placeholder="0" min="0" max="100" readonly required style="width:46px;max-width:46px;text-align:center;padding:3px 4px;margin:0 auto;">
                                    </td>
                                    <td style="padding:4px; background-color: #e9ecef !important;"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <div class="card-footer">
                <?php
                    $is_submitted = $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1;
                    $btn_text = $is_submitted ? "Cập nhật minh chứng" : "Nộp phiếu đánh giá";
                    $btn_class = $is_submitted ? "btn text-white" : "btn btn-success";
                    $btn_style = $is_submitted ? 'style="background-color: #003366;"' : '';
                ?>
                <input type="submit" value="<?= $btn_text ?>" class="<?= $btn_class ?> btn-lg float-right font-weight-bold" <?= $btn_style ?> id="submit"
                    <?= $dotchamdiem__Get_By_Id->trang_thai == 0 ? 'disabled' : '' ?>>
            </div>
            </div>
        </form>




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
            document.getElementById("submit").setAttribute("value", "Nộp phiếu đánh giá");
            $("#sum_sv").removeClass("bg-danger");
        }
    });

});
</script>

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
            if (!existingNames.has(this.files[i].name)) {
                dt.items.add(this.files[i]);
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
        // Removed dynamic toggling of btn-outline-primary because we use inline styling
    }
});
</script>


<?php endif ?>
