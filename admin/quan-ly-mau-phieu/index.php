 <?php
    // Nhựt sửa lỗi: tạo CSRF token cho form
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    $mauphieu_old_input = isset($_SESSION['mauphieu_old_input']) && is_array($_SESSION['mauphieu_old_input']) ? $_SESSION['mauphieu_old_input'] : array();

    if (!function_exists('mauphieu_escape')) {
        function mauphieu_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('mauphieu_old_value')) {
        function mauphieu_old_value($field, $context, $default = '') {
            global $mauphieu_old_input;
            if (isset($mauphieu_old_input['context']) && $mauphieu_old_input['context'] === $context && isset($mauphieu_old_input[$field])) {
                return $mauphieu_old_input[$field];
            }
            return $default;
        }
    }

    // require "../models/getModel.php";
    $mauphieu__Get_All = $mauphieu->mauphieu__Get_All();
    $dieu__Get_All = $dieu->dieu__Get_All();
 ?>

<style>
/* Style for the select boxes */
select#bootstrap-duallistbox-nonselected-list_id_dieu\[\],
select#bootstrap-duallistbox-nonselected-list_,
select#bootstrap-duallistbox-selected-list_id_dieu\[\],
select#bootstrap-duallistbox-selected-list_ {
    display: block;
    width: 100%;
    height: 200px !important;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    box-shadow: inset 0 1px 2px rgba(0,0,0,.075);
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}

select#bootstrap-duallistbox-nonselected-list_id_dieu\[\] option,
select#bootstrap-duallistbox-nonselected-list_ option,
select#bootstrap-duallistbox-selected-list_id_dieu\[\] option,
select#bootstrap-duallistbox-selected-list_ option {
    padding: 5px 8px;
    margin-bottom: 2px;
    border-radius: 4px;
    background-color: #fff;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
}

select#bootstrap-duallistbox-nonselected-list_id_dieu\[\] option:hover,
select#bootstrap-duallistbox-nonselected-list_ option:hover,
select#bootstrap-duallistbox-selected-list_id_dieu\[\] option:hover,
select#bootstrap-duallistbox-selected-list_ option:hover {
    background-color: #e9ecef;
}

select#bootstrap-duallistbox-nonselected-list_id_dieu\[\] option:checked,
select#bootstrap-duallistbox-nonselected-list_ option:checked,
select#bootstrap-duallistbox-selected-list_id_dieu\[\] option:checked,
select#bootstrap-duallistbox-selected-list_ option:checked {
    background-color: #007bff;
    color: white;
}

/* Move All Button */
button.btn.moveall.btn-outline-secondary {
    font-size: 0; /* Hide default text >> */
    background-color: #28a745;
    color: white;
    border: none;
    font-weight: bold;
    border-radius: 4px;
    margin-top: 5px;
    transition: background-color 0.3s;
}
button.btn.moveall.btn-outline-secondary:hover {
    background-color: #218838;
}
button.btn.moveall.btn-outline-secondary:before {
    content: 'Chưa chọn (Click chọn tất cả)';
    font-size: 1rem;
}

/* Remove All Button */
button.btn.removeall.btn-outline-secondary {
    font-size: 0; /* Hide default text << */
    background-color: #dc3545;
    color: white;
    border: none;
    font-weight: bold;
    border-radius: 4px;
    margin-top: 5px;
    transition: background-color 0.3s;
}
button.btn.removeall.btn-outline-secondary:hover {
    background-color: #c82333;
}
button.btn.removeall.btn-outline-secondary:before {
    content: 'Đã chọn (Click bỏ chọn tất cả)';
    font-size: 1rem;
}

/* Info Text */
.bootstrap-duallistbox-container .info-container {
    margin-bottom: 5px;
    font-weight: bold;
    color: #6c757d;
}
</style>


 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header pb-0">
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

     <?php
        $is_add_error = isset($mauphieu_old_input['context']) && $mauphieu_old_input['context'] === 'add';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
     ?>

     <section class="content mb-2">
         <button type="button" class="btn <?= $is_add_error ? 'btn-cancel-custom' : 'btn-success' ?> font-weight-bold" id="btn-toggle-add" onclick="toggle_add_form()">
             <i class="fas <?= $is_add_error ? 'fa-times' : 'fa-plus' ?>"></i> <?= $is_add_error ? '' : 'Thêm mới' ?>
         </button>
     </section>

     <section class="content" id="div_add_form" style="<?= $is_add_error ? 'display: block;' : 'display: none;' ?>">
         <form class="row form" action="quan-ly-mau-phieu/action.php?req=add" method="post" enctype="multipart/form-data">
             <input type="hidden" name="csrf_token" value="<?=mauphieu_escape($_SESSION['csrf_token'])?>">
             <div class="col-12">
                 <div class="card card-success">
                     <div class="card-header">
                         <h3 class="card-title">Thêm mới Mẫu phiếu</h3>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-12">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="ten_mau_phieu">Tên mẫu phiếu <span class="color-crimson">*</span></label>
                                     <input type="text" id="ten_mau_phieu" name="ten_mau_phieu" class="form-control <?= ($is_add_error && $status == 'invalid-ten') ? 'is-invalid' : '' ?>" required
                                         placeholder="Nhập tên mẫu phiếu" value="<?=mauphieu_escape(mauphieu_old_value('ten_mau_phieu', 'add'))?>">
                                     <?php if ($is_add_error && $status == 'invalid-ten'): ?>
                                         <small class="text-danger mt-1">Tên mẫu phiếu không được để trống.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-12">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="id_dieu">Chọn điều <span class="color-crimson">*</span></label>
                                     <?php $old_id_dieu = mauphieu_old_value('id_dieu', 'add', array()); ?>
                                     <select class="duallistbox <?= ($is_add_error && $status == 'invalid-dieu') ? 'is-invalid' : '' ?>" multiple="multiple" name="id_dieu[]" id="id_dieu" required>
                                         <?php foreach($dieu__Get_All as $item):?>
                                         <option value="<?=$item->id_dieu?>" <?= in_array($item->id_dieu, $old_id_dieu) ? 'selected' : '' ?>><?=$item->ten_dieu?></option>
                                         <?php endforeach; ?>
                                     </select>
                                     <?php if ($is_add_error && $status == 'invalid-dieu'): ?>
                                         <small class="text-danger mt-1">Vui lòng chọn ít nhất một điều.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-12">
                                 <div class="form-group">
                                     <label class="label-sidebar" for="ghi_chu">Ghi chú</label>
                                     <textarea id="ghi_chu" name="ghi_chu" class="form-control" rows="2"
                                         placeholder="Nhập ghi chú"><?=mauphieu_escape(mauphieu_old_value('ghi_chu', 'add'))?></textarea>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <!-- /.card-body -->
                     <div class="card-footer py-2">
                         <input type="submit" value="Thêm mới" class="btn btn-success float-right font-weight-bold">
                         <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" onclick="toggle_add_form()">Hủy</button>
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
                                     onclick="return confirm_delete_sweet('quan-ly-mau-phieu/action.php?req=delete&id_mau_phieu=<?=$item->id_mau_phieu?>&csrf_token=<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>', 'Mẫu phiếu')">
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
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'locked'): ?>
            Toast.fire('Từ chối!', 'Không thể Sửa/Xóa. Mẫu phiếu này đã được sử dụng trong Đợt chấm điểm!', 'warning');
        <?php elseif ($_GET['status'] == 'not-found'): ?>
            Toast.fire('Lỗi!', 'Không tìm thấy Mẫu phiếu này!', 'error');
        <?php elseif ($_GET['status'] == 'locked_update'): ?>
            Toast.fire('Cảnh báo!', 'Mẫu phiếu này đã sử dụng trong lịch sử. Vui lòng tạo mới thay vì sửa!', 'warning');
        <?php endif; ?>
    <?php endif; ?>

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

function toggle_add_form() {
    var addForm = document.getElementById('div_add_form');
    var btnToggle = document.getElementById('btn-toggle-add');
    var icon = btnToggle.querySelector('i');
    
    if (addForm.style.display === 'none' || addForm.style.display === '') {
        addForm.style.display = 'block';
        btnToggle.classList.remove('btn-success');
        btnToggle.classList.add('btn-cancel-custom');
        icon.classList.remove('fa-plus');
        icon.classList.add('fa-times');
        btnToggle.innerHTML = '<i class="fas fa-times"></i>';
        $('#div_update').html('');
    } else {
        addForm.style.display = 'none';
        btnToggle.classList.remove('btn-cancel-custom');
        btnToggle.classList.add('btn-success');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-plus');
        btnToggle.innerHTML = '<i class="fas fa-plus"></i> Thêm mới';
    }
}

function cancel_update() {
    $('#div_update').html('');
}

function update_obj(id_mau_phieu) {
    $.post('quan-ly-mau-phieu/update.php', {
        'id_mau_phieu': id_mau_phieu,
        'csrf_token': '<?=mauphieu_escape($_SESSION["csrf_token"])?>'
    }, function(data) {
        document.getElementById('div_add_form').style.display = 'none';
        var btnToggle = document.getElementById('btn-toggle-add');
        btnToggle.classList.remove('btn-cancel-custom');
        btnToggle.classList.add('btn-success');
        btnToggle.innerHTML = '<i class="fas fa-plus"></i> Thêm mới';
        $('#div_update').html(data);
        $('html, body').animate({
            scrollTop: $("#div_update").offset().top - 80
        }, 500);
    });
}

function update_obj_dieu(id_mau_phieu) {
    $.post('quan-ly-mau-phieu/update_dieu.php', {
        'id_mau_phieu': id_mau_phieu,
        'csrf_token': '<?=mauphieu_escape($_SESSION["csrf_token"])?>'
    }, function(data) {
        document.getElementById('div_add_form').style.display = 'none';
        var btnToggle = document.getElementById('btn-toggle-add');
        btnToggle.classList.remove('btn-cancel-custom');
        btnToggle.classList.add('btn-success');
        btnToggle.innerHTML = '<i class="fas fa-plus"></i> Thêm mới';
        $('#div_update').html(data);
        $('html, body').animate({
            scrollTop: $("#div_update").offset().top - 80
        }, 500);
    });
}
 </script>
