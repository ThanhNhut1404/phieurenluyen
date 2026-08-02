 <?php
    // require "../models/getModel.php";
    // Nhựt sửa lỗi: tạo CSRF token cho các thao tác thêm/sửa/xóa ngành học.
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    // Nhựt sửa lỗi: giữ lại dữ liệu nhập gần nhất khi validate lỗi để cải thiện UX.
    $nganhhoc_old_input = isset($_SESSION['nganhhoc_old_input']) && is_array($_SESSION['nganhhoc_old_input']) ? $_SESSION['nganhhoc_old_input'] : array();
    // Nhựt sửa lỗi: chỉ giữ dữ liệu cũ cho form thêm đúng một lần rồi xóa để không bị dính lại khi quay trang.
    if (isset($nganhhoc_old_input['context']) && $nganhhoc_old_input['context'] === 'add') {
        unset($_SESSION['nganhhoc_old_input']);
    }

    // Nhựt sửa lỗi: escape dữ liệu trước khi hiển thị để tránh stored XSS.
    if (!function_exists('nganhhoc_escape')) {
        function nganhhoc_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    // Nhựt sửa lỗi: lấy lại dữ liệu cũ theo đúng context của form thêm.
    if (!function_exists('nganhhoc_old_value')) {
        function nganhhoc_old_value($field, $context, $default = '') {
            global $nganhhoc_old_input;
            if (isset($nganhhoc_old_input['context']) && $nganhhoc_old_input['context'] === $context && isset($nganhhoc_old_input[$field])) {
                return $nganhhoc_old_input[$field];
            }
            return $default;
        }
    }

    $nganhhoc__Get_All = $nganhhoc->nganhhoc__Get_All();
    $khoa__Get_All = $khoa->khoa__Get_All();
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý ngành học</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="#">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý ngành học</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <section class="content">
         <form class="row form" action="quan-ly-nganh-hoc/action.php?req=add" method="post"
             enctype="multipart/form-data">
             <input type="hidden" name="csrf_token" value="<?=nganhhoc_escape($_SESSION['csrf_token'])?>">
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
                             <label for="">Khoa <span class="color-crimson">(*)</span></label>
                             <select class="form-control" name="id_khoa" required>
                                 <option value="">Chọn khoa</option>
                                 <?php foreach ($khoa__Get_All as $item):?>
                                 <option value="<?=(int)$item->id_khoa?>" <?=((int)nganhhoc_old_value('id_khoa', 'add') === (int)$item->id_khoa) ? "selected" : ""?>><?=nganhhoc_escape($item->ten_khoa)?></option>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                         <div class="form-group">
                             <label for="">Tên ngành học <span class="color-crimson">(*)</span></label>
                             <!-- Nhựt sửa lỗi: giới hạn tên ngành học ở client khớp với validate server-side và DB. -->
                             <input type="text" id="ten_nganh_hoc" name="ten_nganh_hoc" class="form-control" required maxlength="50"
                                 value="<?=nganhhoc_escape(nganhhoc_old_value('ten_nganh_hoc', 'add'))?>"
                                 placeholder="Nhập tên ngành học">
                         </div>
                         <div class="form-group">
                             <label for="">Ghi chú</label>
                             <textarea id="ghi_chu" name="ghi_chu" class="form-control" maxlength="2000"
                                 placeholder="Nhập ghi chú"><?=nganhhoc_escape(nganhhoc_old_value('ghi_chu', 'add'))?></textarea>
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
                 <h3 class="card-title">Danh sách</h3>
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
                             <th>#</th>
                             <th>Khoa</th>
                             <th>Tên ngành học</th>
                             <th>Ghi chú</th>
                             <th>Thao tác</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($nganhhoc__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <td> <?=nganhhoc_escape($item->ten_khoa ?? 'Khoa không tồn tại')?></td>
                             <td><?=nganhhoc_escape($item->ten_nganh_hoc)?></td>
                             <td><?=nganhhoc_escape($item->ghi_chu)?></td>
                             <td>
                                 <a href="javascript:void(0)" class="btn btn-warning" onclick="return update_obj(<?=(int)$item->id_nganh_hoc?>)">
                                     <i class="fas fa-edit"></i>
                                 </a>
                                 <a href="#" type="button" class="btn btn-danger"
                                     onclick="return delete_obj(<?=(int)$item->id_nganh_hoc?>)">
                                     <i class="fas fa-trash"></i>
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
        // Nhựt sửa lỗi: đưa dropdown chọn số dòng lên hàng riêng phía trên các nút xuất dữ liệu.
        "dom": "<'row'<'col-sm-12'l>><'row'<'col-sm-12'B>><'row'<'col-sm-12'f>>rtip",
        "pagingType": "full_numbers",
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "language": {
            "decimal": ",",
            "thousands": ".",
            "emptyTable": "Không có dữ liệu trong bảng",
            "info": "Hiển thị _START_ đến _END_ của _TOTAL_ dòng",
            "infoEmpty": "Hiển thị 0 đến 0 của 0 dòng",
            "infoFiltered": "(lọc từ _MAX_ dòng)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ dòng",
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
                "extend": "copy",
                "exportOptions": {
                    "columns": ":visible:not(:last-child)"
                }
            },
            {
                "extend": "csv",
                "exportOptions": {
                    "columns": ":visible:not(:last-child)"
                }
            },
            {
                "extend": "excel",
                "exportOptions": {
                    "columns": ":visible:not(:last-child)"
                }
            },
            {
                "extend": "pdf",
                "exportOptions": {
                    "columns": ":visible:not(:last-child)"
                }
            },
            {
                "extend": "print",
                "exportOptions": {
                    "columns": ":visible:not(:last-child)"
                }
            }
        ]
    }).buttons().container().appendTo('#tablejs_wrapper .col-md-6:eq(0)');
});

function update_obj(id_nganh_hoc) {
    $.ajax({
        url: 'quan-ly-nganh-hoc/update.php',
        method: 'POST',
        data: { 'id_nganh_hoc': id_nganh_hoc },
        success: function(data) {
            $(".card.card-success").addClass('collapsed-card');
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
                text = 'Ngành học cần sửa không tồn tại.';
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
    $(".card.card-success").removeClass('collapsed-card');
}

function delete_obj(id_nganh_hoc) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: 'Thao tác này sẽ xóa ngành học đã chọn.',
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
        form.action = 'quan-ly-nganh-hoc/action.php?req=delete';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id_nganh_hoc';
        idInput.value = id_nganh_hoc;
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

<?php if (isset($nganhhoc_old_input['context']) && $nganhhoc_old_input['context'] === 'update' && isset($nganhhoc_old_input['id_nganh_hoc'])): ?>
window.addEventListener("load", function() {
    update_obj(<?=(int)$nganhhoc_old_input['id_nganh_hoc']?>);
});
<?php endif; ?>
 </script>
