 <?php
    // require "../models/getModel.php";
    $bithudoankhoa__Get_All = $bithudoankhoa->bithudoankhoa__Get_All();
    $khoa__Get_All = $khoa->khoa__Get_All();
    if (isset($_GET['view'])) {
        $bithudoankhoa__Get_All = $bithudoankhoa->bithudoankhoa__Get_By_Id_Khoa($_GET['view']);
    }
    ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý bí thư đoàn khoa</h1>
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

     <section class="content">
         <form class="row form" action="quan-ly-bi-thu-doan-khoa/action.php?req=add" method="post"
             enctype="multipart/form-data">
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
                                     <label for="">Tên bí thư đoàn khoa <span class="color-crimson">(*)</span></label>
                                     <input type="text" id="ten_bi_thu" name="ten_bi_thu" class="form-control" required
                                         placeholder="Nhập tên bí thư đoàn khoa">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Giới tính <span class="color-crimson">(*)</span></label>
                                     <select class="form-control" name="gioi_tinh" required>
                                         <option value="">Chọn giới tính</option>
                                         <option value="0">Nữ</option>
                                         <option value="1">Nam</option>
                                     </select>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label for="">Ngày sinh <span class="color-crimson">(*)</span></label>
                                     <input type="date" id="ngay_sinh" name="ngay_sinh" class="form-control" required
                                         value="<?= date('Y-m-d', strtotime('-22 years')) ?>"
                                         min="<?= date('Y-m-d', strtotime('-100 years')) ?>"
                                         max="<?= date('Y-m-d', strtotime('-10 years')) ?>"
                                         placeholder="Nhập ngày sinh">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Email <span class="color-crimson">(*)</span></label>
                                     <input type="email" id="email" name="email" class="form-control" required
                                         placeholder="Nhập email">
                                 </div>
                             </div>
                              <div class="col-6">
                                  <div class="form-group">
                                      <label for="">Số điện thoại 1 <span class="color-crimson">(*)</span></label>
                                      <input type="text" id="so_dien_thoai_1" name="so_dien_thoai_1"
                                          pattern="0[0-9]{9,10}" class="form-control" required
                                          title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 1"
                                          minlength="10" maxlength="11">
                                  </div>
                                  <div class="form-group">
                                      <label for="">Số điện thoại 2 <span class="color-crimson">(*)</span></label>
                                      <input type="text" id="so_dien_thoai_2" name="so_dien_thoai_2"
                                          pattern="0[0-9]{9,10}" class="form-control" required
                                          title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 2"
                                          minlength="10" maxlength="11">
                                  </div>
                              </div>

                             <div class="col-6">
                                 <!-- quân sửa: Chia nhỏ phần nhập địa chỉ thành 4 ô (Thêm mới) -->
                                 <div class="form-group">
                                     <label for="">Địa chỉ liên lạc <span class="color-crimson">(*)</span></label>
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
                                     <label for="">Khoa <span class="color-crimson">(*)</span></label>
                                     <select class="form-control" name="id_khoa" required>
                                         <option value="">Chọn Khoa</option>
                                         <?php foreach ($khoa__Get_All as $item) : ?>
                                         <option value="<?= $item->id_khoa ?>"><?= $item->ten_khoa ?></option>
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
                         <label for="">Lớp khoa</label>
                         <select class="form-control" name="id_khoa" required onchange="location.href=this.value">
                             <option value="?page=quan-ly-bi-thu-doan-khoa">Xem tất cả</option>
                             <?php foreach ($khoa__Get_All as $item) : ?>
                             <option value="?page=quan-ly-bi-thu-doan-khoa&view=<?= $item->id_khoa ?>"
                                 <?= isset($_GET['view']) && $_GET['view'] == $item->id_khoa ? 'selected' : '' ?>>
                                 <?= $item->ten_khoa ?></option>
                             <?php endforeach; ?>
                         </select>
                     </div>
                 </div>
                 <div class="table-responsive">
                     <table id="tablejs" class="table table-bordered table-striped display responsive" width="100%">
                         <thead>
                         <tr>
                             <th>STT</th>
                             <th>Tên bí thư đoàn khoa</th>
                             <th>Giới tính</th>
                             <th>Ngày sinh</th>
                             <th>Số điện thoại 1</th>
                             <th>Địa chỉ liên lạc</th>
                             <th>Khoa</th>
                             <th>Thao tác</th>
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
                              <td><?= $item->gioi_tinh == 1 ? "Nam" : "Nữ" ?></td>
                              <td><?= $item->ngay_sinh ?></td>
                              <td><?= $item->so_dien_thoai_1 ?></td>
                              <!-- quân sửa: Rút gọn hiển thị địa chỉ để không làm mất cột thao tác -->
                              <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($item->dia_chi_lien_lac ?? '') ?>">
                                  <?= htmlspecialchars($item->dia_chi_lien_lac ?? '') ?>
                              </td>
                              <td><?= isset($arr_khoa[$item->id_khoa]) ? htmlspecialchars($arr_khoa[$item->id_khoa]) : '' ?></td>

                             <td>
                                 <a href="#" type="button" class="btn  btn-warning m-2"
                                     onclick="update_obj(<?= $item->id_bi_thu ?>)">
                                     <i class="ri-edit-2-line"></i>
                                 </a>
                                 <a href="#" type="button" class="btn  btn-danger m-2"
                                     onclick="return confirm_sweet('quan-ly-bi-thu-doan-khoa/action.php?req=delete&id_bi_thu=<?= $item->id_bi_thu ?>')">
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
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#tablejs_wrapper .col-md-6:eq(0)');
});

function update_obj(id_bi_thu) {
    $.post('quan-ly-bi-thu-doan-khoa/update.php', {
        'id_bi_thu': id_bi_thu,
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