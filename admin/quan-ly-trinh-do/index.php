<?php
    // require "../models/getModel.php";
    // Nhựt sửa lỗi: tạo CSRF token cho các thao tác thêm/sửa/xóa trình độ.
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    $trinhdo_old_input = isset($_SESSION['trinhdo_old_input']) && is_array($_SESSION['trinhdo_old_input']) ? $_SESSION['trinhdo_old_input'] : array();
    // Nhựt sửa lỗi: dữ liệu lỗi chỉ hiển thị lại một lần rồi xóa để không bị dính form khi quay trang.
    if (isset($trinhdo_old_input['context']) && $trinhdo_old_input['context'] === 'add') {
        unset($_SESSION['trinhdo_old_input']);
    }

    if (!function_exists('trinhdo_escape')) {
        function trinhdo_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('trinhdo_old_value')) {
        function trinhdo_old_value($field, $context, $default = '') {
            global $trinhdo_old_input;
            if (isset($trinhdo_old_input['context']) && $trinhdo_old_input['context'] === $context && isset($trinhdo_old_input[$field])) {
                return $trinhdo_old_input[$field];
            }
            return $default;
        }
    }

    $trinhdo__Get_All = $trinhdo->trinhdo__Get_All();
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header pb-0">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Quản lý Trình độ</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                        <li class="breadcrumb-item active">Quản lý trình độ</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <?php $is_add_error = isset($trinhdo_old_input['context']) && $trinhdo_old_input['context'] === 'add'; ?>

    <!-- Nhựt sửa: Thêm nút bật/tắt form thêm mới -->
    <section class="content mb-2">
        <button type="button" class="btn <?= $is_add_error ? 'btn-cancel-custom' : 'btn-success' ?> font-weight-bold" id="btn-toggle-add" onclick="toggle_add_form()">
            <?php if ($is_add_error): ?>
                <i class="fas fa-times"></i>
            <?php else: ?>
                <i class="fas fa-plus"></i> Thêm mới<?php endif; ?>
        </button>
    </section>

    <!-- Nhựt sửa: Ẩn form thêm mới mặc định -->
    <section class="content" id="div_add_form" style="<?= $is_add_error ? '' : 'display: none;' ?>">
        <form class="row form" action="quan-ly-trinh-do/action.php?req=add" method="post">
            <input type="hidden" name="csrf_token" value="<?=trinhdo_escape($_SESSION['csrf_token'])?>">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Thêm mới Trình độ</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="label-sidebar">Tên trình độ <span class="color-crimson">*</span></label>
                            <input type="text" id="ten_trinh_do" name="ten_trinh_do" class="form-control <?= ($is_add_error && in_array($_GET['status'] ?? '', ['duplicate', 'invalid-ten-trinh-do'])) ? 'is-invalid' : '' ?>" required maxlength="50"
                                value="<?=trinhdo_escape(trinhdo_old_value('ten_trinh_do', 'add'))?>" placeholder="Nhập tên trình độ">
                            <?php if ($is_add_error && isset($_GET['status'])): ?>
                                <?php if ($_GET['status'] == 'duplicate'): ?>
                                    <small class="text-danger mt-1">Tên trình độ đã tồn tại trong hệ thống.</small>
                                <?php elseif ($_GET['status'] == 'invalid-ten-trinh-do'): ?>
                                    <small class="text-danger mt-1">Tên trình độ không được để trống và tối đa 50 ký tự.</small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="label-sidebar">Ghi chú</label>
                            <textarea id="ghi_chu" name="ghi_chu" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-ghichu') ? 'is-invalid' : '' ?>" maxlength="2000"
                                placeholder="Nhập ghi chú"><?=trinhdo_escape(trinhdo_old_value('ghi_chu', 'add'))?></textarea>
                            <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-ghichu'): ?>
                                <small class="text-danger mt-1">Ghi chú không được vượt quá 2000 ký tự.</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
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
                <h3 class="card-title">Danh sách Trình độ</h3>
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
                            <th style="width: 20%;">Tên trình độ</th>
                            <th style="width: 65%;">Ghi chú</th>
                            <th style="width: 10%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $num = 0;?>
                        <?php foreach($trinhdo__Get_All as $item):?>
                        <tr>
                            <td><?=++$num?></td>
                            <td class="text-center"><?=trinhdo_escape($item->ten_trinh_do)?></td>
                            <td><?=trinhdo_escape($item->ghi_chu)?></td>
                            <td>
                                <a href="javascript:void(0)" class="btn  btn-warning m-2"
                                    onclick="return update_obj(<?=(int)$item->id_trinh_do?>)">
                                    <i class="ri-edit-2-line"></i>
                                  </a>
                                <a href="javascript:void(0)" class="btn  btn-danger m-2"
                                    onclick="return delete_obj(<?=(int)$item->id_trinh_do?>)">
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
        // Nhựt sửa lỗi: đồng bộ phân trang, tìm kiếm và hàng nút xuất dữ liệu như các màn quản lý khác.
        "dom": "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end align-items-center'Bf>>rtip",
        "pagingType": "full_numbers",
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "language": {
            "decimal": ",",
            "thousands": ".",
            "emptyTable": "Không có dữ liệu trong bảng",
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ trình độ",
            "infoEmpty": "Hiển thị 0 - 0 của 0 trình độ",
            "infoFiltered": "(lọc từ _MAX_ trình độ)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ trình độ",
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

function toggle_add_form() {
    var addForm = $('#div_add_form');
    var btn = $('#btn-toggle-add');
    var isUpdateVisible = $("#div_update").html().trim() !== '';

    if (isUpdateVisible) {
        $("#div_update").html('');
    }
    
    addForm.slideToggle(300, function() {
        if (addForm.is(':visible')) {
            btn.html('<i class="fas fa-times"></i>').removeClass('btn-success').addClass('btn-cancel-custom');
        } else {
            btn.html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-cancel-custom').addClass('btn-success');
        }
    });
}

function update_obj(id_trinh_do, error_status = '') {
    $.ajax({
        url: 'quan-ly-trinh-do/update.php',
        method: 'POST',
        data: { 'id_trinh_do': id_trinh_do, 'error_status': error_status },
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
                text = 'Trình độ cần sửa không tồn tại.';
            }
            Swal.fire(title, text, 'error');
        }
    });
    return false;
}

function cancel_update() {
    $("#div_update").html('');
}

function delete_obj(id_trinh_do) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: 'Thao tác này sẽ xóa trình độ đã chọn.',
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
        form.action = 'quan-ly-trinh-do/action.php?req=delete';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id_trinh_do';
        idInput.value = id_trinh_do;
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

<?php if (isset($trinhdo_old_input['context']) && $trinhdo_old_input['context'] === 'update' && isset($trinhdo_old_input['id_trinh_do'])): ?>
window.addEventListener("load", function() {
    update_obj(<?=(int)$trinhdo_old_input['id_trinh_do']?>, '<?=$_GET['status'] ?? ''?>');
});
<?php endif; ?>
</script>
