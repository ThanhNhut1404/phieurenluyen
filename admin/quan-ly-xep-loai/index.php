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

    $xep_loai_old_input = isset($_SESSION['xep_loai_old_input']) && is_array($_SESSION['xep_loai_old_input']) ? $_SESSION['xep_loai_old_input'] : array();
    if (isset($xep_loai_old_input['context']) && $xep_loai_old_input['context'] === 'add') {
        unset($_SESSION['xep_loai_old_input']);
    }

    if (!function_exists('xep_loai_escape')) {
        function xep_loai_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('xep_loai_old_value')) {
        function xep_loai_old_value($field, $context, $default = '') {
            global $xep_loai_old_input;
            if (isset($xep_loai_old_input['context']) && $xep_loai_old_input['context'] === $context && isset($xep_loai_old_input[$field])) {
                return $xep_loai_old_input[$field];
            }
            return $default;
        }
    }

    $xeploai__Get_All = $xeploai->xeploai__Get_All();
    $has_active_dot = $dotchamdiem->dotchamdiem__Has_Dang_Dien_Ra(date('Y-m-d'));
 ?>

 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header pb-0">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý Xếp loại</h1>
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

     <?php $is_add_error = isset($xep_loai_old_input['context']) && $xep_loai_old_input['context'] === 'add'; ?>

     <!-- Nhựt sửa: Thêm nút bật/tắt form thêm mới -->
     <section class="content mb-2">
         <button type="button" class="btn <?= $is_add_error ? 'btn-cancel-custom' : 'btn-success' ?> font-weight-bold" id="btn-toggle-add" <?= $has_active_dot ? 'disabled title="Đang có đợt chấm điểm diễn ra"' : 'onclick="toggle_add_form()"' ?>>
             <i class="fas <?= $is_add_error ? 'fa-times' : 'fa-plus' ?>"></i> <?= $is_add_error ? '' : 'Thêm mới' ?>
         </button>
     </section>

     <!-- Nhựt sửa: Ẩn form thêm mới mặc định -->
     <section class="content" id="div_add_form" <?= $is_add_error ? '' : 'style="display: none;"' ?>>
         <form class="row form" action="quan-ly-xep-loai/action.php?req=add" method="post" enctype="multipart/form-data">
             <input type="hidden" name="csrf_token" value="<?=xep_loai_escape($_SESSION['csrf_token'])?>">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title">Thêm mới Xếp loại</h3>
                     </div>
                     <div class="card-body">
                         <div class="form-group">
                             <label class="label-sidebar" for="ten_xep_loai">Tên xếp loại <span class="color-crimson">*</span></label>
                             <input type="text" id="ten_xep_loai" name="ten_xep_loai" class="form-control <?= ($is_add_error && in_array($_GET['status'] ?? '', ['duplicate-name', 'invalid-ten'])) ? 'is-invalid' : '' ?>" required
                                 placeholder="Nhập tên xếp loại" value="<?=xep_loai_escape(xep_loai_old_value('ten_xep_loai', 'add'))?>">
                             <?php if ($is_add_error && isset($_GET['status'])): ?>
                                 <?php if ($_GET['status'] == 'duplicate-name'): ?>
                                     <small class="text-danger mt-1">Tên xếp loại đã tồn tại trong hệ thống.</small>
                                 <?php elseif ($_GET['status'] == 'invalid-ten'): ?>
                                     <small class="text-danger mt-1">Tên xếp loại không được để trống.</small>
                                 <?php endif; ?>
                             <?php endif; ?>
                         </div>
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="can_duoi">Điểm tối thiểu <span class="color-crimson">*</span></label>
                                     <input type="number" id="can_duoi" name="can_duoi"
                                         class="form-control <?= ($is_add_error && in_array($_GET['status'] ?? '', ['invalid-diem', 'overlap-xep-loai'])) ? 'is-invalid' : '' ?>" required title="Thấp nhất là 0, lớn nhất là 100"
                                         placeholder="Nhập điểm tối thiểu" min="0" max="100" step="1" value="<?=xep_loai_escape(xep_loai_old_value('can_duoi', 'add'))?>">
                                     <?php if ($is_add_error && isset($_GET['status'])): ?>
                                         <?php if ($_GET['status'] == 'invalid-diem'): ?>
                                             <small class="text-danger mt-1">Điểm không hợp lệ (từ 0 đến 100, tối thiểu <= tối đa).</small>
                                         <?php elseif ($_GET['status'] == 'overlap-xep-loai'): ?>
                                             <small class="text-danger mt-1">Khoảng điểm bị trùng lặp với xếp loại khác.</small>
                                         <?php endif; ?>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="can_tren">Điểm tối đa <span class="color-crimson">*</span></label>
                                     <input type="number" id="can_tren" name="can_tren"
                                         class="form-control <?= ($is_add_error && in_array($_GET['status'] ?? '', ['invalid-diem', 'overlap-xep-loai'])) ? 'is-invalid' : '' ?>" required title="Thấp nhất là 0, lớn nhất là 100"
                                         placeholder="Nhập điểm tối đa" min="0" max="100" step="1" value="<?=xep_loai_escape(xep_loai_old_value('can_tren', 'add'))?>">
                                     <?php if ($is_add_error && isset($_GET['status'])): ?>
                                         <?php if ($_GET['status'] == 'invalid-diem'): ?>
                                             <small class="text-danger mt-1">Điểm không hợp lệ (từ 0 đến 100, tối thiểu <= tối đa).</small>
                                         <?php elseif ($_GET['status'] == 'overlap-xep-loai'): ?>
                                             <small class="text-danger mt-1">Khoảng điểm bị trùng lặp với xếp loại khác.</small>
                                         <?php endif; ?>
                                     <?php endif; ?>
                                 </div>
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="ha_bac">Hạ bậc <span class="color-crimson">*</span></label>
                                     <input type="number" id="ha_bac" name="ha_bac" min="10" max="15" step="1"
                                         class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-habac') ? 'is-invalid' : '' ?>" required title="Thấp nhất là 10, lớn nhất là 15"
                                         placeholder="Nhập điểm hạ bậc" value="<?=xep_loai_escape(xep_loai_old_value('ha_bac', 'add'))?>">
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-habac'): ?>
                                         <small class="text-danger mt-1">Điểm hạ bậc không hợp lệ (từ 10 đến 15).</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="ghi_chu">Ghi chú</label>
                                     <textarea id="ghi_chu" name="ghi_chu" class="form-control" rows="1"
                                         placeholder="Nhập ghi chú"><?=xep_loai_escape(xep_loai_old_value('ghi_chu', 'add'))?></textarea>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <!-- /.card-body -->
                     <div class="card-footer py-2">
                         <input type="submit" value="Thêm mới" class="btn btn-success float-right font-weight-bold">
                         <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" onclick="toggle_add_form()">Hủy</button>
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
                                 <a href="javascript:void(0)" class="btn btn-warning m-2 <?= $has_active_dot ? 'disabled' : '' ?>"
                                     <?= $has_active_dot ? 'title="Đang có đợt chấm điểm diễn ra" style="pointer-events: none; opacity: 0.6;"' : 'onclick="update_obj('.$item->id_xep_loai.')"' ?>>
                                     <i class="ri-edit-2-line"></i>
                                 </a>
                                 <a href="javascript:void(0)" class="btn btn-danger m-2 <?= $has_active_dot ? 'disabled' : '' ?>"
                                     <?= $has_active_dot ? 'title="Đang có đợt chấm điểm diễn ra" style="pointer-events: none; opacity: 0.6;"' : 'onclick="return confirm_delete_sweet(\'quan-ly-xep-loai/action.php?req=delete&id_xep_loai='.$item->id_xep_loai.'&csrf_token='.htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8').'\', \'Xếp loại\')"' ?>>
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
            btn.html('<i class="fas fa-times"></i>').removeClass('btn-success').addClass('btn-cancel-custom');
        } else {
            btn.html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-cancel-custom').addClass('btn-success');
        }
    });
}

function update_obj(id_xep_loai, error_status = '') {
    $.ajax({
        url: 'quan-ly-xep-loai/update.php',
        method: 'POST',
        data: { 'id_xep_loai': id_xep_loai, 'error_status': error_status },
        success: function(data) {
            // Nhựt sửa: Ẩn form thêm mới khi mở form cập nhật
            $('#div_add_form').slideUp(300);
            $('#btn-toggle-add').html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-cancel-custom').addClass('btn-success');
            $("#div_update").html(data);
        },
        error: function(xhr) {
            var title = 'Lỗi hệ thống';
            var text = 'Không thể tải form cập nhật.';
            if (xhr.status === 403) {
                title = 'Không có quyền';
                text = 'Phiên đăng nhập không hợp lệ hoặc đã hết hạn.';
            } else if (xhr.status === 404) {
                title = 'Không tìm thấy';
                text = 'Xếp loại cần sửa không tồn tại.';
            } else if (xhr.status >= 500) {
                title = 'Lỗi máy chủ';
                text = 'Máy chủ đang gặp sự cố.';
            }
            Swal.fire(title, text, 'error');
        }
    });
    return false;
}

function cancel_update() {
    $("#div_update").html('');
}

<?php if (isset($xep_loai_old_input['context']) && $xep_loai_old_input['context'] === 'update' && isset($xep_loai_old_input['id_xep_loai'])): ?>
window.addEventListener("load", function() {
    update_obj(<?=(int)$xep_loai_old_input['id_xep_loai']?>, '<?=$_GET['status'] ?? ''?>');
});
<?php endif; ?>
 </script>



