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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
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
        <link rel="stylesheet" href="../../assets/css/user.css">
        <?php if (isset($phieuchamdiem__Get_By_Id_Sinh_Vien->id_lop_ap_dung)) : ?>
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
                             <?php // Nhựt sửa lỗi: Tránh cảnh báo Warning: Attempt to read property on bool khi chưa tổng kết kết quả xếp loại. ?>
                            <div class="row">
                                <div class="col">
                                    <p class="card-title">Điểm rèn luyện: <?= $ketquaxeploai__Get_By_Id_Phieu ? $ketquaxeploai__Get_By_Id_Phieu->ket_qua : "Chưa tổng kết" ?></p>
                                </div>

                                <div class="col text-right">
                                    <p class="card-title w-100">Ngày xếp loại:
                                        <?= $ketquaxeploai__Get_By_Id_Phieu ? $ketquaxeploai__Get_By_Id_Phieu->ngay_xep_loai : "Chưa tổng kết" ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <p class="card-title w-100">Xếp loại: <?= $ketquaxeploai__Get_By_Id_Phieu ? $ketquaxeploai__Get_By_Id_Phieu->xep_loai : "Chưa tổng kết" ?></p>
                                </div>

                                <div class="col text-right">
                                    <p class="card-title w-100">Ghi chú: <?= $ketquaxeploai__Get_By_Id_Phieu ? $ketquaxeploai__Get_By_Id_Phieu->ghi_chu : "" ?></p>
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
                                                        <td class="w-90 no-border full  h-0 ">
                                                            Nội dung đánh giá
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
                                                    </tr>
                                                    <tr>
                                                        <td class="w-90 no-border full vertical-align-middle">
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
                                                                    <td class="w-90 no-border full  h-0 ">
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
                                                                    <td class="w-60 no-border full vertical-align-middle">
                                                                        - <?= $muc->muc__Get_By_Id($item_3->id_muc)->ten_muc ?>
                                                                    </td>

                                                                    <td class="w-10 no-border full vertical-align-middle">
                                                                        <input type="number" class="form-control kq_sv" name="kq_sv[]" pattern="[-+]?[0-9]{1-2}" title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0" max="<?= $item_2->can_tren ?>" required disabled value="<?= $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_sv)[$i] ?>">
                                                                    </td>
                                                                    <td class="w-10 no-border full vertical-align-middle">
                                                                        <input type="number" class="form-control kq_lt_bt" name="kq_lt_bt[]" pattern="[-+]?[0-9]{1-2}" title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0" max="<?= $item_2->can_tren ?>" required disabled value="<?= isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_lt_bt)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_lt_bt)[$i] : 00 ?>">
                                                                    </td>
                                                                    <td class="w-10 no-border full vertical-align-middle">
                                                                        <input type="number" class="form-control kq_btdk" name="kq_btdk[]" pattern="[-+]?[0-9]{1-2}" title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0" max="<?= $item_2->can_tren ?>" required disabled value="<?= isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_btdk)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_btdk)[$i] : 00 ?>">
                                                                    </td>
                                                                    <td class="w-10 no-border full vertical-align-middle">
                                                                        <input type="number" class="form-control kq_gv" name="kq_gv[]" pattern="[-+]?[0-9]{1-2}" title="max is <?= $item_2->can_tren ?>" placeholder="0" min="0" max="<?= $item_2->can_tren ?>" required disabled value="<?= isset($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_gv)[$i]) ? $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieuchamdiem__Get_By_Id_Sinh_Vien->kq_gv)[$i] : 00 ?>">
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
                                        <td class="w-90 no-border full  h-0 ">
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
                                    </tr>
                                    <tr>
                                        <td class="w-60 no-border full vertical-align-middle h-0">
                                            <?= $dw ?>
                                            Tổng
                                        </td>
                                        <td class="w-10 no-border full vertical-align-middle">
                                            <input type="number" class="form-control font-weight-bold" placeholder="0" id="sum_sv" min="0" pattern="[-+]?[0-9]{1-2}" title="max is 100" max="100" readonly required>
                                        </td>
                                        <td class="w-10 no-border full vertical-align-middle">
                                            <input type="number" class="form-control font-weight-bold" placeholder="0" id="sum_lt_bt" min="0" pattern="[-+]?[0-9]{1-2}" title="max is 100" max="100" readonly required>
                                        </td>
                                        <td class="w-10 no-border full vertical-align-middle">
                                            <input type="number" class="form-control font-weight-bold" placeholder="0" id="sum_btdk" min="0" pattern="[-+]?[0-9]{1-2}" title="max is 100" max="100" readonly required>
                                        </td>
                                        <td class="w-10 no-border full vertical-align-middle">
                                            <input type="number" class="form-control font-weight-bold" placeholder="0" id="sum_gv" min="0" pattern="[-+]?[0-9]{1-2}" title="max is 100" max="100" readonly required>
                                        </td>
                                        </td>
                                </tbody>
                            </table>
                        </th>
                    </tr>
                    <tr>

                </footer>
                </table>
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
                                                        <a href="image.php?id_minh_chung=<?= $item->id_minh_chung ?>" data-toggle="lightbox">
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
            <p class="mb-0"><b>• Quy định:</b></p>
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
