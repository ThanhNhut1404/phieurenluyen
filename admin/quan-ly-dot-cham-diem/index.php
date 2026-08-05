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
select#bootstrap-duallistbox-nonselected-list_id_lop_hoc\[\],
select#bootstrap-duallistbox-nonselected-list_,
select#bootstrap-duallistbox-selected-list_id_lop_hoc\[\],
select#bootstrap-duallistbox-selected-list_ {
    display: block;
    width: 100%;
    height: calc(2.25rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    box-shadow: inset 0 0 0 transparent;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}

button.btn.moveall.btn-outline-secondary:before {
    content: 'Chưa chọn (Click chọn tất cả)';
}

button.btn.removeall.btn-outline-secondary:before {
    content: 'Đã chọn (Click bỏ chọn tất cả)';
}
 </style>

 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý đợt chấm điểm</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="#">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý đợt chấm điểm</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <section class="content">
         <form class="row form" action="quan-ly-dot-cham-diem/action.php?req=add" method="post"
             enctype="multipart/form-data">
             <?php // Nhựt sửa lỗi: Thêm CSRF token cho form thêm đợt chấm điểm. ?>
             <input type="hidden" name="csrf_token" value="<?=dotchamdiem_escape($_SESSION['csrf_token'])?>">
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
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label for="">Tên đợt chấm điểm <span class="color-crimson">(*)</span></label>
                                     <input type="text" id="ten_dot" name="ten_dot" class="form-control" required
                                         placeholder="Nhập tên đợt chấm điểm">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Thời gian bắt đầu<span class="color-crimson">(*)</span></label>
                                     <input type="date" id="thoi_gian_bat_dau" name="thoi_gian_bat_dau"
                                         class="form-control" required placeholder="Nhập thời gian bắt đầu"
                                         value="<?=date('Y-m-d')?>">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Thời gian kết thúc<span class="color-crimson">(*)</span></label>
                                     <input type="date" id="thoi_gian_ket_thuc" name="thoi_gian_ket_thuc"
                                         class="form-control" required placeholder="Nhập thời gian kết thúc"
                                         value="<?=date('Y-m-d')?>">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Ghi chú</label>
                                     <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                                         placeholder="Nhập Ghi chú"></textarea>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <?php // Nhựt sửa lỗi: Thêm combobox Năm học để lọc Học kỳ theo Năm học. ?>
                                     <label for="">Năm học <span class="color-crimson">(*)</span></label>
                                     <select class="form-control" id="id_nam_hoc" name="id_nam_hoc" required>
                                         <option value="">Chọn năm học</option>
                                         <?php foreach ($namhoc__Get_All as $item):?>
                                         <option value="<?=$item->id_nam_hoc?>"><?=dotchamdiem_format_range_label($item->ten_nam_hoc, $item->ngay_bat_dau, $item->ngay_ket_thuc)?></option>
                                         <?php endforeach; ?>
                                     </select>
                                 </div>
                                 <div class="form-group">
                                     <?php // Nhựt sửa lỗi: Học kỳ ban đầu bị disable, chỉ tải theo Năm học đã chọn. ?>
                                     <label for="">Học kỳ <span class="color-crimson">(*)</span></label>
                                     <select class="form-control" id="id_hoc_ky" name="id_hoc_ky" required disabled>
                                         <option value="">--- Chọn học kỳ ---</option>
                                     </select>
                                 </div>
                                 <div class="form-group">
                                     <label for="">Mẫu phiếu <span class="color-crimson">(*)</span></label>
                                     <select class="form-control" name="id_mau_phieu" required>
                                         <option value="">Chọn Mẫu phiếu</option>
                                         <?php foreach ($mauphieu__Get_All as $item):?>
                                         <option value="<?=$item->id_mau_phieu?>"><?=$item->ten_mau_phieu?></option>
                                         <?php endforeach; ?>
                                     </select>
                                 </div>
                                 <div class="form-group">
                                     <label for="">Lớp áp dụng <span class="color-crimson">(*)</span></label>
                                     <select class="duallistbox" multiple="multiple" name="id_lop_hoc[]" required>
                                         <?php foreach ($lophoc__Get_All as $item):?>
                                         <option value="<?=$item->id_lop_hoc?>"><?=$item->ten_lop_hoc?></option>
                                         <?php endforeach; ?>
                                     </select>
                                 </div>
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
                             <th>Tên đợt</th>
                             <?php // Nhựt sửa lỗi: Tách riêng cột Học kỳ và Năm học trong danh sách. ?>
                             <th>Học kỳ</th>
                             <th>Năm học</th>
                             <th>Thời gian bắt đầu</th>
                             <th>Thời gian kết thúc</th>
                             <?php // Nhựt sửa lỗi: Bổ sung cột trạng thái tính động theo thời gian. ?>
                             <th>Trạng thái</th>
                             <th>Lớp áp dụng</th>
                             <th>Mẫu phiếu</th>
                             <th>Ghi chú</th>
                             <th>Thao tác</th>
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
                             <td><?=++$num?></td>
                             <?php // Nhựt sửa lỗi: Escape dữ liệu tên đợt lấy từ database để tránh XSS. ?>
                             <td><?=dotchamdiem_escape($item->ten_dot)?></td>
                             <?php // Nhựt sửa lỗi: Hiển thị riêng Học kỳ và Năm học để danh sách dễ đọc hơn. ?>
                             <td><?=dotchamdiem_escape($item->ten_hoc_ky)?></td>
                             <td><?=dotchamdiem_escape($item->ten_nam_hoc)?></td>
                             <td><?=$item->thoi_gian_bat_dau?></td>
                             <td><?=$item->thoi_gian_ket_thuc?></td>
                             <?php // Nhựt sửa lỗi: Hiển thị trạng thái động theo ngày hiện tại. ?>
                             <td><?=dotchamdiem_escape(dotchamdiem_trang_thai_hien_thi($item->thoi_gian_bat_dau, $item->thoi_gian_ket_thuc))?></td>
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
                             <?php // Nhựt sửa lỗi: Escape ghi chú lấy từ database để tránh XSS. ?>
                             <td><?=dotchamdiem_escape($item->ghi_chu)?></td>
                             <td>
                                 <a href="#" type="button" class="btn  btn-warning m-2"
                                     onclick="update_obj(<?=$item->id_dot?>)">
                                     <i class="fas fa-edit"></i>
                                 </a>
                                 <?php // Nhựt sửa lỗi: Thêm CSRF token vào URL xóa Đợt chấm điểm. ?>
                                 <a href="#" type="button" class="btn  btn-danger m-2"
                                     onclick="return confirm_sweet('quan-ly-dot-cham-diem/action.php?req=delete&id_dot=<?=$item->id_dot?>&csrf_token=<?=dotchamdiem_escape($_SESSION['csrf_token'])?>')">
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
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#tablejs_wrapper .col-md-6:eq(0)');

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

function update_obj(id_dot) {
    $.post('quan-ly-dot-cham-diem/update.php', {
        'id_dot': id_dot,
    }, function(data) {
        $(".card.card-success").addClass('collapsed-card');
        $('#div_update').html(data);
    });
}

function cancel_update() {
    $("#div_update").html('');
    $(".card.card-success").removeClass('collapsed-card');
}
 </script>
