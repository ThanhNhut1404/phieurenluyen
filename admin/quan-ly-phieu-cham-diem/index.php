 <?php
    $id_dot = -2;
    $id_lop_hoc  = -2;
    $id_sinh_vien  = -2;
    $phieuchamdiem__Get_By_Id_Lop = $phieuchamdiem->phieuchamdiem__Get_By_Id_Lop($id_lop_hoc, $id_dot);
    $view = "xem-tat-ca";
    $dotchamdiem__Get_All = $dotchamdiem->dotchamdiem__Get_All();
    $lophoc__Get_All = $lophoc->lophoc__Get_All();

  
    if(isset($_GET['id_dot'])){
        $id_dot = $_GET['id_dot'];
        // Nhựt sửa lỗi: Chỉ hiển thị Lớp áp dụng thuộc Đợt đã chọn, không hiển thị toàn bộ lớp.
        $lophoc__Get_All = $lopapdung->lopapdung__Get_Lop_Hoc_By_Id_Dot($id_dot);
    }
    if(isset($_GET['id_lop_hoc'])){
        $id_lop_hoc = $_GET['id_lop_hoc'];
        $sinhvien__Get_By_Id_Lop_Hoc_Chua_Cham = $phieuchamdiem->phieuchamdiem__Get_By_Id_Lop_Chua_Cham($id_lop_hoc, $id_dot);
        $sinhvien__Get_By_Id_Lop_Hoc_Da_Cham = $phieuchamdiem->phieuchamdiem__Get_By_Id_Lop_Da_Cham($id_lop_hoc, $id_dot);
        $sinhvien__Get_By_Id_Lop_Hoc_All = $phieuchamdiem->phieuchamdiem__Get_By_Id_Lop_All($id_lop_hoc, $id_dot);
    }
  
    if(isset($_GET['view'])){
        $view = $_GET['view'];
    }

    if($view == "xem-tat-ca"){
        $phieuchamdiem__Get_By_Id_Lop = $phieuchamdiem->phieuchamdiem__Get_By_Id_Lop_All($id_lop_hoc, $id_dot);
    }
    if($view == "da-cham-diem"){
        $phieuchamdiem__Get_By_Id_Lop = $phieuchamdiem->phieuchamdiem__Get_By_Id_Lop_Da_Cham($id_lop_hoc, $id_dot);
    }
    if($view == "chua-cham-diem"){
        $phieuchamdiem__Get_By_Id_Lop = $phieuchamdiem->phieuchamdiem__Get_By_Id_Lop_Chua_Cham($id_lop_hoc, $id_dot);
    }
   
  
 ?>

 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý phiếu chấm điểm </h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="#">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý phiếu chấm điểm</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <section class="content">
         <form class="row form" action="quan-ly-phieu-cham-diem/action.php?req=add" method="post"
             enctype="multipart/form-data">
             <input type="hidden" name="id_dot" value="<?=$id_dot?>">
             <input type="hidden" name="id_lop_hoc" value="<?=$id_lop_hoc?>">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title">Xử lý phiếu chấm điểm</h3>
                         <div class="card-tools">
                             <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                 <i class="fas fa-minus"></i>
                             </button>
                         </div>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col">
                                 <label for="">Chọn đợt (<?=count($dotchamdiem__Get_All)?>)</label>
                                 <select class="form-control" name="" required onchange="location.href=this.value">
                                     <option value="">Chọn đợt</option>
                                     <?php foreach ($dotchamdiem__Get_All as $item):?>
                                     <option value="?page=quan-ly-phieu-cham-diem&id_dot=<?=$item->id_dot?>"
                                         <?=$id_dot == $item->id_dot ? "selected" : ""?>>
                                         <?=$item->ten_dot?>
                                     </option>
                                     <?php endforeach; ?>
                                 </select>
                             </div>

                             <div class="col">
                                 <?php if(isset($_GET['id_dot'])):?>
                                 <label for="">Chọn lớp học (<?=count($lophoc__Get_All)?>)</label>
                                 <select class="form-control" name="" required onchange="location.href=this.value">
                                     <option value="">Chọn lớp học</option>
                                     <?php foreach ($lophoc__Get_All as $item):?>
                                     <option
                                         value="?page=quan-ly-phieu-cham-diem&id_dot=<?=$id_dot?>&id_lop_hoc=<?=$item->id_lop_hoc?>&view=<?=$view?>"
                                         <?=$id_lop_hoc == $item->id_lop_hoc ? "selected" : ""?>>
                                         <?=$item->ten_lop_hoc?>
                                     </option>
                                     <?php endforeach; ?>
                                 </select>
                                 <?php endif; ?>
                             </div>

                         </div>

                     </div>
                     <!-- /.card-body -->
                     <div class="card-footer">
                         <input type="submit" value="Xử lý" class="btn btn-success float-right"
                             <?=isset($_GET['id_lop_hoc']) ? "" : "disabled"?>>
                     </div>
                 </div>
             </div>
         </form>
     </section>

     <section class="content" id="div_update">
     </section>

     <section class="content">
         <div class="card card-primary">
             <div class="card-header">
                 <h3 class="card-title">Danh sách</h3>
                 <?php if(isset($_GET['id_lop_hoc'])):?>
                 <div class="card-tools">
                     <a class="btn btn-outline-primary"
                         href="?page=quan-ly-phieu-cham-diem&id_dot=<?=$id_dot?>&id_lop_hoc=<?=$id_lop_hoc?>&view=xem-tat-ca">
                         Tất cả
                         (<?=count($sinhvien__Get_By_Id_Lop_Hoc_All)?>)</a>
                     </a>
                     <a class="btn btn-outline-primary"
                         href="?page=quan-ly-phieu-cham-diem&id_dot=<?=$id_dot?>&id_lop_hoc=<?=$id_lop_hoc?>&view=da-cham-diem">Cố
                         vấn đã
                         chấm điểm (<?=count($sinhvien__Get_By_Id_Lop_Hoc_Da_Cham)?>)</a>
                     </a>
                     <a class="btn btn-outline-primary"
                         href="?page=quan-ly-phieu-cham-diem&id_dot=<?=$id_dot?>&id_lop_hoc=<?=$id_lop_hoc?>&view=chua-cham-diem">Cố
                         vấn chưa
                         chấm điểm (<?=count($sinhvien__Get_By_Id_Lop_Hoc_Chua_Cham)?>)</a>
                     </button>
                 </div>
                 <?php endif; ?>
             </div>
             <!-- /.card-header -->
             <div class="card-body">
                 <table id="tablejs" class="table table-bordered table-striped display responsive nowrap" width="100%">
                     <thead>
                         <tr>
                             <th>#</th>
                             <th>Mã sinh viên</th>
                             <th>Tên sinh viên</th>
                             <th>Kết quả sinh viên</th>
                             <th>Kết quả lớp trưởng/bí thư</th>
                             <th>Kết quả bí thư đoàn khoa</th>
                             <th>Kết quả cố vấn</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($phieuchamdiem__Get_By_Id_Lop as $item):?>
                         <tr
                             onclick="return confirm_sweet_chi_tiet('../user/sinh-vien/chi-tiet.php?id_lop_hoc=<?=$item->id_lop_hoc?>&id_dot=<?=$item->id_dot?>&id_sinh_vien=<?=$item->id_sinh_vien?>')">
                             <td><?=++$num?></td>
                             <td><?=$sinhvien->sinhvien__Get_By_Id($item->id_sinh_vien)->ma_sinh_vien?>
                             <td><?=$sinhvien->sinhvien__Get_By_Id($item->id_sinh_vien)->ten_sinh_vien?>
                             <td><?=$phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($item->kq_sv))?>
                             </td>
                             <td><?=$phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($item->kq_lt_bt))?>
                             </td>
                             <td><?=$phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($item->kq_btdk))?>
                             </td>
                             <td><?=$phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($item->kq_gv))?>
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
 </script>
