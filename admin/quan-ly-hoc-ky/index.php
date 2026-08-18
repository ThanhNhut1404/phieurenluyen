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
    <section class="content-header pb-0">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Quản lý Học kỳ</h1>
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

    <?php $is_add_error = isset($hocky_old_input['context']) && $hocky_old_input['context'] === 'add'; ?>
    
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
        <form class="row form" action="quan-ly-hoc-ky/action.php?req=add" method="post">
            <input type="hidden" name="csrf_token" value="<?=hocky_escape($_SESSION['csrf_token'])?>">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Thêm mới Học kỳ</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="label-sidebar">Năm học <span class="color-crimson">*</span></label>
                                <select class="form-control" name="id_nam_hoc" required>
                                    <option value="">Chọn năm học</option>
                                    <?php foreach ($namhoc__Get_All as $item):?>
                                    <option value="<?=(int)$item->id_nam_hoc?>" <?=((int)hocky_old_value('id_nam_hoc', 'add') === (int)$item->id_nam_hoc) ? 'selected' : ''?>><?=hocky_escape(hocky_format_namhoc_combobox($item))?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="label-sidebar">Tên học kỳ <span class="color-crimson">*</span></label>
                                <input type="text" id="ten_hoc_ky" name="ten_hoc_ky" class="form-control <?= ($is_add_error && in_array($_GET['status'] ?? '', ['duplicate', 'invalid-ten-hoc-ky'])) ? 'is-invalid' : '' ?>" required maxlength="50"
                                    value="<?=hocky_escape(hocky_old_value('ten_hoc_ky', 'add'))?>" placeholder="Nhập tên học kỳ">
                                <?php if ($is_add_error && isset($_GET['status'])): ?>
                                    <?php if ($_GET['status'] == 'duplicate'): ?>
                                        <small class="text-danger mt-1">Học kỳ này đã tồn tại trong năm học được chọn.</small>
                                    <?php elseif ($_GET['status'] == 'invalid-ten-hoc-ky'): ?>
                                        <small class="text-danger mt-1">Tên học kỳ không được để trống và tối đa 50 ký tự.</small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="label-sidebar">Ngày bắt đầu <span class="color-crimson">*</span></label>
                                <input type="date" id="ngay_bat_dau" name="ngay_bat_dau" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-ngay') ? 'is-invalid' : '' ?>" required
                                    value="<?=hocky_escape(hocky_old_value('ngay_bat_dau', 'add'))?>">
                                <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-ngay'): ?>
                                    <small class="text-danger mt-1">Ngày bắt đầu phải nhỏ hơn ngày kết thúc.</small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="label-sidebar">Ngày kết thúc <span class="color-crimson">*</span></label>
                                <input type="date" id="ngay_ket_thuc" name="ngay_ket_thuc" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-ngay') ? 'is-invalid' : '' ?>" required
                                    value="<?=hocky_escape(hocky_old_value('ngay_ket_thuc', 'add'))?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="label-sidebar">Ghi chú</label>
                            <textarea id="ghi_chu" name="ghi_chu" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-ghichu') ? 'is-invalid' : '' ?>" maxlength="2000"
                                placeholder="Nhập ghi chú"><?=hocky_escape(hocky_old_value('ghi_chu', 'add'))?></textarea>
                            <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-ghichu'): ?>
                                <small class="text-danger mt-1">Ghi chú không được vượt quá 2000 ký tự.</small>
                            <?php endif; ?>
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
                <h3 class="card-title">Danh sách Học kỳ</h3>
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
        "dom": "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end align-items-center'B>>rt<'row mt-3 mb-n2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
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
        },
        {
            "text": "<i class='fas fa-filter'></i>",
            "titleAttr": "Bộ lọc",
            "className": "btn btn-sm btn-custom-filter ml-1",
            "attr": {
                "id": "btn-filter-dropdown"
            }
        }],
        "initComplete": function() {
            var filterHtml = `
            <style>
            .dataTables_wrapper .dt-buttons .btn-custom-filter {
                background-color: #0f2a5a !important;
                border: 1px solid #0f2a5a !important;
                color: #fff !important;
                border-radius: 4px !important;
                padding: 6px 12px !important;
                font-size: 14px !important;
                font-weight: 500 !important;
                box-shadow: none !important;
                transition: all 0.15s ease-in-out !important;
                display: inline-flex !important;
                align-items: center !important;
            }
            .dataTables_wrapper .dt-buttons .btn-custom-filter:hover {
                background-color: transparent !important;
                border-color: #0f2a5a !important;
                color: #0f2a5a !important;
            }
            #custom-filter-menu {
                display: none;
                position: absolute;
                right: 0;
                top: 100%;
                margin-top: 5px;
                width: 300px;
                background: #fff;
                border: 1px solid rgba(0,0,0,.15);
                border-radius: .25rem;
                box-shadow: 0 .5rem 1rem rgba(0,0,0,.175);
                z-index: 1050;
            }
            </style>
            <div id="custom-filter-menu" class="p-3">
                <div class="form-group mb-2">
                    <label class="label-sidebar">Năm học:</label>
                    <select id="filter_nam_hoc" class="form-control form-control-sm">
                        <option value="">-- Tất cả năm học --</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label class="label-sidebar">Học kỳ:</label>
                    <select id="filter_hoc_ky" class="form-control form-control-sm">
                        <option value="">-- Tất cả học kỳ --</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-cancel-custom mr-2 font-weight-bold" id="btn-cancel-filter">Hủy</button>
                    <button type="button" class="btn btn-success font-weight-bold" id="btn-apply-filter">Áp dụng</button>
                </div>
            </div>`;
            
            var $btn = $('#btn-filter-dropdown');
            $btn.wrap('<div style="position: relative; display: inline-block;"></div>');
            $btn.parent().append(filterHtml);

            var table = $('#tablejs').DataTable();

            function updateCascadeDropdowns() {
                var selectedNam = $('#filter_nam_hoc').val() || "";
                var selectedHocKy = $('#filter_hoc_ky').val() || "";
                
                var namHocOptions = [];
                var hocKyOptions = [];
                
                table.rows().every(function() {
                    var data = this.data();
                    var nam = $('<div>').html(data[2]).text().trim();
                    var hocky = $('<div>').html(data[1]).text().trim();
                    
                    if (namHocOptions.indexOf(nam) === -1) namHocOptions.push(nam);
                    
                    if (selectedNam === "" || nam === selectedNam) {
                        if (hocKyOptions.indexOf(hocky) === -1) hocKyOptions.push(hocky);
                    }
                });
                
                var select = $('#filter_nam_hoc');
                select.empty().append('<option value="">-- Tất cả năm học --</option>');
                namHocOptions.sort().forEach(function(opt) {
                    select.append('<option value="'+opt+'">'+opt+'</option>');
                });
                if (namHocOptions.indexOf(selectedNam) !== -1) {
                    select.val(selectedNam);
                } else {
                    select.val('');
                }
                
                select = $('#filter_hoc_ky');
                select.empty().append('<option value="">-- Tất cả học kỳ --</option>');
                hocKyOptions.sort().forEach(function(opt) {
                    select.append('<option value="'+opt+'">'+opt+'</option>');
                });
                if (hocKyOptions.indexOf(selectedHocKy) !== -1) {
                    select.val(selectedHocKy);
                } else {
                    select.val('');
                }
            }

            $('#filter_nam_hoc').on('change', function() {
                $('#filter_hoc_ky').val(''); // Reset child
                updateCascadeDropdowns();
            });
            $('#filter_hoc_ky').on('change', function() {
                updateCascadeDropdowns();
            });

            // Initial population
            updateCascadeDropdowns();

            // Custom dropdown toggle logic
            $btn.on('click', function(e) {
                e.stopPropagation();
                $('#custom-filter-menu').fadeToggle(200);
            });

            $('#custom-filter-menu').on('click', function(e) {
                e.stopPropagation();
            });

            $(document).on('click', function() {
                $('#custom-filter-menu').fadeOut(200);
            });

            $('#btn-apply-filter').on('click', function() {
                var namVal = $('#filter_nam_hoc').val();
                var hocKyVal = $('#filter_hoc_ky').val();
                
                table.column(2).search(namVal ? '^' + $.fn.dataTable.util.escapeRegex(namVal) + '$' : '', true, false)
                     .column(1).search(hocKyVal ? '^' + $.fn.dataTable.util.escapeRegex(hocKyVal) + '$' : '', true, false)
                     .draw();
                $('#custom-filter-menu').fadeOut(200);
            });

            $('#btn-cancel-filter').on('click', function() {
                $('#filter_nam_hoc').val('');
                $('#filter_hoc_ky').val('');
                updateCascadeDropdowns();
                table.column(2).search('')
                     .column(1).search('')
                     .draw();
                $('#custom-filter-menu').fadeOut(200);
            });
        }
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
            btn.html('Đóng lại').removeClass('btn-primary').addClass('btn-secondary');
        } else {
            btn.html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-secondary').addClass('btn-primary');
        }
    });
}

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

function update_obj(id_hoc_ky, error_status = '') {
    $.ajax({
        url: 'quan-ly-hoc-ky/update.php',
        method: 'POST',
        data: { 'id_hoc_ky': id_hoc_ky, 'error_status': error_status },
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
}

function delete_obj(id_hoc_ky) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        html: 'Thao tác này sẽ xóa <b>Học kỳ</b><br>đã chọn và không thể hoàn tác.',
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
    update_obj(<?=(int)$hocky_old_input['id_hoc_ky']?>, '<?=$_GET['status'] ?? ''?>');
});
<?php endif; ?>
</script>








