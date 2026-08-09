 <?php
    // require "../models/getModel.php";
    $dieu__Get_All = $dieu->dieu__Get_All();
    // Nhựt sửa lỗi: Giới hạn thứ tự trên form thêm từ 1 đến max + 1 để chặn nhập số âm, 0 hoặc vượt giới hạn ở client.
    $dieu__Max_Thu_Tu = $dieu->dieu__Get_Max_Thu_Tu();
 ?>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý điều</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý điều</li>
                     </ol>
                 </div>
             </div>
             <!-- quân sửa: Cảnh báo khi dữ liệu đang bị khoá bởi Đợt chấm điểm -->
             <?php if(isset($_GET['status']) && $_GET['status'] == 'locked_by_dotchamdiem'): ?>
                 <div class="alert alert-danger alert-dismissible mt-2">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <h5><i class="icon fas fa-lock"></i> Thao tác thất bại!</h5>
                     Dữ liệu này đang được sử dụng trong một Đợt chấm điểm đang diễn ra. Bạn không thể Sửa hoặc Xoá vào lúc này!
                 </div>
             <?php endif; ?>
             <?php if(isset($_GET['status']) && $_GET['status'] == 'locked_update'): ?>
                 <div class="alert alert-danger alert-dismissible mt-2">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <h5><i class="icon fas fa-lock"></i> Thao tác thất bại!</h5>
                     Dữ liệu này đã được sử dụng trong một Đợt chấm điểm. Để bảo toàn lịch sử, bạn không thể Sửa dữ liệu này. Hãy tạo mới thay thế!
                 </div>
             <?php endif; ?>
         </div><!-- /.container-fluid -->
     </section>

     <section class="content">
         <form class="row form" action="quan-ly-dieu/action.php?req=add" method="post" enctype="multipart/form-data">
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
                             <label for="">Tên điều <span class="color-crimson">(*)</span></label>
                             <input type="text" id="ten_dieu" name="ten_dieu" class="form-control" required
                                 placeholder="Nhập tên điều">
                         </div>
                         <div class="form-group">
                             <label for="">Nội dung chi tiết</label>
                             <textarea id="ghi_chu" name="ghi_chu" class="form-control" required
                                 placeholder="Nhập nội dung chi tiết"></textarea>
                         </div>
                         <div class="form-group">
                             <!-- quân sửa: Cập nhật lời nhắc Thứ tự tự động -->
                             <label for="">Thứ tự</label>
                             <input type="number" id="thu_tu" name="thu_tu" class="form-control" min="1"
                                 max="<?=$dieu__Max_Thu_Tu + 1?>" step="1"
                                 placeholder="Nhập thứ tự (Có thể để trống)">
                             <small class="form-text text-muted">Mẹo: Để trống hệ thống sẽ tự động xếp cuối. Nếu nhập trùng, hệ thống sẽ tự động hoán đổi.</small>
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
                 <h3 class="card-title">Danh sách Điều</h3>
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
                              <th style="width: 5%; white-space: nowrap;">STT</th>
                              <th style="width: 28%; white-space: nowrap;">Tên điều</th>
                              <th style="width: 45%; white-space: nowrap;">Ghi chú</th>
                              <th style="width: 12%; white-space: nowrap;">Thứ tự</th>
                              <th style="width: 10%; white-space: nowrap;">Thao tác</th>
                          </tr>
                      </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($dieu__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <td class="text-center" style="text-align: center !important;"><?=$item->ten_dieu?></td>
                             <td><?=$item->ghi_chu?></td>
                             <td class="text-center" style="text-align: center !important;"><?=$item->thu_tu?></td>
                             <td>
                                 <?php if($dieu->dieu__Is_Edit_Locked($item->id_dieu)): ?>
                                     <button class="btn btn-warning m-2 disabled" title="Mẫu đã phát sinh đợt chấm cho nội dung này.">
                                         <i class="ri-edit-2-line"></i>
                                     </button>
                                 <?php else: ?>
                                     <a href="#" type="button" class="btn btn-warning m-2"
                                         onclick="update_obj(<?=$item->id_dieu?>)">
                                         <i class="ri-edit-2-line"></i>
                                     </a>
                                 <?php endif; ?>
                                     <a href="#" type="button" class="btn  btn-danger m-2"
                                         onclick="return confirm_delete_dieu('quan-ly-dieu/action.php?req=delete&id_dieu=<?=$item->id_dieu?>')">
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
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ điều",
            "infoEmpty": "Hiển thị 0 - 0 của 0 điều",
            "infoFiltered": "(lọc từ _MAX_ điều)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ điều",
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

function update_obj(id_dieu) {
    $.post('quan-ly-dieu/update.php', {
        'id_dieu': id_dieu,
    }, function(data) {
        $(".card.card-success").addClass('collapsed-card');
        $('#div_update').html(data);
    });
}

// Quân sửa: Thêm hàm xác nhận xóa Điều với thông báo cảnh báo xóa toàn bộ dữ liệu con
function confirm_delete_dieu(url) {
    Swal.fire({
        title: 'Bạn có chắc muốn xóa điều này?',
        text: "Toàn bộ khoản và mục thuộc điều này cũng sẽ bị xóa và không thể khôi phục.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Đồng ý xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            location.href = url;
        }
    })
}
 </script>
