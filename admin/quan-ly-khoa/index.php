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

     <section class="content">
         <form class="row form" action="quan-ly-khoa/action.php?req=add" method="post"
             enctype="multipart/form-data">
             <input type="hidden" name="csrf_token" value="<?=khoa_escape($_SESSION['csrf_token'])?>">
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
                             <label for="">Tên khoa <span class="color-crimson">(*)</span></label>
                             <!-- Nhựt sửa lỗi: giới hạn tên khoa ở client khớp với validate server-side và DB. -->
                              <input type="text" id="ten_khoa" name="ten_khoa" class="form-control" required maxlength="50"
                                  value="<?=khoa_escape(khoa_old_value('ten_khoa', 'add'))?>" placeholder="Nhập tên khoa">
                         </div>
                         <div class="form-group">
                             <label for="">Ghi chú</label>
                              <textarea id="ghi_chu" name="ghi_chu" class="form-control" maxlength="2000"
                                  placeholder="Nhập ghi chú"><?=khoa_escape(khoa_old_value('ghi_chu', 'add'))?></textarea>
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


  <style>
      .dataTables_wrapper .dt-buttons {
          margin-right: 15px;
      }
      /* Style the main "Xuất dữ liệu" collection button to match the dark blue sidebar color */
      .dataTables_wrapper .dt-buttons .buttons-collection {
          background-color: #0f2a5a !important;
          border-color: #0f2a5a !important;
          color: #fff !important;
          border-radius: 4px !important;
          padding: 6px 12px !important;
          font-size: 14px !important;
          font-weight: 500 !important;
          box-shadow: none !important;
          transition: all 0.15s ease-in-out !important;
          display: inline-flex !important;
          align-items: center !important;
          border: 1px solid transparent !important;
      }
      .dataTables_wrapper .dt-buttons .buttons-collection:hover {
          background-color: #0c2147 !important;
          border-color: #0c2147 !important;
      }
      .dataTables_wrapper .dataTables_filter {
          margin-top: 0 !important;
      }
      .dataTables_wrapper .dt-button-collection,
      .dataTables_wrapper .dt-button-collection > div,
      .dataTables_wrapper .dt-button-collection .dropdown-menu {
          display: grid !important;
          grid-auto-flow: column !important;
          grid-template-rows: repeat(3, auto) !important;
          grid-template-columns: repeat(2, 80px) !important;
          gap: 6px !important;
          width: auto !important;
          min-width: auto !important;
      }
      .dataTables_wrapper .dt-button-collection {
          padding: 8px 8px 5px 8px !important;
          border-radius: 6px !important;
          border: 1px solid #dcdcdc !important;
          box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
          background: #fff !important;
          margin-top: 5px !important;
      }
      /* Reset style if there is an inner wrapper element and make it span full width of grid */
      .dataTables_wrapper .dt-button-collection > div,
      .dataTables_wrapper .dt-button-collection .dropdown-menu {
          grid-column: span 2 !important;
          background: transparent !important;
          border: none !important;
          box-shadow: none !important;
          padding: 0 !important;
          margin: 0 !important;
          position: static !important;
          transform: none !important;
      }
      .dataTables_wrapper .dt-button-collection .dt-button,
      .dataTables_wrapper .dt-button-collection .dropdown-item,
      .dataTables_wrapper .dt-button-collection .buttons-copy,
      .dataTables_wrapper .dt-button-collection .buttons-csv,
      .dataTables_wrapper .dt-button-collection .buttons-excel,
      .dataTables_wrapper .dt-button-collection .buttons-pdf,
      .dataTables_wrapper .dt-button-collection .buttons-print {
          margin-bottom: 0 !important;
          border-radius: 6px !important;
          padding: 6px 8px !important;
          font-weight: 600 !important;
          display: inline-flex !important;
          align-items: center !important;
          justify-content: center !important;
          font-size: 13px !important;
          transition: all 0.2s ease-in-out !important;
          width: 80px !important;
          max-width: 80px !important;
          min-width: 80px !important;
          flex: none !important;
          white-space: nowrap !important;
          background-color: #fff !important;
          border: 1px solid #dee2e6 !important;
      }
      .dataTables_wrapper .dt-button-collection .dt-button i,
      .dataTables_wrapper .dt-button-collection .dropdown-item i,
      .dataTables_wrapper .dt-button-collection .buttons-copy i,
      .dataTables_wrapper .dt-button-collection .buttons-csv i,
      .dataTables_wrapper .dt-button-collection .buttons-excel i,
      .dataTables_wrapper .dt-button-collection .buttons-pdf i,
      .dataTables_wrapper .dt-button-collection .buttons-print i {
          margin-right: 6px !important;
          font-size: 13px !important;
          width: 14px !important;
          text-align: center !important;
      }
      /* Style specific buttons inside the collection dropdown to match action buttons */
      /* Copy button - Grey */
      .dataTables_wrapper .dt-button-collection .buttons-copy {
          color: #6c757d !important;
      }
      .dataTables_wrapper .dt-button-collection .buttons-copy:hover {
          background-color: rgba(108, 117, 125, 0.08) !important;
          border-color: #6c757d !important;
      }
      /* CSV button - Greenish Teal */
      .dataTables_wrapper .dt-button-collection .buttons-csv {
          color: #17a2b8 !important;
      }
      .dataTables_wrapper .dt-button-collection .buttons-csv:hover {
          background-color: rgba(23, 162, 184, 0.08) !important;
          border-color: #17a2b8 !important;
      }
      /* Excel button - Green */
      .dataTables_wrapper .dt-button-collection .buttons-excel {
          color: #16a34a !important;
      }
      .dataTables_wrapper .dt-button-collection .buttons-excel:hover {
          background-color: rgba(22, 163, 74, 0.08) !important;
          border-color: #16a34a !important;
      }
      /* PDF button - Red */
      .dataTables_wrapper .dt-button-collection .buttons-pdf {
          color: #dc2626 !important;
      }
      .dataTables_wrapper .dt-button-collection .buttons-pdf:hover {
          background-color: rgba(220, 38, 38, 0.08) !important;
          border-color: #dc2626 !important;
      }
      /* Print button - Orange/Yellow */
      .dataTables_wrapper .dt-button-collection .buttons-print {
          color: #d97706 !important;
      }
      .dataTables_wrapper .dt-button-collection .buttons-print:hover {
          background-color: rgba(217, 119, 6, 0.08) !important;
          border-color: #d97706 !important;
      }
  </style>
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

function update_obj(id_khoa) {
    $.ajax({
        url: 'quan-ly-khoa/update.php',
        method: 'POST',
        data: { 'id_khoa': id_khoa },
        success: function(data) {
            $(".card.card-success").addClass('collapsed-card');
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
    $(".card.card-success").removeClass('collapsed-card');
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
