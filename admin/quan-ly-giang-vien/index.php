 <?php
    // require "../models/getModel.php";
    $giangvien__Get_All = $giangvien->giangvien__Get_All();
    $trinhdo__Get_All = $trinhdo->trinhdo__Get_All();
    
    // Hàm escape và old_value
    function giangvien_escape($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_GET['status']) || $_GET['status'] === 'success') {
        unset($_SESSION['giangvien_old_input']);
    }
    $giangvien_old_input = $_SESSION['giangvien_old_input'] ?? [];
    
    function giangvien_old_value($key, $context, $default = '') {
        global $giangvien_old_input;
        if (isset($giangvien_old_input['context']) && $giangvien_old_input['context'] === $context && isset($giangvien_old_input[$key])) {
            return $giangvien_old_input[$key];
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
                     <h1>Quản lý Giảng viên</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý giảng viên</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <?php $is_add_error = isset($giangvien_old_input['context']) && $giangvien_old_input['context'] === 'add'; ?>

     <!-- Nhựt sửa: Thêm nút bật/tắt form thêm mới -->
     <section class="content mb-2">
         <button type="button" class="btn <?= $is_add_error ? 'btn-cancel-custom' : 'btn-success' ?> font-weight-bold" id="btn-toggle-add" onclick="toggle_add_form()">
             <?php if ($is_add_error): ?>
                 <i class="fas fa-times"></i>
             <?php else: ?>
                 <i class="fas fa-plus"></i> Thêm mới
             <?php endif; ?>
         </button>
     </section>

     <!-- Nhựt sửa: Ẩn form thêm mới mặc định -->
     <section class="content" id="div_add_form" style="<?= $is_add_error ? '' : 'display: none;' ?>">
         <form class="row form" action="quan-ly-giang-vien/action.php?req=add" method="post"
             enctype="multipart/form-data">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title">Thêm mới Giảng viên</h3>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Mã giảng viên <span class="color-crimson">*</span></label>
                                     <input type="text" id="ma_giang_vien" name="ma_giang_vien" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'duplicate-giangvien') ? 'is-invalid' : '' ?>"
                                         required placeholder="Nhập mã giảng viên" value="<?= htmlspecialchars(giangvien_old_value('ma_giang_vien', 'add')) ?>">
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'duplicate-giangvien'): ?>
                                         <small class="text-danger mt-1">Mã giảng viên hoặc Email đã tồn tại.</small>
                                     <?php endif; ?>
                                 </div>
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Giới tính <span class="color-crimson">*</span></label>
                                     <select class="form-control" name="gioi_tinh" required>
                                         <option value="">Chọn giới tính</option>
                                         <option value="0" <?= giangvien_old_value('gioi_tinh', 'add') === '0' ? 'selected' : '' ?>>Nữ</option>
                                         <option value="1" <?= giangvien_old_value('gioi_tinh', 'add') === '1' ? 'selected' : '' ?>>Nam</option>
                                     </select>
                                 </div>
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Ngày sinh <span class="color-crimson">*</span></label>
                                     <input type="date" id="ngay_sinh" name="ngay_sinh" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-ngay') ? 'is-invalid' : '' ?>" required
                                         value="<?= giangvien_old_value('ngay_sinh', 'add', date('Y-m-d', strtotime('-22 years'))) ?>"
                                         min="<?= date('Y-m-d', strtotime('-100 years')) ?>"
                                         max="<?= date('Y-m-d', strtotime('-10 years')) ?>" placeholder="Nhập ngày sinh">
                                 </div>
                             </div>

                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Tên giảng viên <span class="color-crimson">*</span></label>
                                     <input type="text" id="ten_giang_vien" name="ten_giang_vien" class="form-control"
                                         required placeholder="Nhập tên giảng viên" value="<?= htmlspecialchars(giangvien_old_value('ten_giang_vien', 'add')) ?>">
                                 </div>
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Trình độ <span class="color-crimson">*</span></label>
                                     <select class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid') ? 'is-invalid' : '' ?>" name="id_trinh_do" required>
                                         <option value="">Chọn trình độ</option>
                                         <?php foreach($trinhdo__Get_All as $item):?>
                                         <option value="<?=$item->id_trinh_do?>" <?= giangvien_old_value('id_trinh_do', 'add') == $item->id_trinh_do ? 'selected' : '' ?>><?=$item->ten_trinh_do?></option>
                                         <?php endforeach;?>
                                     </select>
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid'): ?>
                                         <small class="text-danger mt-1">Trình độ không hợp lệ.</small>
                                     <?php endif; ?>
                                 </div>
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Email <span class="color-crimson">*</span></label>
                                     <input type="email" id="email" name="email" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'duplicate-giangvien') ? 'is-invalid' : '' ?>" required
                                         placeholder="Nhập email" value="<?= htmlspecialchars(giangvien_old_value('email', 'add')) ?>">
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'duplicate-giangvien'): ?>
                                         <small class="text-danger mt-1">Email hoặc Mã giảng viên đã tồn tại.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>

                             <div class="col-6">
                                 <!-- quân sửa: Chia nhỏ phần nhập địa chỉ thành 4 ô (Thêm mới) -->
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Địa chỉ liên lạc <span class="color-crimson">*</span></label>
                                     <div class="row">
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_ll_so_nha" class="form-control" required placeholder="Số nhà, đường" value="<?= htmlspecialchars(giangvien_old_value('dc_ll_so_nha', 'add')) ?>">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_ll_ap" class="form-control" required placeholder="Ấp / Khu phố" value="<?= htmlspecialchars(giangvien_old_value('dc_ll_ap', 'add')) ?>">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_ll_xa" class="form-control" required placeholder="Xã / Phường" value="<?= htmlspecialchars(giangvien_old_value('dc_ll_xa', 'add')) ?>">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_ll_tinh" class="form-control" required placeholder="Tỉnh / Thành phố" value="<?= htmlspecialchars(giangvien_old_value('dc_ll_tinh', 'add')) ?>">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Số điện thoại 1 <span class="color-crimson">*</span></label>
                                     <input type="text" id="so_dien_thoai_1" name="so_dien_thoai_1"
                                         pattern="0[0-9]{9,10}" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-sdt') ? 'is-invalid' : '' ?>" required
                                         title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 1"
                                         minlength="10" maxlength="11" value="<?= htmlspecialchars(giangvien_old_value('so_dien_thoai_1', 'add')) ?>">
                                 </div>
                             </div>

                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Địa chỉ thường trú <span class="color-crimson">*</span></label>
                                     <div class="row">
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_tt_so_nha" class="form-control" required placeholder="Số nhà, đường" value="<?= htmlspecialchars(giangvien_old_value('dc_tt_so_nha', 'add')) ?>">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_tt_ap" class="form-control" required placeholder="Ấp / Khu phố" value="<?= htmlspecialchars(giangvien_old_value('dc_tt_ap', 'add')) ?>">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_tt_xa" class="form-control" required placeholder="Xã / Phường" value="<?= htmlspecialchars(giangvien_old_value('dc_tt_xa', 'add')) ?>">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_tt_tinh" class="form-control" required placeholder="Tỉnh / Thành phố" value="<?= htmlspecialchars(giangvien_old_value('dc_tt_tinh', 'add')) ?>">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="form-group">
                                     <label class="label-sidebar" for="">Số điện thoại 2 <span class="color-crimson">*</span></label>
                                     <input type="text" id="so_dien_thoai_2" name="so_dien_thoai_2"
                                         pattern="0[0-9]{9,10}" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-sdt') ? 'is-invalid' : '' ?>" required
                                         title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 2"
                                         minlength="10" maxlength="11" value="<?= htmlspecialchars(giangvien_old_value('so_dien_thoai_2', 'add')) ?>">
                                 </div>
                             </div>
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
                 <h3 class="card-title">Danh sách Giảng viên</h3>
                 <div class="card-tools">
                     <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                         <i class="fas fa-minus"></i>
                     </button>
                 </div>
             </div>
             <!-- /.card-header -->
             <div class="card-body">
                 <div class="table-responsive">
                     <table id="tablejs" class="table table-bordered table-striped display responsive" width="100%">
                         <thead>
                          <tr>
                              <th>STT</th>
                              <th>MGV</th>
                              <th>Họ và tên</th>
                              <th>Giới tính</th>
                              <th>Ngày sinh</th>
                              <th>Số điện thoại 1</th>
                              <th>Địa chỉ liên lạc</th>
                              <th>Trình độ</th>
                              <th>Thao tác</th>
                          </tr>
                     </thead>
                     <tbody>
                         <?php 
                         // quân sửa: Tối ưu hiệu năng tránh N+1 Query cho Trình độ
                         $arr_trinh_do = [];
                         foreach($trinhdo__Get_All as $td) {
                             $arr_trinh_do[$td->id_trinh_do] = $td->ten_trinh_do;
                         }
                         $num = 0;
                         ?>
                         <?php foreach($giangvien__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <td class="text-center" style="text-align: center !important;"><?= htmlspecialchars($item->ma_giang_vien ?? '') ?></td>
                             <!-- quân sửa: Viết hoa chữ cái đầu cho tên riêng -->
                             <td><?= htmlspecialchars(mb_convert_case($item->ten_giang_vien ?? '', MB_CASE_TITLE, "UTF-8")) ?></td>
                             <td class="text-center" style="text-align: center !important;"><?=$item->gioi_tinh == 1 ? "Nam" : "Nữ"?></td>
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
                             <td class="text-center" style="text-align: center !important;"><?= isset($arr_trinh_do[$item->id_trinh_do]) ? htmlspecialchars($arr_trinh_do[$item->id_trinh_do]) : '' ?></td>

                             <td>
                                 <a href="#" type="button" class="btn  btn-warning m-2"
                                     onclick="update_obj(<?=$item->id_giang_vien?>)">
                                     <i class="ri-edit-2-line"></i>
                                 </a>
                                 <a href="#" type="button" class="btn  btn-danger m-2"
                                     onclick="return confirm_sweet('quan-ly-giang-vien/action.php?req=delete&id_giang_vien=<?=$item->id_giang_vien?>')">
                                     <i class="ri-delete-bin-line"></i>
                                 </a>
                             </td>
                         </tr>
                         <?php endforeach ?>
                     </tbody>
                 </table>
                 </div>
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
        "dom": "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end align-items-center'Bf>>rtip",
        "pagingType": "full_numbers",
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "language": {
            "decimal": ",",
            "thousands": ".",
            "emptyTable": "Không có dữ liệu trong bảng",
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ giảng viên",
            "infoEmpty": "Hiển thị 0 - 0 của 0 giảng viên",
            "infoFiltered": "(lọc từ _MAX_ giảng viên)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ giảng viên",
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

function update_obj(id_giang_vien) {
    $.post('quan-ly-giang-vien/update.php', {
        'id_giang_vien': id_giang_vien,
    }, function(data) {
        // Nhựt sửa: Ẩn form thêm mới khi mở form cập nhật
        $('#div_add_form').slideUp(300);
        $('#btn-toggle-add').html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-cancel-custom').addClass('btn-success');
        $('#div_update').html(data);
    });
}

function cancel_update() {
    $("#div_update").html('');
}
 </script>