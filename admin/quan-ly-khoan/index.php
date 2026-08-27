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

    $khoan_old_input = isset($_SESSION['khoan_old_input']) && is_array($_SESSION['khoan_old_input']) ? $_SESSION['khoan_old_input'] : array();
    if (isset($khoan_old_input['context']) && $khoan_old_input['context'] === 'add') {
        unset($_SESSION['khoan_old_input']);
    }

    if (!function_exists('khoan_escape')) {
        function khoan_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('khoan_old_value')) {
        function khoan_old_value($field, $context, $default = '') {
            global $khoan_old_input;
            if (isset($khoan_old_input['context']) && $khoan_old_input['context'] === $context && isset($khoan_old_input[$field])) {
                return $khoan_old_input[$field];
            }
            return $default;
        }
    }

    $khoan__Get_All = $khoan->khoan__Get_All();
    $dieu__Get_All = $dieu->dieu__Get_All();

    // quân sửa: Lấy danh sách id_dieu đã được sử dụng
    $used_dieu = [];
    foreach ($khoan__Get_All as $k) {
        $used_dieu[] = $k->id_dieu;
    }
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header pb-0">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý Khoản</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý khoản</li>
                     </ol>
                 </div>
             </div>

         </div><!-- /.container-fluid -->
     </section>

     <?php $is_add_error = isset($khoan_old_input['context']) && $khoan_old_input['context'] === 'add'; ?>

     <!-- Nhựt sửa: Thêm nút bật/tắt form thêm mới -->
     <section class="content mb-2">
         <button type="button" class="btn <?= $is_add_error ? 'btn-cancel-custom' : 'btn-success' ?> font-weight-bold" id="btn-toggle-add" onclick="toggle_add_form()">
             <i class="fas <?= $is_add_error ? 'fa-times' : 'fa-plus' ?>"></i> <?= $is_add_error ? '' : 'Thêm mới' ?>
         </button>
     </section>

     <!-- Nhựt sửa: Ẩn form thêm mới mặc định -->
     <section class="content" id="div_add_form" <?= $is_add_error ? '' : 'style="display: none;"' ?>>
         <form class="row form" action="quan-ly-khoan/action.php?req=add" method="post" enctype="multipart/form-data">
             <input type="hidden" name="csrf_token" value="<?=khoan_escape($_SESSION['csrf_token'])?>">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title">Thêm mới Khoản</h3>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="id_dieu">Điều <span class="color-crimson">*</span></label>
                                     <select class="form-control <?= ($is_add_error && in_array($_GET['status'] ?? '', ['invalid-dieu', 'duplicate-dieu'])) ? 'is-invalid' : '' ?>" name="id_dieu" id="id_dieu" required>
                                         <option value="">Chọn Điều</option>
                                         <?php foreach ($dieu__Get_All as $item):?>
                                             <?php if(!in_array($item->id_dieu, $used_dieu) || khoan_old_value('id_dieu', 'add') == $item->id_dieu): ?>
                                                 <option value="<?=$item->id_dieu?>" <?= khoan_old_value('id_dieu', 'add') == $item->id_dieu ? 'selected' : '' ?>><?=$item->ten_dieu?></option>
                                             <?php endif; ?>
                                         <?php endforeach; ?>
                                     </select>
                                     <?php if ($is_add_error && isset($_GET['status'])): ?>
                                         <?php if ($_GET['status'] == 'invalid-dieu'): ?>
                                             <small class="text-danger mt-1">Vui lòng chọn Điều hợp lệ.</small>
                                         <?php elseif ($_GET['status'] == 'duplicate-dieu'): ?>
                                             <small class="text-danger mt-1">Điều này đã được sử dụng.</small>
                                         <?php endif; ?>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="ten_khoan">Tên khoản <span class="color-crimson">*</span></label>
                                     <input type="text" id="ten_khoan" name="ten_khoan" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-ten') ? 'is-invalid' : '' ?>" required
                                         placeholder="Nhập tên khoản" value="<?=khoan_escape(khoan_old_value('ten_khoan', 'add'))?>">
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-ten'): ?>
                                         <small class="text-danger mt-1">Tên khoản không được để trống.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                      <label class="label-sidebar" for="can_tren">Điểm tối đa <span class="color-crimson">*</span></label>
                                      <input type="number" id="can_tren" name="can_tren" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-diem') ? 'is-invalid' : '' ?>" required
                                          placeholder="Nhập điểm tối đa" value="<?=khoan_escape(khoan_old_value('can_tren', 'add'))?>">
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-diem'): ?>
                                         <small class="text-danger mt-1">Điểm tối đa không hợp lệ.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <!-- quân sửa: Thêm ô nhập Số lượng mục tối đa -->
                                 <div class="form-group">
                                     <label class="label-sidebar" for="so_luong_muc">Số lượng mục tối đa <span class="color-crimson">*</span></label>
                                     <input type="number" id="so_luong_muc" name="so_luong_muc" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-soluong') ? 'is-invalid' : '' ?>" value="<?=khoan_escape(khoan_old_value('so_luong_muc', 'add', '10'))?>" required
                                         placeholder="Nhập giới hạn số lượng mục" min="1">
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-soluong'): ?>
                                         <small class="text-danger mt-1">Số lượng mục không hợp lệ.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                         </div>
                         <div class="form-group">
                             <label class="label-sidebar" for="ghi_chu">Nội dung chi tiết</label>
                             <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                                 placeholder="Nhập nội dung chi tiết"><?=khoan_escape(khoan_old_value('ghi_chu', 'add'))?></textarea>
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
                 <h3 class="card-title">Danh sách Khoản</h3>
                 <div class="card-tools">
                     <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                         <i class="fas fa-minus"></i>
                     </button>
                 </div>
             </div>
             <!-- /.card-header -->
             <div class="card-body">
                  <table id="tablejs" class="table table-bordered table-striped display responsive" width="100%">
                      <thead>
                          <tr>
                              <th style="width: 3%; white-space: nowrap;">STT</th>
                              <th style="width: 8%; white-space: nowrap;">Điều</th>
                              <th style="width: 35%; white-space: nowrap;">Tên khoản</th>
                              <th style="width: 8%; white-space: nowrap;">Điểm tối đa</th>
                              <!-- quân sửa: Bổ sung cột Số lượng mục vào bảng danh sách Khoản -->
                              <th style="width: 10%; white-space: nowrap;">Số lượng mục</th>
                              <th style="width: 21%; white-space: nowrap;">Ghi chú</th>
                              <th style="width: 15%; white-space: nowrap;">Thao tác</th>
                          </tr>
                      </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($khoan__Get_All as $item):?>
                         <tr>
                              <td><?=++$num?></td>
                              <td class="text-center" style="text-align: center !important;"> <?=htmlspecialchars($dieu->dieu__Get_By_Id($item->id_dieu)->ten_dieu ?? "", ENT_QUOTES, 'UTF-8')?></td>
                              <td><?=htmlspecialchars($item->ten_khoan ?? "", ENT_QUOTES, 'UTF-8')?></td>
                              <td class="text-center" style="text-align: center !important;"><?=htmlspecialchars($item->can_tren ?? "", ENT_QUOTES, 'UTF-8')?></td>
                              <!-- quân sửa: Hiển thị giá trị Số lượng mục -->
                              <td class="text-center" style="text-align: center !important;"><?=htmlspecialchars($item->so_luong_muc ?? "", ENT_QUOTES, 'UTF-8')?></td>
                              <td><?=htmlspecialchars($item->ghi_chu ?? "", ENT_QUOTES, 'UTF-8')?></td>
                             <td>
                                 <?php if($khoan->khoan__Is_Edit_Locked($item->id_khoan)): ?>
                                     <button class="btn btn-warning disabled m-2" title="Mẫu đã phát sinh đợt chấm cho nội dung này.">
                                         <i class="ri-edit-2-line"></i>
                                     </button>
                                 <?php else: ?>
                                     <a href="javascript:void(0)" class="btn btn-warning m-2"
                                         onclick="update_obj(<?=$item->id_khoan?>)">
                                         <i class="ri-edit-2-line"></i>
                                     </a>
                                 <?php endif; ?>
                                 <a href="javascript:void(0)" class="btn btn-danger m-2"
                                     onclick="return confirm_delete_sweet('quan-ly-khoan/action.php?req=delete&id_khoan=<?=$item->id_khoan?>&csrf_token=<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>', 'Khoản')">
                                     <i class="ri-delete-bin-line"></i>
                                 </a>
                                 <a href="javascript:void(0)" class="btn btn-secondary m-2" title="Nhân bản Khoản"
                                     onclick="return confirm_copy_sweet('quan-ly-khoan/action.php?req=copy&id_khoan=<?=$item->id_khoan?>&csrf_token=<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>')">
                                     <i class="ri-file-copy-line"></i>
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
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ khoản",
            "infoEmpty": "Hiển thị 0 - 0 của 0 khoản",
            "infoFiltered": "(lọc từ _MAX_ khoản)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ khoản",
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
        }, {
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
                width: 320px;
                background: #fff;
                border: 1px solid rgba(0,0,0,.15);
                border-radius: .25rem;
                box-shadow: 0 .5rem 1rem rgba(0,0,0,.175);
                z-index: 1050;
            }
            </style>
            <div id="custom-filter-menu" class="p-3 text-left">
                <div class="form-group mb-2">
                    <label class="label-sidebar">Điều:</label>
                    <select id="filter_dieu" class="form-control form-control-sm">
                        <option value="">-- Tất cả Điều --</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label class="label-sidebar">Khoản:</label>
                    <select id="filter_khoan" class="form-control form-control-sm">
                        <option value="">-- Tất cả Khoản --</option>
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
                var selectedDieu = $('#filter_dieu').val() || "";
                var selectedKhoan = $('#filter_khoan').val() || "";
                
                var dieuOptions = [];
                var khoanOptions = [];
                
                table.rows().every(function() {
                    var data = this.data();
                    var dieu = $('<div>').html(data[1]).text().trim();
                    var khoan = $('<div>').html(data[2]).text().trim();
                    
                    if (dieu !== '' && dieuOptions.indexOf(dieu) === -1) dieuOptions.push(dieu);
                    
                    if (selectedDieu === "" || dieu === selectedDieu) {
                        if (khoan !== '' && khoanOptions.indexOf(khoan) === -1) khoanOptions.push(khoan);
                    }
                });
                
                function populate(selectId, options, currentValue, defaultText) {
                    var select = $('#' + selectId);
                    select.empty().append('<option value="">' + defaultText + '</option>');
                    options.sort().forEach(function(opt) {
                        select.append('<option value="'+opt+'">'+opt+'</option>');
                    });
                    if (options.indexOf(currentValue) !== -1) {
                        select.val(currentValue);
                    } else {
                        select.val('');
                    }
                }
                
                populate('filter_dieu', dieuOptions, selectedDieu, '-- Tất cả Điều --');
                populate('filter_khoan', khoanOptions, selectedKhoan, '-- Tất cả Khoản --');
            }

            $('#filter_dieu').on('change', function() {
                $('#filter_khoan').val('');
                updateCascadeDropdowns();
            });

            $('#filter_khoan').on('change', function() {
                updateCascadeDropdowns();
            });

            updateCascadeDropdowns();

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
                var dieuVal = $('#filter_dieu').val();
                var khoanVal = $('#filter_khoan').val();
                
                table.column(1).search(dieuVal ? '^' + $.fn.dataTable.util.escapeRegex(dieuVal) + '$' : '', true, false)
                     .column(2).search(khoanVal ? '^' + $.fn.dataTable.util.escapeRegex(khoanVal) + '$' : '', true, false)
                     .draw();
                $('#custom-filter-menu').fadeOut(200);
            });

            $('#btn-cancel-filter').on('click', function() {
                $('#filter_dieu').val('');
                $('#filter_khoan').val('');
                updateCascadeDropdowns();
                table.column(1).search('')
                     .column(2).search('')
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
            btn.html('<i class="fas fa-times"></i>').removeClass('btn-success').addClass('btn-cancel-custom');
        } else {
            btn.html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-cancel-custom').addClass('btn-success');
        }
    });
}

function update_obj(id_khoan, error_status = '') {
    $.ajax({
        url: 'quan-ly-khoan/update.php',
        method: 'POST',
        data: { 'id_khoan': id_khoan, 'error_status': error_status },
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
                text = 'Khoản cần sửa không tồn tại.';
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

<?php if (isset($khoan_old_input['context']) && $khoan_old_input['context'] === 'update' && isset($khoan_old_input['id_khoan'])): ?>
window.addEventListener("load", function() {
    update_obj(<?=(int)$khoan_old_input['id_khoan']?>, '<?=$_GET['status'] ?? ''?>');
});
<?php endif; ?>

window.addEventListener("load", function() {
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'locked_by_dotchamdiem'): ?>
            Toast.fire('Cảnh báo!', 'Dữ liệu này đang được sử dụng trong một Đợt chấm điểm. Bạn không thể Sửa/Xoá vào lúc này!', 'warning');
        <?php elseif ($_GET['status'] == 'locked_update'): ?>
            Toast.fire('Cảnh báo!', 'Dữ liệu này đã sử dụng trong lịch sử. Vui lòng tạo mới thay vì sửa!', 'warning');
        <?php elseif ($_GET['status'] == 'copy_success'): ?>
            Toast.fire('Thành công!', 'Đã nhân bản Khoản thành công!', 'success');
        <?php endif; ?>
    <?php endif; ?>
});

function confirm_copy_sweet(url) {
    Swal.fire({
        title: 'Nhân bản Khoản này?',
        html: 'Toàn bộ <b>Mục</b> bên trong Khoản này cũng sẽ được nhân bản theo.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Nhân bản',
        cancelButtonText: 'Hủy',
        customClass: {
            confirmButton: 'btn btn-success font-weight-bold mx-2 px-4 py-2',
            cancelButton: 'btn btn-cancel-custom font-weight-bold mx-2 px-4 py-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            location.href = url;
        }
    })
    return false;
}
 </script>
