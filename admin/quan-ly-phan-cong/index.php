 <?php
    // require "../models/getModel.php";
    $phancong__Get_All = $phancong->phancong__Get_All();
    $lophoc__Get_All = $lophoc->lophoc__Get_All();
    $giangvien__Get_All = $giangvien->giangvien__Get_All();
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý phân công</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý phân công</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <section class="content">
         <form class="row form" action="quan-ly-phan-cong/action.php?req=add" method="post"
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
                         <div class="form-group">
                             <label for="">Giảng viên <span class="color-crimson">(*)</span></label>
                             <select class="form-control" name="id_giang_vien" required>
                                 <option value="">Chọn Giảng viên</option>
                                 <?php foreach ($giangvien__Get_All as $item):?>
                                 <option value="<?=$item->id_giang_vien?>"><?=$item->ten_giang_vien?></option>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                         <div class="form-group">
                             <label for="">Lớp học <span class="color-crimson">(*)</span></label>
                             <select class="form-control" name="id_lop_hoc" required>
                                 <option value="">Chọn Lớp học</option>
                                 <?php foreach ($lophoc__Get_All as $item):?>
                                 <option value="<?=$item->id_lop_hoc?>"><?=$item->ten_lop_hoc?></option>
                                 <?php endforeach; ?>
                             </select>
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
                 <h3 class="card-title">Danh sách Phân công</h3>
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
                             <th>Giảng viên</th>
                             <th>Lớp học</th>
                             <th>Ghi chú</th>
                             <th>Thao tác</th>
                         </tr>
                     </thead>
                      <tbody>
                          <?php 
                          $arr_giang_vien = [];
                          foreach ($giangvien__Get_All as $gv) {
                              $arr_giang_vien[$gv->id_giang_vien] = $gv->ten_giang_vien;
                          }
                          $arr_lop_hoc = [];
                          foreach ($lophoc__Get_All as $lh) {
                              $arr_lop_hoc[$lh->id_lop_hoc] = $lh->ten_lop_hoc;
                          }
                          $num = 0;
                          ?>
                          <?php foreach($phancong__Get_All as $item):?>
                          <tr>
                              <td><?=++$num?></td>
                              <td><?= isset($arr_giang_vien[$item->id_giang_vien]) ? htmlspecialchars($arr_giang_vien[$item->id_giang_vien]) : '' ?></td>
                              <td class="text-center" style="text-align: center !important;"><?= isset($arr_lop_hoc[$item->id_lop_hoc]) ? htmlspecialchars($arr_lop_hoc[$item->id_lop_hoc]) : '' ?></td>
                              <td><?= htmlspecialchars($item->ghi_chu ?? '') ?></td>
                             <td>
                                 <a href="#" type="button" class="btn  btn-warning m-2"
                                     onclick="update_obj(<?=$item->id_phan_cong?>)">
                                     <i class="ri-edit-2-line"></i>
                                 </a>
                                 <a href="#" type="button" class="btn  btn-danger m-2"
                                     onclick="return confirm_sweet('quan-ly-phan-cong/action.php?req=delete&id_phan_cong=<?=$item->id_phan_cong?>')">
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
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ phân công cố vấn",
            "infoEmpty": "Hiển thị 0 - 0 của 0 phân công cố vấn",
            "infoFiltered": "(lọc từ _MAX_ phân công cố vấn)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ phân công cố vấn",
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
        "columnDefs": [{
            "targets": -1,
            "orderable": false,
            "searchable": false
        }],
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

function update_obj(id_phan_cong) {
    $.post('quan-ly-phan-cong/update.php', {
        'id_phan_cong': id_phan_cong,
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