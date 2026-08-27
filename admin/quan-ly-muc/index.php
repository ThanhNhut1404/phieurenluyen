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

    $muc_old_input = isset($_SESSION['muc_old_input']) && is_array($_SESSION['muc_old_input']) ? $_SESSION['muc_old_input'] : array();
    if (isset($muc_old_input['context']) && $muc_old_input['context'] === 'add') {
        unset($_SESSION['muc_old_input']);
    }

    if (!function_exists('muc_escape')) {
        function muc_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('muc_old_value')) {
        function muc_old_value($field, $context, $default = '') {
            global $muc_old_input;
            if (isset($muc_old_input['context']) && $muc_old_input['context'] === $context && isset($muc_old_input[$field])) {
                return $muc_old_input[$field];
            }
            return $default;
        }
    }

    $muc__Get_All = $muc->muc__Get_All();
    $khoan__Get_All = $khoan->khoan__Get_All();
 ?>


 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header pb-0">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý Mục</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý mục</li>
                     </ol>
                 </div>
             </div>
             

         </div><!-- /.container-fluid -->
     </section>

     <?php $is_add_error = isset($muc_old_input['context']) && $muc_old_input['context'] === 'add'; ?>

     <!-- Nhựt sửa: Thêm nút bật/tắt form thêm mới -->
     <section class="content mb-2">
         <button type="button" class="btn <?= $is_add_error ? 'btn-cancel-custom' : 'btn-success' ?> font-weight-bold" id="btn-toggle-add" onclick="toggle_add_form()">
             <i class="fas <?= $is_add_error ? 'fa-times' : 'fa-plus' ?>"></i> <?= $is_add_error ? '' : 'Thêm mới' ?>
         </button>
     </section>

     <!-- Nhựt sửa: Ẩn form thêm mới mặc định -->
     <section class="content" id="div_add_form" <?= $is_add_error ? '' : 'style="display: none;"' ?>>
         <form class="row form" action="quan-ly-muc/action.php?req=add" method="post" enctype="multipart/form-data">
             <input type="hidden" name="csrf_token" value="<?=muc_escape($_SESSION['csrf_token'])?>">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title">Thêm mới Mục</h3>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="id_khoan_add">Khoản <span class="color-crimson">*</span></label>
                                     <select class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-khoan') ? 'is-invalid' : '' ?>" name="id_khoan" id="id_khoan_add" required onchange="loadThuTu(this.value, '<?=muc_escape(muc_old_value('thu_tu', 'add'))?>')">
                                         <option value="">-- Chọn Khoản --</option>
                                         <?php $old_khoan = muc_old_value('id_khoan', 'add'); ?>
                                         <?php foreach ($khoan__Get_All as $item):?>
                                         <?php 
                                            $dieu_obj = $dieu->dieu__Get_By_Id($item->id_dieu);
                                            $ban_sao_text_dieu = preg_match('/\(Bản sao thứ \d+\)/', $dieu_obj->ghi_chu ?? '', $m) ? ' ' . $m[0] : '';
                                            $ban_sao_text_khoan = preg_match('/\(Bản sao thứ \d+\)/', $item->ghi_chu, $m) ? ' ' . $m[0] : ''; 
                                         ?>
                                         <option value="<?=$item->id_khoan?>" <?= $old_khoan == $item->id_khoan ? 'selected' : '' ?>><?=$dieu_obj->ten_dieu . $ban_sao_text_dieu?> -
                                             <?=$item->ten_khoan . $ban_sao_text_khoan?></option>
                                         <?php endforeach; ?>
                                     </select>
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-khoan'): ?>
                                         <small class="text-danger mt-1">Khoản không hợp lệ.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <!-- quân sửa: Đổi thành thẻ select để chọn thứ tự linh động thông qua AJAX -->
                                     <label class="label-sidebar" for="thu_tu">Thứ tự <span class="color-crimson">*</span></label>
                                     <select id="thu_tu" name="thu_tu" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-thutu') ? 'is-invalid' : '' ?>" required>
                                         <option value="">-- Vui lòng chọn Khoản trước --</option>
                                     </select>
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-thutu'): ?>
                                         <small class="text-danger mt-1">Thứ tự không hợp lệ.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                         </div>
                         
                         <div class="row">
                             <div class="col-6">
                                 <!-- quân sửa: Thêm ô nhập Điểm tối đa cho Mục -->
                                 <div class="form-group">
                                     <label class="label-sidebar" for="diem_toi_da">Điểm tối đa <span class="color-crimson">*</span></label>
                                     <input type="number" id="diem_toi_da" name="diem_toi_da" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-diem') ? 'is-invalid' : '' ?>" required
                                         placeholder="Nhập điểm tối đa của mục" min="0" value="<?=muc_escape(muc_old_value('diem_toi_da', 'add', '0'))?>">
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-diem'): ?>
                                         <small class="text-danger mt-1">Điểm tối đa không hợp lệ.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="ghi_chu">Nội dung chi tiết</label>
                                     <input type="text" id="ghi_chu" name="ghi_chu" class="form-control"
                                         placeholder="Nhập nội dung chi tiết" value="<?=muc_escape(muc_old_value('ghi_chu', 'add'))?>">
                                 </div>
                             </div>
                             </div>

                             <div class="form-group">
                                 <label class="label-sidebar" for="ten_muc">Tên mục <span class="color-crimson">*</span></label>
                                 <textarea id="ten_muc" name="ten_muc" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'invalid-ten') ? 'is-invalid' : '' ?>" required
                                     placeholder="Nhập tên mục"><?=muc_escape(muc_old_value('ten_muc', 'add'))?></textarea>
                                 <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'invalid-ten'): ?>
                                     <small class="text-danger mt-1">Tên mục không được để trống.</small>
                                 <?php endif; ?>
                             </div>
                         
                         <!-- quân sửa: Thêm tuỳ chọn Yêu cầu minh chứng -->
                         <div class="form-group">
                             <div class="icheck-danger d-inline">
                                 <input type="checkbox" id="co_minh_chung" name="co_minh_chung" value="1" <?= muc_old_value('co_minh_chung', 'add', 0) == 1 ? 'checked' : '' ?>>
                                 <label for="co_minh_chung" class="text-danger">Yêu cầu sinh viên nộp minh chứng cho Mục này</label>
                             </div>
                         </div>
                         <div class="form-group">
                             <label class="label-sidebar">Quyền chấm điểm</label>
                             <div class="row">
                                 <div class="col-md-3 text-center">
                                     <div class="icheck-primary d-inline">
                                         <input type="checkbox" id="quyen_sv" name="quyen_sv" value="1" <?= (!$is_add_error || muc_old_value('quyen_sv', 'add', 0) == 1) ? 'checked' : '' ?>>
                                         <label for="quyen_sv">Sinh viên</label>
                                     </div>
                                 </div>
                                 <div class="col-md-3 text-center">
                                     <div class="icheck-primary d-inline">
                                         <input type="checkbox" id="quyen_lt" name="quyen_lt" value="1" <?= (!$is_add_error || muc_old_value('quyen_lt', 'add', 0) == 1) ? 'checked' : '' ?>>
                                         <label for="quyen_lt">Lớp trưởng/Bí thư</label>
                                     </div>
                                 </div>
                                 <div class="col-md-3 text-center">
                                     <div class="icheck-primary d-inline">
                                         <input type="checkbox" id="quyen_btdk" name="quyen_btdk" value="1" <?= (!$is_add_error || muc_old_value('quyen_btdk', 'add', 0) == 1) ? 'checked' : '' ?>>
                                         <label for="quyen_btdk">BCH Đoàn Khoa/BM</label>
                                     </div>
                                 </div>
                                 <div class="col-md-3 text-center">
                                     <div class="icheck-primary d-inline">
                                         <input type="checkbox" id="quyen_gv" name="quyen_gv" value="1" <?= (!$is_add_error || muc_old_value('quyen_gv', 'add', 0) == 1) ? 'checked' : '' ?>>
                                         <label for="quyen_gv">Cố vấn học tập</label>
                                     </div>
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
                 <h3 class="card-title">Danh sách Mục</h3>
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
                             <td class="text-center" style="text-align: center !important;"><?= $di ? htmlspecialchars($di->ten_dieu, ENT_QUOTES, 'UTF-8') . (preg_match('/\(Bản sao thứ \d+\)/', $di->ghi_chu ?? '', $m) ? " <small class='text-muted'>{$m[0]}</small>" : "") : '<span class="text-danger">Chưa xác định</span>' ?></td>
                             <td><?= $kh ? htmlspecialchars($kh->ten_khoan, ENT_QUOTES, 'UTF-8') . (preg_match('/\(Bản sao thứ \d+\)/', $kh->ghi_chu ?? '', $m) ? " <small class='text-muted'>{$m[0]}</small>" : "") : '<span class="text-danger">Chưa xác định</span>' ?></td>
                             <td><?=$item->ten_muc?><?php $bs_m = preg_match('/\(Bản sao thứ \d+\)/', $item->ghi_chu, $m) ? $m[0] : ''; echo $bs_m ? " <small class='text-muted'>$bs_m</small>" : ""; ?></td>
                             <td class="text-center" style="text-align: center !important;"><?=$item->diem_toi_da?></td>
                             <td class="text-center" style="text-align: center !important;">
                                 <?php if($item->co_minh_chung == 1): ?>
                                     <span class="badge badge-success">Có</span>
                                 <?php else: ?>
                                     <span class="badge badge-danger">Không</span>
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
                                 <a href="#" type="button" class="btn btn-danger m-1"
                                     onclick="return confirm_delete_sweet('quan-ly-muc/action.php?req=delete&id_muc=<?=$item->id_muc?>&csrf_token=<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>', 'Mục')">
                                     <i class="ri-delete-bin-line"></i>
                                 </a>
                                 <a href="#" type="button" class="btn btn-secondary m-1" title="Nhân bản Mục"
                                     onclick="return confirm_copy_sweet('quan-ly-muc/action.php?req=copy&id_muc=<?=$item->id_muc?>&csrf_token=<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>')">
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
                  
                  var dieuOptions = [];
                  
                  table.rows().every(function() {
                      var data = this.data();
                      var dieu = $('<div>').html(data[1]).text().trim();
                      
                      if (dieu !== '' && dieuOptions.indexOf(dieu) === -1) dieuOptions.push(dieu);
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
              }

              $('#filter_dieu').on('change', function() {
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
                  
                  table.column(1).search(dieuVal ? '^' + $.fn.dataTable.util.escapeRegex(dieuVal) + '$' : '', true, false)
                       .draw();
                  $('#custom-filter-menu').fadeOut(200);
              });

              $('#btn-cancel-filter').on('click', function() {
                  $('#filter_dieu').val('');
                  updateCascadeDropdowns();
                  table.column(1).search('')
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

function update_obj(id_muc) {
    $.post('quan-ly-muc/update.php', {
        'id_muc': id_muc,
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
 document.addEventListener("DOMContentLoaded", function() {
     var firstKhoan = $('#id_khoan_add').val();
     if(firstKhoan) {
         loadThuTu(firstKhoan);
     }
     
     $('#ten_muc').summernote({
         height: 150,
         toolbar: [
             ['font', ['bold', 'italic', 'underline', 'clear']],
             ['color', ['color']],
             ['para', ['ul', 'ol', 'paragraph']],
             ['insert', ['link']],
             ['view', ['fullscreen', 'codeview']]
         ]
     });
     
     <?php if (isset($_GET['status'])): ?>
         <?php if ($_GET['status'] == 'failed_over_limit'): ?>
             Toast.fire('Lỗi!', 'Tổng điểm của các Mục đã vượt quá Điểm tối đa của Khoản. Vui lòng kiểm tra lại!', 'error');
         <?php elseif ($_GET['status'] == 'locked_by_dotchamdiem'): ?>
             Toast.fire('Cảnh báo!', 'Dữ liệu này đang được sử dụng trong một Đợt chấm điểm. Bạn không thể Sửa/Xoá vào lúc này!', 'warning');
         <?php elseif ($_GET['status'] == 'locked_update'): ?>
             Toast.fire('Cảnh báo!', 'Dữ liệu này đã sử dụng trong lịch sử. Vui lòng tạo mới thay vì sửa!', 'warning');
         <?php elseif ($_GET['status'] == 'copy_success'): ?>
             Toast.fire('Thành công!', 'Đã nhân bản Mục thành công!', 'success');
         <?php endif; ?>
     <?php endif; ?>
 });

 function confirm_copy_sweet(url) {
     Swal.fire({
         title: 'Nhân bản Mục này?',
         html: 'Bạn có chắc chắn muốn nhân bản Mục này không?',
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



