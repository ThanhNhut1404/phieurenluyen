<?php
    // Nhựt sửa lỗi: tạo CSRF token cho thao tác thêm/sửa/xóa học kỳ.
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    // Nhựt sửa lỗi: giữ lại dữ liệu đã nhập khi validate lỗi để form thêm không bị mất nội dung.
    $hocky_old_input = isset($_SESSION['hocky_old_input']) && is_array($_SESSION['hocky_old_input']) ? $_SESSION['hocky_old_input'] : array();
    if (isset($hocky_old_input['context']) && $hocky_old_input['context'] === 'add') {
        unset($_SESSION['hocky_old_input']);
    }

    if (!function_exists('hocky_escape')) {
        function hocky_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('hocky_format_date')) {
        function hocky_format_date($value) {
            // Nhựt sửa lỗi: Hiển thị ngày trong danh sách theo dd/mm/yyyy nhưng vẫn giữ ngày gốc để DataTable sắp xếp đúng.
            $date = DateTime::createFromFormat('Y-m-d', (string)$value);
            if (!$date) {
                return $value;
            }
            return $date->format('d/m/Y');
        }
    }

    if (!function_exists('hocky_old_value')) {
        function hocky_old_value($field, $context, $default = '') {
            global $hocky_old_input;
            if (isset($hocky_old_input['context']) && $hocky_old_input['context'] === $context && isset($hocky_old_input[$field])) {
                return $hocky_old_input[$field];
            }
            return $default;
        }
    }

    if (!function_exists('hocky_format_namhoc_combobox')) {
        function hocky_format_namhoc_combobox($item) {
            $bd = DateTime::createFromFormat('Y-m-d', (string)$item->ngay_bat_dau);
            $kt = DateTime::createFromFormat('Y-m-d', (string)$item->ngay_ket_thuc);
            if ($bd && $kt) {
                return $item->ten_nam_hoc . ' (' . $bd->format('j/n/y') . ' - ' . $kt->format('j/n/y') . ')';
            }
            return $item->ten_nam_hoc;
        }
    }

    $hocky__Get_All = $hocky->hocky__Get_All();
    $namhoc__Get_All = $namhoc->namhoc__Get_All();
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Quản lý học kỳ</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                        <li class="breadcrumb-item active">Quản lý học kỳ</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <form class="row form" action="quan-ly-hoc-ky/action.php?req=add" method="post">
            <input type="hidden" name="csrf_token" value="<?=hocky_escape($_SESSION['csrf_token'])?>">
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
                            <label for="">Năm học <span class="color-crimson">(*)</span></label>
                            <select class="form-control" name="id_nam_hoc" required>
                                <option value="">Chọn năm học</option>
                                <?php foreach ($namhoc__Get_All as $item):?>
                                <option value="<?=(int)$item->id_nam_hoc?>" <?=((int)hocky_old_value('id_nam_hoc', 'add') === (int)$item->id_nam_hoc) ? 'selected' : ''?>><?=hocky_escape(hocky_format_namhoc_combobox($item))?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Tên học kỳ <span class="color-crimson">(*)</span></label>
                            <input type="text" id="ten_hoc_ky" name="ten_hoc_ky" class="form-control" required maxlength="50"
                                value="<?=hocky_escape(hocky_old_value('ten_hoc_ky', 'add'))?>" placeholder="Nhập tên học kỳ">
                        </div>
                        <div class="form-group">
                            <label for="">Ngày bắt đầu <span class="color-crimson">(*)</span></label>
                            <input type="date" id="ngay_bat_dau" name="ngay_bat_dau" class="form-control" required
                                value="<?=hocky_escape(hocky_old_value('ngay_bat_dau', 'add'))?>">
                        </div>
                        <div class="form-group">
                            <label for="">Ngày kết thúc <span class="color-crimson">(*)</span></label>
                            <input type="date" id="ngay_ket_thuc" name="ngay_ket_thuc" class="form-control" required
                                value="<?=hocky_escape(hocky_old_value('ngay_ket_thuc', 'add'))?>">
                        </div>
                        <div class="form-group">
                            <label for="">Ghi chú</label>
                            <textarea id="ghi_chu" name="ghi_chu" class="form-control" maxlength="2000"
                                placeholder="Nhập ghi chú"><?=hocky_escape(hocky_old_value('ghi_chu', 'add'))?></textarea>
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
                            <th style="width: 10%;">Tên học kỳ</th>
                            <th style="width: 15%;">Năm học</th>
                            <th style="width: 15%;">Ngày bắt đầu</th>
                            <th style="width: 15%;">Ngày kết thúc</th>
                            <th style="width: 30%;">Ghi chú</th>
                            <th style="width: 10%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $num = 0;?>
                        <?php foreach($hocky__Get_All as $item):?>
                        <tr>
                            <td><?=++$num?></td>
                            <td class="text-center"><?=hocky_escape($item->ten_hoc_ky)?></td>
                            <td class="text-center"><?=hocky_escape($item->ten_nam_hoc ?? 'Năm học không tồn tại')?></td>
                            <td class="text-center" data-order="<?=hocky_escape($item->ngay_bat_dau)?>"><?=hocky_escape(hocky_format_date($item->ngay_bat_dau))?></td>
                            <td class="text-center" data-order="<?=hocky_escape($item->ngay_ket_thuc)?>"><?=hocky_escape(hocky_format_date($item->ngay_ket_thuc))?></td>
                            <td><?=hocky_escape($item->ghi_chu)?></td>
                            <td>
                                <a href="javascript:void(0)" class="btn btn-warning m-2" onclick="return update_obj(<?=(int)$item->id_hoc_ky?>)">
                                    <i class="ri-edit-2-line"></i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-danger m-2" onclick="return delete_obj(<?=(int)$item->id_hoc_ky?>)">
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
        "dom": "<'row'<'col-sm-12'l>><'row'<'col-sm-12'B>><'row'<'col-sm-12'f>>rtip",
        "pagingType": "full_numbers",
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "language": {
            "decimal": ",",
            "thousands": ".",
            "emptyTable": "Không có dữ liệu trong bảng",
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ học kỳ",
            "infoEmpty": "Hiển thị 0 - 0 của 0 học kỳ",
            "infoFiltered": "(lọc từ _MAX_ học kỳ)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ học kỳ",
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

function update_obj(id_hoc_ky) {
    $.ajax({
        url: 'quan-ly-hoc-ky/update.php',
        method: 'POST',
        data: { 'id_hoc_ky': id_hoc_ky },
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
                text = 'Học kỳ cần sửa không tồn tại.';
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

function delete_obj(id_hoc_ky) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: 'Thao tác này sẽ xóa học kỳ đã chọn.',
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
        form.action = 'quan-ly-hoc-ky/action.php?req=delete';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id_hoc_ky';
        idInput.value = id_hoc_ky;
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

<?php if (isset($hocky_old_input['context']) && $hocky_old_input['context'] === 'update' && isset($hocky_old_input['id_hoc_ky'])): ?>
window.addEventListener("load", function() {
    update_obj(<?=(int)$hocky_old_input['id_hoc_ky']?>);
});
<?php endif; ?>
</script>
