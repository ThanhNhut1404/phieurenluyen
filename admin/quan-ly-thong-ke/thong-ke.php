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
$id_lop_hoc = $lophoc__Get_Last ? $lophoc__Get_Last->id_lop_hoc : 0;

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
}

foreach($ketquaxeploai__Get_By_Id_Dot as $item){
    $labels_1[] = "% ". $item->xep_loai;
    $labels_2[] = $item->xep_loai;
    $data_1[] = isset($item->sum_so_luong) ? $item->sum_so_luong : 0;
    // Nhựt sửa lỗi: Không chia cho 0 khi chưa có dữ liệu kết quả xếp loại.
    $data_2[] = $ketquaxeploai__Get_By_Id_Dot_All > 0 ? (isset($item->sum_so_luong) ? $item->sum_so_luong : 0) / $ketquaxeploai__Get_By_Id_Dot_All * 100 : 0;
}



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
        </div>
        <div class="row">
            <div class="col">
                <label for="">Chọn đợt (<?=count($dotchamdiem->dotchamdiem__Get_All() )?>)</label>

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
                <label for="">Chọn lớp (<?=count($lophoc__Get_All)?>)</label>
                <select class="form-control" name="id_lop_hoc" required onchange="location.href=this.value">
                    <option value="">Chọn lớp</option>
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
        <!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">

                    <!-- DONUT CHART -->
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">Tỷ lệ xếp loại</h3>
                            <!-- 
                              <div class="card-tools">
                                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                      <i class="fas fa-minus"></i>
                                  </button>
                                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                                      <i class="fas fa-times"></i>
                                  </button>
                              </div> -->
                        </div>
                        <div class="card-body">
                            <canvas id="donutChart"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                </div>
                <!-- /.col (LEFT) -->
                <div class="col-md-6">
                    <!-- BAR CHART -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Số lượng</h3>

                            <!-- <div class="card-tools">
                                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                      <i class="fas fa-minus"></i>
                                  </button>
                                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                                      <i class="fas fa-times"></i>
                                  </button>
                              </div> -->
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

                </div>
                <!-- /.col (RIGHT) -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Danh sách Kết quả thống kê</h3>
                <div class="card-tools">
                    <form action="./quan-ly-thong-ke/action.php?req=export" method="post">
                        <input type="hidden" name="id_lop_hoc" value="<?=$id_lop_hoc?>">
                        <input type="hidden" name="id_dot" value="<?=$id_dot?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-print"></i> EXPORT
                        </button>
                    </form>
                </div>
            </div>
            <!-- /.card-header -->


        </div>
        <!-- /.card-body -->
        <div class="table responsive w-100">
            <table id="tablejs" class="table table-bordered table-striped display responsive nowrap" width="100%">

                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã sinh viên</th>
                        <th>Tên sinh viên</th>
                        <th>Tên lớp</th>
                        <th>Điểm rèn luyện</th>
                        <th>Kết quả xếp loại</th>
                        <th>Ngày xếp loại</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $num = 0;?>
                    <?php $sum_1 = 0;?>
                    <?php $sum_2 = 0;?>
                    <?php $sum_3 = 0;?>
                    <?php $sum_4 = 0;?>
                    <?php $sum_5 = 0;?>
                    <?php $sum_6 = 0;?>
                    <?php foreach($ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot as $item):?>
                    <tr>
                        <?php 

                                $item->xep_loai == "Xuất sắc" ? $sum_1++ : "";
                                $item->xep_loai == "Giỏi" ? $sum_2++ : "";
                                $item->xep_loai == "Khá" ? $sum_3++ : "";
                                $item->xep_loai == "Trung bình" ? $sum_4++ : "";
                                $item->xep_loai == "Yếu" ? $sum_5++ : "";
                                $item->xep_loai == "Kém" ? $sum_6++ : "";
                            
                            ?>
                        <td><?=++$num?></td>
                        <td><?=$item->ma_sinh_vien?></td>
                        <td><?=$item->ten_sinh_vien?></td>
                        <td><?=$item->ten_lop_hoc?></td>
                        <td><?=$item->ket_qua?></td>
                        <td><?=$item->xep_loai?></td>
                        <td><?=$item->ngay_xep_loai?></td>
                        <td><?=$item->ghi_chu?></td>

                    </tr>
                    <?php endforeach?>
                </tbody>
                <tfoot>
                    <b>
                        Xuất sắc: <?=$sum_1?> | Giỏi: <?=$sum_2?> | Khá: <?=$sum_3?> | Trung bình: <?=$sum_4?>
                        | Yếu: <?=$sum_5?> | Kém: <?=$sum_6?>
                    </b>
                </tfoot>
            </table>
        </div>
        <!-- /.content-wrapper -->
    </section>


    <script src="../assets/vendor/jquery/jquery.min.js"></script>
    <!-- ChartJS -->
    <script src="../assets/theme/plugins/chart.js/Chart.min.js"></script>
    <script>
    window.addEventListener('load', function() {

        $('#tablejs').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            // Nhựt sửa: Cấu hình dropdown "Xuất dữ liệu" đầy đủ và đổi tên nút Column visibility thành Ẩn/Hiện cột
            buttons: [{
                    extend: "collection",
                    text: "<i class='fas fa-file-export'></i> Xuất dữ liệu",
                    className: "btn btn-sm btn-primary",
                    align: "button-right",
                    buttons: [
                        {
                            extend: "copy",
                            text: "<i class='far fa-copy'></i> Copy",
                            exportOptions: {
                                columns: ":visible"
                            }
                        },
                        {
                            extend: "csv",
                            text: "<i class='fas fa-file-csv'></i> CSV",
                            bom: true,
                            exportOptions: {
                                columns: ":visible"
                            }
                        },
                        {
                            extend: "excel",
                            text: "<i class='far fa-file-excel'></i> Excel",
                            exportOptions: {
                                columns: ":visible"
                            }
                        },
                        {
                            extend: "pdf",
                            text: "<i class='far fa-file-pdf'></i> PDF",
                            exportOptions: {
                                columns: ":visible"
                            }
                        },
                        {
                            extend: "print",
                            text: "<i class='fas fa-print'></i> In",
                            exportOptions: {
                                columns: ":visible"
                            }
                        }
                    ]
                },
                {
                    extend: 'colvis',
                    text: 'Ẩn/Hiện cột'
                }
            ],
            columnDefs: [{
                targets: -1,
                visible: false
            }]
        }).buttons().container().appendTo('#tablejs_wrapper .col-md-6:eq(0)');
    })
    // //- DONUT CHART -
    // //-------------
    // // Get context with jQuery - using jQuery's .get() method.
    var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
    var donutData = {
        labels: <?=json_encode($labels_2)?>,
        datasets: [{
            label: "Tỷ lệ",
            data: <?=json_encode($data_1)?>,
            backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
        }]
    }
    var donutOptions = {
        maintainAspectRatio: false,
        responsive: true,
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    new Chart(donutChartCanvas, {
        type: 'doughnut',
        data: donutData,
        options: donutOptions
    })

    //-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas = $('#barChart').get(0).getContext('2d')
    var barData = {
        labels: <?=json_encode($labels_1)?>,
        datasets: [{
            label: "Số lượng",
            data: <?=json_encode($data_1)?>,
            backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
        }]
    }
    var barOptions = {
        maintainAspectRatio: false,
        responsive: true,
    }
    new Chart(barChartCanvas, {
        type: 'bar',
        data: barData,
        options: barOptions
    })
    </script>
