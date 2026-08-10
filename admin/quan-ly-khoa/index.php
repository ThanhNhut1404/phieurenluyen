 <?php
    // require "../models/getModel.php";
    // Nhựt sửa lỗi: tạo CSRF token cho các thao tác thêm/sửa/xóa khoa.
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    // Nhựt sửa lỗi: giữ lại dữ liệu nhập gần nhất khi validate lỗi để cải thiện UX.
    $khoa_old_input = isset($_SESSION['khoa_old_input']) && is_array($_SESSION['khoa_old_input']) ? $_SESSION['khoa_old_input'] : array();
    // Nhựt sửa lỗi: chỉ giữ dữ liệu cũ cho form thêm đúng một lần rồi xóa để không bị dính lại khi quay trang.
    if (isset($khoa_old_input['context']) && $khoa_old_input['context'] === 'add') {
        unset($_SESSION['khoa_old_input']);
    }

    // Nhựt sửa lỗi: escape dữ liệu trước khi hiển thị để tránh stored XSS.
    if (!function_exists('khoa_escape')) {
        function khoa_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    // Nhựt sửa lỗi: lấy lại dữ liệu cũ theo đúng context của form thêm.
    if (!function_exists('khoa_old_value')) {
        function khoa_old_value($field, $context, $default = '') {
            global $khoa_old_input;
            if (isset($khoa_old_input['context']) && $khoa_old_input['context'] === $context && isset($khoa_old_input[$field])) {
                return $khoa_old_input[$field];
            }
            return $default;
        }
    }

    $khoa__Get_All = $khoa->khoa__Get_All();
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý khoa</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý khoa</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

      <!-- Nhựt sửa: Thêm nút mở Modal thêm mới Khoa -->
      <section class="content mb-2">
          <div class="col-12">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_add_khoa">
                  <i class="fas fa-plus mr-1"></i> Thêm mới Khoa
              </button>
          </div>
      </section>

      <style>
          /* Nhựt sửa: CSS tuỳ biến cho nút Hủy và hiệu ứng Hover chuẩn giống nút xóa trên bảng */
          .btn-cancel-custom {
              border-radius: 6px;
              font-weight: 550;
              padding: 6px 16px;
              background-color: #ffffff !important;
              color: #dc3545 !important;
              border: 1px solid #dc3545 !important;
              transition: all 0.2s ease;
          }
          .btn-cancel-custom:hover {
              background-color: #fdf2f2 !important; /* màu nền đỏ nhạt khi rê chuột vào */
              color: #dc3545 !important;
              border-color: #dc3545 !important;
          }
          /* Nhựt sửa: CSS tuỳ biến cho nút Thêm mới và hiệu ứng sáng lên khi hover */
          .btn-add-custom {
              border-radius: 6px;
              font-weight: 550;
              padding: 6px 16px;
              background-color: #28a745 !important;
              border-color: #28a745 !important;
              color: #ffffff !important;
              transition: all 0.2s ease;
          }
          .btn-add-custom:hover {
              background-color: #2ebd59 !important; /* màu xanh lá sáng hơn khi hover */
              border-color: #2ebd59 !important;
              color: #ffffff !important;
          }
      </style>
      <!-- Nhựt sửa: Modal Thêm mới Khoa -->
      <div class="modal fade" id="modal_add_khoa" tabindex="-1" role="dialog" aria-labelledby="modalAddLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                  <form action="quan-ly-khoa/action.php?req=add" method="post" enctype="multipart/form-data">
                      <input type="hidden" name="csrf_token" value="<?=khoa_escape($_SESSION['csrf_token'])?>">
                      <div class="modal-header" style="background-color: #28a745; color: #ffffff; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 10px 20px;">
                          <h5 class="modal-title" id="modalAddLabel" style="font-weight: 600; font-size: 1.1rem;">Thêm mới Khoa</h5>
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff; opacity: 0.8; outline: none; font-weight: normal; margin-top: -2px;">
                               <span aria-hidden="true" style="font-size: 1.8rem; font-weight: normal;">&times;</span>
                           </button>
                      </div>
                      <div class="modal-body" style="padding: 20px 24px;">
                          <div class="form-group">
                               <label style="font-weight: bold; color: #000000;">Tên khoa <span class="color-crimson">(*)</span></label>
                              <input type="text" id="ten_khoa" name="ten_khoa" class="form-control" required maxlength="50"
                                     value="<?=khoa_escape(khoa_old_value('ten_khoa', 'add'))?>" placeholder="Nhập tên khoa"
                                     style="border-radius: 6px; border: 1px solid #ced4da;">
                          </div>
                          <div class="form-group">
                               <label style="font-weight: bold; color: #000000;">Ghi chú</label>
                              <textarea id="ghi_chu" name="ghi_chu" class="form-control" maxlength="2000"
                                        placeholder="Nhập ghi chú" style="border-radius: 6px; border: 1px solid #ced4da; height: 100px;"><?=khoa_escape(khoa_old_value('ghi_chu', 'add'))?></textarea>
                          </div>
                      </div>
                       <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 6px 16px; background-color: #f8f9fa; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; height: auto;">
                           <button type="button" class="btn btn-cancel-custom" data-dismiss="modal" style="margin-right: 8px !important;">Hủy</button>
                           <button type="submit" class="btn btn-add-custom">Thêm mới</button>
                       </div>
                  </form>
              </div>
          </div>
      </div>

      <!-- Nhựt sửa: Tự động bật Modal nếu có lỗi Validate từ Server -->
      <?php if (isset($khoa_old_input['context']) && $khoa_old_input['context'] === 'add'): ?>
      <script>
          window.addEventListener('load', function() {
              $('#modal_add_khoa').modal('show');
          });
      </script>
      <?php endif; ?>

     <section class="content" id="div_update">
     </section>

     <section class="content">
         <div class="card card-primary">
             <div class="card-header">
                 <h3 class="card-title">Danh sách Khoa</h3>
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
                             <th style="width: 30%;">Tên khoa</th>
                             <th style="width: 55%;">Ghi chú</th>
                             <th style="width: 10%;">Thao tác</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($khoa__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <td class="text-center" style="text-align: center !important;"><?=khoa_escape($item->ten_khoa)?></td>
                             <td><?=khoa_escape($item->ghi_chu)?></td>
                             <td>
                                 <a href="javascript:void(0)" class="btn  btn-warning m-2" onclick="return update_obj(<?=(int)$item->id_khoa?>)">
                                     <i class="ri-edit-2-line"></i>
                                 </a>
                                 <a href="#" type="button" class="btn  btn-danger m-2"
                                     onclick="return delete_obj(<?=(int)$item->id_khoa?>)">
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
             "info": "Hiển thị _START_ - _END_ của _TOTAL_ khoa",
             "infoEmpty": "Hiển thị 0 - 0 của 0 khoa",
             "infoFiltered": "(lọc từ _MAX_ khoa)",
             "infoPostFix": "",
             "lengthMenu": "Hiển thị _MENU_ khoa",
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

// Quân sửa: Hàm bật/tắt hiển thị form thêm mới
function toggle_add_form() {
    var addForm = $('#div_add_form');
    var btn = $('#btn-toggle-add');
    
    // Đóng form cập nhật nếu đang mở
    $("#div_update").html('');
    
    addForm.slideToggle(300, function() {
        if (addForm.is(':visible')) {
            btn.html('<i class="fas fa-times"></i> Đóng lại').removeClass('btn-primary').addClass('btn-secondary');
        } else {
            btn.html('<i class="fas fa-plus"></i> Thêm mới Khoa').removeClass('btn-secondary').addClass('btn-primary');
        }
    });
}

function update_obj(id_khoa) {
    $.ajax({
        url: 'quan-ly-khoa/update.php',
        method: 'POST',
        data: { 'id_khoa': id_khoa },
        success: function(data) {
            // Quân sửa: Ẩn form thêm mới khi mở form cập nhật
            $('#div_add_form').slideUp(300);
            $('#btn-toggle-add').html('<i class="fas fa-plus"></i> Thêm mới Khoa').removeClass('btn-secondary').addClass('btn-primary');
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
                text = 'Khoa cần sửa không tồn tại.';
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

function delete_obj(id_khoa) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: 'Thao tác này sẽ xóa khoa đã chọn.',
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
        form.action = 'quan-ly-khoa/action.php?req=delete';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id_khoa';
        idInput.value = id_khoa;
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

<?php if (isset($khoa_old_input['context']) && $khoa_old_input['context'] === 'update' && isset($khoa_old_input['id_khoa'])): ?>
window.addEventListener("load", function() {
    update_obj(<?=(int)$khoa_old_input['id_khoa']?>);
});
<?php endif; ?>
 </script>
