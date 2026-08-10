 <?php
    // require "../models/getModel.php";
    $mauphieu__Get_All = $mauphieu->mauphieu__Get_All();
    $dieu__Get_All = $dieu->dieu__Get_All();
 ?>

 <style>
select#bootstrap-duallistbox-nonselected-list_id_dieu\[\],
select#bootstrap-duallistbox-nonselected-list_,
select#bootstrap-duallistbox-selected-list_id_dieu\[\],
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
                     <h1>Quản lý mẫu phiếu</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý mẫu phiếu</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <section class="content">
         <form class="row form" action="quan-ly-mau-phieu/action.php?req=add" method="post"
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
                             <label for="">Tên mẫu phiếu <span class="color-crimson">(*)</span></label>
                             <input type="text" id="ten_mau_phieu" name="ten_mau_phieu" class="form-control" required
                                 placeholder="Nhập tên mẫu phiếu">
                         </div>
                         <div class="form-group">
                             <label for="">Ghi chú</label>
                             <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                                 placeholder="Nhập ghi chú"></textarea>
                         </div>

                         <div class="form-group">
                             <label for="">Chọn điều</label>
                             <select class="duallistbox" multiple="multiple" name="id_dieu[]" required>
                                 <?php foreach($dieu__Get_All as $item):?>
                                 <option value="<?=$item->id_dieu?>"><?=$item->ten_dieu?></option>
                                 <?php endforeach; ?>
                             </select>
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
                 <h3 class="card-title">Danh sách Mẫu phiếu</h3>
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
                             <th style="width: 35%; white-space: nowrap;">Tên mẫu phiếu</th>
                             <th style="width: 12%; white-space: nowrap;">Điều</th>
                             <th style="width: 30%; white-space: nowrap;">Ghi chú</th>
                             <th style="width: 20%; white-space: nowrap;">Thao tác</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php $num = 0;?>
                         <?php foreach($mauphieu__Get_All as $item):?>
                         <tr>
                             <td><?=++$num?></td>
                             <td><?=$item->ten_mau_phieu?></td>
                             <td><?php foreach($dieu->dieu__Get_All_Selected($item->id_mau_phieu) as $item_dieu){echo $item_dieu->ten_dieu ."<br/>";}?>
                             </td>
                             <td><?=$item->ghi_chu?></td>
                             <td>
                                 <?php if($mauphieu->mauphieu__Is_Edit_Locked($item->id_mau_phieu)): ?>
                                     <button class="btn btn-sm btn-info m-2 disabled" title="Mẫu đã phát sinh đợt chấm cho nội dung này." style="width: auto !important; padding: 0 12px !important;">
                                         <i class="ri-article-line mr-1"></i> Sửa điều
                                     </button>
                                     <button class="btn btn-warning m-2 disabled" title="Mẫu đã phát sinh đợt chấm cho nội dung này.">
                                         <i class="ri-edit-2-line"></i>
                                     </button>
                                 <?php else: ?>
                                     <a href="#" type="button" class="btn btn-sm btn-info m-2" style="width: auto !important; padding: 0 12px !important;"
                                         onclick="update_obj_dieu(<?=$item->id_mau_phieu?>)">
                                         <i class="ri-article-line mr-1"></i> Sửa điều
                                     </a>
                                     <a href="#" type="button" class="btn btn-warning m-2"
                                         onclick="update_obj(<?=$item->id_mau_phieu?>)">
                                         <i class="ri-edit-2-line"></i>
                                     </a>
                                 <?php endif; ?>

                                 <a href="#" type="button" class="btn  btn-danger m-2"
                                     onclick="return confirm_sweet('quan-ly-mau-phieu/action.php?req=delete&id_mau_phieu=<?=$item->id_mau_phieu?>')">
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
        "dom": "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end align-items-center'Bf>>rtip",
        "pagingType": "full_numbers",
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "language": {
            "decimal": ",",
            "thousands": ".",
            "emptyTable": "Không có dữ liệu trong bảng",
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ mẫu phiếu",
            "infoEmpty": "Hiển thị 0 - 0 của 0 mẫu phiếu",
            "infoFiltered": "(lọc từ _MAX_ mẫu phiếu)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ mẫu phiếu",
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

function update_obj(id_mau_phieu) {
    $.post('quan-ly-mau-phieu/update.php', {
        'id_mau_phieu': id_mau_phieu,
    }, function(data) {
        $(".card.card-success").addClass('collapsed-card');
        $('#div_update').html(data);
    });
}

function update_obj_dieu(id_mau_phieu) {
    $.post('quan-ly-mau-phieu/update_dieu.php', {
        'id_mau_phieu': id_mau_phieu,
    }, function(data) {
        $(".card.card-success").addClass('collapsed-card');
        $('#div_update').html(data);
    });
}
 </script>