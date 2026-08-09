 <?php
    // Nhựt sửa lỗi: Khởi tạo csrf_token tránh lỗi Undefined index khi trang load lần đầu.
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

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
   
    $arr_sinh_vien = [];
    if ($id_lop_hoc > 0) {
        $sinhvien__Get_By_Class = $sinhvien->sinhvien__Get_By_Id_Lop_Hoc($id_lop_hoc);
        foreach ($sinhvien__Get_By_Class as $sv) {
            $arr_sinh_vien[$sv->id_sinh_vien] = $sv;
        }
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
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
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
             <!-- Nhựt sửa lỗi: Bổ sung csrf_token chống tấn công CSRF. -->
             <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>">
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
                 <h3 class="card-title">Danh sách Phiếu chấm điểm</h3>
                 <?php if(isset($_GET['id_lop_hoc'])):?>
                 <div class="card-tools">
                     <a class="btn btn-outline-primary"
                         href="?page=quan-ly-phieu-cham-diem&id_dot=<?=$id_dot?>&id_lop_hoc=<?=$id_lop_hoc?>&view=xem-tat-ca">
                         Tất cả
                         (<?=count($sinhvien__Get_By_Id_Lop_Hoc_All)?>)
                     </a>
                     <a class="btn btn-outline-primary"
                         href="?page=quan-ly-phieu-cham-diem&id_dot=<?=$id_dot?>&id_lop_hoc=<?=$id_lop_hoc?>&view=da-cham-diem">Cố
                         vấn đã
                         chấm điểm (<?=count($sinhvien__Get_By_Id_Lop_Hoc_Da_Cham)?>)
                     </a>
                     <a class="btn btn-outline-primary"
                         href="?page=quan-ly-phieu-cham-diem&id_dot=<?=$id_dot?>&id_lop_hoc=<?=$id_lop_hoc?>&view=chua-cham-diem">Cố
                         vấn chưa
                         chấm điểm (<?=count($sinhvien__Get_By_Id_Lop_Hoc_Chua_Cham)?>)
                     </a>
                 </div>
                 <?php endif; ?>
             </div>
             <!-- /.card-header -->
             <div class="card-body">
                  <table id="tablejs" class="table table-bordered table-striped display responsive" width="100%">
                      <thead>
                          <tr>
                              <th style="width: 3%; white-space: nowrap;">STT</th>
                              <th style="width: 10%; white-space: nowrap;">MSSV</th>
                              <th style="width: 25%; white-space: nowrap;">HỌ VÀ TÊN</th>
                              <th style="width: 15%; white-space: nowrap;">Sinh viên</th>
                              <th style="width: 16%; white-space: nowrap;">Lớp trưởng / Bí thư</th>
                              <th style="width: 16%; white-space: nowrap;">Bí thư Đoàn khoa</th>
                              <th style="width: 15%; white-space: nowrap;">Cố vấn học tập</th>
                          </tr>
                      </thead>
                     <tbody>
                          <?php $num = 0;?>
                          <?php foreach($phieuchamdiem__Get_By_Id_Lop as $item):?>
                          <tr
                              onclick="return confirm_sweet_chi_tiet('../user/sinh-vien/chi-tiet.php?id_lop_hoc=<?=$item->id_lop_hoc?>&id_dot=<?=$item->id_dot?>&id_sinh_vien=<?=$item->id_sinh_vien?>')">
                              <td><?=++$num?></td>
                              <?php
                                  $sv = isset($arr_sinh_vien[$item->id_sinh_vien]) ? $arr_sinh_vien[$item->id_sinh_vien] : null;
                                  $ma_sv = $sv ? $sv->ma_sinh_vien : '';
                                  $ten_sv = $sv ? mb_convert_case($sv->ten_sinh_vien, MB_CASE_TITLE, "UTF-8") : '';
                               ?>
                              <td class="text-center" style="text-align: center !important;"><?=htmlspecialchars($ma_sv)?></td>
                              <td><b><?=htmlspecialchars($ten_sv)?></b></td>
                              <td class="text-center" style="text-align: center !important;"><?=$phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($item->kq_sv))?></td>
                              <td class="text-center" style="text-align: center !important;"><?=$phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($item->kq_lt_bt))?></td>
                              <td class="text-center" style="text-align: center !important;"><?=$phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($item->kq_btdk))?></td>
                              <td class="text-center" style="text-align: center !important;"><?=$phieuchamdiem->phieuchamdiem__Get_Sum_Ket_Qua($phieuchamdiem->phieuchamdiem__Get_Ket_Qua($item->kq_gv))?></td>
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
        "dom": "<'row'<'col-sm-12'l>><'row'<'col-sm-12'B>><'row'<'col-sm-12'f>>rtip",
        "pagingType": "full_numbers",
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "language": {
            "decimal": ",",
            "thousands": ".",
            "emptyTable": "Không có dữ liệu trong bảng",
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ phiếu chấm điểm",
            "infoEmpty": "Hiển thị 0 - 0 của 0 phiếu chấm điểm",
            "infoFiltered": "(lọc từ _MAX_ phiếu chấm điểm)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ phiếu chấm điểm",
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
        }]
    }).buttons().container().appendTo('#tablejs_wrapper .col-md-6:eq(0)');

});
 </script>
