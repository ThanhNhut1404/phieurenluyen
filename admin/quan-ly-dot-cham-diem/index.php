 <?php
    // require "../models/getModel.php";
    $dotchamdiem__Get_All = $dotchamdiem->dotchamdiem__Get_All();
    $hocky__Get_All = $hocky->hocky__Get_All();
    $mauphieu__Get_All = $mauphieu->mauphieu__Get_All();
    $lophoc__Get_All = $lophoc->lophoc__Get_All();
 ?>

 <style>
select#bootstrap-duallistbox-nonselected-list_id_lop_hoc\[\],
select#bootstrap-duallistbox-nonselected-list_,
select#bootstrap-duallistbox-selected-list_id_lop_hoc\[\],
select#bootstrap-duallistbox-selected-list_ {
    display: block;
    width: 100%;
    height: calc(2.25rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    box-shadow: inset 0 0 0 transparent;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}

button.btn.moveall.btn-outline-secondary:before {
    content: 'Chưa chọn (Click chọn tất cả)';
}

button.btn.removeall.btn-outline-secondary:before {
    content: 'Đã chọn (Click bỏ chọn tất cả)';
}
 </style>

 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý đợt chấm điểm</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="#">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý đợt chấm điểm</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <section class="content">
         <form class="row form" action="quan-ly-dot-cham-diem/action.php?req=add" method="post"
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
                                     <label for="">Tên đợt chấm điểm <span class="color-crimson">(*)</span></label>
                                     <input type="text" id="ten_dot" name="ten_dot" class="form-control" required
                                         placeholder="Nhập tên đợt chấm điểm">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Thời gian bắt đầu<span class="color-crimson">(*)</span></label>
                                     <input type="date" id="thoi_gian_bat_dau" name="thoi_gian_bat_dau"
                                         class="form-control" required placeholder="Nhập thời gian bắt đầu"
                                         value="<?=date('Y-m-d')?>">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Thời gian kết thúc<span class="color-crimson">(*)</span></label>
                                     <input type="date" id="thoi_gian_ket_thuc" name="thoi_gian_ket_thuc"
                                         class="form-control" required placeholder="Nhập thời gian kết thúc"
                                         value="<?=date('Y-m-d')?>">
                                 </div>
                                 <div class="form-group">
                                     <label for="">Ghi chú</label>
                                     <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                                         placeholder="Nhập Ghi chú"></textarea>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                     <label for="">Học kỳ <span class="color-crimson">(*)</span></label>
                                     <select class="form-control" name="id_hoc_ky" required>
                                         <option value="">Chọn học kỳ</option>
                                         <?php foreach ($hocky__Get_All as $item):?>
                                         <option value="<?=$item->id_hoc_ky?>"><?=$item->ten_hoc_ky?> -
                                             <?=$namhoc->namhoc__Get_By_Id($item->id_nam_hoc)->ten_nam_hoc?></option>
                                         <?php endforeach; ?>
                                     </select>
                                 </div>
                                 <div class="form-group">
                                     <label for="">Mẫu phiếu <span class="color-crimson">(*)</span></label>
                                     <select class="form-control" name="id_mau_phieu" required>
                                         <option value="">Chọn Mẫu phiếu</option>
                                         <?php foreach ($mauphieu__Get_All as $item):?>
                                         <option value="<?=$item->id_mau_phieu?>"><?=$item->ten_mau_phieu?></option>
                                         <?php endforeach; ?>
                                     </select>
                                 </div>
                                 <div class="form-group">
                                     <label for="">Lớp áp dụng <span class="color-crimson">(*)</span></label>
                                     <select class="duallistbox" multiple="multiple" name="id_lop_hoc[]" required>
                                         <?php foreach ($lophoc__Get_All as $item):?>
                                         <option value="<?=$item->id_lop_hoc?>"><?=$item->ten_lop_hoc?></option>
                                         <?php endforeach; ?>
                                     </select>
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
                             <th>Tên đợt</th>
                             <th>Thời gian bắt đầu</th>
                             <th>Thời gian kết thúc</th>
                             <th>Lớp áp dụng</th>
                             <th>Mẫu phiếu</th>
                             <th>Ghi chú</th>
                             <th>Thao tác</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($dotchamdiem__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <td><?=$item->ten_dot?></td>
                             <td><?=$item->thoi_gian_bat_dau?></td>
                             <td><?=$item->thoi_gian_ket_thuc?></td>
                             <td><?php foreach($lopapdung->lopapdung__Get_By_Id_Dot($item->id_dot) as $item_2){echo $lophoc->lophoc__Get_By_Id($item_2->id_lop_hoc)->ten_lop_hoc . "<br/> ";}?>
                             </td>
                             <td><?=$mauphieu->mauphieu__Get_By_Id($lopapdung->lopapdung__Get_By_Id_Dot($item->id_dot)[0]->id_mau_phieu)->ten_mau_phieu?>
                             </td>
                             <td><?=$item->ghi_chu?></td>
                             <td>
                                 <a href="#" type="button" class="btn  btn-warning m-2"
                                     onclick="update_obj(<?=$item->id_dot?>)">
                                     <i class="fas fa-edit"></i>
                                 </a>
                                 <a href="#" type="button" class="btn  btn-danger m-2"
                                     onclick="return confirm_sweet('quan-ly-dot-cham-diem/action.php?req=delete&id_dot=<?=$item->id_dot?>')">
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

    thoi_gian_bat_dau = document.getElementById('thoi_gian_bat_dau');
    $("#thoi_gian_bat_dau").change(function() {
        $('#thoi_gian_ket_thuc').attr({
            "min": thoi_gian_bat_dau.value
        });
    });
});



function update_obj(id_dot) {
    $.post('quan-ly-dot-cham-diem/update.php', {
        'id_dot': id_dot,
    }, function(data) {
        $(".card.card-success").addClass('collapsed-card');
        $('#div_update').html(data);
    });
}
 </script>