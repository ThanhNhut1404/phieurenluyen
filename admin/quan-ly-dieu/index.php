 <?php
    // require "../models/getModel.php";
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    $dieu_old_input = isset($_SESSION['dieu_old_input']) && is_array($_SESSION['dieu_old_input']) ? $_SESSION['dieu_old_input'] : array();
    if (isset($dieu_old_input['context']) && $dieu_old_input['context'] === 'add') {
        unset($_SESSION['dieu_old_input']);
    }

    if (!function_exists('dieu_escape')) {
        function dieu_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('dieu_old_value')) {
        function dieu_old_value($field, $context, $default = '') {
            global $dieu_old_input;
            if (isset($dieu_old_input['context']) && $dieu_old_input['context'] === $context && isset($dieu_old_input[$field])) {
                return $dieu_old_input[$field];
            }
            return $default;
        }
    }

    $dieu__Get_All = $dieu->dieu__Get_All();
    // Nhựt sửa lỗi: Giới hạn thứ tự trên form thêm từ 1 đến max + 1 để chặn nhập số âm, 0 hoặc vượt giới hạn ở client.
    $dieu__Max_Thu_Tu = $dieu->dieu__Get_Max_Thu_Tu();
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header pb-0">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý Điều</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý Điều</li>
                     </ol>
                 </div>
             </div>
             <!-- quân sửa: Cảnh báo khi dữ liệu đang bị khoá bởi Đợt chấm điểm -->
             <?php if(isset($_GET['status']) && $_GET['status'] == 'locked_by_dotchamdiem'): ?>
                 <div class="alert alert-danger alert-dismissible mt-2">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <h5><i class="icon fas fa-lock"></i> Thao tác thất bại!</h5>
                     Dữ liệu này đang được sử dụng trong một Đợt chấm điểm đang diễn ra. Bạn không thể Sửa hoặc Xoá vào lúc này!
                 </div>
             <?php endif; ?>
             <?php if(isset($_GET['status']) && $_GET['status'] == 'locked_update'): ?>
                 <div class="alert alert-danger alert-dismissible mt-2">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <h5><i class="icon fas fa-lock"></i> Thao tác thất bại!</h5>
                     Dữ liệu này đã được sử dụng trong một Đợt chấm điểm. Để bảo toàn lịch sử, bạn không thể Sửa dữ liệu này. Hãy tạo mới thay thế!
                 </div>
             <?php endif; ?>
         </div><!-- /.container-fluid -->
     </section>

     <?php $is_add_error = isset($dieu_old_input['context']) && $dieu_old_input['context'] === 'add'; ?>

     <!-- Nhựt sửa: Thêm nút bật/tắt form thêm mới -->
     <section class="content mb-2">
         <button type="button" class="btn <?= $is_add_error ? 'btn-cancel-custom' : 'btn-success' ?> font-weight-bold" id="btn-toggle-add" onclick="toggle_add_form()">
             <i class="fas <?= $is_add_error ? 'fa-times' : 'fa-plus' ?>"></i> <?= $is_add_error ? '' : 'Thêm mới' ?>
         </button>
     </section>

     <!-- Nhựt sửa: Ẩn form thêm mới mặc định -->
     <section class="content" id="div_add_form" <?= $is_add_error ? '' : 'style="display: none;"' ?>>
         <form class="row form" action="quan-ly-dieu/action.php?req=add" method="post" enctype="multipart/form-data">
             <input type="hidden" name="csrf_token" value="<?=dieu_escape($_SESSION['csrf_token'] ?? '')?>">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title">Thêm mới Điều</h3>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="ten_dieu">Tên điều <span class="color-crimson">*</span></label>
                                     <input type="text" id="ten_dieu" name="ten_dieu" class="form-control <?= ($is_add_error && in_array($_GET['status'] ?? '', ['duplicate-name', 'invalid'])) ? 'is-invalid' : '' ?>" required
                                         placeholder="Nhập tên điều" value="<?=dieu_escape(dieu_old_value('ten_dieu', 'add'))?>">
                                     <?php if ($is_add_error && isset($_GET['status'])): ?>
                                         <?php if ($_GET['status'] == 'duplicate-name'): ?>
                                             <small class="text-danger mt-1">Tên điều đã tồn tại trong hệ thống.</small>
                                         <?php elseif ($_GET['status'] == 'invalid'): ?>
                                             <small class="text-danger mt-1">Tên điều không được để trống.</small>
                                         <?php endif; ?>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <!-- quân sửa: Cập nhật lời nhắc Thứ tự tự động -->
                                     <label class="label-sidebar" for="thu_tu">Thứ tự</label>
                                     <input type="number" id="thu_tu" name="thu_tu" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-thutu') ? 'is-invalid' : '' ?>" min="1"
                                         max="<?=$dieu__Max_Thu_Tu + 1?>" step="1"
                                         placeholder="Nhập thứ tự (Có thể để trống)" value="<?=dieu_escape(dieu_old_value('thu_tu', 'add'))?>">
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-thutu'): ?>
                                         <small class="text-danger mt-1">Thứ tự không hợp lệ.</small><br/>
                                     <?php endif; ?>
                                     <small class="form-text text-muted">Mẹo: Để trống hệ thống sẽ tự động xếp cuối. Nếu nhập trùng, hệ thống sẽ tự động hoán đổi.</small>
                                 </div>
                             </div>
                         </div>
                         <div class="form-group">
                             <label class="label-sidebar" for="ghi_chu">Nội dung chi tiết</label>
                             <textarea id="ghi_chu" name="ghi_chu" class="form-control" required
                                 placeholder="Nhập nội dung chi tiết"><?=dieu_escape(dieu_old_value('ghi_chu', 'add'))?></textarea>
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
                 <h3 class="card-title">Danh sách Điều</h3>
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
                              <th style="width: 5%; white-space: nowrap;">STT</th>
                              <th style="width: 28%; white-space: nowrap;">Tên điều</th>
                              <th style="width: 45%; white-space: nowrap;">Ghi chú</th>
                              <th style="width: 12%; white-space: nowrap;">Thứ tự</th>
                              <th style="width: 10%; white-space: nowrap;">Thao tác</th>
                          </tr>
                      </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($dieu__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <td class="text-center" style="text-align: center !important;"><?=$item->ten_dieu?></td>
                             <td><?=$item->ghi_chu?></td>
                             <td class="text-center" style="text-align: center !important;"><?=$item->thu_tu?></td>
                             <td>
                                 <?php if($dieu->dieu__Is_Edit_Locked($item->id_dieu)): ?>
                                     <button class="btn btn-warning m-2 disabled" title="Mẫu đã phát sinh đợt chấm cho nội dung này.">
                                         <i class="ri-edit-2-line"></i>
                                     </button>
                                 <?php else: ?>
                                     <a href="#" type="button" class="btn btn-warning m-2"
                                         onclick="update_obj(<?=$item->id_dieu?>)">
                                         <i class="ri-edit-2-line"></i>
                                     </a>
                                 <?php endif; ?>
                                     <a href="#" type="button" class="btn  btn-danger m-2"
                                         onclick="return confirm_delete_dieu('quan-ly-dieu/action.php?req=delete&id_dieu=<?=$item->id_dieu?>')">
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
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ điều",
            "infoEmpty": "Hiển thị 0 - 0 của 0 điều",
            "infoFiltered": "(lọc từ _MAX_ điều)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ điều",
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

function update_obj(id_dieu, error_status = '') {
    $.ajax({
        url: 'quan-ly-dieu/update.php',
        method: 'POST',
        data: { 'id_dieu': id_dieu, 'error_status': error_status },
        success: function(data) {
            // Nhựt sửa: Ẩn form thêm mới khi mở form cập nhật
            $('#div_add_form').slideUp(300);
            $('#btn-toggle-add').html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-cancel-custom').addClass('btn-success');
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
                text = 'Điều cần sửa không tồn tại.';
            } else if (xhr.status >= 500) {
                title = 'Lỗi máy chủ';
                text = 'Máy chủ đang gặp sự cố.';
            }
            Swal.fire(title, text, 'error');
        }
    });
    return false;
}

<?php if (isset($dieu_old_input['context']) && $dieu_old_input['context'] === 'update' && isset($dieu_old_input['id_dieu'])): ?>
window.addEventListener("load", function() {
    update_obj(<?=(int)$dieu_old_input['id_dieu']?>, '<?=$_GET['status'] ?? ''?>');
});
<?php endif; ?>

function cancel_update() {
    $("#div_update").html('');
}

// Quân sửa: Thêm hàm xác nhận xóa Điều với thông báo cảnh báo xóa toàn bộ dữ liệu con
function confirm_delete_dieu(url) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        html: "Toàn bộ khoản và mục thuộc <b>Điều</b> này<br>cũng sẽ bị xóa và không thể khôi phục!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
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
            location.href = url;
        }
    })
}
 </script>








