 <?php
    // require "../models/getModel.php";
    // Nhựt sửa lỗi: Tạo CSRF token cho form thêm/cập nhật đợt chấm điểm.
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }
    function dotchamdiem_escape($value) {
        // Nhựt sửa lỗi: Escape dữ liệu hiển thị của đợt chấm điểm để tránh XSS.
        return htmlspecialchars($value ?? "", ENT_QUOTES, 'UTF-8');
    }
    function dotchamdiem_format_range_label($title, $start, $end) {
        // Nhựt sửa lỗi: Giữ hàm cũ nhưng combobox chỉ hiển thị tên theo yêu cầu mới.
        return dotchamdiem_escape($title);
    }
    function dotchamdiem_trang_thai_hien_thi($thoi_gian_bat_dau, $thoi_gian_ket_thuc) {
        // Nhựt sửa lỗi: Tính trạng thái đợt chấm điểm động theo ngày hiện tại, không dùng cột trang_thai.
        $today = date('Y-m-d');
        if ($today < $thoi_gian_bat_dau) {
            return "Chưa bắt đầu";
        }
        if ($today >= $thoi_gian_bat_dau && $today <= $thoi_gian_ket_thuc) {
            return "Đang diễn ra";
        }
        return "Đã kết thúc";
    }
    $dotchamdiem__Get_All = $dotchamdiem->dotchamdiem__Get_All();
    $namhoc__Get_All = $namhoc->namhoc__Get_All();
    $mauphieu__Get_All = $mauphieu->mauphieu__Get_All();
    $lophoc__Get_All = $lophoc->lophoc__Get_All();
 ?>

<style>
/* Style for the select boxes */
select#bootstrap-duallistbox-nonselected-list_id_lop_hoc\[\],
select#bootstrap-duallistbox-nonselected-list_,
select#bootstrap-duallistbox-selected-list_id_lop_hoc\[\],
select#bootstrap-duallistbox-selected-list_ {
    display: block;
    width: 100%;
    height: 200px !important;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    box-shadow: inset 0 1px 2px rgba(0,0,0,.075);
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}

select#bootstrap-duallistbox-nonselected-list_id_lop_hoc\[\] option,
select#bootstrap-duallistbox-nonselected-list_ option,
select#bootstrap-duallistbox-selected-list_id_lop_hoc\[\] option,
select#bootstrap-duallistbox-selected-list_ option {
    padding: 5px 8px;
    margin-bottom: 2px;
    border-radius: 4px;
    background-color: #fff;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
}

select#bootstrap-duallistbox-nonselected-list_id_lop_hoc\[\] option:hover,
select#bootstrap-duallistbox-nonselected-list_ option:hover,
select#bootstrap-duallistbox-selected-list_id_lop_hoc\[\] option:hover,
select#bootstrap-duallistbox-selected-list_ option:hover {
    background-color: #e9ecef;
}

select#bootstrap-duallistbox-nonselected-list_id_lop_hoc\[\] option:checked,
select#bootstrap-duallistbox-nonselected-list_ option:checked,
select#bootstrap-duallistbox-selected-list_id_lop_hoc\[\] option:checked,
select#bootstrap-duallistbox-selected-list_ option:checked {
    background-color: #007bff;
    color: white;
}

/* Move All Button */
button.btn.moveall.btn-outline-secondary {
    font-size: 0; /* Hide default text >> */
    background-color: #28a745;
    color: white;
    border: none;
    font-weight: bold;
    border-radius: 4px;
    margin-top: 5px;
    transition: background-color 0.3s;
}
button.btn.moveall.btn-outline-secondary:hover {
    background-color: #218838;
}
button.btn.moveall.btn-outline-secondary:before {
    content: 'Chưa chọn (Click chọn tất cả)';
    font-size: 1rem;
}

/* Remove All Button */
button.btn.removeall.btn-outline-secondary {
    font-size: 0; /* Hide default text << */
    background-color: #dc3545;
    color: white;
    border: none;
    font-weight: bold;
    border-radius: 4px;
    margin-top: 5px;
    transition: background-color 0.3s;
}
button.btn.removeall.btn-outline-secondary:hover {
    background-color: #c82333;
}
button.btn.removeall.btn-outline-secondary:before {
    content: 'Đã chọn (Click bỏ chọn tất cả)';
    font-size: 1rem;
}

/* Info Text */
.bootstrap-duallistbox-container .info-container {
    margin-bottom: 5px;
    font-weight: bold;
    color: #6c757d;
}
</style>

 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header pb-0">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý Đợt chấm điểm</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý đợt chấm điểm</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <!-- Nhựt sửa: Thêm nút bật/tắt form thêm mới -->
     <?php
        $dotchamdiem_old_input = isset($_SESSION['dotchamdiem_old_input']) && is_array($_SESSION['dotchamdiem_old_input']) ? $_SESSION['dotchamdiem_old_input'] : array();
        
        if (!function_exists('dotchamdiem_old_value')) {
            function dotchamdiem_old_value($field, $default = '') {
                global $dotchamdiem_old_input;
                if (isset($dotchamdiem_old_input['context']) && $dotchamdiem_old_input['context'] === 'add' && isset($dotchamdiem_old_input[$field])) {
                    return $dotchamdiem_old_input[$field];
                }
                return $default;
            }
        }
        $is_add_error = isset($dotchamdiem_old_input['context']) && $dotchamdiem_old_input['context'] === 'add';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
     ?>
     <section class="content mb-2">
         <button type="button" class="btn <?= $is_add_error ? 'btn-cancel-custom' : 'btn-success' ?> font-weight-bold" id="btn-toggle-add" onclick="toggle_add_form()">
             <i class="fas <?= $is_add_error ? 'fa-times' : 'fa-plus' ?>"></i> <?= $is_add_error ? '' : 'Thêm mới' ?>
         </button>
     </section>

     <!-- Nhựt sửa: Ẩn form thêm mới mặc định -->
     <section class="content" id="div_add_form" style="<?= $is_add_error ? 'display: block;' : 'display: none;' ?>">
         <form class="row form" action="quan-ly-dot-cham-diem/action.php?req=add" method="post"
             enctype="multipart/form-data">
             <?php // Nhựt sửa lỗi: Thêm CSRF token cho form thêm đợt chấm điểm. ?>
             <input type="hidden" name="csrf_token" value="<?=dotchamdiem_escape($_SESSION['csrf_token'])?>">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title">Thêm mới Đợt chấm điểm</h3>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="ten_dot">Tên đợt chấm điểm <span class="color-crimson">*</span></label>
                                     <input type="text" id="ten_dot" name="ten_dot" class="form-control <?= ($is_add_error && $status == 'invalid-ten') ? 'is-invalid' : '' ?>" required
                                         placeholder="Nhập tên đợt chấm điểm" value="<?=dotchamdiem_escape(dotchamdiem_old_value('ten_dot'))?>">
                                     <?php if ($is_add_error && $status == 'invalid-ten'): ?>
                                         <small class="text-danger mt-1">Tên đợt không được để trống.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="id_mau_phieu">Mẫu phiếu <span class="color-crimson">*</span></label>
                                     <select class="form-control <?= ($is_add_error && $status == 'invalid-mauphieu') ? 'is-invalid' : '' ?>" name="id_mau_phieu" id="id_mau_phieu" required>
                                         <option value="">Chọn Mẫu phiếu</option>
                                         <?php $old_mau_phieu = dotchamdiem_old_value('id_mau_phieu'); ?>
                                         <?php foreach ($mauphieu__Get_All as $item):?>
                                         <option value="<?=$item->id_mau_phieu?>" <?= $old_mau_phieu == $item->id_mau_phieu ? 'selected' : '' ?>><?=$item->ten_mau_phieu?></option>
                                         <?php endforeach; ?>
                                     </select>
                                     <?php if ($is_add_error && $status == 'invalid-mauphieu'): ?>
                                         <small class="text-danger mt-1">Mẫu phiếu không hợp lệ.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                         </div>
                         
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="id_nam_hoc">Năm học <span class="color-crimson">*</span></label>
                                     <select class="form-control <?= ($is_add_error && $status == 'invalid-namhoc') ? 'is-invalid' : '' ?>" id="id_nam_hoc" name="id_nam_hoc" required>
                                         <option value="">Chọn năm học</option>
                                         <?php $old_nam_hoc = dotchamdiem_old_value('id_nam_hoc'); ?>
                                         <?php foreach ($namhoc__Get_All as $item):?>
                                         <option value="<?=$item->id_nam_hoc?>" <?= $old_nam_hoc == $item->id_nam_hoc ? 'selected' : '' ?>><?=dotchamdiem_format_range_label($item->ten_nam_hoc, $item->ngay_bat_dau, $item->ngay_ket_thuc)?></option>
                                         <?php endforeach; ?>
                                     </select>
                                     <?php if ($is_add_error && $status == 'invalid-namhoc'): ?>
                                         <small class="text-danger mt-1">Năm học không hợp lệ.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="id_hoc_ky">Học kỳ <span class="color-crimson">*</span></label>
                                     <select class="form-control <?= ($is_add_error && $status == 'invalid-semester') ? 'is-invalid' : '' ?>" id="id_hoc_ky" name="id_hoc_ky" required disabled>
                                         <option value="">--- Chọn học kỳ ---</option>
                                     </select>
                                     <?php if ($is_add_error && $status == 'invalid-semester'): ?>
                                         <small class="text-danger mt-1">Học kỳ không hợp lệ.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                         </div>

                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="thoi_gian_bat_dau">Thời gian bắt đầu <span class="color-crimson">*</span></label>
                                     <input type="date" id="thoi_gian_bat_dau" name="thoi_gian_bat_dau"
                                         class="form-control <?= ($is_add_error && $status == 'invalid-date') ? 'is-invalid' : '' ?>" required placeholder="Nhập thời gian bắt đầu"
                                         value="<?=dotchamdiem_escape(dotchamdiem_old_value('thoi_gian_bat_dau', date('Y-m-d')))?>">
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="thoi_gian_ket_thuc">Thời gian kết thúc <span class="color-crimson">*</span></label>
                                     <input type="date" id="thoi_gian_ket_thuc" name="thoi_gian_ket_thuc"
                                         class="form-control <?= ($is_add_error && $status == 'invalid-date') ? 'is-invalid' : '' ?>" required placeholder="Nhập thời gian kết thúc"
                                         value="<?=dotchamdiem_escape(dotchamdiem_old_value('thoi_gian_ket_thuc', date('Y-m-d')))?>">
                                     <?php if ($is_add_error && $status == 'invalid-date'): ?>
                                         <small class="text-danger mt-1">Thời gian không hợp lệ (Bắt đầu phải nhỏ hơn hoặc bằng Kết thúc).</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                         </div>

                         <div class="row">
                             <div class="col-12">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="id_lop_hoc">Lớp áp dụng <span class="color-crimson">*</span></label>
                                     <?php $old_lop_hoc = dotchamdiem_old_value('id_lop_hoc', array()); ?>
                                     <select class="duallistbox <?= ($is_add_error && $status == 'invalid-lop') ? 'is-invalid' : '' ?>" multiple="multiple" name="id_lop_hoc[]" id="id_lop_hoc" required>
                                         <?php foreach ($lophoc__Get_All as $item):?>
                                         <option value="<?=$item->id_lop_hoc?>" <?= in_array($item->id_lop_hoc, $old_lop_hoc) ? 'selected' : '' ?>><?=$item->ten_lop_hoc?></option>
                                         <?php endforeach; ?>
                                     </select>
                                     <?php if ($is_add_error && $status == 'invalid-lop'): ?>
                                         <small class="text-danger mt-1">Lớp áp dụng không hợp lệ.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-12">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="ghi_chu">Ghi chú</label>
                                     <textarea id="ghi_chu" name="ghi_chu" class="form-control" rows="2"
                                         placeholder="Nhập Ghi chú"><?=dotchamdiem_escape(dotchamdiem_old_value('ghi_chu'))?></textarea>
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
         <script>
            $(document).ready(function() {
                <?php if ($is_add_error && dotchamdiem_old_value('id_nam_hoc') != ''): ?>
                    load_hoc_ky_by_nam_hoc(<?=json_encode(dotchamdiem_old_value('id_nam_hoc'))?>, '#id_hoc_ky', <?=json_encode(dotchamdiem_old_value('id_hoc_ky'))?>);
                <?php endif; ?>
            });
         </script>
     </section>

     <section class="content" id="div_update">
     </section>

     <section class="content">
         <div class="card card-primary">
             <div class="card-header">
                 <h3 class="card-title">Danh sách Đợt chấm điểm</h3>
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
                             <th style="width: 10%; white-space: nowrap;">Tên đợt</th>
                             <?php // Nhựt sửa lỗi: Tách riêng cột Học kỳ và Năm học trong danh sách. ?>
                             <th style="width: 8%; white-space: nowrap;">Năm học</th>
                             <th style="width: 6%; white-space: nowrap;">Học kỳ</th>
                             <th style="width: 10%; white-space: nowrap;">Bắt đầu</th>
                             <th style="width: 10%; white-space: nowrap;">Kết thúc</th>
                             <?php // Nhựt sửa lỗi: Bổ sung cột trạng thái tính động theo thời gian. ?>
                             <th style="width: 8%; white-space: nowrap;">Trạng thái</th>
                             <th style="width: 22%; white-space: nowrap;">Lớp áp dụng</th>
                             <th style="width: 15%; white-space: nowrap;">Mẫu phiếu</th>
                             <th style="width: 8%; white-space: nowrap;">Thao tác</th>
                         </tr>
                     </thead>
                      <tbody>
                          <?php 
                          $num = 0;
                          $arr_lop_hoc = [];
                          foreach ($lophoc__Get_All as $lh) {
                              $arr_lop_hoc[$lh->id_lop_hoc] = $lh->ten_lop_hoc;
                          }
                          $arr_mau_phieu = [];
                          foreach ($mauphieu__Get_All as $mp) {
                              $arr_mau_phieu[$mp->id_mau_phieu] = $mp->ten_mau_phieu;
                          }
                          $lopapdung__Get_All = $lopapdung->lopapdung__Get_All();
                          $arr_lop_ap_dung = [];
                          foreach ($lopapdung__Get_All as $lad) {
                              $arr_lop_ap_dung[$lad->id_dot][] = $lad;
                          }
                          ?>
                          <?php foreach($dotchamdiem__Get_All as $item):?>
                         <?php // Nhựt sửa lỗi: Lấy lớp áp dụng một lần để tránh lỗi truy cập mảng rỗng khi hiển thị. ?>
                         <?php $lopapdung__Get_By_Id_Dot = isset($arr_lop_ap_dung[$item->id_dot]) ? $arr_lop_ap_dung[$item->id_dot] : [];?>
                         <tr>
                             <td class="text-center" style="text-align: center !important;"><?=++$num?></td>
                             <?php // Nhựt sửa lỗi: Escape dữ liệu tên đợt lấy từ database để tránh XSS. ?>
                             <td><?=dotchamdiem_escape($item->ten_dot)?></td>
                             <?php // Nhựt sửa lỗi: Hiển thị riêng Học kỳ và Năm học để danh sách dễ đọc hơn. ?>
                             <td class="text-center" style="text-align: center !important;"><?=dotchamdiem_escape($item->ten_nam_hoc)?></td>
                             <td class="text-center" style="text-align: center !important;"><?=dotchamdiem_escape($item->ten_hoc_ky)?></td>
                             <td class="text-center" style="text-align: center !important;"><?= !empty($item->thoi_gian_bat_dau) ? date("d/m/Y", strtotime($item->thoi_gian_bat_dau)) : '' ?></td>
                             <td class="text-center" style="text-align: center !important;"><?= !empty($item->thoi_gian_ket_thuc) ? date("d/m/Y", strtotime($item->thoi_gian_ket_thuc)) : '' ?></td>
                             <?php // Nhựt sửa lỗi: Hiển thị trạng thái động theo ngày hiện tại. ?>
                             <td class="text-center" style="text-align: center !important;"><?=dotchamdiem_escape(dotchamdiem_trang_thai_hien_thi($item->thoi_gian_bat_dau, $item->thoi_gian_ket_thuc))?></td>
                             <?php // Nhựt sửa lỗi: Kiểm tra lớp còn tồn tại trước khi hiển thị để tránh lỗi object rỗng. ?>
                             <td><?php foreach($lopapdung__Get_By_Id_Dot as $item_2){
                                 $ten_lop = isset($arr_lop_hoc[$item_2->id_lop_hoc]) ? $arr_lop_hoc[$item_2->id_lop_hoc] : '';
                                 if ($ten_lop != '') {echo dotchamdiem_escape($ten_lop) . "<br/> ";}
                             }?>
                             </td>
                             <?php // Nhựt sửa lỗi: Kiểm tra lopapdung/mẫu phiếu tồn tại trước khi hiển thị để tránh lỗi mảng rỗng. ?>
                             <td><?php if (count($lopapdung__Get_By_Id_Dot) > 0) {
                                 $ten_mau = isset($arr_mau_phieu[$lopapdung__Get_By_Id_Dot[0]->id_mau_phieu]) ? $arr_mau_phieu[$lopapdung__Get_By_Id_Dot[0]->id_mau_phieu] : '';
                                 echo dotchamdiem_escape($ten_mau);
                             }?>
                             </td>
                             <td>
                                 <a href="#" type="button" class="btn  btn-warning m-2"
                                     onclick="update_obj(<?=$item->id_dot?>)">
                                     <i class="ri-edit-2-line"></i>
                                 </a>
                                 <?php // Nhựt sửa lỗi: Thêm CSRF token vào URL xóa Đợt chấm điểm. ?>
                                 <a href="#" type="button" class="btn  btn-danger m-2"
                                     onclick="return confirm_delete_sweet('quan-ly-dot-cham-diem/action.php?req=delete&id_dot=<?=$item->id_dot?>&csrf_token=<?=dotchamdiem_escape($_SESSION['csrf_token'])?>', 'Đợt chấm điểm')">
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
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ đợt chấm điểm",
            "infoEmpty": "Hiển thị 0 - 0 của 0 đợt chấm điểm",
            "infoFiltered": "(lọc từ _MAX_ đợt chấm điểm)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ đợt chấm điểm",
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
                    <label class="label-sidebar">Năm học:</label>
                    <select id="filter_nam_hoc" class="form-control form-control-sm">
                        <option value="">-- Tất cả Năm học --</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label class="label-sidebar">Học kỳ:</label>
                    <select id="filter_hoc_ky" class="form-control form-control-sm">
                        <option value="">-- Tất cả Học kỳ --</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label class="label-sidebar">Mẫu phiếu:</label>
                    <select id="filter_mau_phieu" class="form-control form-control-sm">
                        <option value="">-- Tất cả Mẫu phiếu --</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label class="label-sidebar">Trạng thái:</label>
                    <select id="filter_trang_thai" class="form-control form-control-sm">
                        <option value="">-- Tất cả Trạng thái --</option>
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
                var selectedNamHoc = $('#filter_nam_hoc').val() || "";
                var selectedHocKy = $('#filter_hoc_ky').val() || "";
                var selectedMauPhieu = $('#filter_mau_phieu').val() || "";
                var selectedTrangThai = $('#filter_trang_thai').val() || "";
                
                var namHocOptions = [];
                var hocKyOptions = [];
                var mauPhieuOptions = [];
                var trangThaiOptions = [];
                
                table.rows().every(function() {
                    var data = this.data();
                    var namHoc = $('<div>').html(data[2]).text().trim();
                    var hocKy = $('<div>').html(data[3]).text().trim();
                    var trangThai = $('<div>').html(data[6]).text().trim();
                    var mauPhieu = $('<div>').html(data[8]).text().trim();
                    
                    if (namHoc !== '' && namHocOptions.indexOf(namHoc) === -1) namHocOptions.push(namHoc);
                    
                    if (selectedNamHoc === "" || namHoc === selectedNamHoc) {
                        if (hocKy !== '' && hocKyOptions.indexOf(hocKy) === -1) hocKyOptions.push(hocKy);
                    }
                    
                    if ((selectedNamHoc === "" || namHoc === selectedNamHoc) && (selectedHocKy === "" || hocKy === selectedHocKy)) {
                        if (mauPhieu !== '' && mauPhieuOptions.indexOf(mauPhieu) === -1) mauPhieuOptions.push(mauPhieu);
                        if (trangThai !== '' && trangThaiOptions.indexOf(trangThai) === -1) trangThaiOptions.push(trangThai);
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
                
                populate('filter_nam_hoc', namHocOptions, selectedNamHoc, '-- Tất cả Năm học --');
                populate('filter_hoc_ky', hocKyOptions, selectedHocKy, '-- Tất cả Học kỳ --');
                populate('filter_mau_phieu', mauPhieuOptions, selectedMauPhieu, '-- Tất cả Mẫu phiếu --');
                populate('filter_trang_thai', trangThaiOptions, selectedTrangThai, '-- Tất cả Trạng thái --');
            }

            $('#filter_nam_hoc').on('change', function() {
                $('#filter_hoc_ky').val('');
                $('#filter_mau_phieu').val('');
                $('#filter_trang_thai').val('');
                updateCascadeDropdowns();
            });

            $('#filter_hoc_ky').on('change', function() {
                $('#filter_mau_phieu').val('');
                $('#filter_trang_thai').val('');
                updateCascadeDropdowns();
            });
            
            $('#filter_mau_phieu').on('change', function() {
                updateCascadeDropdowns();
            });

            $('#filter_trang_thai').on('change', function() {
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
                var namHocVal = $('#filter_nam_hoc').val();
                var hocKyVal = $('#filter_hoc_ky').val();
                var mauPhieuVal = $('#filter_mau_phieu').val();
                var trangThaiVal = $('#filter_trang_thai').val();
                
                table.column(2).search(namHocVal ? '^' + $.fn.dataTable.util.escapeRegex(namHocVal) + '$' : '', true, false)
                     .column(3).search(hocKyVal ? '^' + $.fn.dataTable.util.escapeRegex(hocKyVal) + '$' : '', true, false)
                     .column(8).search(mauPhieuVal ? '^' + $.fn.dataTable.util.escapeRegex(mauPhieuVal) + '$' : '', true, false)
                     .column(6).search(trangThaiVal ? $.fn.dataTable.util.escapeRegex(trangThaiVal) : '', true, false)
                     .draw();
                $('#custom-filter-menu').fadeOut(200);
            });

            $('#btn-cancel-filter').on('click', function() {
                $('#filter_nam_hoc').val('');
                $('#filter_hoc_ky').val('');
                $('#filter_mau_phieu').val('');
                $('#filter_trang_thai').val('');
                updateCascadeDropdowns();
                table.column(2).search('')
                     .column(3).search('')
                     .column(8).search('')
                     .column(6).search('')
                     .draw();
                $('#custom-filter-menu').fadeOut(200);
            });
        }
    });

    // Nhựt sửa lỗi: Khi chọn Năm học thì tải lại danh sách Học kỳ tương ứng.
    $("#id_nam_hoc").change(function() {
        load_hoc_ky_by_nam_hoc($(this).val(), '#id_hoc_ky', '');
    });

    thoi_gian_bat_dau = document.getElementById('thoi_gian_bat_dau');
    // Nhựt sửa lỗi: Khi form vừa load đã có ngày bắt đầu thì cập nhật min cho ngày kết thúc ngay.
    $('#thoi_gian_ket_thuc').attr({
        "min": thoi_gian_bat_dau.value
    });
    $("#thoi_gian_bat_dau").change(function() {
        $('#thoi_gian_ket_thuc').attr({
            "min": thoi_gian_bat_dau.value
        });
    });
});



function load_hoc_ky_by_nam_hoc(id_nam_hoc, id_hoc_ky_selector, selected_id_hoc_ky) {
    // Nhựt sửa lỗi: Dùng AJAX để chỉ hiển thị Học kỳ thuộc Năm học đã chọn.
    const hocKySelect = $(id_hoc_ky_selector);
    hocKySelect.html('<option value="">--- Chọn học kỳ ---</option>');
    hocKySelect.prop('disabled', true);

    if (!id_nam_hoc) {
        return;
    }

    $.getJSON('quan-ly-dot-cham-diem/action.php?req=get-hoc-ky', {
        id_nam_hoc: id_nam_hoc
    }, function(data) {
            $.each(data, function(index, item) {
            // Nhựt sửa lỗi: Combobox Học kỳ chỉ hiển thị tên theo yêu cầu mới.
            const option = $('<option></option>').val(item.id_hoc_ky).text(item.ten_hoc_ky);
            if (selected_id_hoc_ky && selected_id_hoc_ky == item.id_hoc_ky) {
                option.prop('selected', true);
            }
            hocKySelect.append(option);
        });
        hocKySelect.prop('disabled', false);
    }).fail(function() {
        // Nhựt sửa lỗi: Nếu AJAX lấy học kỳ thất bại thì báo lỗi và giữ combobox học kỳ bị khóa.
        hocKySelect.html('<option value="">Không tải được danh sách Học kỳ.</option>');
        hocKySelect.prop('disabled', true);
    });
}

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

function update_obj(id_dot) {
    $.post('quan-ly-dot-cham-diem/update.php', {
        'id_dot': id_dot,
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



