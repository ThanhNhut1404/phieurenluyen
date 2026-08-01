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
<div class="ml-0 mr-3 content-wrapper">
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
<div class="ml-0 mr-3 content-wrapper">
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
        <form class="form" action="sinh-vien/action.php?req=add" method="post" enctype="multipart/form-data">

            <input type="hidden" name="id_phieu" value="<?= $phieuchamdiem__Get_By_Id_Sinh_Vien->id_phieu ?>">
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
                <!-- /.card-header -->
                <div class="card-body responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="w-10  text-center vertical-align-middle">Điều</th>
                                <th class="w-90  text-center vertical-align-middle no-padding">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td class="w-50 no-border full  h-0 ">
                                                    <?= $dw ?>
                                                </td>
                                                <td class="w-10 h-0 no-border full  h-0">
                                                    Sv tự chấm
                                                    <br>
                                                    Tự chấm
                                                </td>
                                                <td class="w-10 h-0 no-border full  h-0">
                                                    Lớp trưởng <br />
                                                    Bí thư
                                                </td>
                                                <td class="w-10 h-0 no-border full  h-0">
                                                    BCH <br /> đoàn khoa
                                                </td>
                                                <td class="w-10 h-0 no-border full  h-0">
                                                    Cố vấn học tập
                                                </td>
                                                <td class="w-10 h-0 no-border full  h-0">
                                                    Minh chứng
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="w-50 no-border full vertical-align-middle">
                                                    Nội dung chấm điểm
                                                </td>
                                                <td class="w-10 no-border full vertical-align-middle">
                                                    Sinh viên
                                                    <br>
                                                    Tự chấm
                                                </td>
                                                <td class="w-10 no-border full vertical-align-middle">
                                                    Lớp trưởng <br />
                                                    Bí thư
                                                </td>
                                                <td class="w-10 no-border full vertical-align-middle">
                                                    BCH <br /> đoàn khoa
                                                </td>
                                                <td class="w-10 no-border full vertical-align-middle">
                                                    Cố vấn <br /> học tập
                                                </td>
                                                <td class="w-10 no-border full vertical-align-middle">
                                                    Kèm ảnh
                                                </td>
                                        </tbody>
                                    </table>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>

                            <?php foreach ($bocauhoi__Get_By_Id_Mau_Phieu as $item_1) : ?>
                            <tr>
                                <td class="vertical-align-middle">
                                    <table class="table no-border text-center">
                                        <tbody>
                                            <tr>
                                                <th class="p-0">
                                                    <?= $dieu->dieu__Get_By_Id($item_1->id_dieu)->ten_dieu ?>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th class="p-2">
                                                </th>
                                            </tr>
                                            <tr>
                                                <th class="p-0">
                                                    <?= $dieu->dieu__Get_By_Id($item_1->id_dieu)->ghi_chu ?>
                                                </th>
                                            </tr>

                                        </tbody>
                                    </table>
                                </td>

                                <td class="no-padding">
                                    <?php foreach ($khoan->khoan__Get_All_By_Id_Dieu($item_1->id_dieu) as $item_2) : ?>

                                    <table class="w-100 table-striped">
                                        <tbody>
                                            <tr>
                                                <th class="w-100 table table-border">
                                                    <?= $khoan->khoan__Get_By_Id($item_2->id_khoan)->ten_khoan ?>
                                                </th>

                                            </tr>
                                        </tbody>
                                    </table>
                                    <?php foreach ($muc->muc__Get_All_By_Id_Khoan($item_2->id_khoan) as $item_3) : ?>
                                    <?php $dw = $item_3->ten_muc ?>

                                    <table class="w-100">
                                        <tbody>
                                            <tr>
                                                <td class="w-50 no-border full  h-0 ">
                                                    <?= $dw ?>
                                                </td>
                                                <td class="w-10 h-0 no-border full">
                                                    Sv tự chấm
                                                    <br>
                                                    Tự chấm
                                                </td>
                                                <td class="w-10 h-0 no-border full">
                                                    Lớp trưởng <br />
                                                    Bí thư
                                                </td>
                                                <td class="w-10 h-0 no-border full">
                                                    BCH <br /> đoàn khoa
                                                </td>
                                                <td class="w-10 h-0 no-border full">
                                                    Cố vấn học tập
                                                </td>
                                            </tr>
                                            <tr class="border-bottom">
                                                <td class="w-50 no-border full vertical-align-middle">
                                                    - <?= $muc->muc__Get_By_Id($item_3->id_muc)->ten_muc ?>
                                                </td>

                                                <td class="w-10 no-border full vertical-align-middle">
                                                    <input type="number" class="form-control kq_sv" name="kq_sv[]"
                                                        <?= $dotchamdiem__Get_By_Id->trang_thai == 0 ? 'readonly' : '' ?>
                                                        <?= $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1 ? 'readonly' : '' ?>
                                                        pattern="[-+]?[0-9]{1-2}"
                                                        title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0"
                                                        max="<?= $item_2->can_tren ?>" required
                                                        value="<?= $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_sv)[$i] ?>">
                                                </td>
                                                <td class="w-10 no-border full vertical-align-middle">
                                                    <input type="number" class="form-control kq_lt_bt" name="kq_lt_bt[]"
                                                        pattern="[-+]?[0-9]{1-2}"
                                                        title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0"
                                                        max="<?= $item_2->can_tren ?>" required disabled
                                                        value="<?= isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_lt_bt)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_lt_bt)[$i] : 0 ?>">
                                                </td>
                                                <td class="w-10 no-border full vertical-align-middle">
                                                    <input type="number" class="form-control kq_btdk" name="kq_btdk[]"
                                                        pattern="[-+]?[0-9]{1-2}"
                                                        title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0"
                                                        max="<?= $item_2->can_tren ?>" required disabled
                                                        value="<?= isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_btdk)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_btdk)[$i] : 0 ?>">
                                                </td>
                                                <td class="w-10 no-border full vertical-align-middle">
                                                    <input type="number" class="form-control kq_gv" name="kq_gv[]"
                                                        pattern="[-+]?[0-9]{1-2}"
                                                        title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0"
                                                        max="<?= $item_2->can_tren ?>" required disabled
                                                        value="<?= isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_gv)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_gv)[$i] : 0 ?>">
                                                </td>
                                                <td class="w-10 no-border full vertical-align-middle">
                                                    <?php if ($muc->muc__Get_By_Id($item_3->id_muc)->co_minh_chung == 1): ?>
                                                        <input type="file" name="minh_chung_muc[<?= $item_3->id_muc ?>][]" multiple accept="image/*"
                                                            <?= $dotchamdiem__Get_By_Id->trang_thai == 0 ? 'disabled' : '' ?>
                                                            <?= $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1 ? 'disabled' : '' ?>
                                                            class="form-control-file" style="font-size: 0.8rem;">
                                                        <?php
                                                            // Hiển thị ảnh đã nộp nếu có
                                                            $minh_chung_cua_muc = $minhchung->minhchung__Get_By_Id_Phieu_And_Muc($phieuchamdiem__Get_By_Id_Sinh_Vien->id_phieu, $item_3->id_muc);
                                                            if (count($minh_chung_cua_muc) > 0) {
                                                                echo '<div class="mt-1 d-flex flex-wrap">';
                                                                foreach ($minh_chung_cua_muc as $mc) {
                                                                    echo '<a href="sinh-vien/image.php?id_minh_chung=' . $mc->id_minh_chung . '" data-toggle="lightbox" class="mr-1 mb-1 border" style="width: 30px; height: 30px; overflow: hidden; display: inline-block;">';
                                                                    echo '<img src="' . $mc->hinh_anh . '" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;" />';
                                                                    echo '</a>';
                                                                }
                                                                echo '</div>';
                                                            }
                                                        ?>
                                                    <?php else: ?>
                                                        <span class="text-muted text-center d-block w-100" style="font-size: 0.8rem;">(Không yêu cầu)</span>
                                                    <?php endif; ?>
                                                </td>

                                </td>
                            </tr>

                        </tbody>
                    </table>
                    <?php $i++ ?>
                    <?php endforeach ?>

                    <?php endforeach ?>

                    </td>
                    </tr>

                    <?php endforeach ?>

                    </tbody>
                    <footer>
                        <tr>
                            <th class="w-10  text-center vertical-align-middle">Điều</th>
                            <th class="w-90  text-center vertical-align-middle no-padding">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="w-50 no-border full  h-0 ">
                                                <?= $dw ?>
                                            </td>
                                            <td class="w-10 h-0 no-border full  h-0">
                                                Sv tự chấm
                                                <br>
                                                Tự chấm
                                            </td>
                                            <td class="w-10 h-0 no-border full  h-0">
                                                Lớp trưởng <br />
                                                Bí thư
                                            </td>
                                            <td class="w-10 h-0 no-border full  h-0">
                                                BCH <br /> đoàn khoa
                                            </td>
                                            <td class="w-10 h-0 no-border full  h-0">
                                                Cố vấn học tập
                                            </td>
                                            <td class="w-10 h-0 no-border full  h-0">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="w-50 no-border full vertical-align-middle h-0">
                                                <?= $dw ?>
                                                Tổng
                                            </td>
                                            <td class="w-10 no-border full vertical-align-middle">
                                                <input type="number" class="form-control font-weight-bold  bg-success"
                                                    <?= $dotchamdiem__Get_By_Id->trang_thai == 0 ? 'readonly' : '' ?>
                                                    <?= $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1 ? 'readonly' : '' ?>
                                                    placeholder="0" id="sum_sv" min="0" pattern="[-+]?[0-9]{1-2}"
                                                    title="max is 100" max="100" readonly required>
                                            </td>
                                            <td class="w-10 no-border full vertical-align-middle">
                                                <input type="number" class="form-control font-weight-bold"
                                                    placeholder="0" id="sum_lt_bt" min="0" pattern="[-+]?[0-9]{1-2}"
                                                    title="max is 100" max="100" readonly required>
                                            </td>
                                            <td class="w-10 no-border full vertical-align-middle">
                                                <input type="number" class="form-control font-weight-bold"
                                                    placeholder="0" id="sum_btdk" min="0" pattern="[-+]?[0-9]{1-2}"
                                                    title="max is 100" max="100" readonly required>
                                            </td>
                                            <td class="w-10 no-border full vertical-align-middle">
                                                <input type="number" class="form-control font-weight-bold"
                                                    placeholder="0" id="sum_gv" min="0" pattern="[-+]?[0-9]{1-2}"
                                                    title="max is 100" max="100" readonly required>
                                            </td>
                                            <td class="w-10 no-border full vertical-align-middle"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </th>
                        </tr>
                        <tr>

                    </footer>

                    </table>
                </div>
            </div>
            <div class="card-footer">
                <label for="" class="text-muted text-crimson">Vui lòng thêm minh chứng trước khi nhấn cập
                    nhật</label>
                <input type="submit" value="Cập nhật" class="btn btn-danger float-right" id="submit"
                    <?= $dotchamdiem__Get_By_Id->trang_thai == 0 ? 'disabled' : '' ?>
                    <?= $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1 ? 'disabled' : '' ?>>
            </div>
        </form>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Thêm các minh chứng</h3>
                    </div>
                    <form id="uploadForm"
                        class=<?= $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1 ? '' : 'dropzone' ?>
                        method="post" enctype="multipart/form-data" action="sinh-vien/upload.php">
                        <input type="hidden" class="form-control" id="id_phieu" name="id_phieu"
                            value="<?= $phieuchamdiem__Get_By_Id_Sinh_Vien->id_phieu ?>">
                        <button <?= $phieuchamdiem__Get_By_Id_Sinh_Vien->trang_thai != 1 ? 'disabled' : '' ?>
                            <?= $dotchamdiem__Get_By_Id->trang_thai == 0 ? '' : 'disabled' ?> type="submit"
                            class="btn btn-success" id="btn_upload">Tải lên minh
                            chứng!</button>
                    </form>
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
                            <div class="filter-container p-0 row">
                                <?php foreach ($minhchung->minhchung__Get_By_Id_Phieu($phieuchamdiem__Get_By_Id_Sinh_Vien->id_phieu) as $item) : ?>
                                <div class="card">
                                    <div class="filtr-item no-flexed " data-category="1">
                                        <a href="sinh-vien/image.php?id_minh_chung=<?= $item->id_minh_chung ?>"
                                            data-toggle="lightbox">
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

})
</script>


<?php endif ?>
