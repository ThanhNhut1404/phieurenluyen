 <?php
    // require "../models/getModel.php";
    $khoan__Get_All = $khoan->khoan__Get_All();
    $dieu__Get_All = $dieu->dieu__Get_All();

    // quân sửa: Lấy danh sách id_dieu đã được sử dụng
    $used_dieu = [];
    foreach ($khoan__Get_All as $k) {
        $used_dieu[] = $k->id_dieu;
    }
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý khoản</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="#">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý khoản</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <section class="content">
         <form class="row form" action="quan-ly-khoan/action.php?req=add" method="post" enctype="multipart/form-data">
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
                             <label for="">Điều <span class="color-crimson">(*)</span></label>
                             <select class="form-control" name="id_dieu" required>
                                 <option value="">Chọn Điều</option>
                                 <?php foreach ($dieu__Get_All as $item):?>
                                     <?php if(!in_array($item->id_dieu, $used_dieu)): ?>
                                         <option value="<?=$item->id_dieu?>"><?=$item->ten_dieu?></option>
                                     <?php endif; ?>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                         <div class="form-group">
                             <label for="">Tên khoản <span class="color-crimson">(*)</span></label>
                             <input type="text" id="ten_khoan" name="ten_khoan" class="form-control" required
                                 placeholder="Nhập tên khoản">
                         </div>
                         <div class="form-group">
                             <label for="">Nội dung chi tiết</label>
                             <textarea id="ghi_chu" name="ghi_chu" class="form-control" required
                                 placeholder="Nhập nội dung chi tiết"></textarea>
                         </div>
                         <!-- quân sửa: Xoá ô nhập Thứ tự ở đây vì đã tự động đồng bộ theo Điều -->
                         <div class="form-group">
                             <label for="">Điểm tối đa</label>
                             <input type="number" id="can_tren" name="can_tren" class="form-control"
                                 placeholder="Nhập điểm tối đa">
                         </div>
                         <!-- quân sửa: Thêm ô nhập Số lượng mục tối đa -->
                         <div class="form-group">
                             <label for="">Số lượng mục tối đa</label>
                             <input type="number" id="so_luong_muc" name="so_luong_muc" class="form-control" value="10" required
                                 placeholder="Nhập giới hạn số lượng mục">
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
                             <th>Điều</th>
                             <th>Điểm tối đa</th>
                             <!-- quân sửa: Bổ sung cột Số lượng mục vào bảng danh sách Khoản -->
                             <th>Số lượng mục</th>
                             <th>Tên khoản</th>
                             <th>Ghi chú</th>
                             <th>Thao tác</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($khoan__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <td> <?=$dieu->dieu__Get_By_Id($item->id_dieu)->ten_dieu?></td>
                             <td><?=$item->can_tren?></td>
                             <!-- quân sửa: Hiển thị giá trị Số lượng mục -->
                             <td><?=$item->so_luong_muc?></td>
                             <td><?=$item->ten_khoan?></td>
                             <td><?=$item->ghi_chu?></td>
                             <td>
                                 <a href="#" type=" button" class="btn btn-warning"
                                     onclick="update_obj(<?=$item->id_khoan?>)">
                                     <i class="fas fa-edit"></i>
                                 </a>
                                 <a href="#" type="button" class="btn btn-danger"
                                     onclick="return confirm_sweet('quan-ly-khoan/action.php?req=delete&id_khoan=<?=$item->id_khoan?>')">
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

function update_obj(id_khoan) {
    $.post('quan-ly-khoan/update.php', {
        'id_khoan': id_khoan,
    }, function(data) {
        $(".card.card-success").addClass('collapsed-card');
        $('#div_update').html(data);
    });
}
 </script>