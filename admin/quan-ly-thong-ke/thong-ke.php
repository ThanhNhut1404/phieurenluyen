<?php 

$labels_1=[];
$labels_2=[];
$data_1=[];
$data_2=[];

$dotchamdiem__Get_All = $dotchamdiem->dotchamdiem__Get_All();
$lophoc__Get_All = $lophoc->lophoc__Get_All();
$dotchamdiem__Get_Last = $dotchamdiem->dotchamdiem__Get_Last();
$lophoc__Get_Last = $lophoc->lophoc__Get_Last();

// Nhựt sửa lỗi: Tránh lỗi đọc id_dot/id_lop_hoc trên false khi chưa có Đợt chấm điểm hoặc Lớp học.
$id_dot = $dotchamdiem__Get_Last ? $dotchamdiem__Get_Last->id_dot : 0;
// Đổi mặc định thành -1 để hiển thị Toàn trường khi mới vào trang
$id_lop_hoc = -1;

if(isset($_GET['id_dot'])){
   $id_dot = $_GET['id_dot'];
}
if(isset($_GET['id_lop_hoc'])){
    $id_lop_hoc = $_GET['id_lop_hoc'];

 }
$ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot = array();
$ketquaxeploai__Get_By_Id_Dot = array();
$ketquaxeploai__Get_By_Id_Dot_All = 0;

if ($id_dot > 0 && $id_lop_hoc > 0) {
    $ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot = $ketquaxeploai->ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot($id_lop_hoc, $id_dot);
    $ketquaxeploai__Get_By_Id_Dot = $ketquaxeploai->ketquaxeploai__Get_By_Id_Dot($id_dot, $id_lop_hoc);
    $ketquaxeploai__Get_By_Id_Dot_All = $ketquaxeploai->ketquaxeploai__Get_By_Id_Dot_All($id_dot, $id_lop_hoc)->sum;
} else if ($id_dot > 0 && $id_lop_hoc == -1) {
    $ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot = $ketquaxeploai->ketquaxeploai__Get_By_Id_Dot_For_All_Lop($id_dot);
    $ketquaxeploai__Get_By_Id_Dot = $ketquaxeploai->ketquaxeploai__Get_By_Id_Dot_Summary_For_All_Lop($id_dot);
    $ketquaxeploai__Get_By_Id_Dot_All = $ketquaxeploai->ketquaxeploai__Get_By_Id_Dot_All_For_All_Lop($id_dot)->sum;
}

// Nhựt sửa logic: Chuẩn hóa dữ liệu biểu đồ theo đúng thứ tự (Ordinal) và hiển thị đủ các mức 0
$standard_levels = ["Kém", "Yếu", "Trung bình", "Khá", "Tốt", "Xuất sắc"];
$chart_map = array_fill_keys($standard_levels, 0);

foreach($ketquaxeploai__Get_By_Id_Dot as $item) {
    $xep_loai = trim($item->xep_loai);
    // Chuẩn hóa "Giỏi" thành "Tốt" nếu có trong DB vì điểm rèn luyện dùng "Tốt"
    if ($xep_loai == "Giỏi") $xep_loai = "Tốt";
    
    if (array_key_exists($xep_loai, $chart_map)) {
        $chart_map[$xep_loai] += isset($item->sum_so_luong) ? $item->sum_so_luong : 0;
    } else {
        // Nếu có xếp loại lạ chưa biết, vẫn thêm vào cuối
        $chart_map[$xep_loai] = isset($item->sum_so_luong) ? $item->sum_so_luong : 0;
    }
}

foreach($chart_map as $lvl => $count) {
    $labels_1[] = "% " . $lvl;
    $labels_2[] = $lvl;
    $data_1[] = $count;
    $data_2[] = $ketquaxeploai__Get_By_Id_Dot_All > 0 ? ($count / $ketquaxeploai__Get_By_Id_Dot_All * 100) : 0;
}


// === TÍNH TOÁN DỮ LIỆU TIẾN ĐỘ CHẤM ĐIỂM ===
$tien_do_1 = 0; // Chưa nộp
$tien_do_2 = 0; // Chờ Lớp trưởng duyệt
$tien_do_3 = 0; // Chờ Cố vấn duyệt
$tien_do_4 = 0; // Hoàn thành

$phieu_tien_do = array();
if ($id_dot > 0 && $id_lop_hoc > 0) {
    $phieu_tien_do = $phieuchamdiem->phieuchamdiem__Get_By_Id_Lop_All($id_lop_hoc, $id_dot);
} else if ($id_dot > 0 && $id_lop_hoc == -1) {
    $phieu_tien_do = $phieuchamdiem->phieuchamdiem__Get_By_Id_Dot_For_All_Lop($id_dot);
}

foreach($phieu_tien_do as $phieu) {
    $kq_sv = isset($phieu->kq_sv) ? trim($phieu->kq_sv) : '';
    $kq_lt_bt = isset($phieu->kq_lt_bt) ? trim($phieu->kq_lt_bt) : '';
    $kq_gv = isset($phieu->kq_gv) ? trim($phieu->kq_gv) : '';
    
    if ($kq_sv === '') {
        $tien_do_1++;
    } else if ($kq_sv !== '' && $kq_lt_bt === '') {
        $tien_do_2++;
    } else if ($kq_lt_bt !== '' && $kq_gv === '') {
        $tien_do_3++;
    } else if ($kq_gv !== '') {
        $tien_do_4++;
    }
}
$labels_tiendo = ["Chưa nộp", "Chờ Lớp trưởng", "Chờ Cố vấn", "Đã hoàn thành"];
$data_tiendo = [$tien_do_1, $tien_do_2, $tien_do_3, $tien_do_4];
// ==========================================

// === TÍNH TOÁN DỮ LIỆU PHỔ ĐIỂM (HISTOGRAM) ===
$histogram_buckets = [
    '<50' => 0,
    '50-54' => 0,
    '55-59' => 0,
    '60-64' => 0,
    '65-69' => 0,
    '70-74' => 0,
    '75-79' => 0,
    '80-84' => 0,
    '85-89' => 0,
    '90-94' => 0,
    '95-100' => 0,
];

foreach($ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot as $item) {
    if (isset($item->ket_qua)) {
        $score = floatval($item->ket_qua);
        if ($score < 50) $histogram_buckets['<50']++;
        elseif ($score < 55) $histogram_buckets['50-54']++;
        elseif ($score < 60) $histogram_buckets['55-59']++;
        elseif ($score < 65) $histogram_buckets['60-64']++;
        elseif ($score < 70) $histogram_buckets['65-69']++;
        elseif ($score < 75) $histogram_buckets['70-74']++;
        elseif ($score < 80) $histogram_buckets['75-79']++;
        elseif ($score < 85) $histogram_buckets['80-84']++;
        elseif ($score < 90) $histogram_buckets['85-89']++;
        elseif ($score < 95) $histogram_buckets['90-94']++;
        else $histogram_buckets['95-100']++;
    }
}
$labels_histogram = array_keys($histogram_buckets);
$data_histogram = array_values($histogram_buckets);
// ==========================================

// === TÍNH TOÁN DỮ LIỆU ĐƯỜNG XU HƯỚNG THEO THỜI GIAN ===
if ($id_lop_hoc > 0) {
    $trend_data_raw = $ketquaxeploai->ketquaxeploai__Get_Average_Score_By_Dot($id_lop_hoc);
} else {
    $trend_data_raw = $ketquaxeploai->ketquaxeploai__Get_Average_Score_By_Dot_For_All_Lop();
}

$trend_labels = [];
$trend_data = [];
foreach ($trend_data_raw as $t) {
    // Chỉ lấy tên đợt và học kỳ cho ngắn gọn để vẽ trục X
    $trend_labels[] = $t->ten_dot . ' - ' . $t->ten_hoc_ky . ' (' . $t->ten_nam_hoc . ')';
    $trend_data[] = round($t->avg_score, 2);
}
// ========================================================

?>




<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Thống kê tổng quan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                        <li class="breadcrumb-item active">Thống kê tổng quan</li>
                    </ol>
                </div>
            </div>
        <div class="row">
            <div class="col">
                <label class="label-sidebar" for="">Chọn đợt (<?=count($dotchamdiem->dotchamdiem__Get_All() )?>)</label>

                <select name="" id="" onchange="location.href=this.value" required class="form-control">
                    <option value="">Chọn đợt</option>
                    <?php foreach($dotchamdiem->dotchamdiem__Get_All() as $item):?>
                    <option <?=$item->id_dot == $id_dot ? "selected" : ""?>
                        value="?page=thong-ke&id_dot=<?=$item->id_dot?>&id_lop_hoc=<?=$id_lop_hoc?>">
                        <?=$item->ten_dot." - ".$item->ten_hoc_ky."(".$item->ten_nam_hoc.")"?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col">
                <label class="label-sidebar" for="">Chọn lớp (<?=count($lophoc__Get_All)?>)</label>
                <select class="form-control" name="id_lop_hoc" required onchange="location.href=this.value">
                    <option value="?page=thong-ke&id_dot=<?=$id_dot?>&id_lop_hoc=-1" <?=$id_lop_hoc == -1 ? "selected" : ""?>>-- Toàn trường --</option>
                    <?php foreach ($lophoc__Get_All as $item):?>
                    <option <?=$item->id_lop_hoc == $id_lop_hoc ? "selected" : ""?>
                        value="?page=thong-ke&id_dot=<?=$id_dot?>&id_lop_hoc=<?=$item->id_lop_hoc?>"
                        <?=$id_lop_hoc == $item->id_lop_hoc ? "selected" : ""?>>
                        <?=$item->ten_lop_hoc?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        </div>
        <!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">

                    <!-- DONUT CHART -->
                    <div class="card card-danger card-outline">
                        <div class="card-header" style="background-color: #e9ecef;">
                            <h3 class="card-title" style="color: #001f3f; font-weight: bold;">Tỷ lệ xếp loại</h3>
                            <div class="card-tools">
                                <button type="button" class="btn-chart-download danger" onclick="downloadChart('donutChart', 'Ty_le_xep_loai')" title="Tải hình ảnh biểu đồ">
                                    <i class="fas fa-image"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="donutChart"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                    <!-- PROGRESS BAR CHART -->
                    <div class="card card-primary card-outline">
                        <div class="card-header" style="background-color: #e9ecef;">
                            <h3 class="card-title" style="color: #001f3f; font-weight: bold;">Tiến độ chấm điểm <span style="font-size: 0.9rem; font-weight: normal; color: #001f3f;">(Tổng cộng: <?=array_sum($data_tiendo)?> phiếu)</span></h3>
                            <div class="card-tools">
                                <button type="button" class="btn-chart-download primary" onclick="downloadChart('progressChart', 'Tien_do_cham_diem')" title="Tải hình ảnh biểu đồ">
                                    <i class="fas fa-image"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="progressChart"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                </div>
                <!-- /.col (LEFT) -->
                <div class="col-md-6">
                    <!-- BAR CHART -->
                    <div class="card card-success card-outline">
                        <div class="card-header" style="background-color: #e9ecef;">
                            <h3 class="card-title" style="color: #001f3f; font-weight: bold;">Phân bố số lượng</h3>
                            <div class="card-tools">
                                <button type="button" class="btn-chart-download success" onclick="downloadChart('barChart', 'Phan_bo_so_luong')" title="Tải hình ảnh biểu đồ">
                                    <i class="fas fa-image"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="barChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                    <!-- HISTOGRAM CHART -->
                    <div class="card card-info card-outline">
                        <div class="card-header" style="background-color: #e9ecef;">
                            <h3 class="card-title" style="color: #001f3f; font-weight: bold;">Phổ điểm rèn luyện</h3>
                            <div class="card-tools">
                                <button type="button" class="btn-chart-download info" onclick="downloadChart('histogramChart', 'Pho_diem_ren_luyen')" title="Tải hình ảnh biểu đồ">
                                    <i class="fas fa-image"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="histogramChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                </div>
                <!-- /.col (RIGHT) -->
            </div>
            <!-- /.row -->
            
            <div class="row">
                <div class="col-12">
                    <!-- TREND LINE CHART -->
                    <div class="card card-navy card-outline">
                        <div class="card-header" style="background-color: #e9ecef;">
                            <h3 class="card-title" style="color: #001f3f; font-weight: bold;">Xu hướng điểm rèn luyện qua các Đợt / Học kỳ</h3>
                            <div class="card-tools">
                                <button type="button" class="btn-chart-download navy" onclick="downloadChart('trendChart', 'Xu_huong_diem_ren_luyen')" title="Tải hình ảnh biểu đồ">
                                    <i class="fas fa-image"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="trendChart"
                                    style="min-height: 250px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-navy">
            <div class="card-header">
                <h3 class="card-title" style="color: #ffffff; font-weight: bold;">Danh sách Kết quả thống kê</h3>
                
                <?php
                $sum_1 = $sum_2 = $sum_3 = $sum_4 = $sum_5 = $sum_6 = 0;
                foreach($ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot as $item) {
                    if ($item->xep_loai == "Xuất sắc") $sum_1++;
                    // Sửa lỗi: Hỗ trợ tính cả "Tốt" và "Giỏi" gom chung vào sum_2
                    elseif ($item->xep_loai == "Giỏi" || trim($item->xep_loai) == "Tốt") $sum_2++;
                    elseif ($item->xep_loai == "Khá") $sum_3++;
                    elseif ($item->xep_loai == "Trung bình") $sum_4++;
                    elseif ($item->xep_loai == "Yếu") $sum_5++;
                    elseif ($item->xep_loai == "Kém") $sum_6++;
                }
                ?>
                <div class="card-tools d-flex align-items-center">
                    <span class="mr-3 font-weight-bold" style="font-size: 0.9rem;">
                        Xuất sắc: <span style="color: #28a745;"><?=$sum_1?></span> | 
                        Tốt: <span style="color: #007bff;"><?=$sum_2?></span> | 
                        Khá: <span style="color: #17a2b8;"><?=$sum_3?></span> | 
                        Trung bình: <span style="color: #ffc107;"><?=$sum_4?></span> | 
                        Yếu: <span style="color: #fd7e14;"><?=$sum_5?></span> | 
                        Kém: <span style="color: #dc3545;"><?=$sum_6?></span>
                    </span>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            
            <div id="export-form-container" style="display: none;">
                <?php if ($id_dot > 0): ?>
                <form action="./quan-ly-thong-ke/action.php?req=export" method="post" class="mr-1 mb-0 d-inline-block" id="form-export-btn">
                    <input type="hidden" name="id_lop_hoc" value="<?=$id_lop_hoc?>">
                    <input type="hidden" name="id_dot" value="<?=$id_dot?>">
                    <button type="submit" class="dt-button btn btn-sm btn-primary">
                        <i class="fas fa-print"></i> EXPORT
                    </button>
                </form>
                <?php endif; ?>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="w-100">
                    <table id="tablejs" class="table table-bordered table-striped display" width="100%">

                <thead>
                    <tr>
                        <th>STT</th>
                        <th>MSSV</th>
                        <th>Họ và tên</th>
                        <th>Tên lớp</th>
                        <th class="text-center">Điểm rèn luyện</th>
                        <th class="text-center">Kết quả xếp loại</th>
                        <th class="text-center">Ngày xếp loại</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $num = 0;?>
                    <?php foreach($ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot as $item):?>
                    <tr>
                        <td><?=++$num?></td>
                        <td><?=$item->ma_sinh_vien?></td>
                        <td><?=$item->ten_sinh_vien?></td>
                        <td><?=$item->ten_lop_hoc?></td>
                        <td class="text-center"><?=$item->ket_qua?></td>
                        <td class="text-center"><?=$item->xep_loai?></td>
                        <td class="text-center"><?=date('d/m/Y', strtotime($item->ngay_xep_loai))?></td>
                        <td>
                            <?php
                            $ghi_chu_text = htmlspecialchars($item->ghi_chu ?? "", ENT_QUOTES, 'UTF-8');
                            if (!empty($ghi_chu_text)) {
                                echo '<span class="text-danger" style="font-style: italic; font-weight: normal !important;">' . $ghi_chu_text . '</span>';
                            }
                            ?>
                        </td>

                    </tr>
                    <?php endforeach?>
                </tbody>

            </table>
        </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
</div>
<!-- /.content-wrapper -->

    <style>
    /* Tinh chỉnh riêng cho bảng danh sách thống kê */
    #tablejs {
        font-size: 13px;
    }
    #tablejs th, #tablejs td {
        vertical-align: middle;
    }
    /* Cho phép cột Ghi chú (cột cuối) được xuống dòng */
    #tablejs th:last-child, #tablejs td:last-child {
        min-width: 180px;
    }
    
    .dataTables_wrapper .dt-buttons .btn-custom-filter {
        background-color: #0f2a5a !important;
        border: 1px solid #0f2a5a !important;
        color: #fff !important;
        border-radius: 4px !important;
        padding: 6px 12px !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        box-shadow: none !important;
        transition: all 0.15s ease-in-out !important;
        display: inline-flex !important;
        align-items: center !important;
    }
    .dataTables_wrapper .dt-buttons .btn-custom-filter:hover {
        background-color: transparent !important;
        border-color: #0f2a5a !important;
        color: #0f2a5a !important;
    }
    .dataTables_wrapper .dt-buttons .btn-custom-outline {
        background-color: #fff !important;
        border: 1px solid #0f2a5a !important;
        color: #0f2a5a !important;
        border-radius: 4px !important;
        padding: 6px 12px !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        box-shadow: none !important;
        transition: all 0.15s ease-in-out !important;
        display: inline-flex !important;
        align-items: center !important;
    }
    .dataTables_wrapper .dt-buttons .btn-custom-outline:hover {
        background-color: #0f2a5a !important;
        border-color: #0f2a5a !important;
        color: #fff !important;
    }
    .dataTables_wrapper .dt-buttons .btn-custom-filter.dropdown-toggle::after {
        display: none !important;
    }
    .dataTables_wrapper .dt-buttons .dt-button-down-arrow {
        display: none !important;
    }
    #custom-filter-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        margin-top: 5px;
        width: 320px;
        background: #fff;
        border: 1px solid rgba(0,0,0,.15);
        border-radius: .25rem;
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.175);
        z-index: 1050;
    }
    .btn-chart-download {
        background-color: #fff !important;
        border-radius: 4px !important;
        padding: 4px 10px !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        box-shadow: none !important;
        transition: all 0.15s ease-in-out !important;
        display: inline-flex !important;
        align-items: center !important;
    }
    .btn-chart-download.danger { border: 1px solid #dc3545 !important; color: #dc3545 !important; }
    .btn-chart-download.danger:hover { background-color: rgba(220, 53, 69, 0.1) !important; color: #dc3545 !important; }
    
    .btn-chart-download.primary { border: 1px solid #007bff !important; color: #007bff !important; }
    .btn-chart-download.primary:hover { background-color: rgba(0, 123, 255, 0.1) !important; color: #007bff !important; }
    
    .btn-chart-download.success { border: 1px solid #28a745 !important; color: #28a745 !important; }
    .btn-chart-download.success:hover { background-color: rgba(40, 167, 69, 0.1) !important; color: #28a745 !important; }
    
    .btn-chart-download.info { border: 1px solid #17a2b8 !important; color: #17a2b8 !important; }
    .btn-chart-download.info:hover { background-color: rgba(23, 162, 184, 0.1) !important; color: #17a2b8 !important; }
    
    .btn-chart-download.navy { border: 1px solid #001f3f !important; color: #001f3f !important; }
    .btn-chart-download.navy:hover { background-color: rgba(0, 31, 63, 0.1) !important; color: #001f3f !important; }
    </style>
    <script src="../assets/vendor/jquery/jquery.min.js"></script>
    <!-- ChartJS -->
    <script src="../assets/theme/plugins/chart.js/Chart.min.js"></script>
    <script>
    window.addEventListener('load', function() {

        $('#tablejs').DataTable({
            "responsive": false,
            "autoWidth": false,
            "dom": "<'row'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-6 d-flex justify-content-end align-items-center'B>>rt<'row mt-3 mb-n2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
            "pagingType": "full_numbers",
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "language": {
                "decimal": ",",
                "thousands": ".",
                "emptyTable": "Không có dữ liệu trong bảng",
                "info": "Hiển thị _START_ - _END_ của _TOTAL_ kết quả",
                "infoEmpty": "Hiển thị 0 - 0 của 0 kết quả",
                "infoFiltered": "(lọc từ _MAX_ kết quả)",
                "infoPostFix": "",
                "lengthMenu": "Hiển thị _MENU_ kết quả",
                "loadingRecords": "Đang tải...",
                "processing": "Đang xử lý...",
                "search": "Tìm kiếm:",
                "zeroRecords": "Không tìm thấy kết quả phù hợp",
                "paginate": {
                    "first": "&laquo;",
                    "last": "&raquo;",
                    "next": "&rsaquo;",
                    "previous": "&lsaquo;"
                },
                "aria": {
                    "sortAscending": ": kích hoạt để sắp xếp cột tăng dần",
                    "sortDescending": ": kích hoạt để sắp xếp cột giảm dần"
                }
            },
            // Nhựt sửa: Cấu hình dropdown "Xuất dữ liệu" đầy đủ và đổi tên nút Column visibility thành Ẩn/Hiện cột
            "buttons": [
                {
                    "text": "<i class='fas fa-print'></i> EXPORT",
                    "className": "btn btn-sm btn-custom-filter mr-1",
                    "action": function ( e, dt, node, config ) {
                        $('#form-export-btn').submit();
                    }
                },

                {
                    "extend": "collection",
                    "text": "<i class='fas fa-file-export'></i> Xuất dữ liệu",
                    "className": "btn btn-sm btn-custom-outline",
                    "align": "button-right",
                    "buttons": [
                        {
                            "extend": "copy",
                            "text": "<i class='far fa-copy'></i> Copy",
                            "exportOptions": {
                                "columns": ":visible"
                            }
                        },
                        {
                            "extend": "csv",
                            "text": "<i class='fas fa-file-csv'></i> CSV",
                            "bom": true,
                            "exportOptions": {
                                "columns": ":visible"
                            }
                        },
                        {
                            "extend": "excel",
                            "text": "<i class='far fa-file-excel'></i> Excel",
                            "exportOptions": {
                                "columns": ":visible"
                            }
                        },
                        {
                            "extend": "pdf",
                            "text": "<i class='far fa-file-pdf'></i> PDF",
                            "exportOptions": {
                                "columns": ":visible"
                            }
                        },
                        {
                            "extend": "print",
                            "text": "<i class='fas fa-print'></i> In",
                            "exportOptions": {
                                "columns": ":visible"
                            }
                        }
                    ]
                },
                {
                    "text": "<i class='fas fa-filter'></i>",
                    "titleAttr": "Bộ lọc",
                    "className": "btn btn-sm btn-custom-filter ml-1",
                    "attr": {
                        "id": "btn-filter-dropdown"
                    }
                }
            ],
            "initComplete": function() {
                // Logic bộ lọc
                var filterHtml = `
                <div id="custom-filter-menu" class="p-3 text-left">
                    <div class="form-group mb-2">
                        <label class="label-sidebar">Lớp học:</label>
                        <select id="filter_lop" class="form-control form-control-sm">
                            <option value="">-- Tất cả Lớp --</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="label-sidebar">Kết quả xếp loại:</label>
                        <select id="filter_xep_loai" class="form-control form-control-sm">
                            <option value="">-- Tất cả Kết quả --</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-cancel-custom mr-2 font-weight-bold" id="btn-cancel-filter">Hủy</button>
                        <button type="button" class="btn btn-success font-weight-bold" id="btn-apply-filter">Áp dụng</button>
                    </div>
                </div>`;
                
                var $btn = $('#btn-filter-dropdown');
                $btn.wrap('<div style="position: relative; display: inline-block;"></div>');
                $btn.parent().append(filterHtml);

                var table = $('#tablejs').DataTable();

                function updateCascadeDropdowns() {
                    var selectedLop = $('#filter_lop').val() || "";
                    var selectedXepLoai = $('#filter_xep_loai').val() || "";
                    
                    var lopOptions = [];
                    var xepLoaiOptions = [];
                    
                    table.rows({ search: 'applied' }).every(function() {
                        var data = this.data();
                        var lop = $('<div>').html(data[3]).text().trim();
                        var xepLoai = $('<div>').html(data[5]).text().trim();
                        
                        if (lop !== '' && lopOptions.indexOf(lop) === -1) lopOptions.push(lop);
                        
                        if (selectedLop === "" || lop === selectedLop) {
                            if (xepLoai !== '' && xepLoaiOptions.indexOf(xepLoai) === -1) xepLoaiOptions.push(xepLoai);
                        }
                    });
                    
                    function populate(selectId, options, currentValue, defaultText) {
                        var select = $('#' + selectId);
                        select.empty().append('<option value="">' + defaultText + '</option>');
                        options.sort().forEach(function(opt) {
                            select.append('<option value="'+opt+'">'+opt+'</option>');
                        });
                        if (options.indexOf(currentValue) !== -1) {
                            select.val(currentValue);
                        } else {
                            select.val('');
                        }
                    }
                    
                    populate('filter_lop', lopOptions, selectedLop, '-- Tất cả Lớp --');
                    populate('filter_xep_loai', xepLoaiOptions, selectedXepLoai, '-- Tất cả Kết quả --');
                }

                $('#filter_lop').on('change', function() {
                    $('#filter_xep_loai').val('');
                    updateCascadeDropdowns();
                });

                $('#filter_xep_loai').on('change', function() {
                    updateCascadeDropdowns();
                });

                updateCascadeDropdowns();

                $btn.on('click', function(e) {
                    e.stopPropagation();
                    $('#custom-filter-menu').fadeToggle(200);
                });

                $('#custom-filter-menu').on('click', function(e) {
                    e.stopPropagation();
                });

                $(document).on('click', function() {
                    $('#custom-filter-menu').fadeOut(200);
                });

                $('#btn-apply-filter').on('click', function() {
                    var lopVal = $('#filter_lop').val();
                    var xepLoaiVal = $('#filter_xep_loai').val();
                    
                    // Nhựt sửa: Lưu trạng thái filter vào sessionStorage
                    sessionStorage.setItem('filter_thongke_lop', lopVal);
                    sessionStorage.setItem('filter_thongke_xeploai', xepLoaiVal);
                    
                    table.column(3).search(lopVal ? '^' + $.fn.dataTable.util.escapeRegex(lopVal) + '$' : '', true, false)
                         .column(5).search(xepLoaiVal ? '^' + $.fn.dataTable.util.escapeRegex(xepLoaiVal) + '$' : '', true, false)
                         .draw();
                    $('#custom-filter-menu').fadeOut(200);
                });

                $('#btn-cancel-filter').on('click', function() {
                    $('#filter_lop').val('');
                    $('#filter_xep_loai').val('');
                    
                    // Nhựt sửa: Xóa trạng thái filter khỏi sessionStorage
                    sessionStorage.removeItem('filter_thongke_lop');
                    sessionStorage.removeItem('filter_thongke_xeploai');
                    
                    updateCascadeDropdowns();
                    table.column(3).search('')
                         .column(5).search('')
                         .draw();
                    $('#custom-filter-menu').fadeOut(200);
                });

                // Nhựt sửa: Khôi phục trạng thái filter
                var savedLop = sessionStorage.getItem('filter_thongke_lop');
                var savedXepLoai = sessionStorage.getItem('filter_thongke_xeploai');
                if (savedLop || savedXepLoai) {
                    if (savedLop) $('#filter_lop').val(savedLop);
                    if (savedXepLoai) $('#filter_xep_loai').val(savedXepLoai);
                    
                    table.column(3).search(savedLop ? '^' + $.fn.dataTable.util.escapeRegex(savedLop) + '$' : '', true, false)
                         .column(5).search(savedXepLoai ? '^' + $.fn.dataTable.util.escapeRegex(savedXepLoai) + '$' : '', true, false);
                }
            }
        });
    })
    // Định nghĩa bộ màu chuẩn theo cấp độ (Từ Kém đến Xuất sắc)
    var gradeColors = ['#dc3545', '#fd7e14', '#ffc107', '#17a2b8', '#007bff', '#28a745'];

    // //- DONUT CHART -
    // //-------------
    // // Get context with jQuery - using jQuery's .get() method.
    var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
    var donutData = {
        labels: <?=json_encode($labels_2)?>,
        datasets: [{
            label: "Tỷ lệ",
            data: <?=json_encode($data_1)?>,
            backgroundColor: gradeColors,
        }]
    }
    var donutOptions = {
        maintainAspectRatio: false,
        responsive: true,
        // Đưa bảng chú giải (Legend) xuống dưới cùng
        legend: {
            position: 'bottom'
        },
        // Tùy chỉnh Tooltip để hiện cả Số lượng và %
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    var label = data.labels[tooltipItem.index] || '';
                    var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                    var dataset = data.datasets[tooltipItem.datasetIndex];
                    var total = dataset.data.reduce(function(acc, current) {
                        return acc + current;
                    }, 0);
                    var percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                    return label + ': ' + value + ' Sinh viên (' + percentage + '%)';
                }
            }
        },
        hover: {
            animationDuration: 0 // Ngăn giật text khi hover
        }
    }

    // Plugin cục bộ để vẽ Text lên biểu đồ Donut
    var donutPlugins = [{
        afterDraw: function(chart) {
            if (chart.config.type !== 'doughnut') return;
            var width = chart.chart.width;
            var height = chart.chart.height;
            var ctx = chart.chart.ctx;

            // Tính tổng
            var total = 0;
            chart.data.datasets[0].data.forEach(function(value) {
                total += value;
            });

            ctx.restore();
            
            var meta = chart.getDatasetMeta(0);
            if (!meta.data || meta.data.length === 0) return;
            
            // Lấy tọa độ tâm chính xác của vòng tròn Donut
            var centerX = meta.data[0]._model.x;
            var centerY = meta.data[0]._model.y;

            // 1. Vẽ chữ Tổng số ở giữa lõi
            if (total > 0) {
                var fontSize = (height / 180).toFixed(2);
                ctx.font = "bold " + fontSize + "em sans-serif";
                ctx.textAlign = "center"; // Căn giữa theo trục X
                ctx.textBaseline = "middle"; // Căn giữa theo trục Y
                ctx.fillStyle = "#444";

                var text = "Tổng: " + total;
                ctx.fillText(text, centerX, centerY);
            }

            // 2. Vẽ phần trăm lên lát cắt
            var dataset = chart.data.datasets[0];
            
            ctx.font = "bold 13px Arial";
            ctx.fillStyle = "#fff";
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";

            meta.data.forEach(function(arc, index) {
                var value = dataset.data[index];
                if (value > 0) {
                    var percentage = Math.round((value / total) * 100) + "%";
                    var view = arc._view;
                    // Tính tọa độ trung tâm của lát cắt
                    var angle = view.startAngle + (view.endAngle - view.startAngle) / 2;
                    var radius = view.innerRadius + (view.outerRadius - view.innerRadius) / 2;
                    var x = view.x + Math.cos(angle) * radius;
                    var y = view.y + Math.sin(angle) * radius;

                    // Viền đen mờ cho chữ dễ đọc trên nền sáng
                    ctx.shadowColor = "rgba(0,0,0,0.6)";
                    ctx.shadowBlur = 3;
                    ctx.fillText(percentage, x, y);
                    ctx.shadowBlur = 0;
                }
            });
            ctx.save();
        }
    }];

    //Create pie or douhnut chart
    new Chart(donutChartCanvas, {
        type: 'doughnut',
        data: donutData,
        options: donutOptions,
        plugins: donutPlugins
    })

    //-------------
    //- PROGRESS CHART -
    //-------------
    var progressChartCanvas = $('#progressChart').get(0).getContext('2d');
    var progressData = {
        labels: <?=json_encode($labels_tiendo)?>,
        datasets: [
            {
                label: 'Số lượng sinh viên',
                // Màu sắc tương ứng: Đỏ (Chưa nộp), Cam (Chờ LT), Xanh nhạt (Chờ CV), Xanh đậm (Hoàn thành)
                backgroundColor: ['#dc3545', '#fd7e14', '#17a2b8', '#28a745'],
                data: <?=json_encode($data_tiendo)?>
            }
        ]
    };

    var progressOptions = {
        maintainAspectRatio: false,
        responsive: true,
        legend: {
            display: false
        },
        layout: {
            padding: {
                right: 30 // Thêm khoảng trống bên phải để số liệu không bị cắt
            }
        },
        scales: {
            xAxes: [{
                ticks: {
                    beginAtZero: true,
                    precision: 0 // Chỉ hiển thị số nguyên
                }
            }],
            yAxes: [{
                ticks: {
                    fontStyle: 'bold'
                }
            }]
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    var count = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                    var total = data.datasets[tooltipItem.datasetIndex].data.reduce((a, b) => a + b, 0);
                    var percentage = total > 0 ? ((count / total) * 100).toFixed(1) + '%' : '0%';
                    return ' ' + count + ' sinh viên (' + percentage + ')';
                }
            }
        },
        hover: {
            animationDuration: 0
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(13, 'bold', Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'left';
                ctx.textBaseline = 'middle';
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        // Lấy màu nền của thanh hiện tại
                        var barColor = dataset.backgroundColor[index];
                        ctx.fillStyle = barColor; // Chữ cùng màu với tên cột
                        
                        // Nếu data = 0 thì x = tọa độ bắt đầu của trục hoành (thường là left của chartArea)
                        var xPos = data > 0 ? bar._model.x + 5 : chartInstance.chartArea.left + 5;
                        
                        // Vẽ chữ số (vẽ cả số 0)
                        ctx.fillText(' ' + data, xPos, bar._model.y);
                    });
                });
            }
        }
    };

    new Chart(progressChartCanvas, {
        type: 'horizontalBar',
        data: progressData,
        options: progressOptions
    });

    //-------------
    //- COMBO CHART (BAR + LINE) -
    //-------------
    var barChartCanvas = $('#barChart').get(0).getContext('2d')
    var barData = {
        labels: <?=json_encode($labels_2)?>, // Không dùng nhãn có %
        datasets: [
            {
                type: 'line',
                label: "Đường xu hướng",
                data: <?=json_encode($data_1)?>,
                borderColor: 'rgba(60, 141, 188, 0.7)', // Màu của đường nối nhạt hơn một chút
                backgroundColor: 'transparent',
                pointBackgroundColor: 'rgba(60, 141, 188, 0.7)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(60, 141, 188, 0.7)',
                borderWidth: 2,
                tension: 0.4, // Độ cong
                fill: false // Không đổ màu vùng
            },
            {
                type: 'bar',
                label: "Số lượng",
                data: <?=json_encode($data_1)?>,
                // Dùng chung bộ màu với biểu đồ Tròn để đồng bộ chú thích
                backgroundColor: gradeColors,
            }
        ]
    }
    var barOptions = {
        maintainAspectRatio: false,
        responsive: true,
        layout: {
            padding: {
                top: 20 // Tạo không gian phía trên để không bị cắt chữ số lượng
            }
        },
        legend: {
            display: false // Ẩn chú thích của tập dữ liệu vì nó chỉ lấy màu đầu tiên, người dùng có thể xem chú thích ở biểu đồ Tròn
        },
        // Tùy chỉnh Tooltip khi rê chuột
        tooltips: {
            mode: 'index',
            intersect: false,
            callbacks: {
                label: function(tooltipItem, data) {
                    var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                    return datasetLabel + ': ' + tooltipItem.yLabel + ' Sinh viên';
                }
            }
        },
        // Vẽ số lượng trực tiếp lên đỉnh cột
        hover: {
            animationDuration: 0 // Chống giật lag label khi hover
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(12, 'bold', Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                this.data.datasets.forEach(function (dataset, i) {
                    // Chỉ vẽ số cho biểu đồ Cột (bar)
                    if (dataset.type === 'bar') {
                        var meta = chartInstance.controller.getDatasetMeta(i);
                        meta.data.forEach(function (bar, index) {
                            var data = dataset.data[index];
                            if (data > 0) { // Chỉ hiển thị nếu số lượng > 0
                                ctx.fillStyle = dataset.backgroundColor[index]; // Lấy màu tương ứng của cột
                                ctx.fillText(data, bar._model.x, bar._model.y - 5);
                            }
                        });
                    }
                });
            }
        },
        scales: {
            xAxes: [{
                scaleLabel: {
                    display: true,
                    labelString: 'Mức xếp loại',
                    fontStyle: 'bold'
                }
            }],
            yAxes: [{
                scaleLabel: {
                    display: true,
                    labelString: 'Số lượng (Sinh viên)',
                    fontStyle: 'bold'
                },
                ticks: {
                    beginAtZero: true, // Luôn bắt đầu từ 0
                    stepSize: 1 // Chỉ hiển thị số nguyên (1, 2, 3...)
                }
            }]
        }
    }
    new Chart(barChartCanvas, {
        type: 'bar', // Dùng biểu đồ cột để dễ phân màu
        data: barData,
        options: barOptions
    })

    //-------------
    //- HISTOGRAM CHART -
    //-------------
    var histogramCanvas = $('#histogramChart').get(0).getContext('2d');
    var histogramData = {
        labels: <?=json_encode($labels_histogram)?>,
        datasets: [
            {
                label: 'Số lượng sinh viên',
                data: <?=json_encode($data_histogram)?>,
                backgroundColor: 'rgba(23, 162, 184, 0.7)', // Màu xanh lơ (info)
                borderColor: 'rgba(23, 162, 184, 1)',
                borderWidth: 1
            }
        ]
    };
    var histogramOptions = {
        maintainAspectRatio: false,
        responsive: true,
        legend: {
            display: false
        },
        layout: {
            padding: {
                top: 20
            }
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    return tooltipItem.yLabel + ' Sinh viên';
                }
            }
        },
        hover: {
            animationDuration: 0 // Ngăn chặn chớp/giật text khi hover
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(12, 'bold', Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                ctx.fillStyle = '#666';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        if (data > 0) {
                            ctx.fillText(data, bar._model.x, bar._model.y - 5);
                        }
                    });
                });
            }
        },
        scales: {
            xAxes: [{
                scaleLabel: {
                    display: true,
                    labelString: 'Khoảng điểm',
                    fontStyle: 'bold'
                },
                // Để cột dính sát vào nhau giống Histogram thực tế
                categoryPercentage: 1.0,
                barPercentage: 0.95
            }],
            yAxes: [{
                scaleLabel: {
                    display: true,
                    labelString: 'Số lượng (Sinh viên)',
                    fontStyle: 'bold'
                },
                ticks: {
                    beginAtZero: true,
                    stepSize: 1
                }
            }]
        }
    };

    new Chart(histogramCanvas, {
        type: 'bar',
        data: histogramData,
        options: histogramOptions
    });

    //-------------
    //- TREND LINE CHART (TIME SERIES) -
    //-------------
    var trendCanvas = $('#trendChart').get(0).getContext('2d');
    var trendData = {
        labels: <?=json_encode($trend_labels)?>,
        datasets: [
            {
                label: 'Điểm RL trung bình',
                data: <?=json_encode($trend_data)?>,
                backgroundColor: 'rgba(0, 31, 63, 0.1)', // Màu navy trong suốt
                borderColor: '#001f3f',
                pointBackgroundColor: '#001f3f',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#001f3f',
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }
        ]
    };
    var trendOptions = {
        maintainAspectRatio: false,
        responsive: true,
        legend: {
            display: false
        },
        layout: {
            padding: {
                top: 20
            }
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    return 'Điểm trung bình: ' + tooltipItem.yLabel;
                }
            }
        },
        hover: {
            animationDuration: 0 
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(12, 'bold', Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                ctx.fillStyle = '#001f3f';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (point, index) {
                        var data = dataset.data[index];
                        if (data > 0) {
                            ctx.fillText(data, point._model.x, point._model.y - 8);
                        }
                    });
                });
            }
        },
        scales: {
            xAxes: [{
                scaleLabel: {
                    display: true,
                    labelString: 'Dòng thời gian (Đợt/Học kỳ)',
                    fontStyle: 'bold'
                },
                gridLines: {
                    display: false
                }
            }],
            yAxes: [{
                scaleLabel: {
                    display: true,
                    labelString: 'Điểm RL Trung bình',
                    fontStyle: 'bold'
                },
                ticks: {
                    beginAtZero: true,
                    max: 100
                }
            }]
        }
    };

    new Chart(trendCanvas, {
        type: 'line',
        data: trendData,
        options: trendOptions
    });
    function downloadChart(canvasId, fileName) {
        var canvas = document.getElementById(canvasId);
        if(!canvas) return;
        
        // Create a new canvas to ensure white background (default is transparent)
        var newCanvas = document.createElement('canvas');
        newCanvas.width = canvas.width;
        newCanvas.height = canvas.height;
        var ctx = newCanvas.getContext('2d');
        
        // Fill white background
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, newCanvas.width, newCanvas.height);
        
        // Draw original canvas on top
        ctx.drawImage(canvas, 0, 0);
        
        // Create link and trigger download
        var link = document.createElement('a');
        link.href = newCanvas.toDataURL('image/png');
        link.download = fileName + '.png';
        link.click();
    }
    </script>
