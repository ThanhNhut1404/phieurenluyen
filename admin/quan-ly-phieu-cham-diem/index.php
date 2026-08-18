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
     <section class="content-header pb-0">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý Phiếu chấm điểm </h1>
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

     <?php 
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $is_open_form = isset($_GET['id_dot']) || $status != ''; 
     ?>

     <!-- Nhựt sửa: Thêm nút bật/tắt form thêm mới -->
     <section class="content mb-2">
         <button type="button" class="btn <?= $is_open_form ? 'btn-cancel-custom' : 'btn-success' ?> font-weight-bold" id="btn-toggle-add" onclick="toggle_add_form()">
             <i class="fas <?= $is_open_form ? 'fa-times' : 'fa-plus' ?>"></i> <?= $is_open_form ? '' : 'Xử lý Phiếu' ?>
         </button>
     </section>

     <section class="content" id="div_add_form" <?= $is_open_form ? '' : 'style="display: none;"' ?>>
         <form class="row form" action="quan-ly-phieu-cham-diem/action.php?req=add" method="post"
             enctype="multipart/form-data">
             <input type="hidden" name="id_dot" value="<?=$id_dot?>">
             <input type="hidden" name="id_lop_hoc" value="<?=$id_lop_hoc?>">
             <!-- Nhựt sửa lỗi: Bổ sung csrf_token chống tấn công CSRF. -->
             <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title">Xử lý Phiếu chấm điểm</h3>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="id_dot_select">Chọn đợt (<?=count($dotchamdiem__Get_All)?>) <span class="color-crimson">*</span></label>
                                     <select class="form-control <?= ($status != '' && !isset($_GET['id_dot'])) ? 'is-invalid' : '' ?>" id="id_dot_select" name="" required onchange="location.href=this.value">
                                         <option value="">Chọn đợt</option>
                                         <?php foreach ($dotchamdiem__Get_All as $item):?>
                                         <option value="?page=quan-ly-phieu-cham-diem&id_dot=<?=$item->id_dot?>"
                                             <?=$id_dot == $item->id_dot ? "selected" : ""?>>
                                             <?=$item->ten_dot?>
                                         </option>
                                         <?php endforeach; ?>
                                     </select>
                                 </div>
                             </div>

                             <div class="col-6">
                                 <?php if(isset($_GET['id_dot'])):?>
                                 <div class="form-group">
                                     <label class="label-sidebar" for="id_lop_hoc_select">Chọn lớp học (<?=count($lophoc__Get_All)?>) <span class="color-crimson">*</span></label>
                                     <select class="form-control <?= ($status == 'invalid' || $status == 'incomplete-xep-loai' || $status == 'unscored-phieu') ? 'is-invalid' : '' ?>" id="id_lop_hoc_select" name="" required onchange="location.href=this.value">
                                         <option value="">Chọn lớp học</option>
                                         <?php foreach ($lophoc__Get_All as $item):?>
                                         <option
                                             value="?page=quan-ly-phieu-cham-diem&id_dot=<?=$id_dot?>&id_lop_hoc=<?=$item->id_lop_hoc?>&view=<?=$view?>"
                                             <?=$id_lop_hoc == $item->id_lop_hoc ? "selected" : ""?>>
                                             <?=$item->ten_lop_hoc?>
                                         </option>
                                         <?php endforeach; ?>
                                     </select>
                                     <?php if ($status == 'invalid'): ?>
                                         <small class="text-danger mt-1">Vui lòng chọn đợt và lớp học hợp lệ.</small>
                                     <?php elseif ($status == 'incomplete-xep-loai'): ?>
                                         <small class="text-danger mt-1">Hệ thống chưa cấu hình đủ xếp loại. Vui lòng cập nhật trước khi xử lý.</small>
                                     <?php elseif ($status == 'unscored-phieu'): ?>
                                         <small class="text-danger mt-1">Lớp này còn phiếu chưa được Cố vấn học tập chấm điểm.</small>
                                     <?php endif; ?>
                                 </div>
                                 <?php endif; ?>
                             </div>
                         </div>
                     </div>
                     <!-- /.card-body -->
                     <div class="card-footer py-2">
                         <input type="submit" value="Xử lý" class="btn btn-success float-right font-weight-bold"
                             <?=isset($_GET['id_lop_hoc']) ? "" : "disabled"?>>
                         <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" onclick="toggle_add_form()">Hủy</button>
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
                 <div class="card-tools <?=isset($_GET['id_lop_hoc']) ? 'd-flex align-items-center' : ''?>">
                     <?php if(isset($_GET['id_lop_hoc'])):?>
                     <a class="btn btn-tool"
                         href="?page=quan-ly-phieu-cham-diem&id_dot=<?=$id_dot?>&id_lop_hoc=<?=$id_lop_hoc?>&view=xem-tat-ca">
                         Tất cả (<?=count($sinhvien__Get_By_Id_Lop_Hoc_All)?>)
                     </a> |
                     <a class="btn btn-tool"
                         href="?page=quan-ly-phieu-cham-diem&id_dot=<?=$id_dot?>&id_lop_hoc=<?=$id_lop_hoc?>&view=da-cham-diem">
                         Cố vấn đã chấm điểm (<?=count($sinhvien__Get_By_Id_Lop_Hoc_Da_Cham)?>)
                     </a> |
                     <a class="btn btn-tool mr-2"
                         href="?page=quan-ly-phieu-cham-diem&id_dot=<?=$id_dot?>&id_lop_hoc=<?=$id_lop_hoc?>&view=chua-cham-diem">
                         Cố vấn chưa chấm điểm (<?=count($sinhvien__Get_By_Id_Lop_Hoc_Chua_Cham)?>)
                     </a>
                     <?php endif; ?>
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
        "dom": "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end align-items-center'B>>rt<'row mt-3 mb-n2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
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
    });

});

function toggle_add_form() {
    var addForm = $('#div_add_form');
    var btn = $('#btn-toggle-add');
    
    // Đóng form cập nhật nếu đang mở
    $("#div_update").html('');
    
    addForm.slideToggle(300, function() {
        if (addForm.is(':visible')) {
            btn.html('<i class="fas fa-times"></i>').removeClass('btn-success').addClass('btn-cancel-custom');
        } else {
            btn.html('<i class="fas fa-plus"></i> Xử lý phiếu chấm điểm').removeClass('btn-cancel-custom').addClass('btn-success');
        }
    });
}
 </script>



