<?php
    // require "../models/getModel.php";
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        // Nhựt sửa lỗi: Tạo CSRF token cho Add/Delete Xếp loại giống các module danh mục khác.
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }
    $xeploai__Get_All = $xeploai->xeploai__Get_All();
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý xếp loại</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý xếp loại</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <!-- Nhựt sửa: Thêm nút bật/tắt form thêm mới -->
     <section class="content mb-2">
         <div class="col-12">
             <button type="button" class="btn btn-primary" id="btn-toggle-add" onclick="toggle_add_form()">
                 <i class="fas fa-plus"></i> Thêm mới Xếp loại
             </button>
         </div>
     </section>

     <!-- Nhựt sửa: Ẩn form thêm mới mặc định -->
     <section class="content" id="div_add_form" style="display: none;">
         <form class="row form" action="quan-ly-xep-loai/action.php?req=add" method="post" enctype="multipart/form-data">
             <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title">Thêm mới</h3>
                         <div class="card-tools">
                             <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                 <i class="fas fa-minus"></i>
                             </button>
                         </div>
                     </div>
                     <div class="card-body">
                         <div class="form-group">
                             <label for="">Tên xếp loại <span class="color-crimson">(*)</span></label>
                             <input type="text" id="ten_xep_loai" name="ten_xep_loai" class="form-control" required
                                 placeholder="Nhập tên xếp loại">
                         </div>
                         <div class="form-group">
                             <label for="">Ghi chú</label>
                             <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                                 placeholder="Nhập ghi chú"></textarea>
                         </div>
                         <div class="form-group">
                             <label for="">Điểm tối thiểu <span class="color-crimson">(*)</span></label>
                             <input type="number" id="can_duoi" name="can_duoi"
                                 class=" form-control" required title="Thấp nhất là 0, lớn nhất là 100"
                                 placeholder="Nhập điểm tối thiểu" min="0" max="100" step="1">
                         </div>
                         <div class="form-group">
                             <label for="">Điểm tối đa <span class="color-crimson">(*)</span></label>
                             <input type="number" id="can_tren" name="can_tren"
                                 class=" form-control" required title="Thấp nhất là 0, lớn nhất là 100"
                                 placeholder="Nhập điểm tối đa" min="0" max="100" step="1">
                         </div>
                         <div class="form-group">
                             <label for="">Hạ bậc <span class="color-crimson">(*)</span></label>
                             <input type="number" id="ha_bac" name="ha_bac" min="10" max="15" step="1"
                                 class=" form-control" required title="Thấp nhất là 10, lớn nhất là 15"
                                 placeholder="Nhập điểm hạ bậc">
                         </div>
                     </div>
                     <!-- /.card-body -->
                     <div class="card-footer">
                         <input type="submit" value="Thêm mới" class="btn btn-success float-right">
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
                 <h3 class="card-title">Danh sách Xếp loại</h3>
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
                             <th>Tên xếp loại</th>
                             <th>Điểm tối thiểu</th>
                             <th>Điểm tối đa</th>
                             <th>Hạ bậc</th>
                             <th>Ghi chú</th>
                             <th>Thao tác</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($xeploai__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <td class="text-center" style="text-align: center !important;"><?=htmlspecialchars($item->ten_xep_loai ?? "", ENT_QUOTES, 'UTF-8')?></td>
                             <td class="text-center" style="text-align: center !important;"><?=$item->can_duoi?></td>
                             <td class="text-center" style="text-align: center !important;"><?=$item->can_tren?></td>
                             <td class="text-center" style="text-align: center !important;"><?=$item->ha_bac?></td>
                             <td><?=htmlspecialchars($item->ghi_chu ?? "", ENT_QUOTES, 'UTF-8')?></td>
                             <td>
                                 <a href="javascript:void(0)" class="btn btn-warning m-2"
                                     onclick="update_obj(<?=$item->id_xep_loai?>)">
                                     <i class="ri-edit-2-line"></i>
                                 </a>
                                 <a href="javascript:void(0)" class="btn btn-danger m-2"
                                     onclick="confirm_sweet('quan-ly-xep-loai/action.php?req=delete&id_xep_loai=<?=$item->id_xep_loai?>&csrf_token=<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>')">
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
        "dom": "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end align-items-center'Bf>>rtip",
        "pagingType": "full_numbers",
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "language": {
            "decimal": ",",
            "thousands": ".",
            "emptyTable": "Không có dữ liệu trong bảng",
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ xếp loại",
            "infoEmpty": "Hiển thị 0 - 0 của 0 xếp loại",
            "infoFiltered": "(lọc từ _MAX_ xếp loại)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ xếp loại",
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
        }]
    });

    can_duoi = document.getElementById('can_duoi');
    $("#can_duoi").change(function() {
        $('#can_tren').attr({
            "min": can_duoi.value
        });
    });
});

// Nhựt sửa: Hàm bật/tắt hiển thị form thêm mới
function toggle_add_form() {
    var addForm = $('#div_add_form');
    var btn = $('#btn-toggle-add');
    
    // Đóng form cập nhật nếu đang mở
    $("#div_update").html('');
    
    addForm.slideToggle(300, function() {
        if (addForm.is(':visible')) {
            btn.html('<i class="fas fa-times"></i> Đóng lại').removeClass('btn-primary').addClass('btn-secondary');
        } else {
            btn.html('<i class="fas fa-plus"></i> Thêm mới Xếp loại').removeClass('btn-secondary').addClass('btn-primary');
        }
    });
}

function update_obj(id_xep_loai) {
    $.post('quan-ly-xep-loai/update.php', {
        'id_xep_loai': id_xep_loai,
    }, function(data) {
        // Nhựt sửa: Ẩn form thêm mới khi mở form cập nhật
        $('#div_add_form').slideUp(300);
        $('#btn-toggle-add').html('<i class="fas fa-plus"></i> Thêm mới Xếp loại').removeClass('btn-secondary').addClass('btn-primary');
        $('#div_update').html(data);
    });
}

function cancel_update() {
    $("#div_update").html('');
}
 </script>
