<?php
    // require "../models/getModel.php";
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        // Nhựt sửa lỗi: Tạo CSRF token cho Add/Delete Xếp loại giống các module danh mục khác.
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }
    $xeploai__Get_All = $xeploai->xeploai__Get_All();
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý xếp loại</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý xếp loại</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <section class="content">
         <form class="row form" action="quan-ly-xep-loai/action.php?req=add" method="post"
             enctype="multipart/form-data">
             <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>">
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
                             <label for="">Tên xếp loại <span class="color-crimson">(*)</span></label>
                             <input type="text" id="ten_xep_loai" name="ten_xep_loai" class="form-control" required
                                 placeholder="Nhập tên xếp loại">
                         </div>
                         <div class="form-group">
                             <label for="">Ghi chú</label>
                             <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                                 placeholder="Nhập ghi chú"></textarea>
                         </div>
                         <div class="form-group">
                             <label for="">Điểm tối thiểu <span class="color-crimson">(*)</span></label>
                             <input type="number" id="can_duoi" name="can_duoi"
                                 class=" form-control" required title="Thấp nhất là 0, lớn nhất là 100"
                                 placeholder="Nhập điểm tối thiểu" min="0" max="100" step="1">
                         </div>
                         <div class="form-group">
                             <label for="">Điểm tối đa <span class="color-crimson">(*)</span></label>
                             <input type="number" id="can_tren" name="can_tren"
                                 class=" form-control" required title="Thấp nhất là 0, lớn nhất là 100"
                                 placeholder="Nhập điểm tối đa" min="0" max="100" step="1">
                         </div>
                         <div class="form-group">
                             <label for="">Hạ bậc <span class="color-crimson">(*)</span></label>
                             <input type="number" id="ha_bac" name="ha_bac" min="10" max="15" step="1"
                                 class=" form-control" required title="Thấp nhất là 10, lớn nhất là 15"
                                 placeholder="Nhập điểm hạ bậc">
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
                             <th>STT</th>
                             <th>Tên xếp loại</th>
                             <th>Điểm tối thiểu</th>
                             <th>Điểm tối đa</th>
                             <th>Hạ bậc</th>
                             <th>Ghi chú</th>
                             <th>Thao tác</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($xeploai__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <td class="text-center" style="text-align: center !important;"><?=htmlspecialchars($item->ten_xep_loai ?? "", ENT_QUOTES, 'UTF-8')?></td>
                             <td class="text-center" style="text-align: center !important;"><?=$item->can_duoi?></td>
                             <td class="text-center" style="text-align: center !important;"><?=$item->can_tren?></td>
                             <td class="text-center" style="text-align: center !important;"><?=$item->ha_bac?></td>
                             <td><?=htmlspecialchars($item->ghi_chu ?? "", ENT_QUOTES, 'UTF-8')?></td>
                             <td>
                                 <a href="javascript:void(0)" class="btn btn-warning m-2"
                                     onclick="update_obj(<?=$item->id_xep_loai?>)">
                                     <i class="ri-edit-2-line"></i>
                                 </a>
                                 <a href="javascript:void(0)" class="btn btn-danger m-2"
                                     onclick="confirm_sweet('quan-ly-xep-loai/action.php?req=delete&id_xep_loai=<?=$item->id_xep_loai?>&csrf_token=<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>')">
                                     <i class="ri-delete-bin-line"></i>
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
        "dom": "<'row'<'col-sm-12'l>><'row'<'col-sm-12'B>><'row'<'col-sm-12'f>>rtip",
        "pagingType": "full_numbers",
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "language": {
            "decimal": ",",
            "thousands": ".",
            "emptyTable": "Không có dữ liệu trong bảng",
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ xếp loại",
            "infoEmpty": "Hiển thị 0 - 0 của 0 xếp loại",
            "infoFiltered": "(lọc từ _MAX_ xếp loại)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ xếp loại",
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
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#tablejs_wrapper .col-md-6:eq(0)');

    can_duoi = document.getElementById('can_duoi');
    $("#can_duoi").change(function() {
        $('#can_tren').attr({
            "min": can_duoi.value
        });
    });
});

function update_obj(id_xep_loai) {
    $.post('quan-ly-xep-loai/update.php', {
        'id_xep_loai': id_xep_loai,
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
