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
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Quản lý trình độ</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Quản lý trình độ</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <form class="row form" action="quan-ly-trinh-do/action.php?req=add" method="post">
            <input type="hidden" name="csrf_token" value="<?=trinhdo_escape($_SESSION['csrf_token'])?>">
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
                            <label for="">Tên trình độ <span class="color-crimson">(*)</span></label>
                            <input type="text" id="ten_trinh_do" name="ten_trinh_do" class="form-control" required maxlength="50"
                                value="<?=trinhdo_escape(trinhdo_old_value('ten_trinh_do', 'add'))?>" placeholder="Nhập tên trình độ">
                        </div>
                        <div class="form-group">
                            <label for="">Ghi chú</label>
                            <textarea id="ghi_chu" name="ghi_chu" class="form-control" maxlength="2000"
                                placeholder="Nhập ghi chú"><?=trinhdo_escape(trinhdo_old_value('ghi_chu', 'add'))?></textarea>
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
                            <th>Tên trình độ</th>
                            <th>Ghi chú</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $num = 0;?>
                        <?php foreach($trinhdo__Get_All as $item):?>
                        <tr>
                            <td><?=++$num?></td>
                            <td><?=trinhdo_escape($item->ten_trinh_do)?></td>
                            <td><?=trinhdo_escape($item->ghi_chu)?></td>
                            <td>
                                <a href="javascript:void(0)" class="btn  btn-warning m-2"
                                    onclick="return update_obj(<?=(int)$item->id_trinh_do?>)">
                                    <i class="fas fa-edit"></i>
                                  </a>
                                <a href="javascript:void(0)" class="btn  btn-danger m-2"
                                    onclick="return delete_obj(<?=(int)$item->id_trinh_do?>)">
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
        // Nhựt sửa lỗi: đồng bộ phân trang, tìm kiếm và hàng nút xuất dữ liệu như các màn quản lý khác.
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

function update_obj(id_trinh_do) {
    $.ajax({
        url: 'quan-ly-trinh-do/update.php',
        method: 'POST',
        data: { 'id_trinh_do': id_trinh_do },
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
                text = 'Trình độ cần sửa không tồn tại.';
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
    update_obj(<?=(int)$trinhdo_old_input['id_trinh_do']?>);
});
<?php endif; ?>
</script>
