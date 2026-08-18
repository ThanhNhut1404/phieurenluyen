 <?php
    // require "../models/getModel.php";
    $phancong__Get_All = $phancong->phancong__Get_All();
    $lophoc__Get_All = $lophoc->lophoc__Get_All();
    $giangvien__Get_All = $giangvien->giangvien__Get_All();
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_GET['status']) || $_GET['status'] === 'success') {
        unset($_SESSION['phancong_old_input']);
    }
    $phancong_old_input = $_SESSION['phancong_old_input'] ?? [];
    
    function phancong_old_value($key, $context, $default = '') {
        global $phancong_old_input;
        if (isset($phancong_old_input['context']) && $phancong_old_input['context'] === $context && isset($phancong_old_input[$key])) {
            return $phancong_old_input[$key];
        }
        return $default;
    }
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header pb-0">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý Phân công</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý phân công</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <?php $is_add_error = isset($phancong_old_input['context']) && $phancong_old_input['context'] === 'add'; ?>

     <section class="content mb-2">
         <button type="button" class="btn <?= $is_add_error ? 'btn-cancel-custom' : 'btn-success' ?> font-weight-bold" id="btn-toggle-add" onclick="toggle_add_form()">
             <?php if ($is_add_error): ?>
                 <i class="fas fa-times"></i>
             <?php else: ?>
                 <i class="fas fa-plus"></i> Thêm mới
             <?php endif; ?>
         </button>
     </section>

     <section class="content" id="div_add_form" style="<?= $is_add_error ? '' : 'display: none;' ?>">
         <form class="row form" action="quan-ly-phan-cong/action.php?req=add" method="post"
             enctype="multipart/form-data">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title">Thêm mới Phân công</h3>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Giảng viên <span class="color-crimson">*</span></label>
                                     <select class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'duplicate-phancong') ? 'is-invalid' : '' ?>" name="id_giang_vien" required>
                                         <option value="">Chọn Giảng viên</option>
                                         <?php foreach ($giangvien__Get_All as $item):?>
                                         <option value="<?=$item->id_giang_vien?>" <?= phancong_old_value('id_giang_vien', 'add') == $item->id_giang_vien ? 'selected' : '' ?>><?=$item->ten_giang_vien?></option>
                                         <?php endforeach; ?>
                                     </select>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Lớp học <span class="color-crimson">*</span></label>
                                     <select class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'duplicate-phancong') ? 'is-invalid' : '' ?>" name="id_lop_hoc" required>
                                         <option value="">Chọn Lớp học</option>
                                         <?php foreach ($lophoc__Get_All as $item):?>
                                         <option value="<?=$item->id_lop_hoc?>" <?= phancong_old_value('id_lop_hoc', 'add') == $item->id_lop_hoc ? 'selected' : '' ?>><?=$item->ten_lop_hoc?></option>
                                         <?php endforeach; ?>
                                     </select>
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'duplicate-phancong'): ?>
                                         <small class="text-danger mt-1">Giảng viên đã được phân công lớp học này.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                         </div>
                         <div class="form-group">
                             <label class="label-sidebar" for="">Ghi chú</label>
                             <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                                 placeholder="Nhập ghi chú"><?= htmlspecialchars(phancong_old_value('ghi_chu', 'add')) ?></textarea>
                         </div>
                     </div>
                     <!-- /.card-body -->
                     <div class="card-footer py-2">
                         <input type="submit" value="Thêm mới" class="btn btn-success float-right font-weight-bold" style="font-weight: bold;">
                         <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" style="font-weight: bold;" onclick="toggle_add_form()">Hủy</button>
                     </div>
                 </div>
                 <!-- /.card -->
             </div>
         </form>
     </section>

     <section class="content" id="div_update">
     </section>

     <section class="content">
         <div class="card card-primary">
             <div class="card-header">
                 <h3 class="card-title">Danh sách Phân công</h3>
                 <div class="card-tools">
                     <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                         <i class="fas fa-minus"></i>
                     </button>
                 </div>
             </div>
             <!-- /.card-header -->
             <div class="card-body">
                 <table id="tablejs" class="table table-bordered table-striped display responsive nowrap" width="100%">
                     <thead>
                         <tr>
                             <th>STT</th>
                             <th>Giảng viên</th>
                             <th>Lớp học</th>
                             <th>Ghi chú</th>
                             <th>Thao tác</th>
                         </tr>
                     </thead>
                      <tbody>
                          <?php 
                          $arr_giang_vien = [];
                          foreach ($giangvien__Get_All as $gv) {
                              $arr_giang_vien[$gv->id_giang_vien] = $gv->ten_giang_vien;
                          }
                          $arr_lop_hoc = [];
                          foreach ($lophoc__Get_All as $lh) {
                              $arr_lop_hoc[$lh->id_lop_hoc] = $lh->ten_lop_hoc;
                          }
                          $num = 0;
                          ?>
                          <?php foreach($phancong__Get_All as $item):?>
                          <tr>
                              <td><?=++$num?></td>
                              <td><?= isset($arr_giang_vien[$item->id_giang_vien]) ? htmlspecialchars($arr_giang_vien[$item->id_giang_vien]) : '' ?></td>
                              <td class="text-center" style="text-align: center !important;"><?= isset($arr_lop_hoc[$item->id_lop_hoc]) ? htmlspecialchars($arr_lop_hoc[$item->id_lop_hoc]) : '' ?></td>
                              <td><?= htmlspecialchars($item->ghi_chu ?? '') ?></td>
                             <td>
                                 <a href="javascript:void(0)" class="btn btn-warning m-2" onclick="return update_obj(<?=(int)$item->id_phan_cong?>)">
                                     <i class="ri-edit-2-line"></i>
                                 </a>
                                 <a href="#" type="button" class="btn  btn-danger m-2"
                                     onclick="return confirm_delete_sweet('quan-ly-phan-cong/action.php?req=delete&id_phan_cong=<?=$item->id_phan_cong?>', 'Phân công')">
                                     <i class="ri-delete-bin-line"></i>
                                 </a>
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
    $("#tablejs").DataTable({
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
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ phân công cố vấn",
            "infoEmpty": "Hiển thị 0 - 0 của 0 phân công cố vấn",
            "infoFiltered": "(lọc từ _MAX_ phân công cố vấn)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ phân công cố vấn",
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
        "columnDefs": [{
            "targets": -1,
            "orderable": false,
            "searchable": false
        }],
        "buttons": [{
            "extend": "collection",
            "text": "<i class='fas fa-file-export'></i> Xuất dữ liệu",
            "className": "btn btn-sm btn-primary",
            "align": "button-right",
            "buttons": [
                {
                    "extend": "copy",
                    "text": "<i class='far fa-copy'></i> Copy",
                    "exportOptions": {
                        "columns": ":visible:not(:last-child)"
                    }
                },
                {
                    "extend": "csv",
                    "text": "<i class='fas fa-file-csv'></i> CSV",
                    "bom": true,
                    "exportOptions": {
                        "columns": ":visible:not(:last-child)"
                    }
                },
                {
                    "extend": "excel",
                    "text": "<i class='far fa-file-excel'></i> Excel",
                    "exportOptions": {
                        "columns": ":visible:not(:last-child)"
                    }
                },
                {
                    "extend": "pdf",
                    "text": "<i class='far fa-file-pdf'></i> PDF",
                    "exportOptions": {
                        "columns": ":visible:not(:last-child)"
                    }
                },
                {
                    "extend": "print",
                    "text": "<i class='fas fa-print'></i> In",
                    "exportOptions": {
                        "columns": ":visible:not(:last-child)"
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
        }],
        "initComplete": function() {
            var filterHtml = `
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
            #custom-filter-menu {
                display: none;
                position: absolute;
                right: 0;
                top: 100%;
                margin-top: 5px;
                width: 300px;
                background: #fff;
                border: 1px solid rgba(0,0,0,.15);
                border-radius: .25rem;
                box-shadow: 0 .5rem 1rem rgba(0,0,0,.175);
                z-index: 1050;
            }
            </style>
            <div id="custom-filter-menu" class="p-3">
                <div class="form-group mb-2">
                    <label class="label-sidebar">Lớp học:</label>
                    <select id="filter_lop_hoc" class="form-control form-control-sm">
                        <option value="">-- Tất cả lớp học --</option>
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
                var selectedLop = $('#filter_lop_hoc').val() || "";
                var lopHocOptions = [];
                
                table.rows().every(function() {
                    var data = this.data();
                    var lophoc = $('<div>').html(data[2]).text().trim();
                    
                    if (lophoc !== '' && lopHocOptions.indexOf(lophoc) === -1) lopHocOptions.push(lophoc);
                });
                
                var select = $('#filter_lop_hoc');
                select.empty().append('<option value="">-- Tất cả lớp học --</option>');
                lopHocOptions.sort().forEach(function(opt) {
                    select.append('<option value="'+opt+'" '+(opt === selectedLop ? 'selected' : '')+'>'+opt+'</option>');
                });
            }

            $('#filter_lop_hoc').on('change', function() {
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
                var lopVal = $('#filter_lop_hoc').val();
                
                table.column(2).search(lopVal ? '^' + $.fn.dataTable.util.escapeRegex(lopVal) + '$' : '', true, false)
                     .draw();
                $('#custom-filter-menu').fadeOut(200);
            });

            $('#btn-cancel-filter').on('click', function() {
                $('#filter_lop_hoc').val('');
                updateCascadeDropdowns();
                table.column(2).search('').draw();
                $('#custom-filter-menu').fadeOut(200);
            });
        }
    });
});

function toggle_add_form() {
    var addForm = $('#div_add_form');
    var btn = $('#btn-toggle-add');
    
    $("#div_update").html('');
    
    addForm.slideToggle(300, function() {
        if (addForm.is(':visible')) {
            btn.html('<i class="fas fa-times"></i>').removeClass('btn-success').addClass('btn-cancel-custom');
        } else {
            btn.html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-cancel-custom').addClass('btn-success');
        }
    });
}

function update_obj(id_phan_cong) {
    $.post('quan-ly-phan-cong/update.php', {
        'id_phan_cong': id_phan_cong,
    }, function(data) {
        $('#div_add_form').slideUp(300);
        $('#btn-toggle-add').html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-cancel-custom').addClass('btn-success');
        $('#div_update').html(data);
    });
}

function cancel_update() {
    $("#div_update").html('');
    $(".card.card-success").removeClass('collapsed-card');
}
 </script>



