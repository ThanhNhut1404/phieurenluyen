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
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý khoản</li>
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
                 <h3 class="card-title">Danh sách Khoản</h3>
                 <div class="card-tools">
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
                              <th style="width: 8%; white-space: nowrap;">Điều</th>
                              <th style="width: 35%; white-space: nowrap;">Tên khoản</th>
                              <th style="width: 8%; white-space: nowrap;">Điểm tối đa</th>
                              <!-- quân sửa: Bổ sung cột Số lượng mục vào bảng danh sách Khoản -->
                              <th style="width: 10%; white-space: nowrap;">Số lượng mục</th>
                              <th style="width: 21%; white-space: nowrap;">Ghi chú</th>
                              <th style="width: 15%; white-space: nowrap;">Thao tác</th>
                          </tr>
                      </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($khoan__Get_All as $item):?>
                         <tr>
                              <td><?=++$num?></td>
                              <td class="text-center" style="text-align: center !important;"> <?=$dieu->dieu__Get_By_Id($item->id_dieu)->ten_dieu?></td>
                              <td><?=$item->ten_khoan?></td>
                              <td class="text-center" style="text-align: center !important;"><?=$item->can_tren?></td>
                              <!-- quân sửa: Hiển thị giá trị Số lượng mục -->
                              <td class="text-center" style="text-align: center !important;"><?=$item->so_luong_muc?></td>
                              <td><?=$item->ghi_chu?></td>
                             <td>
                                 <?php if(!$khoan->khoan__Is_Used_In_Bocauhoi($item->id_khoan)): ?>
                                 <a href="#" type=" button" class="btn btn-warning"
                                     onclick="update_obj(<?=$item->id_khoan?>)">
                                     <i class="ri-edit-2-line"></i>
                                 </a>
                                 <?php endif; ?>
                                 <a href="#" type="button" class="btn btn-danger"
                                     onclick="return confirm_sweet('quan-ly-khoan/action.php?req=delete&id_khoan=<?=$item->id_khoan?>')">
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
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ khoản",
            "infoEmpty": "Hiển thị 0 - 0 của 0 khoản",
            "infoFiltered": "(lọc từ _MAX_ khoản)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ khoản",
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

function update_obj(id_khoan) {
    $.post('quan-ly-khoan/update.php', {
        'id_khoan': id_khoan,
    }, function(data) {
        $(".card.card-success").addClass('collapsed-card');
        $('#div_update').html(data);
    });
}
 </script>