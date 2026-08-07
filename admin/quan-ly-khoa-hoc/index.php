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
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Quản lý khóa học</h1>
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

    <section class="content">
        <form class="row form" action="quan-ly-khoa-hoc/action.php?req=add" method="post">
            <input type="hidden" name="csrf_token" value="<?=khoahoc_escape($_SESSION['csrf_token'])?>">
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
                            <label for="">Tên khóa học <span class="color-crimson">(*)</span></label>
                            <input type="text" id="ten_khoa_hoc" name="ten_khoa_hoc" class="form-control" required maxlength="50"
                                value="<?=khoahoc_escape(khoahoc_old_value('ten_khoa_hoc', 'add'))?>" placeholder="Nhập tên khóa học">
                        </div>
                        <div class="form-group">
                            <label for="">Năm nhập học <span class="color-crimson">(*)</span></label>
                            <input type="number" id="nam_nhap_hoc" name="nam_nhap_hoc" class="form-control" required
                                min="2006" max="2099" value="<?=khoahoc_escape(khoahoc_old_value('nam_nhap_hoc', 'add'))?>" placeholder="Nhập năm nhập học">
                        </div>
                        <div class="form-group">
                            <label for="">Hệ đào tạo <span class="color-crimson">(*)</span></label>
                            <input type="number" id="he_dao_tao" name="he_dao_tao" class="form-control" required
                                min="2" max="8" step="0.5" value="<?=khoahoc_escape(khoahoc_old_value('he_dao_tao', 'add'))?>" placeholder="Nhập số năm đào tạo">
                        </div>
                        <div class="form-group">
                            <label for="">Ghi chú</label>
                            <textarea id="ghi_chu" name="ghi_chu" class="form-control" maxlength="2000"
                                placeholder="Nhập ghi chú"><?=khoahoc_escape(khoahoc_old_value('ghi_chu', 'add'))?></textarea>
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
        "dom": "<'row'<'col-sm-12'l>><'row'<'col-sm-12'B>><'row'<'col-sm-12'f>>rtip",
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

function update_obj(id_khoa_hoc) {
    $.ajax({
        url: 'quan-ly-khoa-hoc/update.php',
        method: 'POST',
        data: { 'id_khoa_hoc': id_khoa_hoc },
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
                text = 'Khóa học cần sửa không tồn tại.';
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
    update_obj(<?=(int)$khoahoc_old_input['id_khoa_hoc']?>);
});
<?php endif; ?>
</script>
