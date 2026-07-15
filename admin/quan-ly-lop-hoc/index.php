 <?php
    // require "../models/getModel.php";
    $lophoc__Get_All = $lophoc->lophoc__Get_All();
    $khoahoc__Get_All = $khoahoc->khoahoc__Get_All();
    $nganhhoc__Get_All = $nganhhoc->nganhhoc__Get_All();
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper" id="div_top">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý lớp học</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="#">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý lớp học</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <section class="content">
         <form class="row form" action="quan-ly-lop-hoc/action.php?req=add" method="post" enctype="multipart/form-data">
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
                             <label for="">Khóa học <span class="color-crimson">(*)</span></label>
                             <select class="form-control" name="id_khoa_hoc" required>
                                 <option value="">Chọn khóa học</option>
                                 <?php foreach ($khoahoc__Get_All as $item):?>
                                 <option value="<?=$item->id_khoa_hoc?>"><?=$item->ten_khoa_hoc?></option>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                         <div class="form-group">
                             <label for="">Ngành học <span class="color-crimson">(*)</span></label>
                             <select class="form-control" name="id_nganh_hoc" required>
                                 <option value="">Chọn ngành học</option>
                                 <?php foreach ($nganhhoc__Get_All as $item):?>
                                 <option value="<?=$item->id_nganh_hoc?>"><?=$item->ten_nganh_hoc?></option>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                         <div class="form-group">
                             <label for="">Tên lớp học <span class="color-crimson">(*)</span></label>
                             <input type="text" id="ten_lop_hoc" name="ten_lop_hoc" class="form-control" required
                                 placeholder="Nhập tên lớp học">
                         </div>

                         <div class="form-group">
                             <label for="">Ghi chú</label>
                             <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                                 placeholder="Nhập ghi chú"></textarea>
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
                             <th>Khóa học</th>
                             <th>Ngành học</th>
                             <th>Tên lớp học</th>
                             <th>Ghi chú</th>
                             <th>Thao tác</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($lophoc__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <td><?=$khoahoc->khoahoc__Get_By_Id($item->id_khoa_hoc)->ten_khoa_hoc?></td>
                             <td><?=$nganhhoc->nganhhoc__Get_By_Id($item->id_nganh_hoc)->ten_nganh_hoc?></td>
                             <td><?=$item->ten_lop_hoc?></td>
                             <td><?=$item->ten_lop_hoc?></td>
                             <td><?=$item->ghi_chu?></td>
                             <td>
                                 <a href="#" type="button" class="btn  btn-warning m-2"
                                     onclick="update_obj(<?=$item->id_lop_hoc?>)">
                                     <i class="fas fa-edit"></i>
                                 </a>
                                 <a href="#" type="button" class="btn  btn-danger m-2"
                                     onclick="return confirm_sweet('quan-ly-lop-hoc/action.php?req=delete&id_lop_hoc=<?=$item->id_lop_hoc?>')">
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

function update_obj(id_lop_hoc) {
    $.post('quan-ly-lop-hoc/update.php', {
        'id_lop_hoc': id_lop_hoc,
    }, function(data) {
        $(".card.card-success").addClass('collapsed-card');
        $('#div_update').html(data);
    });
}
 </script>