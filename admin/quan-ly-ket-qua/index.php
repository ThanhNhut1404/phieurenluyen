 <?php
    // require "../models/getModel.php";
    // Nhựt sửa lỗi: Khởi tạo csrf_token tránh lỗi Undefined index.
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    $id_dot = -2;
    $id_lop_hoc  = -2;
    $ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot = $ketquaxeploai->ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot($id_lop_hoc, $id_dot);
    $dotchamdiem__Get_All = $dotchamdiem->dotchamdiem__Get_All();
    $lophoc__Get_All = $lophoc->lophoc__Get_All();

  
    if(isset($_GET['id_dot'])){
        $id_dot = $_GET['id_dot'];
        // Nhựt sửa lỗi: Chỉ hiển thị Lớp áp dụng thuộc Đợt đã chọn, không hiển thị toàn bộ lớp.
        $lophoc__Get_All = $lopapdung->lopapdung__Get_Lop_Hoc_By_Id_Dot($id_dot);
    }
    if(isset($_GET['id_lop_hoc'])){
        $id_lop_hoc = $_GET['id_lop_hoc'];
        $ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot = $ketquaxeploai->ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot($id_lop_hoc, $id_dot);
    }
  
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header pb-0">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý Kết quả xếp loại</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý Kết quả xếp loại</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <?php 
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $is_open_form = true; 
     ?>

     <section class="content" id="div_add_form">
         <div class="card card-success">
             <div class="card-header">
                     <h3 class="card-title">Xử lý Phiếu chấm điểm</h3>
                     <div class="card-tools">
                         <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                             <i class="fas fa-minus"></i>
                         </button>
                     </div>
                 </div>
                 <div class="card-body">
                     <div class="row">
                         <div class="col-6">
                             <div class="form-group">
                                 <label class="label-sidebar" for="id_dot_select">Chọn đợt (<?=count($dotchamdiem__Get_All)?>) <span class="color-crimson">*</span></label>
                                 <select class="form-control" id="id_dot_select" name="" required onchange="location.href=this.value">
                                     <option value="">Chọn đợt</option>
                                     <?php foreach ($dotchamdiem__Get_All as $item):?>
                                     <option value="?page=quan-ly-ket-qua&id_dot=<?=$item->id_dot?>"
                                         <?=$id_dot == $item->id_dot ? "selected" : ""?>>
                                         <?=$item->ten_dot?>
                                     </option>
                                     <?php endforeach; ?>
                                 </select>
                             </div>
                         </div>

                         <div class="col-6">
                             <?php if(isset($_GET['id_dot'])):?>
                             <div class="form-group">
                                 <label class="label-sidebar" for="id_lop_hoc_select">Chọn lớp học (<?=count($lophoc__Get_All)?>) <span class="color-crimson">*</span></label>
                                 <select class="form-control <?= ($status == 'duplicate-ket-qua') ? 'is-invalid' : '' ?>" id="id_lop_hoc_select" name="" required onchange="location.href=this.value">
                                     <option value="">Chọn lớp học</option>
                                     <?php foreach ($lophoc__Get_All as $item):?>
                                     <option
                                         value="?page=quan-ly-ket-qua&id_dot=<?=$id_dot?>&id_lop_hoc=<?=$item->id_lop_hoc?>"
                                         <?=$id_lop_hoc == $item->id_lop_hoc ? "selected" : ""?>>
                                         <?=$item->ten_lop_hoc?>
                                     </option>
                                     <?php endforeach; ?>
                                 </select>
                                 <?php if ($status == 'duplicate-ket-qua'): ?>
                                     <small class="text-danger mt-1">Kết quả xếp loại cho đợt và lớp này đã được xử lý từ trước.</small>
                                 <?php endif; ?>
                             </div>
                             <?php endif; ?>
                         </div>
                     </div>
                 </div>
                 <!-- /.card-body -->
                 <div class="card-footer py-2">
                     <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" onclick="toggle_add_form()">Hủy</button>
                 </div>
             </div>
     </section>

     <section class="content" id="div_update">
     </section>

     <section class="content">
         <div class="card card-primary">
             <div class="card-header">
                 <h3 class="card-title">Danh sách Kết quả</h3>

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
         <!-- /.card-header -->
         <div id="export-form-container" style="display: none;">
             <?php if ($id_lop_hoc > 0 && $id_dot > 0): ?>
             <form action="./quan-ly-thong-ke/action.php?req=export" method="post" id="form-export-btn">
                 <input type="hidden" name="id_lop_hoc" value="<?=$id_lop_hoc?>">
                 <input type="hidden" name="id_dot" value="<?=$id_dot?>">
             </form>
             <?php endif; ?>
         </div>
         <div class="card-body">
             <table id="tablejs" class="table table-bordered table-striped display responsive nowrap" width="100%">
                  <thead>
                      <tr>
                          <th style="width: 5%">STT</th>
                          <!-- Nhựt sửa: Đổi tên cột từ Mã sinh viên thành MSSV -->
                          <th style="width: 10%">MSSV</th>
                          <!-- Nhựt sửa: Đổi tên cột từ Tên sinh viên thành HỌ VÀ TÊN -->
                          <th style="width: 20%">HỌ VÀ TÊN</th>
                          <th style="width: 10%">Tên lớp</th>
                          <th style="width: 12%">Điểm rèn luyện</th>
                          <th style="width: 13%">Kết quả xếp loại</th>
                          <th style="width: 12%">Ngày xếp loại</th>
                          <th style="width: 18%">Ghi chú</th>
                      </tr>
                  </thead>
                 <tbody>
                     <?php $num = 0;?>
                     <?php foreach($ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot as $item):?>
                      <!-- Nhựt sửa lỗi: Truyền thêm csrf_token qua GET request để bảo mật hành động hạ bậc. -->
                     <tr>
                         <td><?=++$num?></td>
                         <td><?=htmlspecialchars($item->ma_sinh_vien ?? "", ENT_QUOTES, 'UTF-8')?></td>
                         <td><?=htmlspecialchars($item->ten_sinh_vien ?? "", ENT_QUOTES, 'UTF-8')?></td>
                         <td><?=htmlspecialchars($item->ten_lop_hoc ?? "", ENT_QUOTES, 'UTF-8')?></td>
                         <td><?=htmlspecialchars($item->ket_qua ?? "", ENT_QUOTES, 'UTF-8')?></td>
                         <td><?=htmlspecialchars($item->xep_loai ?? "", ENT_QUOTES, 'UTF-8')?></td>
                         <td><?=htmlspecialchars($item->ngay_xep_loai ?? "", ENT_QUOTES, 'UTF-8')?></td>
                         <td>
                             <?php
                             $ghi_chu_text = htmlspecialchars($item->ghi_chu ?? "", ENT_QUOTES, 'UTF-8');
                             if (stripos($ghi_chu_text, 'hạ bậc') !== false || stripos($ghi_chu_text, 'Không tự đánh giá') !== false) {
                                 echo '<span class="text-danger" style="font-style: italic; font-weight: normal !important;">' . $ghi_chu_text . '</span>';
                             } else {
                                 echo $ghi_chu_text;
                             }
                             ?>
                         </td>

                     </tr>
                     <?php endforeach?>
                 </tbody>
             </table>
         </div>
         <!-- /.card-body -->
 </div>
 </section>

 </div>


 <!-- /.content-wrapper -->


 <script>
window.addEventListener("load", function() {
    // $("#tablejs").DataTable({
    //     "responsive": true,
    //     "autoWidth": false,
    //     "buttons": ["copy", "csv", "excel", "pdf", "print"],

    // }).buttons().container().appendTo('#tablejs_wrapper .col-md-6:eq(0)');
    $('#tablejs').DataTable({
        "responsive": true,
        "autoWidth": false,
        "dom": "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end align-items-center'B>>rt<'row mt-3 mb-n2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
        "pagingType": "full_numbers",
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "language": {
            "decimal": ",",
            "thousands": ".",
            "emptyTable": "Không có dữ liệu trong bảng",
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ kết quả xếp loại",
            "infoEmpty": "Hiển thị 0 - 0 của 0 kết quả xếp loại",
            "infoFiltered": "(lọc từ _MAX_ kết quả xếp loại)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ kết quả xếp loại",
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
        // Nhựt sửa: Thêm nút xuất dữ liệu EXPORT và nút Công bố
        buttons: [
            <?php if ($id_lop_hoc > 0 && $id_dot > 0 && count($ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot) > 0): ?>
            <?php
            $is_published = false;
            $is_published = isset($ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot[0]->trang_thai_cong_bo) && (int)$ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot[0]->trang_thai_cong_bo === 1;
            ?>
            {
                text: "<?= $is_published ? '<i class=\'fas fa-eye-slash\'></i> Ẩn điểm với SV' : '<i class=\'fas fa-eye\'></i> Cho SV xem điểm' ?>",
                className: "btn btn-sm <?= $is_published ? 'btn-custom-outline-danger' : 'btn-custom-outline-success' ?> mr-1",
                action: function ( e, dt, node, config ) {
                    Swal.fire({
                        title: '<?= $is_published ? '<span style="white-space: nowrap;">Xác nhận Ẩn điểm?</span>' : '<span style="white-space: nowrap;">Xác nhận cho Sinh viên</span><br>xem điểm?' ?>',
                        html: '<?= $is_published ? 'Thao tác này sẽ <b>Ẩn điểm rèn luyện</b><br>của toàn bộ sinh viên lớp này.' : 'Thao tác này sẽ <b>Hiển thị điểm rèn luyện</b><br>cho toàn bộ sinh viên lớp này.' ?>',
                        icon: '<?= $is_published ? 'warning' : 'info' ?>',
                        width: '36rem',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: '<b>Đóng</b>',
                        customClass: {
                            confirmButton: 'btn btn-success font-weight-bold mx-2 px-4 py-2',
                            cancelButton: 'btn btn-cancel-custom font-weight-bold mx-2 px-4 py-2'
                        },
                        buttonsStyling: false,
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown animate__faster'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp animate__faster'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.post('quan-ly-ket-qua/action.php?req=toggle_cong_bo', {
                                id_dot: <?= $id_dot ?>,
                                id_lop_hoc: <?= $id_lop_hoc ?>,
                                trang_thai: <?= $is_published ? 0 : 1 ?>,
                                csrf_token: '<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>'
                            }, function(res) {
                                try {
                                    var data = JSON.parse(res);
                                    if(data.success) {
                                        location.reload();
                                    } else {
                                        Swal.fire('Lỗi!', data.message || 'Có lỗi xảy ra!', 'error');
                                    }
                                } catch(e) {
                                    location.reload();
                                }
                            });
                        }
                    });
                }
            },
            <?php endif; ?>
            {
                text: "<i class='fas fa-file-export'></i> EXPORT",
                className: "btn btn-sm btn-custom-outline mr-1",
                action: function ( e, dt, node, config ) {
                    $('#form-export-btn').submit();
                }
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i>',
                titleAttr: 'Ẩn/Hiện cột',
                className: 'btn btn-sm btn-custom-filter mr-1'
            },
            {
                extend: "collection",
                text: "<i class='fas fa-file-export'></i> Xuất dữ liệu",
                className: "btn btn-sm btn-primary",
                align: "button-right",
                buttons: [
                    { extend: "copy", text: "<i class='far fa-copy'></i> Copy", exportOptions: { columns: ":visible" } },
                    { extend: "csv", text: "<i class='fas fa-file-csv'></i> CSV", bom: true, exportOptions: { columns: ":visible" } },
                    { extend: "excel", text: "<i class='far fa-file-excel'></i> Excel", exportOptions: { columns: ":visible" } },
                    { extend: "pdf", text: "<i class='far fa-file-pdf'></i> PDF", exportOptions: { columns: ":visible" } },
                    { extend: "print", text: "<i class='fas fa-print'></i> In", exportOptions: { columns: ":visible" } }
                ]
            },
            {
                text: "<i class='fas fa-filter'></i>",
                titleAttr: "Bộ lọc",
                className: "btn btn-sm btn-custom-filter ml-1",
                attr: {
                    id: "btn-filter-dropdown"
                }
            }
        ],
        columnDefs: [{
            targets: -1,
            visible: false
        }],
        initComplete: function() {
            var filterHtml = `
            <style>
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
            .dataTables_wrapper .dt-buttons .btn-custom-outline-success {
                background-color: #fff !important;
                border: 1px solid #28a745 !important;
                color: #28a745 !important;
                border-radius: 4px !important;
                padding: 6px 12px !important;
                font-size: 14px !important;
                font-weight: 500 !important;
                box-shadow: none !important;
                transition: all 0.15s ease-in-out !important;
                display: inline-flex !important;
                align-items: center !important;
            }
            .dataTables_wrapper .dt-buttons .btn-custom-outline-success:hover {
                background-color: #28a745 !important;
                border-color: #28a745 !important;
                color: #fff !important;
            }
            .dataTables_wrapper .dt-buttons .btn-custom-outline-danger {
                background-color: #fff !important;
                border: 1px solid #dc3545 !important;
                color: #dc3545 !important;
                border-radius: 4px !important;
                padding: 6px 12px !important;
                font-size: 14px !important;
                font-weight: 500 !important;
                box-shadow: none !important;
                transition: all 0.15s ease-in-out !important;
                display: inline-flex !important;
                align-items: center !important;
            }
            .dataTables_wrapper .dt-buttons .btn-custom-outline-danger:hover {
                background-color: #dc3545 !important;
                border-color: #dc3545 !important;
                color: #fff !important;
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


});

function toggle_add_form() {
    // Đóng form cập nhật nếu đang mở
    $("#div_update").html('');
    
    var collapseBtn = $('#div_add_form [data-card-widget="collapse"]');
    if (collapseBtn.length) {
        collapseBtn.click();
    }
}
 </script>


