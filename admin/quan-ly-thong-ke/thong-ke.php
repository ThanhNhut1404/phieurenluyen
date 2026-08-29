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
            
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Danh sách Kết quả thống kê</h3>
                
                <?php
                $sum_1 = $sum_2 = $sum_3 = $sum_4 = $sum_5 = $sum_6 = 0;
                foreach($ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot as $item) {
                    if ($item->xep_loai == "Xuất sắc") $sum_1++;
                    elseif ($item->xep_loai == "Giỏi") $sum_2++;
                    elseif ($item->xep_loai == "Khá") $sum_3++;
                    elseif ($item->xep_loai == "Trung bình") $sum_4++;
                    elseif ($item->xep_loai == "Yếu") $sum_5++;
                    elseif ($item->xep_loai == "Kém") $sum_6++;
                }
                ?>
                <div class="card-tools d-flex align-items-center">
                    <span class="mr-3 font-weight-bold" style="font-size: 0.9rem;">
                        Xuất sắc: <span class="text-success"><?=$sum_1?></span> | 
                        Giỏi: <span class="text-primary"><?=$sum_2?></span> | 
                        Khá: <span class="text-info"><?=$sum_3?></span> | 
                        Trung bình: <span class="text-warning"><?=$sum_4?></span> | 
                        Yếu: <span class="text-danger"><?=$sum_5?></span> | 
                        Kém: <span class="text-danger"><?=$sum_6?></span>
                    </span>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            
            <div id="export-form-container" style="display: none;">
                <?php if ($id_lop_hoc > 0 && $id_dot > 0): ?>
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
                <div class="table responsive w-100">
                    <table id="tablejs" class="table table-bordered table-striped display responsive nowrap" width="100%">

                <thead>
                    <tr>
                        <th>STT</th>
                        <th>MSSV</th>
                        <th>Họ và tên</th>
                        <th>Tên lớp</th>
                        <th>Điểm rèn luyện</th>
                        <th>Kết quả xếp loại</th>
                        <th>Ngày xếp loại</th>
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
                        <td><?=$item->ket_qua?></td>
                        <td><?=$item->xep_loai?></td>
                        <td><?=$item->ngay_xep_loai?></td>
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
    </style>
    <script src="../assets/vendor/jquery/jquery.min.js"></script>
    <!-- ChartJS -->
    <script src="../assets/theme/plugins/chart.js/Chart.min.js"></script>
    <script>
    window.addEventListener('load', function() {

        $('#tablejs').DataTable({
            "responsive": true,
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
                    "className": "btn btn-sm btn-custom-outline mr-1",
                    "action": function ( e, dt, node, config ) {
                        $('#form-export-btn').submit();
                    }
                },
                {
                    "extend": 'colvis',
                    "text": '<i class="fas fa-columns"></i>',
                    "titleAttr": 'Ẩn/Hiện cột',
                    "className": 'btn btn-sm btn-custom-filter mr-1'
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
            "columnDefs": [{
                "targets": -1,
                "visible": false
            }],
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
                    
                    table.column(3).search(lopVal ? '^' + $.fn.dataTable.util.escapeRegex(lopVal) + '$' : '', true, false)
                         .column(5).search(xepLoaiVal ? '^' + $.fn.dataTable.util.escapeRegex(xepLoaiVal) + '$' : '', true, false)
                         .draw();
                    $('#custom-filter-menu').fadeOut(200);
                });

                $('#btn-cancel-filter').on('click', function() {
                    $('#filter_lop').val('');
                    $('#filter_xep_loai').val('');
                    updateCascadeDropdowns();
                    table.column(3).search('')
                         .column(5).search('')
                         .draw();
                    $('#custom-filter-menu').fadeOut(200);
                });
            }
        });
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
