<?php
    // require "../models/getModel.php";
    // Nhựt sửa lỗi: tạo CSRF token cho các thao tác thêm/sửa/xóa khóa học.
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    $khoahoc_old_input = isset($_SESSION['khoahoc_old_input']) && is_array($_SESSION['khoahoc_old_input']) ? $_SESSION['khoahoc_old_input'] : array();
    // Nhựt sửa lỗi: dữ liệu lỗi chỉ hiển thị lại một lần rồi xóa để không bị dính form khi quay trang.
    if (isset($khoahoc_old_input['context']) && $khoahoc_old_input['context'] === 'add') {
        unset($_SESSION['khoahoc_old_input']);
    }

    if (!function_exists('khoahoc_escape')) {
        function khoahoc_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('khoahoc_old_value')) {
        function khoahoc_old_value($field, $context, $default = '') {
            global $khoahoc_old_input;
            if (isset($khoahoc_old_input['context']) && $khoahoc_old_input['context'] === $context && isset($khoahoc_old_input[$field])) {
                return $khoahoc_old_input[$field];
            }
            return $default;
        }
    }

    $khoahoc__Get_All = $khoahoc->khoahoc__Get_All();
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header pb-0">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Quản lý Khóa học</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                        <li class="breadcrumb-item active">Quản lý khóa học</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <?php $is_add_error = isset($khoahoc_old_input['context']) && $khoahoc_old_input['context'] === 'add'; ?>

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
        <form class="row form" action="quan-ly-khoa-hoc/action.php?req=add" method="post">
            <input type="hidden" name="csrf_token" value="<?=khoahoc_escape($_SESSION['csrf_token'])?>">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Thêm mới Khóa học</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="label-sidebar">Tên khóa học <span class="color-crimson">*</span></label>
                            <input type="text" id="ten_khoa_hoc" name="ten_khoa_hoc" class="form-control <?= ($is_add_error && in_array($_GET['status'] ?? '', ['duplicate', 'invalid-ten-khoa-hoc'])) ? 'is-invalid' : '' ?>" required maxlength="50"
                                value="<?=khoahoc_escape(khoahoc_old_value('ten_khoa_hoc', 'add'))?>" placeholder="Nhập tên khóa học">
                            <?php if ($is_add_error && isset($_GET['status'])): ?>
                                <?php if ($_GET['status'] == 'duplicate'): ?>
                                    <small class="text-danger mt-1">Tên khóa học đã tồn tại trong hệ thống.</small>
                                <?php elseif ($_GET['status'] == 'invalid-ten-khoa-hoc'): ?>
                                    <small class="text-danger mt-1">Tên khóa học không được để trống và tối đa 50 ký tự.</small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="label-sidebar">Năm nhập học <span class="color-crimson">*</span></label>
                            <input type="number" id="nam_nhap_hoc" name="nam_nhap_hoc" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-nam') ? 'is-invalid' : '' ?>" required
                                min="2006" max="2099" value="<?=khoahoc_escape(khoahoc_old_value('nam_nhap_hoc', 'add'))?>" placeholder="Nhập năm nhập học">
                            <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-nam'): ?>
                                <small class="text-danger mt-1">Năm nhập học không hợp lệ.</small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="label-sidebar">Hệ đào tạo <span class="color-crimson">*</span></label>
                            <input type="number" id="he_dao_tao" name="he_dao_tao" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-he') ? 'is-invalid' : '' ?>" required
                                min="2" max="8" step="0.5" value="<?=khoahoc_escape(khoahoc_old_value('he_dao_tao', 'add'))?>" placeholder="Nhập số năm đào tạo">
                            <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-he'): ?>
                                <small class="text-danger mt-1">Hệ đào tạo không hợp lệ.</small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="label-sidebar">Ghi chú</label>
                            <textarea id="ghi_chu" name="ghi_chu" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-ghichu') ? 'is-invalid' : '' ?>" maxlength="2000"
                                placeholder="Nhập ghi chú"><?=khoahoc_escape(khoahoc_old_value('ghi_chu', 'add'))?></textarea>
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
                <h3 class="card-title">Danh sách Khóa học</h3>
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
                            <th style="width: 15%;">Tên khóa học</th>
                            <th style="width: 70%;">Ghi chú</th>
                            <th style="width: 10%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $num = 0;?>
                        <?php foreach($khoahoc__Get_All as $item):?>
                        <tr>
                            <td><?=++$num?></td>
                            <td class="text-center"><?=khoahoc_escape($item->ten_khoa_hoc)?></td>
                            <td><?=khoahoc_escape($item->ghi_chu)?></td>
                            <td>
                                <a href="javascript:void(0)" class="btn  btn-warning m-2" onclick="return update_obj(<?=(int)$item->id_khoa_hoc?>)">
                                    <i class="ri-edit-2-line"></i>
                                </a>
                                <a href="#" type="button" class="btn  btn-danger m-2"
                                    onclick="return delete_obj(<?=(int)$item->id_khoa_hoc?>)">
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
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ khóa học",
            "infoEmpty": "Hiển thị 0 - 0 của 0 khóa học",
            "infoFiltered": "(lọc từ _MAX_ khóa học)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ khóa học",
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

function update_obj(id_khoa_hoc, error_status = '') {
    $.ajax({
        url: 'quan-ly-khoa-hoc/update.php',
        method: 'POST',
        data: { 'id_khoa_hoc': id_khoa_hoc, 'error_status': error_status },
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
                text = 'Khóa học cần sửa không tồn tại.';
            }
            Swal.fire(title, text, 'error');
        }
    });
    return false;
}

function cancel_update() {
    $("#div_update").html('');
}

function delete_obj(id_khoa_hoc) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: 'Thao tác này sẽ xóa khóa học đã chọn.',
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
        form.action = 'quan-ly-khoa-hoc/action.php?req=delete';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id_khoa_hoc';
        idInput.value = id_khoa_hoc;
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

<?php if (isset($khoahoc_old_input['context']) && $khoahoc_old_input['context'] === 'update' && isset($khoahoc_old_input['id_khoa_hoc'])): ?>
window.addEventListener("load", function() {
    update_obj(<?=(int)$khoahoc_old_input['id_khoa_hoc']?>, '<?=$_GET['status'] ?? ''?>');
});
<?php endif; ?>
</script>
