 <?php
    // require "../models/getModel.php";
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    $lophoc_old_input = isset($_SESSION['lophoc_old_input']) && is_array($_SESSION['lophoc_old_input']) ? $_SESSION['lophoc_old_input'] : array();

    if (!function_exists('lophoc_escape')) {
        function lophoc_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('lophoc_old_value')) {
        function lophoc_old_value($field, $context, $default = '') {
            global $lophoc_old_input;
            if (isset($lophoc_old_input['context']) && $lophoc_old_input['context'] === $context && isset($lophoc_old_input[$field])) {
                return $lophoc_old_input[$field];
            }
            return $default;
        }
    }

    $lophoc__Get_All = $lophoc->lophoc__Get_All();
    $khoahoc__Get_All = $khoahoc->khoahoc__Get_All();
    $nganhhoc__Get_All = $nganhhoc->nganhhoc__Get_All();
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper" id="div_top">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý lớp học</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý lớp học</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <!-- Nhựt sửa: Thêm nút bật/tắt form thêm mới -->
     <section class="content mb-2">
         <div class="col-12">
             <button type="button" class="btn btn-primary" id="btn-toggle-add" onclick="toggle_add_form()">
                 <i class="fas fa-plus"></i> Thêm mới Lớp học
             </button>
         </div>
     </section>

     <!-- Nhựt sửa: Ẩn form thêm mới mặc định -->
     <section class="content" id="div_add_form" style="display: none;">
         <form class="row form" action="quan-ly-lop-hoc/action.php?req=add" method="post">
             <input type="hidden" name="csrf_token" value="<?=lophoc_escape($_SESSION['csrf_token'])?>">
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
                             <label for="">Khóa học <span class="color-crimson">(*)</span></label>
                             <select class="form-control" name="id_khoa_hoc" required>
                                 <option value="">Chọn khóa học</option>
                                 <?php foreach ($khoahoc__Get_All as $item):?>
                                 <option value="<?=(int)$item->id_khoa_hoc?>" <?=((int)lophoc_old_value('id_khoa_hoc', 'add') === (int)$item->id_khoa_hoc) ? 'selected' : ''?>><?=lophoc_escape($item->ten_khoa_hoc)?></option>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                         <div class="form-group">
                             <label for="">Ngành học <span class="color-crimson">(*)</span></label>
                             <select class="form-control" name="id_nganh_hoc" required>
                                 <option value="">Chọn ngành học</option>
                                 <?php foreach ($nganhhoc__Get_All as $item):?>
                                 <option value="<?=(int)$item->id_nganh_hoc?>" <?=((int)lophoc_old_value('id_nganh_hoc', 'add') === (int)$item->id_nganh_hoc) ? 'selected' : ''?>><?=lophoc_escape($item->ten_nganh_hoc)?></option>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                         <div class="form-group">
                             <label for="">Tên lớp học <span class="color-crimson">(*)</span></label>
                             <input type="text" id="ten_lop_hoc" name="ten_lop_hoc" class="form-control" required maxlength="50"
                                 value="<?=lophoc_escape(lophoc_old_value('ten_lop_hoc', 'add'))?>" placeholder="Nhập tên lớp học">
                         </div>

                         <div class="form-group">
                             <label for="">Ghi chú</label>
                             <textarea id="ghi_chu" name="ghi_chu" class="form-control" maxlength="2000"
                                 placeholder="Nhập ghi chú"><?=lophoc_escape(lophoc_old_value('ghi_chu', 'add'))?></textarea>
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
                 <h3 class="card-title">Danh sách Lớp học</h3>
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
                             <th style="width: 5%;">STT</th>
                             <th style="width: 10%;">Khóa học</th>
                             <th style="width: 30%;">Ngành học</th>
                             <th style="width: 30%;">Tên lớp học</th>
                             <th style="width: 15%;">Ghi chú</th>
                             <th style="width: 10%;">Thao tác</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($lophoc__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <td class="text-center"><?=lophoc_escape($item->ten_khoa_hoc)?></td>
                             <td class="text-center"><?=lophoc_escape($item->ten_nganh_hoc)?></td>
                             <td class="text-center"><?=lophoc_escape($item->ten_lop_hoc)?></td>
                             <td><?=lophoc_escape($item->ghi_chu)?></td>
                             <td>
                                 <a href="javascript:void(0)" class="btn btn-warning m-2" onclick="return update_obj(<?=(int)$item->id_lop_hoc?>)">
                                     <i class="ri-edit-2-line"></i>
                                 </a>
                                 <a href="javascript:void(0)" class="btn btn-danger m-2" onclick="return delete_obj(<?=(int)$item->id_lop_hoc?>)">
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
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ lớp học",
            "infoEmpty": "Hiển thị 0 - 0 của 0 lớp học",
            "infoFiltered": "(lọc từ _MAX_ lớp học)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ lớp học",
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
        }]
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
            btn.html('<i class="fas fa-plus"></i> Thêm mới Lớp học').removeClass('btn-secondary').addClass('btn-primary');
        }
    });
}

function update_obj(id_lop_hoc) {
    $.ajax({
        url: 'quan-ly-lop-hoc/update.php',
        method: 'POST',
        data: { 'id_lop_hoc': id_lop_hoc },
        success: function(data) {
            // Nhựt sửa: Ẩn form thêm mới khi mở form cập nhật
            $('#div_add_form').slideUp(300);
            $('#btn-toggle-add').html('<i class="fas fa-plus"></i> Thêm mới Lớp học').removeClass('btn-secondary').addClass('btn-primary');
            $('#div_update').html(data);
        },
        error: function(xhr) {
            var title = 'Lỗi hệ thống';
            var text = 'Không thể tải form cập nhật.';
            if (xhr.status === 403) {
                title = 'Không có quyền';
                text = 'Phiên đăng nhập không hợp lệ hoặc đã hết hạn.';
            } else if (xhr.status === 404) {
                title = 'Không tìm thấy';
                text = 'Lớp học cần sửa không tồn tại.';
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

function delete_obj(id_lop_hoc) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: 'Thao tác này sẽ xóa lớp học đã chọn.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Xóa'
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'post';
        form.action = 'quan-ly-lop-hoc/action.php?req=delete';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id_lop_hoc';
        idInput.value = id_lop_hoc;
        form.appendChild(idInput);

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = <?=json_encode($_SESSION['csrf_token'])?>;
        form.appendChild(csrfInput);

        document.body.appendChild(form);
        form.submit();
    });
    return false;
}

<?php if (isset($lophoc_old_input['context']) && $lophoc_old_input['context'] === 'update' && isset($lophoc_old_input['id_lop_hoc'])): ?>
window.addEventListener("load", function() {
    update_obj(<?=(int)$lophoc_old_input['id_lop_hoc']?>);
});
<?php endif; ?>
 </script>
