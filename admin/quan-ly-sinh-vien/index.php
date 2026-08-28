 <?php
    // require "../models/getModel.php";
    $sinhvien__Get_All = $sinhvien->sinhvien__Get_All();
    $lophoc__Get_All = $lophoc->lophoc__Get_All();
    if (isset($_GET['view'])) {
        $sinhvien__Get_All = $sinhvien->sinhvien__Get_By_Id_Lop_Hoc($_GET['view']);
        echo "<script>
            document.querySelector('.btn-tool').click();
            </script>";
    }
    
    $sinhvien_old_input = isset($_SESSION['sinhvien_old_input']) && is_array($_SESSION['sinhvien_old_input']) ? $_SESSION['sinhvien_old_input'] : array();
    
    function sinhvien_old_value($key, $context = 'add', $default = '') {
        global $sinhvien_old_input;
        if (isset($sinhvien_old_input['context']) && $sinhvien_old_input['context'] === $context && isset($sinhvien_old_input[$key])) {
            return $sinhvien_old_input[$key];
        }
        return $default;
    }

    if (isset($sinhvien_old_input['context']) && $sinhvien_old_input['context'] === 'add') {
        unset($_SESSION['sinhvien_old_input']);
    }
    ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header pb-0">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý sinh viên</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý sinh viên</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <?php $is_add_error = isset($sinhvien_old_input['context']) && $sinhvien_old_input['context'] === 'add'; ?>

     <!-- Nhựt sửa: Thêm nút bật/tắt form thêm mới -->
     <style>
         .btn-navy-custom {
             background-color: #1e2b58 !important;
             border-color: #1e2b58 !important;
             color: #ffffff !important;
             transition: filter 0.2s ease;
         }
         .btn-navy-custom:hover {
             filter: brightness(1.25);
             color: #ffffff !important;
         }
     </style>
     <section class="content mb-2">
         <button type="button" class="btn <?= $is_add_error ? 'btn-cancel-custom' : 'btn-success' ?> font-weight-bold" id="btn-toggle-add" onclick="toggle_add_form()">
             <i class="fas <?= $is_add_error ? 'fa-times' : 'fa-plus' ?>"></i> <?= $is_add_error ? '' : 'Thêm mới' ?>
         </button>
         <!-- Thêm nút toggle import excel -->
         <button type="button" class="btn btn-success text-white font-weight-bold ml-2" id="btn-toggle-import" onclick="toggle_import_form()">
             <i class="fas fa-file-excel"></i> Import dữ liệu
         </button>
     </section>

     <section class="content" id="div_import" style="display: none;">
         <form class=" row form" action="quan-ly-sinh-vien/action.php?req=import" method="post" enctype="multipart/form-data">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title font-weight-bold text-white">
                             Tải mẫu file excel  
                         </h3>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar">Chọn file <span class="color-crimson">*</span></label>
                                     <input type="file" id="file" name="file" class="form-control" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                 </div>
                             </div>

                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar">Lớp học <span class="color-crimson">*</span></label>
                                     <select class="form-control" name="id_lop_hoc" required>
                                         <option value="">Chọn Lớp học</option>
                                         <?php foreach ($lophoc__Get_All as $item) : ?>
                                             <option value="<?= $item->id_lop_hoc ?>"><?= $item->ten_lop_hoc ?></option>
                                         <?php endforeach; ?>
                                     </select>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <!-- /.card-body -->
                     <div class="card-footer py-2">
                         <a href="quan-ly-sinh-vien/action.php?req=export" class="btn btn-navy-custom float-left font-weight-bold"><i class="fas fa-download"></i> Tải mẫu</a>
                         <input type="submit" value="Import" class="btn btn-success float-right font-weight-bold">
                         <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" onclick="toggle_import_form()">Hủy</button>
                     </div>
                 </div>
                 <!-- /.card -->
             </div>
         </form>
     </section>

     <!-- Nhựt sửa: Ẩn form thêm mới mặc định -->
     <section class="content" id="div_add" style="<?= $is_add_error ? '' : 'display: none;' ?>">
         <form class="row form" action="quan-ly-sinh-vien/action.php?req=add" method="post" enctype="multipart/form-data">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title font-weight-bold">Thêm mới Sinh viên</h3>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar">Mã số sinh viên <span class="color-crimson">*</span></label>
                                     <input type="text" id="ma_sinh_vien" name="ma_sinh_vien" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'duplicate-ma-sinh-vien') ? 'is-invalid' : '' ?>" required placeholder="Nhập mã sinh viên" value="<?= isset($sinhvien_old_input['ma_sinh_vien']) ? htmlspecialchars($sinhvien_old_input['ma_sinh_vien']) : '' ?>">
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'duplicate-ma-sinh-vien'): ?>
                                         <small class="text-danger mt-1">Mã sinh viên đã tồn tại trong hệ thống.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar">Họ và tên <span class="color-crimson">*</span></label>
                                     <input type="text" id="ten_sinh_vien" name="ten_sinh_vien" class="form-control" required placeholder="Nhập tên sinh viên" value="<?= htmlspecialchars(sinhvien_old_value('ten_sinh_vien', 'add')) ?>">
                                 </div>
                             </div>
                         </div>
                         
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar">Giới tính <span class="color-crimson">*</span></label>
                                     <select class="form-control" name="gioi_tinh" required>
                                         <option value="">Chọn giới tính</option>
                                         <option value="0" <?= sinhvien_old_value('gioi_tinh', 'add') === '0' ? 'selected' : '' ?>>Nữ</option>
                                         <option value="1" <?= sinhvien_old_value('gioi_tinh', 'add') === '1' ? 'selected' : '' ?>>Nam</option>
                                     </select>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar">Ngày sinh <span class="color-crimson">*</span></label>
                                     <input type="date" id="ngay_sinh" name="ngay_sinh" class="form-control" required value="<?= htmlspecialchars(sinhvien_old_value('ngay_sinh', 'add', date('Y-m-d', strtotime('-22 years')))) ?>" min="<?= date('Y-m-d', strtotime('-100 years')) ?>" max="<?= date('Y-m-d', strtotime('-10 years')) ?>" placeholder="Nhập ngày sinh">
                                 </div>
                             </div>
                         </div>
                         
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar">Email <span class="color-crimson">*</span></label>
                                     <input type="email" id="email" name="email" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'duplicate-email-sinh-vien') ? 'is-invalid' : '' ?>" required placeholder="Nhập email" value="<?= isset($sinhvien_old_input['email']) ? htmlspecialchars($sinhvien_old_input['email']) : '' ?>">
                                     <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'duplicate-email-sinh-vien'): ?>
                                         <small class="text-danger mt-1">Email đã tồn tại trong hệ thống.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar">Lớp học <span class="color-crimson">*</span></label>
                                     <select class="form-control" name="id_lop_hoc" required>
                                         <option value="">Chọn Lớp học</option>
                                         <?php foreach ($lophoc__Get_All as $item) : ?>
                                             <option value="<?= $item->id_lop_hoc ?>" <?= sinhvien_old_value('id_lop_hoc', 'add') == $item->id_lop_hoc ? 'selected' : '' ?>><?= $item->ten_lop_hoc ?></option>
                                         <?php endforeach; ?>
                                     </select>
                                 </div>
                             </div>
                         </div>

                          <div class="row">
                             <div class="col-6">
<div class="form-group">
                                     <label class="label-sidebar">Số điện thoại 1 <span class="color-crimson">*</span></label>
                                     <input type="text" id="so_dien_thoai_1" name="so_dien_thoai_1" pattern="0[0-9]{9,10}" class="form-control" required title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 1" minlength="10" maxlength="11" value="<?= htmlspecialchars(sinhvien_old_value('so_dien_thoai_1', 'add')) ?>">
                                 </div>
                             </div>
                             <div class="col-6">
<div class="form-group">
                                     <label class="label-sidebar">Số điện thoại 2 <span class="color-crimson">*</span></label>
                                     <input type="text" id="so_dien_thoai_2" name="so_dien_thoai_2" pattern="0[0-9]{9,10}" class="form-control" required title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 2" minlength="10" maxlength="11" value="<?= htmlspecialchars(sinhvien_old_value('so_dien_thoai_2', 'add')) ?>">
                                 </div>
                             </div>
                         </div>
                         
                         <div class="row">
                             <div class="col-6">
<div class="form-group">
                                     <label class="label-sidebar">Địa chỉ liên lạc <span class="color-crimson">*</span></label>
                                     <!-- quân sửa: Chia nhỏ địa chỉ liên lạc thành 4 cấp -->
                                     <div class="row">
                                         <div class="col-6 mb-2">
                                             <select name="dc_ll_tinh" id="dc_ll_tinh" class="form-control" required>
                                                 <option value="">Chọn Tỉnh / Thành phố</option>
                                             </select>
                                         </div>
                                         <div class="col-6 mb-2">
                                             <select name="dc_ll_xa" id="dc_ll_xa" class="form-control" required>
                                                 <option value="">Chọn Phường / Xã</option>
                                             </select>
                                         </div>
                                         <div class="col-12 mb-2">
                                             <input type="text" name="dc_ll_chitiet" class="form-control" required placeholder="Số nhà, đường, khu phố" value="<?= htmlspecialchars(sinhvien_old_value('dc_ll_chitiet', 'add')) ?>">
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <div class="col-6">
<div class="form-group">
                                     <label class="label-sidebar">Địa chỉ thường trú <span class="color-crimson">*</span></label>
                                     <!-- quân sửa: Chia nhỏ địa chỉ thường trú thành 4 cấp -->
                                     <div class="row">
                                         <div class="col-6 mb-2">
                                             <select name="dc_tt_tinh" id="dc_tt_tinh" class="form-control" required>
                                                 <option value="">Chọn Tỉnh / Thành phố</option>
                                             </select>
                                         </div>
                                         <div class="col-6 mb-2">
                                             <select name="dc_tt_xa" id="dc_tt_xa" class="form-control" required>
                                                 <option value="">Chọn Phường / Xã</option>
                                             </select>
                                         </div>
                                         <div class="col-12 mb-2">
                                             <input type="text" name="dc_tt_chitiet" class="form-control" required placeholder="Số nhà, đường, khu phố" value="<?= htmlspecialchars(sinhvien_old_value('dc_tt_chitiet', 'add')) ?>">
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         

                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar">Chức vụ <span class="color-crimson">*</span></label>
                                     <select class="form-control" name="chuc_vu" required>
                                         <option value="">Chọn Chức vụ</option>
                                         <option value="0" <?= sinhvien_old_value('chuc_vu', 'add') === '0' ? 'selected' : '' ?>>Không có</option>
                                         <option value="1" <?= sinhvien_old_value('chuc_vu', 'add') === '1' ? 'selected' : '' ?>>Lớp trưởng</option>
                                         <option value="2" <?= sinhvien_old_value('chuc_vu', 'add') === '2' ? 'selected' : '' ?>>Bí thư</option>
                                     </select>
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
                 <h3 class="card-title">Danh sách Sinh viên</h3>
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
                             <th style="width: 5%; white-space: nowrap;">STT</th>
                             <th style="width: 8%; white-space: nowrap;">MSSV</th>
                             <th style="width: 25%; white-space: nowrap;">HỌ VÀ TÊN</th>
                             <th style="width: 8%; white-space: nowrap;">Giới tính</th>
                             <th style="width: 10%; white-space: nowrap;">Ngày sinh</th>
                             <th style="width: 12%; white-space: nowrap;">Số điện thoại 1</th>
                             <th style="width: 15%; white-space: nowrap;">Địa chỉ liên lạc</th>
                             <th style="width: 10%; white-space: nowrap;">Lớp học</th>
                             <th style="width: 7%; white-space: nowrap;">Thao tác</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php 
                         // TẠO MẢNG LƯU TRỮ LỚP HỌC ĐỂ TRÁNH N+1 QUERY
                         $arr_lop_hoc = [];
                         foreach ($lophoc__Get_All as $lh) {
                             $arr_lop_hoc[$lh->id_lop_hoc] = $lh->ten_lop_hoc;
                         }
                         $num = 0; 
                         ?>
                         <?php foreach ($sinhvien__Get_All as $item) : ?>
                             <tr>
                                 <td class="text-center" style="text-align: center !important;"><?= ++$num ?></td>
                                 <td class="text-center" style="text-align: center !important;"><?= htmlspecialchars($item->ma_sinh_vien ?? '') ?></td>
                                 <!-- quân sửa: Viết hoa chữ cái đầu của Tên riêng khi hiển thị ra danh sách -->
                                 <td><?= htmlspecialchars(mb_convert_case($item->ten_sinh_vien ?? '', MB_CASE_TITLE, "UTF-8")) ?></td>
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
                                 <td class="text-center" style="text-align: center !important;"><?= isset($arr_lop_hoc[$item->id_lop_hoc]) ? htmlspecialchars($arr_lop_hoc[$item->id_lop_hoc]) : '' ?></td>

                                 <td>
                                     <a href="#" type="button" class="btn  btn-warning m-2" onclick="update_obj(<?= (int)$item->id_sinh_vien ?>)">
                                         <i class="ri-edit-2-line"></i>
                                     </a>
                                     <a href="#" type="button" class="btn  btn-danger m-2" onclick="return confirm_delete_sweet('quan-ly-sinh-vien/action.php?req=delete&id_sinh_vien=<?= (int)$item->id_sinh_vien ?>', 'Sinh viên')">
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
                  "info": "Hiển thị _START_ - _END_ của _TOTAL_ sinh viên",
                  "infoEmpty": "Hiển thị 0 - 0 của 0 sinh viên",
                  "infoFiltered": "(lọc từ _MAX_ sinh viên)",
                  "infoPostFix": "",
                  "lengthMenu": "Hiển thị _MENU_ sinh viên",
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
                          <label class="label-sidebar">Lớp học:</label>
                          <select id="filter_lop_hoc" class="form-control form-control-sm">
                              <option value="">-- Tất cả lớp học --</option>
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
                      var selectedLop = $('#filter_lop_hoc').val() || "";
                      
                      var lopHocOptions = [];
                      
                      table.rows().every(function() {
                          var data = this.data();
                          var lophoc = $('<div>').html(data[7]).text().trim();
                          
                          if (lophoc !== '' && lopHocOptions.indexOf(lophoc) === -1) lopHocOptions.push(lophoc);
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
                      
                      populate('filter_lop_hoc', lopHocOptions, selectedLop, '-- Tất cả lớp học --');
                  }

                  $('#filter_lop_hoc').on('change', function() {
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
                      var lopVal = $('#filter_lop_hoc').val();
                      var gioiTinhVal = $('#filter_gioi_tinh').val();
                      
                      table.column(7).search(lopVal ? '^' + $.fn.dataTable.util.escapeRegex(lopVal) + '$' : '', true, false)
                           .column(4).search(gioiTinhVal ? '^' + $.fn.dataTable.util.escapeRegex(gioiTinhVal) + '$' : '', true, false)
                           .draw();
                      $('#custom-filter-menu').fadeOut(200);
                  });

                  $('#btn-cancel-filter').on('click', function() {
                      $('#filter_lop_hoc').val('');
                      $('#filter_gioi_tinh').val('');
                      updateCascadeDropdowns();
                      table.column(7).search('')
                           .column(4).search('')
                           .draw();
                      $('#custom-filter-menu').fadeOut(200);
                  });
              }
          });
      });

// Nhựt sửa: Hàm bật/tắt hiển thị form thêm mới
function toggle_add_form() {
    var addForm = $('#div_add');
    var btn = $('#btn-toggle-add');
    
    // Đóng form cập nhật nếu đang mở
    $("#div_update").html('');
    
    // Đóng form import nếu đang mở
    $('#div_import').slideUp(300);
    $('#btn-toggle-import').html('<i class="fas fa-file-excel"></i> Import dữ liệu').removeClass('btn-cancel-custom').addClass('btn-success text-white');
    
    addForm.slideToggle(300, function() {
        if (addForm.is(':visible')) {
            btn.html('<i class="fas fa-times"></i>').removeClass('btn-success').addClass('btn-cancel-custom');
        } else {
            btn.html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-cancel-custom').addClass('btn-success');
        }
    });
}

function toggle_import_form() {
    var importForm = $('#div_import');
    var btn = $('#btn-toggle-import');
    
    // Đóng form cập nhật nếu đang mở
    $("#div_update").html('');
    
    // Đóng form thêm mới nếu đang mở
    $('#div_add').slideUp(300);
    $('#btn-toggle-add').html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-cancel-custom').addClass('btn-success');
    
    importForm.slideToggle(300, function() {
        if (importForm.is(':visible')) {
            btn.html('<i class="fas fa-times"></i>').removeClass('btn-success text-white').addClass('btn-cancel-custom');
        } else {
            btn.html('<i class="fas fa-file-excel"></i> Import dữ liệu').removeClass('btn-cancel-custom').addClass('btn-success text-white');
        }
    });
}

function update_obj(id_sinh_vien) {
    $.post('quan-ly-sinh-vien/update.php', {
        'id_sinh_vien': id_sinh_vien,
    }, function(data) {
        // Nhựt sửa: Ẩn form thêm mới khi mở form cập nhật
        $('#div_add').slideUp(300);
        $('#btn-toggle-add').html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-cancel-custom').addClass('btn-success');
        
        // Ẩn form import
        $('#div_import').slideUp(300);
        $('#btn-toggle-import').html('<i class="fas fa-file-excel"></i> Import dữ liệu').removeClass('btn-cancel-custom').addClass('btn-success text-white');
        
        $('#div_update').html(data);
    });
}

function cancel_update() {
    $("#div_update").html('');
}

<?php $is_update_error = isset($sinhvien_old_input['context']) && $sinhvien_old_input['context'] === 'update'; ?>
<?php if ($is_update_error): ?>
    window.addEventListener('DOMContentLoaded', (event) => {
        $.post('quan-ly-sinh-vien/update.php', {
            id_sinh_vien: <?= $sinhvien_old_input['id_sinh_vien'] ?? 0 ?>,
            error_status: '<?= $_GET['status'] ?? '' ?>'
        }, function(data) {
            $('#div_update').html(data);
            $('#div_update').show();
            // hide add form
            $('#div_add').slideUp(300);
            $('#btn-toggle-add').html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-cancel-custom').addClass('btn-success');
        });
    });
<?php endif; ?>

// Logic load Tỉnh/Huyện/Xã từ API
let vnProvincesData = null;

function loadVNProvinces(prefix, preselect = null) {
    var $tinh = $('#' + prefix + '_tinh');
    var $xa = $('#' + prefix + '_xa');
    
    function resetDropdown($select, defaultText) {
        if ($select.hasClass("select2-hidden-accessible")) {
            $select.select2('destroy');
        }
        $select.empty().append('<option value="">' + defaultText + '</option>');
        $select.select2({ theme: 'bootstrap4', width: '100%' });
    }
    
    function populateDropdown($select, data, selectedValue, defaultText) {
        if ($select.hasClass("select2-hidden-accessible")) {
            $select.select2('destroy');
        }
        $select.empty().append('<option value="">' + defaultText + '</option>');
        $.each(data, function(index, item) {
            var selected = (selectedValue === item.name) ? 'selected' : '';
            $select.append('<option value="' + item.name + '" ' + selected + ' data-id="' + item.code + '">' + item.name + '</option>');
        });
        $select.select2({ theme: 'bootstrap4', width: '100%' });
    }

    function init() {
        populateDropdown($tinh, vnProvincesData, preselect ? preselect.tinh : null, 'Chọn Tỉnh / Thành phố');
        if (!$xa.hasClass("select2-hidden-accessible")) {
            $xa.select2({ theme: 'bootstrap4', width: '100%' });
        }
        
        if (preselect && preselect.tinh) {
            $tinh.trigger('change', [preselect.xa]);
        }
    }

    $tinh.on('change', function(e, preXa) {
        var selectedProvinceCode = $(this).find(':selected').data('id');
        
        resetDropdown($xa, 'Chọn Phường / Xã');
        
        if (selectedProvinceCode) {
            var province = vnProvincesData.find(p => p.code == selectedProvinceCode);
            if (province && province.wards) {
                populateDropdown($xa, province.wards, preXa, 'Chọn Phường / Xã');
            }
        }
    });
    
    if (vnProvincesData) {
        init();
    } else {
        $.getJSON('../assets/provinces.json?v=' + new Date().getTime(), function(data) {
            vnProvincesData = data;
            init();
        }).fail(function(jqxhr, textStatus, error) {
            var err = textStatus + ", " + error;
            console.error("Request Failed: " + err);
            alert("Không thể tải danh sách Tỉnh/Thành phố. Vui lòng làm mới trang (F5).");
        });
    }
}

window.addEventListener('load', function() {
    var oldAddLL = {
        tinh: "<?= htmlspecialchars(sinhvien_old_value('dc_ll_tinh', 'add')) ?>",
        xa: "<?= htmlspecialchars(sinhvien_old_value('dc_ll_xa', 'add')) ?>"
    };
    loadVNProvinces('dc_ll', oldAddLL.tinh ? oldAddLL : null);
    
    var oldAddTT = {
        tinh: "<?= htmlspecialchars(sinhvien_old_value('dc_tt_tinh', 'add')) ?>",
        xa: "<?= htmlspecialchars(sinhvien_old_value('dc_tt_xa', 'add')) ?>"
    };
    loadVNProvinces('dc_tt', oldAddTT.tinh ? oldAddTT : null);
});
 </script>



