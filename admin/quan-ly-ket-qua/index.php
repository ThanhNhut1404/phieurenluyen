 <?php
    // require "../models/getModel.php";
    // Nhựt sửa lỗi: Khởi tạo csrf_token tránh lỗi Undefined index.
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    $id_dot = -2;
    $id_lop_hoc  = -2;
    $ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot = $ketquaxeploai->ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot($id_lop_hoc, $id_dot);
    $dotchamdiem__Get_All = $dotchamdiem->dotchamdiem__Get_All();
    $lophoc__Get_All = $lophoc->lophoc__Get_All();

  
    if(isset($_GET['id_dot'])){
        $id_dot = $_GET['id_dot'];
        // Nhựt sửa lỗi: Chỉ hiển thị Lớp áp dụng thuộc Đợt đã chọn, không hiển thị toàn bộ lớp.
        $lophoc__Get_All = $lopapdung->lopapdung__Get_Lop_Hoc_By_Id_Dot($id_dot);
    }
    if(isset($_GET['id_lop_hoc'])){
        $id_lop_hoc = $_GET['id_lop_hoc'];
        $ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot = $ketquaxeploai->ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot($id_lop_hoc, $id_dot);
    }
  
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý kết quả xếp loại</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="#">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý kết quả xếp loại</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <section class="content">
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
                                 <option value="?page=quan-ly-ket-qua&id_dot=<?=$item->id_dot?>"
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
                                     value="?page=quan-ly-ket-qua&id_dot=<?=$id_dot?>&id_lop_hoc=<?=$item->id_lop_hoc?>"
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
                     <!-- <input type="submit" value="Xử lý" class="btn btn-success float-right"
                         <?=isset($_GET['id_lop_hoc']) ? "" : "disabled"?>> -->
                 </div>
             </div>
         </div>
     </section>

     <section class="content" id="div_update">
     </section>

     <section class="content">
         <div class="card card-primary">
             <div class="card-header">
                 <h3 class="card-title">Danh sách</h3>

                 <div class="card-tools">
                      <!-- Nhựt sửa lỗi: Ẩn nút EXPORT khi chưa chọn Lớp học & Đợt chấm hợp lệ. -->
                     <?php if ($id_lop_hoc > 0 && $id_dot > 0): ?>
                     <form action="./quan-ly-thong-ke/action.php?req=export" method="post">
                         <input type="hidden" name="id_lop_hoc" value="<?=$id_lop_hoc?>">
                         <input type="hidden" name="id_dot" value="<?=$id_dot?>">
                         <button type="submit" class="btn btn-primary">
                             <i class="fas fa-print"></i> EXPORT
                         </button>
                     </form>
                     <?php endif; ?>
                 </div>
             </div>
         <!-- /.card-header -->
         <div class="card-body">
             <table id="tablejs" class="table table-bordered table-striped display responsive nowrap" width="100%">
                 <thead>
                     <tr>
                         <th>#</th>
                         <th>Mã sinh viên</th>
                         <th>Tên sinh viên</th>
                         <th>Tên lớp</th>
                         <th>Điểm rèn luyện</th>
                         <th>Kết quả xếp loại</th>
                         <th>Ngày xếp loại</th>
                         <th>Ghi chú</th>
                     </tr>
                 </thead>
                 <tbody>
                     <?php $num = 0;?>
                     <?php $sum_1 = 0;?>
                     <?php $sum_2 = 0;?>
                     <?php $sum_3 = 0;?>
                     <?php $sum_4 = 0;?>
                     <?php $sum_5 = 0;?>
                     <?php $sum_6 = 0;?>
                     <?php foreach($ketquaxeploai__Get_By_Id_Lop_Hoc_And_Id_Dot as $item):?>
                      <!-- Nhựt sửa lỗi: Truyền thêm csrf_token qua GET request để bảo mật hành động hạ bậc. -->
                     <tr
                         ondblclick="return confirm_sweet_ha_bac('quan-ly-ket-qua/action.php?req=update&id_ket_qua=<?=$item->id_ket_qua?>&csrf_token=<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>')">
                         <?php 

                                $item->xep_loai == "Xuất sắc" ? $sum_1++ : "";
                                $item->xep_loai == "Giỏi" ? $sum_2++ : "";
                                $item->xep_loai == "Khá" ? $sum_3++ : "";
                                $item->xep_loai == "Trung bình" ? $sum_4++ : "";
                                $item->xep_loai == "Yếu" ? $sum_5++ : "";
                                $item->xep_loai == "Kém" ? $sum_6++ : "";
                            
                            ?>
                         <td><?=++$num?></td>
                         <td><?=htmlspecialchars($item->ma_sinh_vien ?? "", ENT_QUOTES, 'UTF-8')?></td>
                         <td><?=htmlspecialchars($item->ten_sinh_vien ?? "", ENT_QUOTES, 'UTF-8')?></td>
                         <td><?=htmlspecialchars($item->ten_lop_hoc ?? "", ENT_QUOTES, 'UTF-8')?></td>
                         <td><?=htmlspecialchars($item->ket_qua ?? "", ENT_QUOTES, 'UTF-8')?></td>
                         <td><?=htmlspecialchars($item->xep_loai ?? "", ENT_QUOTES, 'UTF-8')?></td>
                         <td><?=htmlspecialchars($item->ngay_xep_loai ?? "", ENT_QUOTES, 'UTF-8')?></td>
                         <td><?=htmlspecialchars($item->ghi_chu ?? "", ENT_QUOTES, 'UTF-8')?></td>

                     </tr>
                     <?php endforeach?>
                 </tbody>
                 <tfoot>
                     <b>
                         Xuất sắc: <?=$sum_1?> | Giỏi: <?=$sum_2?> | Khá: <?=$sum_3?> | Trung bình: <?=$sum_4?>
                         | Yếu: <?=$sum_5?> | Kém: <?=$sum_6?>
                     </b>
                 </tfoot>
             </table>
         </div>
         <!-- /.card-body -->
 </div>
 </section>

 </div>


 <!-- /.content-wrapper -->


 <script>
window.addEventListener("load", function() {
    // $("#tablejs").DataTable({
    //     "responsive": true,
    //     "autoWidth": false,
    //     "buttons": ["copy", "csv", "excel", "pdf", "print"],

    // }).buttons().container().appendTo('#tablejs_wrapper .col-md-6:eq(0)');
    $('#tablejs').DataTable({
        dom: 'Bfrtip',
        buttons: [{
                extend: 'excel',
                exportOptions: {
                    columns: ':visible'
                }
            },
            'colvis'
        ],
        columnDefs: [{
            targets: -1,
            visible: false
        }]
    }).buttons().container().appendTo('#tablejs_wrapper .col-md-6:eq(0)');


});
 </script>
