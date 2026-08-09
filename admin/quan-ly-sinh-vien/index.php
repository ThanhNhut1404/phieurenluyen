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
    ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
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

     <section class="content" id="div_import">
         <form class=" row form" action="quan-ly-sinh-vien/action.php?req=import" method="post" enctype="multipart/form-data">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title">
                             Tải mẩu file excel  
                             <a href="quan-ly-sinh-vien/action.php?req=export" class="btn btn-sm btn-warning ml-3 text-dark" style="font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                 <i class="fas fa-download"></i> Tải mẫu
                             </a>
                         </h3>
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
                                     <label for="">Chọn file <span class="color-crimson">(*)</span></label>
                                     <input type="file" id="file" name="file" class="form-control" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                 </div>
                             </div>

                             <div class="col-6">
                                 <div class="form-group">
                                     <label for="">Lớp học <span class="color-crimson">(*)</span></label>
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
                     <div class="card-footer">
                         <input type="submit" value="Import" class="btn btn-success float-right">
                     </div>
                 </div>
                 <!-- /.card -->
             </div>
         </form>
     </section>

     <section class="content" id="div_add">
         <form class="row form" action="quan-ly-sinh-vien/action.php?req=add" method="post" enctype="multipart/form-data">
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
                                     <label for="">Mã sinh viên <span class="color-crimson">(*)</span></label>
                                     <input type="text" id="ma_sinh_vien" name="ma_sinh_vien" class="form-control" required placeholder="Nhập mã sinh viên">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Tên sinh viên <span class="color-crimson">(*)</span></label>
                                     <input type="text" id="ten_sinh_vien" name="ten_sinh_vien" class="form-control" required placeholder="Nhập tên sinh viên">
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label for="">Giới tính <span class="color-crimson">(*)</span></label>
                                     <select class="form-control" name="gioi_tinh" required>
                                         <option value="">Chọn giới tính</option>
                                         <option value="0">Nữ</option>
                                         <option value="1">Nam</option>
                                     </select>
                                 </div>
                                 <div class="form-group">
                                     <label for="">Ngày sinh <span class="color-crimson">(*)</span></label>
                                     <input type="date" id="ngay_sinh" name="ngay_sinh" class="form-control" required value="<?= date('Y-m-d', strtotime('-22 years')) ?>" min="<?= date('Y-m-d', strtotime('-100 years')) ?>" max="<?= date('Y-m-d', strtotime('-10 years')) ?>" placeholder="Nhập ngày sinh">
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label for="">Email <span class="color-crimson">(*)</span></label>
                                     <input type="email" id="email" name="email" class="form-control" required placeholder="Nhập email">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Số điện thoại 1 <span class="color-crimson">(*)</span></label>
                                     <input type="text" id="so_dien_thoai_1" name="so_dien_thoai_1" pattern="0[0-9]{9,10}" class="form-control" required title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 1" minlength="10" maxlength="11">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Số điện thoại 2 <span class="color-crimson">(*)</span></label>
                                     <input type="text" id="so_dien_thoai_2" name="so_dien_thoai_2" pattern="0[0-9]{9,10}" class="form-control" required title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 2" minlength="10" maxlength="11">
                                 </div>
                             </div>

                             <div class="col-6">
                                 <div class="form-group">
                                     <label for="">Địa chỉ liên lạc <span class="color-crimson">(*)</span></label>
                                     <!-- quân sửa: Chia nhỏ địa chỉ liên lạc thành 4 cấp -->
                                     <div class="row">
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_ll_so_nha" class="form-control" required placeholder="Số nhà, đường">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_ll_ap" class="form-control" required placeholder="Ấp / Khu phố">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_ll_xa" class="form-control" required placeholder="Xã / Phường">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_ll_tinh" class="form-control" required placeholder="Tỉnh / Thành phố">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="form-group">
                                     <label for="">Địa chỉ thường trú <span class="color-crimson">(*)</span></label>
                                     <!-- quân sửa: Chia nhỏ địa chỉ thường trú thành 4 cấp -->
                                     <div class="row">
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_tt_so_nha" class="form-control" required placeholder="Số nhà, đường">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_tt_ap" class="form-control" required placeholder="Ấp / Khu phố">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_tt_xa" class="form-control" required placeholder="Xã / Phường">
                                         </div>
                                         <div class="col-6 mb-2">
                                             <input type="text" name="dc_tt_tinh" class="form-control" required placeholder="Tỉnh / Thành phố">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="form-group">
                                     <label for="">Chức vụ <span class="color-crimson">(*)</span></label>
                                     <select class="form-control" name="chuc_vu" required>
                                         <option value="">Chọn Chức vụ</option>
                                         <option value="0">Không có</option>
                                         <option value="1">Lớp trưởng</option>
                                         <option value="2">Bí thư</option>
                                     </select>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label for="">Lớp học <span class="color-crimson">(*)</span></label>
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
                 <div class="col-6">
                     <div class="form-group">
                         <label for="">Lớp học</label>
                         <select class="form-control" name="id_lop_hoc" required onchange="location.href=this.value">
                             <option value="?page=quan-ly-sinh-vien">Xem tất cả</option>
                             <?php foreach ($lophoc__Get_All as $item) : ?>
                                 <option value="?page=quan-ly-sinh-vien&view=<?= $item->id_lop_hoc ?>" <?= isset($_GET['view']) && $_GET['view'] == $item->id_lop_hoc ? 'selected' : '' ?>>
                                     <?= $item->ten_lop_hoc ?></option>
                             <?php endforeach; ?>
                         </select>
                     </div>
                 </div>
                 <div class="table-responsive">
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
                         // TẠO MẢNG LƯU TRỮ LỚP HỌC ĐỂ TRÁNH N+1 QUERY (TỐI ƯU HIỆU SUẤT) Nguễn văn quân
                         $arr_lop_hoc = [];
                         foreach ($lophoc__Get_All as $lh) {
                             $arr_lop_hoc[$lh->id_lop_hoc] = $lh->ten_lop_hoc;
                         }
                         $num = 0; 
                         ?>
                         <?php foreach ($sinhvien__Get_All as $item) : ?>
                             <tr>
                                 <!-- BỌC htmlspecialchars ĐỂ CHỐNG LỖI BẢO MẬT XSS Nguễn văn quân
                         $arr_lop_hoc = [];-->
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
                                 <!-- SỬ DỤNG MẢNG ÁNH XẠ THAY VÌ QUERY TRONG VÒNG LẶPNguễn văn quân
                         $arr_lop_hoc = []; -->
                                 <td class="text-center" style="text-align: center !important;"><?= isset($arr_lop_hoc[$item->id_lop_hoc]) ? htmlspecialchars($arr_lop_hoc[$item->id_lop_hoc]) : '' ?></td>

                                 <td>
                                     <a href="#" type="button" class="btn  btn-warning m-2" onclick="update_obj(<?= (int)$item->id_sinh_vien ?>)">
                                         <i class="ri-edit-2-line"></i>
                                     </a>
                                     <a href="#" type="button" class="btn  btn-danger m-2" onclick="return confirm_sweet('quan-ly-sinh-vien/action.php?req=delete&id_sinh_vien=<?= (int)$item->id_sinh_vien ?>')">
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
              "dom": "<'row'<'col-sm-12'l>><'row'<'col-sm-12'B>><'row'<'col-sm-12'f>>rtip",
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
                      "extend": "copy",
                      "exportOptions": {
                          "columns": ":visible:not(:last-child)"
                      }
                  },
                  {
                      "extend": "csv",
                      "exportOptions": {
                          "columns": ":visible:not(:last-child)"
                      }
                  },
                  {
                      "extend": "excel",
                      "exportOptions": {
                          "columns": ":visible:not(:last-child)"
                      }
                  },
                  {
                      "extend": "pdf",
                      "exportOptions": {
                          "columns": ":visible:not(:last-child)"
                      }
                  },
                  {
                      "extend": "print",
                      "exportOptions": {
                          "columns": ":visible:not(:last-child)"
                      }
                  }
              ]
          }).buttons().container().appendTo('#tablejs_wrapper .col-md-6:eq(0)');
      });

     function update_obj(id_sinh_vien) {
         $.post('quan-ly-sinh-vien/update.php', {
             'id_sinh_vien': id_sinh_vien,
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