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
     <section class="content-header pb-0">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý Ngành học</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý ngành học</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <?php $is_add_error = isset($nganhhoc_old_input['context']) && $nganhhoc_old_input['context'] === 'add'; ?>

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
         <form class="row form" action="quan-ly-nganh-hoc/action.php?req=add" method="post"
             enctype="multipart/form-data">
             <input type="hidden" name="csrf_token" value="<?=nganhhoc_escape($_SESSION['csrf_token'])?>">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title">Thêm mới Ngành học</h3>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-md-6 form-group">
                                 <label class="label-sidebar">Khoa <span class="color-crimson">*</span></label>
                                 <select class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-khoa') ? 'is-invalid' : '' ?>" name="id_khoa" required>
                                     <option value="">Chọn khoa</option>
                                     <?php foreach ($khoa__Get_All as $item):?>
                                     <option value="<?=(int)$item->id_khoa?>" <?=((int)nganhhoc_old_value('id_khoa', 'add') === (int)$item->id_khoa) ? "selected" : ""?>><?=nganhhoc_escape($item->ten_khoa)?></option>
                                     <?php endforeach; ?>
                                 </select>
                                 <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-khoa'): ?>
                                     <small class="text-danger mt-1">Khoa không hợp lệ.</small>
                                 <?php endif; ?>
                             </div>
                             <div class="col-md-6 form-group">
                                 <label class="label-sidebar">Tên ngành học <span class="color-crimson">*</span></label>
                                 <!-- Nhựt sửa lỗi: giới hạn tên ngành học ở client khớp với validate server-side và DB. -->
                                 <input type="text" id="ten_nganh_hoc" name="ten_nganh_hoc" class="form-control <?= ($is_add_error && in_array($_GET['status'] ?? '', ['duplicate-nganh-hoc', 'invalid-ten-nganh-hoc'])) ? 'is-invalid' : '' ?>" required maxlength="50"
                                     value="<?=nganhhoc_escape(nganhhoc_old_value('ten_nganh_hoc', 'add'))?>"
                                     placeholder="Nhập tên ngành học">
                                 <?php if ($is_add_error && isset($_GET['status'])): ?>
                                     <?php if ($_GET['status'] == 'duplicate-nganh-hoc'): ?>
                                         <small class="text-danger mt-1">Tên ngành học đã tồn tại trong khoa đã chọn.</small>
                                     <?php elseif ($_GET['status'] == 'invalid-ten-nganh-hoc'): ?>
                                         <small class="text-danger mt-1">Tên ngành học không được để trống và tối đa 50 ký tự.</small>
                                     <?php endif; ?>
                                 <?php endif; ?>
                             </div>
                         </div>
                         <div class="form-group">
                             <label class="label-sidebar">Ghi chú</label>
                             <textarea id="ghi_chu" name="ghi_chu" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-ghichu') ? 'is-invalid' : '' ?>" maxlength="2000" rows="2"
                                 placeholder="Nhập ghi chú"><?=nganhhoc_escape(nganhhoc_old_value('ghi_chu', 'add'))?></textarea>
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
                 <h3 class="card-title">Danh sách Ngành học</h3>
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
                             <th style="width: 15%;">Khoa</th>
                             <th style="width: 35%;">Tên ngành học</th>
                             <th style="width: 35%;">Ghi chú</th>
                             <th style="width: 10%;">Thao tác</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($nganhhoc__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <td> <?=nganhhoc_escape($item->ten_khoa ?? 'Khoa không tồn tại')?></td>
                             <td class="text-center"><?=nganhhoc_escape($item->ten_nganh_hoc)?></td>
                             <td><?=nganhhoc_escape($item->ghi_chu)?></td>
                             <td>
                                 <a href="javascript:void(0)" class="btn btn-warning" onclick="return update_obj(<?=(int)$item->id_nganh_hoc?>)">
                                     <i class="ri-edit-2-line"></i>
                                 </a>
                                 <a href="#" type="button" class="btn btn-danger"
                                     onclick="return delete_obj(<?=(int)$item->id_nganh_hoc?>)">
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
        // Nhựt sửa lỗi: đưa dropdown chọn số dòng lên hàng riêng phía trên các nút xuất dữ liệu.
        "dom": "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end align-items-center'B>>rt<'row mt-3 mb-n2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
        "pagingType": "full_numbers",
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "language": {
            "decimal": ",",
            "thousands": ".",
            "emptyTable": "Không có dữ liệu trong bảng",
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ ngành học",
            "infoEmpty": "Hiển thị 0 - 0 của 0 ngành học",
            "infoFiltered": "(lọc từ _MAX_ ngành học)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ ngành học",
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
                    <label class="label-sidebar">Khoa:</label>
                    <select id="filter_khoa" class="form-control form-control-sm">
                        <option value="">-- Tất cả khoa --</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label class="label-sidebar">Ngành học:</label>
                    <select id="filter_nganh_hoc" class="form-control form-control-sm">
                        <option value="">-- Tất cả ngành học --</option>
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
                var selectedKhoa = $('#filter_khoa').val() || "";
                var selectedNganh = $('#filter_nganh_hoc').val() || "";
                
                var khoaOptions = [];
                var nganhHocOptions = [];
                
                table.rows().every(function() {
                    var data = this.data();
                    var khoa = $('<div>').html(data[1]).text().trim();
                    var nganh = $('<div>').html(data[2]).text().trim();
                    
                    if (khoa !== '' && khoaOptions.indexOf(khoa) === -1) khoaOptions.push(khoa);
                    
                    if (selectedKhoa === "" || khoa === selectedKhoa) {
                        if (nganh !== '' && nganhHocOptions.indexOf(nganh) === -1) nganhHocOptions.push(nganh);
                    }
                });
                
                var select = $('#filter_khoa');
                select.empty().append('<option value="">-- Tất cả khoa --</option>');
                khoaOptions.sort().forEach(function(opt) {
                    select.append('<option value="'+opt+'" '+(opt === selectedKhoa ? 'selected' : '')+'>'+opt+'</option>');
                });
                
                select = $('#filter_nganh_hoc');
                select.empty().append('<option value="">-- Tất cả ngành học --</option>');
                nganhHocOptions.sort().forEach(function(opt) {
                    select.append('<option value="'+opt+'" '+(opt === selectedNganh ? 'selected' : '')+'>'+opt+'</option>');
                });
            }

            $('#filter_khoa').on('change', function() {
                $('#filter_nganh_hoc').val('');
                updateCascadeDropdowns();
            });
            $('#filter_nganh_hoc').on('change', function() {
                updateCascadeDropdowns();
            });

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
                var khoaVal = $('#filter_khoa').val();
                var nganhVal = $('#filter_nganh_hoc').val();
                
                table.column(1).search(khoaVal ? '^' + $.fn.dataTable.util.escapeRegex(khoaVal) + '$' : '', true, false)
                     .column(2).search(nganhVal ? '^' + $.fn.dataTable.util.escapeRegex(nganhVal) + '$' : '', true, false)
                     .draw();
                $('#custom-filter-menu').fadeOut(200);
            });

            $('#btn-cancel-filter').on('click', function() {
                $('#filter_khoa').val('');
                $('#filter_nganh_hoc').val('');
                updateCascadeDropdowns();
                table.column(1).search('').column(2).search('').draw();
                $('#custom-filter-menu').fadeOut(200);
            });
        }
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

function update_obj(id_nganh_hoc, error_status = '') {
    $.ajax({
        url: 'quan-ly-nganh-hoc/update.php',
        method: 'POST',
        data: { 'id_nganh_hoc': id_nganh_hoc, 'error_status': error_status },
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
    update_obj(<?=(int)$nganhhoc_old_input['id_nganh_hoc']?>, '<?=$_GET['status'] ?? ''?>');
});
<?php endif; ?>
 </script>



