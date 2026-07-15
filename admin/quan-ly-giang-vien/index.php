 <?php
    // require "../models/getModel.php";
    $giangvien__Get_All = $giangvien->giangvien__Get_All();
    $trinhdo__Get_All = $trinhdo->trinhdo__Get_All();
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý giảng viên</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="#">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý giảng viên</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <section class="content">
         <form class="row form" action="quan-ly-giang-vien/action.php?req=add" method="post"
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
                                     <label for="">Mã giảng viên <span class="color-crimson">(*)</span></label>
                                     <input type="text" id="ma_giang_vien" name="ma_giang_vien" class="form-control"
                                         required placeholder="Nhập mã giảng viên">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Tên giảng viên <span class="color-crimson">(*)</span></label>
                                     <input type="text" id="ten_giang_vien" name="ten_giang_vien" class="form-control"
                                         required placeholder="Nhập tên giảng viên">
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
                                     <input type="date" id="ngay_sinh" name="ngay_sinh" class="form-control" required
                                         value="<?=date('Y-m-d', strtotime('-22 years'))?>"
                                         min="<?=date('Y-m-d', strtotime('-100 years'))?>"
                                         max="<?=date('Y-m-d', strtotime('-10 years'))?>" placeholder="Nhập ngày sinh">
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label for="">Email <span class="color-crimson">(*)</span></label>
                                     <input type="email" id="email" name="email" class="form-control" required
                                         placeholder="Nhập email">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Số điện thoại 1 <span class="color-crimson">(*)</span></label>
                                     <input type="so_dien_thoai_1" id="so_dien_thoai_1" name="so_dien_thoai_1"
                                         pattern="[0][0-9]{8-9}" class=" form-control" required
                                         title="Số điện thoại có 10 hoặc 11 số" placeholder="Nhập số điện thoại 1"
                                         minlength="10" max="11">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Số điện thoại 2 <span class="color-crimson">(*)</span></label>
                                     <input type="so_dien_thoai_2" id="so_dien_thoai_2" name="so_dien_thoai_2"
                                         pattern="[0][0-9]{8-9}" class=" form-control" required
                                         title="Số điện thoại có 10 hoặc 11 số" placeholder="Nhập số điện thoại 2"
                                         minlength="10" max="11">
                                 </div>
                             </div>

                             <div class="col-6">
                                 <div class="form-group">
                                     <label for="">Địa chỉ liên lạc <span class="color-crimson">(*)</span></label>
                                     <input type="dia_chi_lien_lac" id="dia_chi_lien_lac" name="dia_chi_lien_lac"
                                         class="form-control" required placeholder="Nhập địa chỉ liên lạc">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Địa chỉ thường trú <span class="color-crimson">(*)</span></label>
                                     <input type="dia_chi_thuong_tru" id="dia_chi_thuong_tru" name="dia_chi_thuong_tru"
                                         class="form-control" required placeholder="Nhập địa chỉ thường trú">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Trình độ <span class="color-crimson">(*)</span></label>
                                     <select class="form-control" name="id_trinh_do" required>
                                         <option value="">Chọn trình độ</option>
                                         <?php foreach($trinhdo__Get_All as $item):?>
                                         <option value="<?=$item->id_trinh_do?>"><?=$item->ten_trinh_do?></option>
                                         <?php endforeach;?>
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
                 <table id="tablejs" class="table table-bordered table-striped display responsive nowrap" width="100%">
                     <thead>
                         <tr>
                             <th>#</th>
                             <th>Mã giảng viên</th>
                             <th>Tên giảng viên</th>
                             <th>Giới tính</th>
                             <th>Ngày sinh</th>
                             <th>Số điện thoại 1</th>
                             <th>Địa chỉ liên lạc</th>
                             <th>Trình độ</th>
                             <th>Thao tác</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($giangvien__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <td><?=$item->ma_giang_vien?></td>
                             <td><?=$item->ten_giang_vien?></td>
                             <td><?=$item->gioi_tinh == 1 ? "Nam" : "Nữ"?></td>
                             <td><?=$item->ngay_sinh?></td>
                             <td><?=$item->so_dien_thoai_1?></td>
                             <td><?=$item->dia_chi_lien_lac?></td>
                             <td><?=$trinhdo->trinhdo__Get_By_Id($item->id_trinh_do)->ten_trinh_do?></td>

                             <td>
                                 <a href="#" type="button" class="btn  btn-warning m-2"
                                     onclick="update_obj(<?=$item->id_giang_vien?>)">
                                     <i class="fas fa-edit"></i>
                                 </a>
                                 <a href="#" type="button" class="btn  btn-danger m-2"
                                     onclick="return confirm_sweet('quan-ly-giang-vien/action.php?req=delete&id_giang_vien=<?=$item->id_giang_vien?>')">
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
});

function update_obj(id_giang_vien) {
    $.post('quan-ly-giang-vien/update.php', {
        'id_giang_vien': id_giang_vien,
    }, function(data) {
        $(".card.card-success").addClass('collapsed-card');
        $('#div_update').html(data);
    });
}
 </script>