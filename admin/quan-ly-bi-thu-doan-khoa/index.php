<?php
    // require "../models/getModel.php";
    $bithudoankhoa__Get_All = $bithudoankhoa->bithudoankhoa__Get_All();
    $khoa__Get_All = $khoa->khoa__Get_All();

    
    // Hàm escape và old_value
    function bithu_escape($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    $bithu_old_input = $_SESSION['bithu_old_input'] ?? [];
    // Nhựt sửa lỗi: chỉ giữ dữ liệu cũ cho form thêm đúng một lần rồi xóa để không bị dính lại khi quay trang.
    if (isset($bithu_old_input['context']) && $bithu_old_input['context'] === 'add') {
        unset($_SESSION['bithu_old_input']);
    }
    
    function bithu_old_value($key, $context, $default = '') {
        global $bithu_old_input;
        if (isset($bithu_old_input['context']) && $bithu_old_input['context'] === $context && isset($bithu_old_input[$key])) {
            return $bithu_old_input[$key];
        }
        return $default;
    }
    ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header pb-0">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý Bí thư Đoàn khoa</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý bí thư đoàn khoa</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <?php $is_add_error = isset($bithu_old_input['context']) && $bithu_old_input['context'] === 'add'; ?>

     <!-- Nhựt sửa: Thêm nút bật/tắt form thêm mới -->
     <section class="content mb-2">
         <button type="button" class="btn <?= $is_add_error ? 'btn-cancel-custom' : 'btn-success' ?> font-weight-bold" id="btn-toggle-add" onclick="toggle_add_form()">
             <i class="fas <?= $is_add_error ? 'fa-times' : 'fa-plus' ?>"></i> <?= $is_add_error ? '' : 'Thêm mới' ?>
         </button>
     </section>

     <!-- Nhựt sửa: Ẩn form thêm mới mặc định -->
     <section class="content" id="div_add_form" style="<?= $is_add_error ? '' : 'display: none;' ?>">
         <form class="row form" action="quan-ly-bi-thu-doan-khoa/action.php?req=add" method="post"
             enctype="multipart/form-data">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title">Thêm mới Bí thư Đoàn khoa</h3>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Tên bí thư đoàn khoa <span class="color-crimson">*</span></label>
                                     <input type="text" id="ten_bi_thu" name="ten_bi_thu" class="form-control" required
                                         placeholder="Nhập tên bí thư đoàn khoa" value="<?= htmlspecialchars(bithu_old_value('ten_bi_thu', 'add')) ?>">
                                 </div>
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Ngày sinh <span class="color-crimson">*</span></label>
                                     <input type="date" id="ngay_sinh" name="ngay_sinh" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-ngay') ? 'is-invalid' : '' ?>" required
                                         value="<?= bithu_old_value('ngay_sinh', 'add', date('Y-m-d', strtotime('-22 years'))) ?>"
                                         min="<?= date('Y-m-d', strtotime('-100 years')) ?>"
                                         max="<?= date('Y-m-d', strtotime('-10 years')) ?>"
                                         placeholder="Nhập ngày sinh">
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Giới tính <span class="color-crimson">*</span></label>
                                     <select class="form-control" name="gioi_tinh" required>
                                         <option value="">Chọn giới tính</option>
                                         <option value="0" <?= bithu_old_value('gioi_tinh', 'add') === '0' ? 'selected' : '' ?>>Nữ</option>
                                         <option value="1" <?= bithu_old_value('gioi_tinh', 'add') === '1' ? 'selected' : '' ?>>Nam</option>
                                     </select>
                                 </div>
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Email <span class="color-crimson">*</span></label>
                                     <input type="email" id="email" name="email" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'duplicate-bithu') ? 'is-invalid' : '' ?>" required
                                         placeholder="Nhập email" value="<?= htmlspecialchars(bithu_old_value('email', 'add')) ?>">
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'duplicate-bithu'): ?>
                                         <small class="text-danger mt-1">Email bí thư đoàn khoa đã tồn tại.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <!-- quân sửa: Chia nhỏ phần nhập địa chỉ thành 4 ô (Thêm mới) -->
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Địa chỉ liên lạc <span class="color-crimson">*</span></label>
                                     <div class="row">
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_ll_so_nha" class="form-control" required placeholder="Số nhà, đường" value="<?= htmlspecialchars(bithu_old_value('dc_ll_so_nha', 'add')) ?>">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_ll_ap" class="form-control" required placeholder="Ấp / Khu phố" value="<?= htmlspecialchars(bithu_old_value('dc_ll_ap', 'add')) ?>">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_ll_xa" class="form-control" required placeholder="Xã / Phường" value="<?= htmlspecialchars(bithu_old_value('dc_ll_xa', 'add')) ?>">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_ll_tinh" class="form-control" required placeholder="Tỉnh / Thành phố" value="<?= htmlspecialchars(bithu_old_value('dc_ll_tinh', 'add')) ?>">
                                         </div>
                                     </div>
                                 </div>
                                  <div class="form-group">
                                      <label class="label-sidebar" for="">Số điện thoại 1 <span class="color-crimson">*</span></label>
                                      <input type="text" id="so_dien_thoai_1" name="so_dien_thoai_1"
                                          pattern="0[0-9]{9,10}" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-sdt') ? 'is-invalid' : '' ?>" required
                                          title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 1"
                                          minlength="10" maxlength="11" value="<?= htmlspecialchars(bithu_old_value('so_dien_thoai_1', 'add')) ?>">
                                  </div>
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Khoa <span class="color-crimson">*</span></label>
                                     <select class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-khoa') ? 'is-invalid' : '' ?>" name="id_khoa" required>
                                         <option value="">Chọn Khoa</option>
                                         <?php foreach ($khoa__Get_All as $item) : ?>
                                         <option value="<?= $item->id_khoa ?>" <?= bithu_old_value('id_khoa', 'add') == $item->id_khoa ? 'selected' : '' ?>><?= $item->ten_khoa ?></option>
                                         <?php endforeach; ?>
                                     </select>
                                 </div>
                             </div>

                              <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Địa chỉ thường trú <span class="color-crimson">*</span></label>
                                     <div class="row">
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_tt_so_nha" class="form-control" required placeholder="Số nhà, đường" value="<?= htmlspecialchars(bithu_old_value('dc_tt_so_nha', 'add')) ?>">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_tt_ap" class="form-control" required placeholder="Ấp / Khu phố" value="<?= htmlspecialchars(bithu_old_value('dc_tt_ap', 'add')) ?>">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_tt_xa" class="form-control" required placeholder="Xã / Phường" value="<?= htmlspecialchars(bithu_old_value('dc_tt_xa', 'add')) ?>">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_tt_tinh" class="form-control" required placeholder="Tỉnh / Thành phố" value="<?= htmlspecialchars(bithu_old_value('dc_tt_tinh', 'add')) ?>">
                                         </div>
                                     </div>
                                 </div>
                                  <div class="form-group">
                                      <label class="label-sidebar" for="">Số điện thoại 2 <span class="color-crimson">*</span></label>
                                      <input type="text" id="so_dien_thoai_2" name="so_dien_thoai_2"
                                          pattern="0[0-9]{9,10}" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-sdt') ? 'is-invalid' : '' ?>" required
                                          title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 2"
                                          minlength="10" maxlength="11" value="<?= htmlspecialchars(bithu_old_value('so_dien_thoai_2', 'add')) ?>">
                                  </div>
                              </div>
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
                 <h3 class="card-title">Danh sách Bí thư Đoàn khoa</h3>
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
                               <th style="width: 20%; white-space: nowrap;">Họ và tên</th>
                               <th style="width: 15%; white-space: nowrap;">Khoa</th>
                               <th style="width: 10%; white-space: nowrap;">Giới tính</th>
                               <th style="width: 12%; white-space: nowrap;">Ngày sinh</th>
                               <th style="width: 15%; white-space: nowrap;">Số điện thoại 1</th>
                               <th style="width: 15%; white-space: nowrap;">Địa chỉ liên lạc</th>
                               <th style="width: 10%; white-space: nowrap;">Thao tác</th>
                           </tr>
                     </thead>
                      <tbody>
                          <?php 
                          $arr_khoa = [];
                          foreach ($khoa__Get_All as $kh) {
                              $arr_khoa[$kh->id_khoa] = $kh->ten_khoa;
                          }
                          $num = 0; 
                          ?>
                          <?php foreach ($bithudoankhoa__Get_All as $item) : ?>
                          <tr>
                              <td><?= ++$num ?></td>
                              <!-- quân sửa: Viết hoa chữ cái đầu của Tên riêng khi hiển thị ra danh sách -->
                              <td><?= htmlspecialchars(mb_convert_case($item->ten_bi_thu ?? '', MB_CASE_TITLE, "UTF-8")) ?></td>
                              <td><?= isset($arr_khoa[$item->id_khoa]) ? htmlspecialchars($arr_khoa[$item->id_khoa]) : '' ?></td>
                               <td class="text-center" style="text-align: center !important;"><?= $item->gioi_tinh == 1 ? "Nam" : "Nữ" ?></td>
                               <td class="text-center" style="text-align: center !important;" data-order="<?= htmlspecialchars($item->ngay_sinh ?? '') ?>">
                                   <?php
                                   $date = DateTime::createFromFormat('Y-m-d', (string)($item->ngay_sinh ?? ''));
                                   echo htmlspecialchars($date ? $date->format('d/m/Y') : ($item->ngay_sinh ?? ''));
                                   ?>
                               </td>
                               <td class="text-center" style="text-align: center !important;"><?= htmlspecialchars($item->so_dien_thoai_1 ?? '') ?></td>
                              <!-- quân sửa: Rút gọn hiển thị địa chỉ để không làm mất cột thao tác -->
                              <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($item->dia_chi_lien_lac ?? '') ?>">
                                  <?= htmlspecialchars($item->dia_chi_lien_lac ?? '') ?>
                              </td>

                             <td>
                                 <a href="javascript:void(0)" type="button" class="btn  btn-warning m-2"
                                     onclick="return update_obj(<?= $item->id_bi_thu ?>)">
                                     <i class="ri-edit-2-line"></i>
                                 </a>
                                 <a href="#" type="button" class="btn  btn-danger m-2"
                                     onclick="return confirm_delete_sweet('quan-ly-bi-thu-doan-khoa/action.php?req=delete&id_bi_thu=<?= $item->id_bi_thu ?>', 'Bí thư Đoàn khoa')">
                                     <i class="ri-delete-bin-line"></i>
                                 </a>
                             </td>
                         </tr>
                         <?php endforeach ?>
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
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ bí thư đoàn khoa",
            "infoEmpty": "Hiển thị 0 - 0 của 0 bí thư đoàn khoa",
            "infoFiltered": "(lọc từ _MAX_ bí thư đoàn khoa)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ bí thư đoàn khoa",
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
                    <label class="label-sidebar">Giới tính:</label>
                    <select id="filter_gioi_tinh" class="form-control form-control-sm">
                        <option value="">-- Tất cả giới tính --</option>
                        <option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option>
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
                
                var khoaOptions = [];
                
                table.rows().every(function() {
                    var data = this.data();
                    var khoa = $('<div>').html(data[2]).text().trim();
                    
                    if (khoa !== '' && khoaOptions.indexOf(khoa) === -1) khoaOptions.push(khoa);
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
                
                populate('filter_khoa', khoaOptions, selectedKhoa, '-- Tất cả khoa --');
            }

            $('#filter_khoa').on('change', function() {
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
                var gioiTinhVal = $('#filter_gioi_tinh').val();
                
                table.column(2).search(khoaVal ? '^' + $.fn.dataTable.util.escapeRegex(khoaVal) + '$' : '', true, false)
                     .column(3).search(gioiTinhVal ? '^' + $.fn.dataTable.util.escapeRegex(gioiTinhVal) + '$' : '', true, false)
                     .draw();
                $('#custom-filter-menu').fadeOut(200);
            });

            $('#btn-cancel-filter').on('click', function() {
                $('#filter_khoa').val('');
                $('#filter_gioi_tinh').val('');
                updateCascadeDropdowns();
                table.column(2).search('')
                     .column(3).search('')
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

function update_obj(id_bi_thu, error_status = '') {
    $.ajax({
        url: 'quan-ly-bi-thu-doan-khoa/update.php',
        method: 'POST',
        data: { 'id_bi_thu': id_bi_thu, 'error_status': error_status },
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
                text = 'Bí thư đoàn khoa cần sửa không tồn tại.';
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

<?php if (isset($bithu_old_input['context']) && $bithu_old_input['context'] === 'update' && isset($bithu_old_input['id_bi_thu'])): ?>
window.addEventListener("load", function() {
    update_obj(<?=(int)$bithu_old_input['id_bi_thu']?>, '<?=$_GET['status'] ?? ''?>');
});
<?php endif; ?>
 </script>



