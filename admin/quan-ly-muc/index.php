 <?php
    // require "../models/getModel.php";
    $id_khoan_filter = isset($_GET['id_khoan']) ? $_GET['id_khoan'] : '';
    if ($id_khoan_filter != '') {
        $muc__Get_All = $muc->muc__Get_By_Id_Khoan($id_khoan_filter);
    } else {
        $muc__Get_All = $muc->muc__Get_All();
    }
    $khoan__Get_All = $khoan->khoan__Get_All();
 ?>


 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý mục</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý mục</li>
                     </ol>
                 </div>
             </div>
             
             <!-- quân sửa: Bắt lỗi nếu vượt quỹ điểm Khoản -->
             <?php if(isset($_GET['status']) && $_GET['status'] == 'failed_over_limit'): ?>
                 <div class="alert alert-danger alert-dismissible mt-2">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <h5><i class="icon fas fa-ban"></i> Lỗi!</h5>
                     Tổng điểm của các Mục đã vượt quá Điểm tối đa của Khoản. Vui lòng kiểm tra lại.
                 </div>
             <?php endif; ?>
             <!-- quân sửa: Cảnh báo khi dữ liệu đang bị khoá bởi Đợt chấm điểm -->
             <?php if(isset($_GET['status']) && $_GET['status'] == 'locked_by_dotchamdiem'): ?>
                 <div class="alert alert-danger alert-dismissible mt-2">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <h5><i class="icon fas fa-lock"></i> Thao tác thất bại!</h5>
                     Dữ liệu này đang được sử dụng trong một Đợt chấm điểm đang diễn ra. Bạn không thể Sửa hoặc Xoá vào lúc này!
                 </div>
             <?php endif; ?>
             <?php if(isset($_GET['status']) && $_GET['status'] == 'locked_update'): ?>
                 <div class="alert alert-danger alert-dismissible mt-2">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <h5><i class="icon fas fa-lock"></i> Thao tác thất bại!</h5>
                     Dữ liệu này đã được sử dụng trong một Đợt chấm điểm. Để bảo toàn lịch sử, bạn không thể Sửa dữ liệu này. Hãy tạo mới thay thế!
                 </div>
             <?php endif; ?>
         </div><!-- /.container-fluid -->
     </section>

     <!-- Nhựt sửa: Thêm nút bật/tắt form thêm mới -->
     <section class="content mb-2">
         <div class="col-12">
             <button type="button" class="btn btn-primary" id="btn-toggle-add" onclick="toggle_add_form()">
                 <i class="fas fa-plus"></i> Thêm mới Mục
             </button>
         </div>
     </section>

     <!-- Nhựt sửa: Ẩn form thêm mới mặc định -->
     <section class="content" id="div_add_form" style="display: none;">
         <form class="row form" action="quan-ly-muc/action.php?req=add" method="post" enctype="multipart/form-data">
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
                             <label for="">Khoản <span class="color-crimson">(*)</span></label>
                             <select class="form-control" name="id_khoan" id="id_khoan_add" required onchange="loadThuTu(this.value)">
                                 <option value="">-- Chọn Khoản --</option>
                                 <?php foreach ($khoan__Get_All as $item):?>
                                 <option value="<?=$item->id_khoan?>"><?=$item->ten_khoan?> -
                                     <?=$dieu->dieu__Get_By_Id($item->id_dieu)->ten_dieu?></option>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                         <div class="form-group">
                             <label for="">Tên mục <span class="color-crimson">(*)</span></label>
                             <input type="text" id="ten_muc" name="ten_muc" class="form-control" required
                                 placeholder="Nhập tên mục">
                         </div>
                         <div class="form-group">
                             <label for="">Nội dung chi tiết</label>
                             <textarea id="ghi_chu" name="ghi_chu" class="form-control" required
                                 placeholder="Nhập nội dung chi tiết"></textarea>
                         </div>
                         <div class="form-group">
                             <!-- quân sửa: Đổi thành thẻ select để chọn thứ tự linh động thông qua AJAX -->
                             <label for="">Thứ tự <span class="color-crimson">(*)</span></label>
                             <select id="thu_tu" name="thu_tu" class="form-control" required>
                                 <option value="">-- Vui lòng chọn Khoản trước --</option>
                             </select>
                         </div>
                         <!-- quân sửa: Thêm ô nhập Điểm tối đa cho Mục -->
                         <div class="form-group">
                             <label for="">Điểm tối đa <span class="color-crimson">(*)</span></label>
                             <input type="number" id="diem_toi_da" name="diem_toi_da" class="form-control" required
                                 placeholder="Nhập điểm tối đa của mục" min="0">
                         </div>
                         <!-- quân sửa: Thêm tuỳ chọn Yêu cầu minh chứng -->
                         <div class="form-group">
                             <div class="icheck-danger d-inline">
                                 <input type="checkbox" id="co_minh_chung" name="co_minh_chung" value="1">
                                 <label for="co_minh_chung" class="text-danger">Yêu cầu sinh viên nộp minh chứng cho Mục này</label>
                             </div>
                         </div>
                         <div class="form-group">
                             <label for="">Quyền chấm điểm</label>
                             <div class="row">
                                 <div class="col-md-3">
                                     <div class="icheck-primary d-inline">
                                         <input type="checkbox" id="quyen_sv" name="quyen_sv" value="1" checked>
                                         <label for="quyen_sv">Sinh viên</label>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="icheck-primary d-inline">
                                         <input type="checkbox" id="quyen_lt" name="quyen_lt" value="1" checked>
                                         <label for="quyen_lt">Lớp trưởng/BCS</label>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="icheck-primary d-inline">
                                         <input type="checkbox" id="quyen_btdk" name="quyen_btdk" value="1" checked>
                                         <label for="quyen_btdk">Bí thư đoàn khoa</label>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="icheck-primary d-inline">
                                         <input type="checkbox" id="quyen_gv" name="quyen_gv" value="1" checked>
                                         <label for="quyen_gv">Giảng viên/CVHT</label>
                                     </div>
                                 </div>
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
                 <h3 class="card-title">Danh sách Mục</h3>
                 <div class="card-tools">
                     <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                         <i class="fas fa-minus"></i>
                     </button>
                 </div>
             </div>
             <!-- /.card-header -->
             <div class="card-body">
                 <form action="" method="GET" class="mb-4">
                     <input type="hidden" name="page" value="quan-ly-muc">
                     <div class="row">
                         <div class="col-md-4">
                             <select name="id_khoan" class="form-control" onchange="this.form.submit()">
                                 <option value="">-- Xem tất cả các khoản --</option>
                                 <?php foreach ($khoan__Get_All as $item): ?>
                                     <option value="<?=$item->id_khoan?>" <?=($id_khoan_filter == $item->id_khoan) ? 'selected' : ''?>>
                                         <?=$item->ten_khoan?>
                                     </option>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                     </div>
                 </form>
                 <table id="tablejs" class="table table-bordered table-striped display responsive" width="100%">
                     <thead>
                         <tr>
                             <th style="width: 3%; white-space: nowrap;">STT</th>
                             <th style="width: 10%; white-space: nowrap;">Tên điều</th>
                             <th style="width: 22%; white-space: nowrap;">Tên khoản</th>
                             <th style="width: 25%; white-space: nowrap;">Tên mục</th>
                             <th style="width: 8%; white-space: nowrap;">Điểm tối đa</th>
                             <th style="width: 8%; white-space: nowrap;">Minh chứng</th>
                             <th style="width: 12%; white-space: nowrap;">Ghi chú</th>
                             <th style="width: 12%; white-space: nowrap;">Thao tác</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($muc__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <?php 
                                 $kh = $khoan->khoan__Get_By_Id($item->id_khoan);
                                 $di = $kh ? $dieu->dieu__Get_By_Id($kh->id_dieu) : null;
                             ?>
                             <td class="text-center" style="text-align: center !important;"><?= $di ? htmlspecialchars($di->ten_dieu, ENT_QUOTES, 'UTF-8') : '<span class="text-danger">Chưa xác định</span>' ?></td>
                             <td><?= $kh ? htmlspecialchars($kh->ten_khoan, ENT_QUOTES, 'UTF-8') : '<span class="text-danger">Chưa xác định</span>' ?></td>
                             <td><?=$item->ten_muc?></td>
                             <td class="text-center" style="text-align: center !important;"><?=$item->diem_toi_da?></td>
                             <td class="text-center" style="text-align: center !important;">
                                 <?php if($item->co_minh_chung == 1): ?>
                                     <span class="badge badge-danger"><i class="fas fa-file-upload"></i> Có</span>
                                 <?php else: ?>
                                     <span class="badge badge-secondary">Không</span>
                                 <?php endif; ?>
                             </td>
                             <td><?=$item->ghi_chu?></td>
                             <td>
                                 <?php if($muc->muc__Is_Edit_Locked($item->id_muc)): ?>
                                     <button class="btn btn-warning disabled" title="Mẫu đã phát sinh đợt chấm cho nội dung này.">
                                         <i class="ri-edit-2-line"></i>
                                     </button>
                                 <?php else: ?>
                                     <a href="#" type=" button" class="btn btn-warning"
                                         onclick="update_obj(<?=$item->id_muc?>)">
                                         <i class="ri-edit-2-line"></i>
                                     </a>
                                 <?php endif; ?>
                                 <a href="#" type="button" class="btn btn-danger"
                                     onclick="return confirm_sweet('quan-ly-muc/action.php?req=delete&id_muc=<?=$item->id_muc?>')">
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
          "dom": "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end align-items-center'Bf>>rtip",
          "pagingType": "full_numbers",
          "pageLength": 10,
          "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
          "language": {
              "decimal": ",",
              "thousands": ".",
              "emptyTable": "Không có dữ liệu trong bảng",
              "info": "Hiển thị _START_ - _END_ của _TOTAL_ mục",
              "infoEmpty": "Hiển thị 0 - 0 của 0 mục",
              "infoFiltered": "(lọc từ _MAX_ mục)",
              "infoPostFix": "",
              "lengthMenu": "Hiển thị _MENU_ mục",
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
            btn.html('<i class="fas fa-times"></i> Đóng lại').removeClass('btn-primary').addClass('btn-secondary');
        } else {
            btn.html('<i class="fas fa-plus"></i> Thêm mới Mục').removeClass('btn-secondary').addClass('btn-primary');
        }
    });
}

function update_obj(id_muc) {
    $.post('quan-ly-muc/update.php', {
        'id_muc': id_muc,
    }, function(data) {
        // Nhựt sửa: Ẩn form thêm mới khi mở form cập nhật
        $('#div_add_form').slideUp(300);
        $('#btn-toggle-add').html('<i class="fas fa-plus"></i> Thêm mới Mục').removeClass('btn-secondary').addClass('btn-primary');
        $('#div_update').html(data);
    });
}

function cancel_update() {
    $("#div_update").html('');
}

 // quân sửa: Hàm gọi AJAX load thứ tự còn trống
 function loadThuTu(id_khoan, current_thu_tu = 0, target_id = '#thu_tu') {
     if (!id_khoan) {
         $(target_id).html('<option value="">-- Vui lòng chọn Khoản trước --</option>');
         return;
     }
     $.post('quan-ly-muc/ajax_get_thu_tu.php', {
         'id_khoan': id_khoan,
         'current_thu_tu': current_thu_tu
     }, function(data) {
         $(target_id).html(data);
     });
 }

 // Chạy lần đầu khi load trang nếu đã chọn sẵn khoản
 $(document).ready(function() {
     var firstKhoan = $('#id_khoan_add').val();
     if(firstKhoan) {
         loadThuTu(firstKhoan);
     }
 });
 </script>