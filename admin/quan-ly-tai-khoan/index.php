 <?php
    // require "../models/getModel.php";
    $taikhoan__Get_All = $taikhoan->taikhoan__Get_All();
    $phannhom__Get_All = $phannhom->phannhom__Get_All();
    $lophoc__Get_All = $lophoc->lophoc__Get_All();
    $khoa__Get_All = $khoa->khoa__Get_All();
    $taikhoan__Get_All_Admin = $taikhoan->taikhoan__Get_All_Phan_Nhom(0);
    $taikhoan__Get_All_Manager = $taikhoan->taikhoan__Get_All_Phan_Nhom(1);
    $taikhoan__Get_All_Sv = $taikhoan->taikhoan__Get_All_Phan_Nhom(2);
    $taikhoan__Get_All_Btdk = $taikhoan->taikhoan__Get_All_Phan_Nhom(3);
    $taikhoan__Get_All_Cvht = $taikhoan->taikhoan__Get_All_Phan_Nhom(4);
    $taikhoan__Get_All_All = $taikhoan->taikhoan__Get_All();
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $taikhoan_old_input = $_SESSION['taikhoan_old_input'] ?? [];
    
    function taikhoan_old_value($key, $context, $default = '') {
        global $taikhoan_old_input;
        if (isset($taikhoan_old_input['context']) && $taikhoan_old_input['context'] === $context && isset($taikhoan_old_input[$key])) {
            return $taikhoan_old_input[$key];
        }
        return $default;
    }

    if (isset($_GET['view'])) {
        $view = $_GET['view'];
        if ($view == 'view_all') {
            $taikhoan__Get_All = $taikhoan->taikhoan__Get_All();
        }
        if ($view == 'view_admin') {
            $taikhoan__Get_All = $taikhoan->taikhoan__Get_All_Phan_Nhom(0);
        }
        if ($view == 'view_manager') {
            $taikhoan__Get_All = $taikhoan->taikhoan__Get_All_Phan_Nhom(1);
        }
        if ($view == 'view_sv') {
            $taikhoan__Get_All = $taikhoan->taikhoan__Get_All_Phan_Nhom(2);
        }
        if ($view == 'view_btdk') {
            $taikhoan__Get_All = $taikhoan->taikhoan__Get_All_Phan_Nhom(3);
        }
        if ($view == 'view_cvht') {
            $taikhoan__Get_All = $taikhoan->taikhoan__Get_All_Phan_Nhom(4);
        }
    }
    if (isset($_GET['view_by_lop'])) {
        $taikhoan__Get_All = $taikhoan->taikhoan__Get_By_Lop_Hoc($_GET['view_by_lop']);
        
        $count_lop_truong = 0;
        $count_bi_thu = 0;
        foreach ($taikhoan__Get_All as $item) {
            if (isset($item->chuc_vu)) {
                if ($item->chuc_vu == 1) $count_lop_truong++;
                if ($item->chuc_vu == 2) $count_bi_thu++;
            }
        }
        
        if (isset($_GET['view_chuc_vu'])) {
            $filtered = [];
            foreach ($taikhoan__Get_All as $item) {
                if (isset($item->chuc_vu) && $item->chuc_vu == $_GET['view_chuc_vu']) {
                    $filtered[] = $item;
                }
            }
            $taikhoan__Get_All = $filtered;
        }
    }
    ?>


 <style>
/* Style for the select boxes */
select#bootstrap-duallistbox-nonselected-list_id_nguoi_dung\[\],
select#bootstrap-duallistbox-nonselected-list_,
select#bootstrap-duallistbox-selected-list_id_nguoi_dung\[\],
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

select#bootstrap-duallistbox-nonselected-list_id_nguoi_dung\[\] option,
select#bootstrap-duallistbox-nonselected-list_ option,
select#bootstrap-duallistbox-selected-list_id_nguoi_dung\[\] option,
select#bootstrap-duallistbox-selected-list_ option {
    padding: 5px 8px;
    margin-bottom: 2px;
    border-radius: 4px;
    background-color: #fff;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
}

select#bootstrap-duallistbox-nonselected-list_id_nguoi_dung\[\] option:hover,
select#bootstrap-duallistbox-nonselected-list_ option:hover,
select#bootstrap-duallistbox-selected-list_id_nguoi_dung\[\] option:hover,
select#bootstrap-duallistbox-selected-list_ option:hover {
    background-color: #e9ecef;
}

select#bootstrap-duallistbox-nonselected-list_id_nguoi_dung\[\] option:checked,
select#bootstrap-duallistbox-nonselected-list_ option:checked,
select#bootstrap-duallistbox-selected-list_id_nguoi_dung\[\] option:checked,
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
    color: #6c757d;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 5px;
}
 </style>
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header pb-0">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Quản lý Tài khoản</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý Tài khoản</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <!-- Nhựt sửa: Thêm nút bật/tắt form thêm mới -->
     <?php $is_add_error = isset($taikhoan_old_input['context']) && $taikhoan_old_input['context'] === 'add'; ?>
     
     <section class="content mb-2">
         <button type="button" class="btn <?= $is_add_error ? 'btn-cancel-custom' : 'btn-success' ?> font-weight-bold ml-2" id="btn-toggle-add" onclick="toggle_add_form()">
             <?php if ($is_add_error): ?>
                 <i class="fas fa-times"></i>
             <?php else: ?>
                 <i class="fas fa-plus"></i> Thêm mới
             <?php endif; ?>
         </button>
     </section>

     <!-- Nhựt sửa: Ẩn form thêm mới mặc định -->
     <section class="content" id="div_add_form" style="<?= $is_add_error ? '' : 'display: none;' ?>">
         <div class="container-fluid">
             <form action="quan-ly-tai-khoan/action.php?req=add_admin" method="post" enctype="multipart/form-data" id="form-add-taikhoan">
                 <input type="hidden" name="id_phan_quyen" value="<?= $phanquyen->phanquyen__Get_By_Cap_Bac(2)->id_phan_quyen ?>">
                 <div class="row">
                     <div class="col-12">
                         <div class="card card-success">
                             <div class="card-header">
                                 <h3 class="card-title">Thêm mới Tài khoản</h3>
                             </div>
                             <div class="card-body">
                                 <div class="row">
                                     <div class="col-6">
                                         <div class="form-group">
                                             <label class="label-sidebar" for="">Phân nhóm <span class="color-crimson">*</span></label>
                                             <select class="form-control" name="id_phan_nhom" id="id_phan_nhom_select" required>
                                                 <?php foreach ($phannhom__Get_All as $item) : ?>
                                                 <option value="<?= $item->id_phan_nhom ?>" data-capbac="<?= $item->cap_bac ?>" <?= ($item->cap_bac == 0) ? 'selected' : '' ?>>
                                                     <?= $item->ten_phan_nhom ?>
                                                 </option>
                                                 <?php endforeach; ?>
                                             </select>
                                         </div>
                                     </div>
                                     
                                     <!-- FIELDS FOR ADMIN / MANAGER (cap_bac = 0, 1) -->
                                     <div class="col-6 block-admin-manager">
                                         <div class="form-group">
                                             <label class="label-sidebar" for="">Email <span class="color-crimson">*</span></label>
                                             <input type="email" id="email" name="email" class="form-control <?= ($is_add_error && ($_GET['status'] ?? '') == 'duplicate-email-sinh-vien') ? 'is-invalid' : '' ?>" 
                                                 placeholder="Nhập email" value="<?= htmlspecialchars(taikhoan_old_value('email', 'add')) ?>">
                                             <?php if ($is_add_error && isset($_GET['status']) && $_GET['status'] == 'duplicate-email-sinh-vien'): ?>
                                                 <small class="text-danger mt-1">Email này đã tồn tại trong hệ thống.</small>
                                             <?php endif; ?>
                                         </div>
                                     </div>
                                     <div class="col-6 block-admin-manager">
                                         <div class="form-group">
                                             <label class="label-sidebar" for="">Mật khẩu <span class="color-crimson">*</span></label>
                                             <div class="input-group">
                                                 <input type="password" id="mat_khau" name="mat_khau" class="form-control" 
                                                     placeholder="Nhập mật khẩu" value="<?= htmlspecialchars(taikhoan_old_value('mat_khau', 'add')) ?>">
                                                 <div class="input-group-append">
                                                     <span class="input-group-text bg-white" onclick="togglePasswordVisibility('mat_khau', this)" style="cursor: pointer;">
                                                         <i class="fas fa-eye text-secondary"></i>
                                                     </span>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>

                                     <!-- FIELDS FOR SINH VIEN / CO VAN HOC TAP (cap_bac = 2, 4) -->
                                     <div class="col-6 block-lop-hoc" style="display: none;">
                                         <div class="form-group">
                                             <label class="label-sidebar" for="">Chọn lớp <span class="color-crimson">*</span></label>
                                             <select class="form-control" name="id_lop_hoc" id="id_lop_hoc_select">
                                                 <option value="">Chọn Lớp</option>
                                                 <?php foreach ($lophoc__Get_All as $item) : ?>
                                                 <option value="<?= $item->id_lop_hoc ?>">
                                                     <?= $item->ten_lop_hoc ?>
                                                 </option>
                                                 <?php endforeach; ?>
                                             </select>
                                         </div>
                                     </div>

                                     <!-- FIELDS FOR BI THU DOAN KHOA (cap_bac = 3) -->
                                     <div class="col-6 block-khoa" style="display: none;">
                                         <div class="form-group">
                                             <label class="label-sidebar" for="">Chọn khoa <span class="color-crimson">*</span></label>
                                             <select class="form-control" name="id_khoa" id="id_khoa_select">
                                                 <option value="">Chọn Khoa</option>
                                                 <?php foreach ($khoa__Get_All as $item) : ?>
                                                 <option value="<?= $item->id_khoa ?>">
                                                     <?= $item->ten_khoa ?>
                                                 </option>
                                                 <?php endforeach; ?>
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                                 
                                 <div class="row block-duallistbox" style="display: none;">
                                     <div class="col-12">
                                         <div class="form-group">
                                             <label class="label-sidebar" id="lbl-chon-nguoi-dung">Chọn người dùng <span class="color-crimson">*</span></label>
                                             <select class="duallistbox" multiple="multiple" name="id_nguoi_dung[]" id="id_nguoi_dung_select">
                                             </select>
                                         </div>
                                     </div>
                                 </div>

                             </div>
                             <div class="card-footer py-2">
                                 <button type="submit" class="btn btn-success float-right font-weight-bold" style="font-weight: bold;">Thêm mới</button>
                                 <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" style="font-weight: bold;" onclick="toggle_add_form()">Hủy</button>
                             </div>
                         </div>
                     </div>
                 </div>
             </form>
         </div>
     </section>

     <section class="content" id="div_update">
     </section>

     <section class="content">
         <div class="container-fluid">
             <div class="row">
                 <div class="col-12">
                     <div class="card card-primary">
             <div class="card-header">
                 <h3 class="card-title">Danh sách Tài khoản</h3>
                 <div class="card-tools">
                     <span class="mr-2">
                         <a href="?page=quan-ly-tai-khoan&view=view_all" type="button" class="btn btn-tool">
                             Tất cả <?= count($taikhoan__Get_All_All) ?>
                         </a> |
                         <a href="?page=quan-ly-tai-khoan&view=view_admin" type="button" class="btn btn-tool">
                             Admin <?= count($taikhoan__Get_All_Admin) ?>
                         </a> |
                         <a href="?page=quan-ly-tai-khoan&view=view_manager" type="button" class="btn btn-tool">
                             Manager <?= count($taikhoan__Get_All_Manager) ?>
                         </a> |
                         <a href="?page=quan-ly-tai-khoan&view=view_btdk" type="button" class="btn btn-tool">
                             Bí thư đoàn khoa <?= count($taikhoan__Get_All_Btdk) ?>
                         </a> |
                         <a href="?page=quan-ly-tai-khoan&view=view_cvht" type="button" class="btn btn-tool">
                             Cố vấn <?= count($taikhoan__Get_All_Cvht) ?>
                         </a>
                         <?php if (isset($_GET['view_by_lop'])) : ?>
                         | <a href="?page=quan-ly-tai-khoan&view_by_lop=<?= $_GET['view_by_lop'] ?>&view_chuc_vu=2" type="button" class="btn btn-tool">
                             Bí thư chi đoàn <?= $count_bi_thu ?>
                         </a> |
                         <a href="?page=quan-ly-tai-khoan&view_by_lop=<?= $_GET['view_by_lop'] ?>&view_chuc_vu=1" type="button" class="btn btn-tool">
                             Lớp trưởng <?= $count_lop_truong ?>
                         </a>
                         <?php endif; ?>
                         | <a href="?page=quan-ly-tai-khoan&view=view_sv" type="button" class="btn btn-tool">
                             Sinh viên <?= count($taikhoan__Get_All_Sv) ?>
                         </a>
                     </span>
                     <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                         <i class="fas fa-minus"></i>
                     </button>
                 </div>


             </div>
             <!-- /.card-header -->

             <div class="card-body">
                 <div class="row">
                     <div class="col-6">
                     </div>
                 </div>

                  
                     <table id="tablejs" class="table table-bordered table-striped display responsive" width="100%" style="font-size: 13px;">
                     <thead style="font-size: 13px;">
                          <tr>
                              <th style="width: 3%; white-space: nowrap;">STT</th>
                              <th style="width: 21%; white-space: nowrap;">Họ và tên</th>
                              <th style="width: 22%; white-space: nowrap;">Email</th>
                              <th style="width: 10%; white-space: nowrap;">Phân nhóm</th>
                              <th style="width: 10%; white-space: nowrap;">Phân quyền</th>
                              <th style="width: 7%; white-space: nowrap;">Trạng thái</th>
                              <th style="width: 7%; white-space: nowrap;">Gửi email</th>
                              <th style="width: 12%; white-space: nowrap;">Reset mật khẩu</th>
                              <th style="width: 8%; white-space: nowrap;">Thao tác</th>
                          </tr>
                      </thead>
                     <tbody>
                         <?php 
                          $phanquyen__Get_All = $phanquyen->phanquyen__Get_All();
                          $arr_phan_quyen = [];
                          foreach ($phanquyen__Get_All as $pq_item) {
                              $arr_phan_quyen[$pq_item->id_phan_quyen] = $pq_item;
                          }
                          
                          $arr_phan_nhom = [];
                          foreach ($phannhom__Get_All as $pn_item) {
                              $arr_phan_nhom[$pn_item->id_phan_nhom] = $pn_item;
                          }
                          
                          $sinhvien__Get_All_Raw = $sinhvien->sinhvien__Get_All();
                          $arr_sinh_vien = [];
                          foreach ($sinhvien__Get_All_Raw as $sv_item) {
                              $arr_sinh_vien[$sv_item->id_sinh_vien] = $sv_item->ten_sinh_vien;
                          }
                          
                          $bithudoankhoa__Get_All_Raw = $bithudoankhoa->bithudoankhoa__Get_All();
                          $arr_bi_thu = [];
                          foreach ($bithudoankhoa__Get_All_Raw as $bt_item) {
                              $arr_bi_thu[$bt_item->id_bi_thu] = $bt_item->ten_bi_thu;
                          }
                          
                          $giangvien__Get_All_Raw = $giangvien->giangvien__Get_All();
                          $arr_giang_vien = [];
                          foreach ($giangvien__Get_All_Raw as $gv_item) {
                              $arr_giang_vien[$gv_item->id_giang_vien] = $gv_item->ten_giang_vien;
                          }
                          
                          $num = 0; 
                          ?>
                         <?php foreach ($taikhoan__Get_All as $item) : ?>
                         <?php 
                            // Bỏ qua hiển thị tài khoản đang đăng nhập
                            if (isset($_SESSION['admin']) && $_SESSION['admin']->email == $item->email) {
                                continue;
                            }
                         ?>
                         <tr>
                             <td><?= ++$num ?></td>
                             <?php
                                // quân sửa: Lấy họ tên dựa trên cấp bậc phân nhóm
                                $ho_ten = '<span class="text-secondary">N/A</span>';
                                $pn = isset($arr_phan_nhom[$item->id_phan_nhom]) ? $arr_phan_nhom[$item->id_phan_nhom] : null;
                                if ($pn) {
                                    if ($pn->cap_bac == 0) $ho_ten = "Admin";
                                    elseif ($pn->cap_bac == 1) $ho_ten = "Manager";
                                    elseif ($pn->cap_bac == 2) {
                                        $ho_ten = isset($arr_sinh_vien[$item->id_nguoi_dung]) ? htmlspecialchars($arr_sinh_vien[$item->id_nguoi_dung]) : '<span class="text-secondary">N/A</span>';
                                    } elseif ($pn->cap_bac == 3) {
                                        $ho_ten = isset($arr_bi_thu[$item->id_nguoi_dung]) ? htmlspecialchars($arr_bi_thu[$item->id_nguoi_dung]) : '<span class="text-secondary">N/A</span>';
                                    } elseif ($pn->cap_bac == 4) {
                                        $ho_ten = isset($arr_giang_vien[$item->id_nguoi_dung]) ? htmlspecialchars($arr_giang_vien[$item->id_nguoi_dung]) : '<span class="text-secondary">N/A</span>';
                                    }
                                }
                                $pq = isset($arr_phan_quyen[$item->id_phan_quyen]) ? $arr_phan_quyen[$item->id_phan_quyen] : null;
                             ?>
                             <td><b><?= $ho_ten ?></b></td>
                             <td class="text-center" style="text-align: center !important;"><?= htmlspecialchars($item->email ?? '') ?></td>
                             <!-- quân sửa: Bổ sung kiểm tra dữ liệu tồn tại để tránh lỗi báo Attempt to read property on bool -->
                             <td class="text-center" style="text-align: center !important;"><?= $pn ? htmlspecialchars($pn->ten_phan_nhom) : '<span class="text-danger">Chưa xác định</span>' ?></td>
                             <td class="text-center" style="text-align: center !important;"><?= $pq ? htmlspecialchars($pq->ten_phan_quyen) : '<span class="text-danger">Chưa xác định</span>' ?></td>
                             <td class="text-center" style="text-align: center !important;"
                                 <?php if ($item->email !== 'admin@gmail.com'): ?>
                                 onclick="return confirm_sweet('quan-ly-tai-khoan/action.php?req=active&id_tai_khoan=<?= $item->id_tai_khoan ?>&trang_thai=<?= $item->trang_thai ?>', '<?= $item->trang_thai == 1 ? 'Khóa tài khoản này?' : 'Kích hoạt tài khoản này?' ?>', 'status')"
                                 <?php endif; ?>>
                                 <?php if ($item->email !== 'admin@gmail.com'): ?>
                                 <?= $item->trang_thai == 1 ? "<a href='#' class='btn btn-sm btn-success'><i class='ri-checkbox-circle-line'></i></a>" : "<a href='#' class='btn btn-sm btn-danger'><i class='ri-forbid-line'></i></a>" ?>
                                 <?php endif; ?>
                             </td>
                             <td class="text-center" style="text-align: center !important;">
                                 <?php if ($item->email !== 'admin@gmail.com'): ?>
                                 <a href="#" type="button" class="btn btn-sm btn-warning"
                                     onclick="return send_mail('<?= $item->email ?>', '<?= $item->mat_khau ?>')">
                                     <i class="ri-mail-send-line"></i>
                                 </a>
                                 <?php endif; ?>
                             </td>
                             <td class="text-center" style="text-align: center !important;">
                                 <?php if ($item->email !== 'admin@gmail.com'): ?>
                                 <a href="#" type="button" class="btn btn-sm btn-info"
                                     onclick="return confirm_sweet('quan-ly-tai-khoan/action.php?req=reset&id_tai_khoan=<?= $item->id_tai_khoan ?>', 'Khôi phục mật khẩu tài khoản này về mặc định?', 'reset')">
                                     <i class="ri-key-2-line"></i>
                                 </a>
                                 <?php endif; ?>
                             </td>
                             <td class="text-center" style="text-align: center !important;">
                                 <?php if ($item->email !== 'admin@gmail.com'): ?>
                                 <a href="#" type="button" class="btn btn-sm btn-danger"
                                     onclick="return confirm_delete_sweet('quan-ly-tai-khoan/action.php?req=delete&id_tai_khoan=<?= $item->id_tai_khoan ?>', 'Tài khoản')">
                                     <i class="ri-delete-bin-line"></i>
                                 </a>
                                 <?php endif; ?>
                             </td>

                         </tr>
                         <?php endforeach ?>
                     </tbody>
                 </table>

             </div>
             <!-- /.card-body -->
         </div>
                 </div>
             </div>
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
            "info": "Hiển thị _START_ - _END_ của _TOTAL_ tài khoản",
            "infoEmpty": "Hiển thị 0 - 0 của 0 tài khoản",
            "infoFiltered": "(lọc từ _MAX_ tài khoản)",
            "infoPostFix": "",
            "lengthMenu": "Hiển thị _MENU_ tài khoản",
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
            "targets": [5, 6, 7, 8],
            "orderable": false,
            "searchable": false
        }],
        "buttons": [
            <?php if (isset($_GET['view_by_lop'])) : ?>
            {
                "text": "<i class='ri-mail-send-line'></i> Gửi mail toàn lớp",
                "className": "btn btn-sm btn-custom-mail mr-1",
                "action": function ( e, dt, node, config ) {
                    send_mail_all(<?= $_GET['view_by_lop'] ?>);
                }
            },
            <?php endif; ?>
            {
            "extend": "collection",
            "text": "<i class='fas fa-file-export'></i> Xuất dữ liệu",
            "className": "btn btn-sm btn-primary",
            "align": "button-right",
            "buttons": [
                {
                    "extend": "copy",
                    "text": "<i class='far fa-copy'></i> Copy",
                    "exportOptions": {
                        "columns": ":visible:not(:nth-last-child(-n+4))"
                    }
                },
                {
                    "extend": "csv",
                    "text": "<i class='fas fa-file-csv'></i> CSV",
                    "bom": true,
                    "exportOptions": {
                        "columns": ":visible:not(:nth-last-child(-n+4))"
                    }
                },
                {
                    "extend": "excel",
                    "text": "<i class='far fa-file-excel'></i> Excel",
                    "exportOptions": {
                        "columns": ":visible:not(:nth-last-child(-n+4))"
                    }
                },
                {
                    "extend": "pdf",
                    "text": "<i class='far fa-file-pdf'></i> PDF",
                    "exportOptions": {
                        "columns": ":visible:not(:nth-last-child(-n+4))"
                    }
                },
                {
                    "extend": "print",
                    "text": "<i class='fas fa-print'></i> In",
                    "exportOptions": {
                        "columns": ":visible:not(:nth-last-child(-n+4))"
                    }
                }
            ]
        },
        {
            "text": "<i class='fas fa-filter'></i>",
            "titleAttr": "Bộ lọc",
            "className": "btn btn-sm btn-custom-filter ml-1",
            "attr": {
                "id": "btn-filter-dropdown"
            }
        }],
        "initComplete": function() {
            var filterHtml = `
            <style>
            .dataTables_wrapper .dt-buttons {
                display: flex !important;
                flex-wrap: nowrap !important;
                align-items: center !important;
            }
            .dataTables_wrapper .dt-buttons .btn-custom-filter {
                background-color: #0f2a5a !important;
                border: 1px solid #0f2a5a !important;
                color: #fff !important;
                border-radius: 4px !important;
                padding: 6px 12px !important;
                font-size: 14px !important;
                font-weight: 500 !important;
                box-shadow: none !important;
                transition: all 0.15s ease-in-out !important;
                display: inline-flex !important;
                align-items: center !important;
                white-space: nowrap !important;
            }
            .dataTables_wrapper .dt-buttons .btn-custom-filter:hover {
                background-color: transparent !important;
                border-color: #0f2a5a !important;
                color: #0f2a5a !important;
            }
            .dataTables_wrapper .dt-buttons .btn-custom-mail {
                background-color: #0d6efd !important;
                border: 1px solid #0d6efd !important;
                color: #fff !important;
                border-radius: 4px !important;
                padding: 6px 12px !important;
                font-size: 14px !important;
                font-weight: 500 !important;
                box-shadow: none !important;
                transition: all 0.15s ease-in-out !important;
                display: inline-flex !important;
                align-items: center !important;
                white-space: nowrap !important;
            }
            .dataTables_wrapper .dt-buttons .btn-custom-mail:hover {
                background-color: transparent !important;
                border-color: #0d6efd !important;
                color: #0d6efd !important;
            }
            #custom-filter-menu {
                display: none;
                position: absolute;
                right: 0;
                top: 100%;
                margin-top: 5px;
                width: 300px;
                background: #fff;
                border: 1px solid rgba(0,0,0,.15);
                border-radius: .25rem;
                box-shadow: 0 .5rem 1rem rgba(0,0,0,.175);
                z-index: 1050;
            }
            </style>
            <div id="custom-filter-menu" class="p-3">
                <div class="form-group mb-2 text-left">
                    <label class="label-sidebar">Lớp học:</label>
                    <select id="filter_lop_hoc_reload" class="form-control form-control-sm">
                        <option value="?page=quan-ly-tai-khoan">-- Tất cả lớp học --</option>
                        <?php foreach ($lophoc__Get_All as $item) : ?>
                        <option value="?page=quan-ly-tai-khoan&view_by_lop=<?= $item->id_lop_hoc ?>"
                            <?= isset($_GET['view_by_lop']) && $_GET['view_by_lop'] == $item->id_lop_hoc ? 'selected' : '' ?>>
                            <?= $item->ten_lop_hoc ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-2 text-left">
                    <label class="label-sidebar">Phân nhóm:</label>
                    <select id="filter_phan_nhom" class="form-control form-control-sm">
                        <option value="">-- Tất cả phân nhóm --</option>
                    </select>
                </div>
                <div class="form-group mb-2 text-left">
                    <label class="label-sidebar">Phân quyền:</label>
                    <select id="filter_phan_quyen" class="form-control form-control-sm">
                        <option value="">-- Tất cả phân quyền --</option>
                    </select>
                </div>
                <div class="form-group mb-2 text-left">
                    <label class="label-sidebar">Trạng thái:</label>
                    <select id="filter_trang_thai" class="form-control form-control-sm">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="ri-checkbox-circle-line">Hoạt động</option>
                        <option value="ri-forbid-line">Bị khóa</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-cancel-custom mr-2 font-weight-bold" id="btn-cancel-filter">Hủy</button>
                    <button type="button" class="btn btn-success font-weight-bold" id="btn-apply-filter">Áp dụng</button>
                </div>
            </div>`;
            
            var $btn = $('#btn-filter-dropdown');
            $btn.wrap('<div style="position: relative; display: inline-block;"></div>');
            $btn.parent().append(filterHtml);

            var table = $('#tablejs').DataTable();

            function updateCascadeDropdowns() {
                var selectedNhom = $('#filter_phan_nhom').val() || "";
                var selectedQuyen = $('#filter_phan_quyen').val() || "";
                
                var nhomOptions = [];
                var quyenOptions = [];
                
                table.rows().every(function() {
                    var data = this.data();
                    var nhom = $('<div>').html(data[3]).text().trim();
                    var quyen = $('<div>').html(data[4]).text().trim();
                    
                    if (nhom !== '' && nhom !== 'Chưa xác định' && nhomOptions.indexOf(nhom) === -1) nhomOptions.push(nhom);
                    
                    if (selectedNhom === "" || nhom === selectedNhom) {
                        if (quyen !== '' && quyen !== 'Chưa xác định' && quyenOptions.indexOf(quyen) === -1) quyenOptions.push(quyen);
                    }
                });
                
                var selectNhom = $('#filter_phan_nhom');
                selectNhom.empty().append('<option value="">-- Tất cả phân nhóm --</option>');
                nhomOptions.sort().forEach(function(opt) {
                    selectNhom.append('<option value="'+opt+'" '+(opt === selectedNhom ? 'selected' : '')+'>'+opt+'</option>');
                });
                
                var selectQuyen = $('#filter_phan_quyen');
                selectQuyen.empty().append('<option value="">-- Tất cả phân quyền --</option>');
                quyenOptions.sort().forEach(function(opt) {
                    selectQuyen.append('<option value="'+opt+'" '+(opt === selectedQuyen ? 'selected' : '')+'>'+opt+'</option>');
                });
            }

            $('#filter_phan_nhom').on('change', function() {
                $('#filter_phan_quyen').val('');
                updateCascadeDropdowns();
            });

            updateCascadeDropdowns();
            
            var pendingNhom = sessionStorage.getItem('pending_filter_nhom');
            var pendingQuyen = sessionStorage.getItem('pending_filter_quyen');
            var pendingTrangThai = sessionStorage.getItem('pending_filter_trangthai');
            
            if (pendingNhom !== null || pendingQuyen !== null || pendingTrangThai !== null) {
                if (pendingNhom) $('#filter_phan_nhom').val(pendingNhom);
                if (pendingQuyen) $('#filter_phan_quyen').val(pendingQuyen);
                if (pendingTrangThai) $('#filter_trang_thai').val(pendingTrangThai);
                
                updateCascadeDropdowns();
                
                table.column(3).search(pendingNhom ? '^' + $.fn.dataTable.util.escapeRegex(pendingNhom) + '$' : '', true, false)
                     .column(4).search(pendingQuyen ? '^' + $.fn.dataTable.util.escapeRegex(pendingQuyen) + '$' : '', true, false)
                     .column(5).search(pendingTrangThai ? pendingTrangThai : '', true, false)
                     .draw();
            }

            $btn.on('click', function(e) {
                e.stopPropagation();
                $('#custom-filter-menu').fadeToggle(200);
            });

            $('#custom-filter-menu').on('click', function(e) {
                e.stopPropagation();
            });

            $(document).on('click', function() {
                $('#custom-filter-menu').fadeOut(200);
            });

            $('#btn-apply-filter').on('click', function() {
                var nhomVal = $('#filter_phan_nhom').val() || "";
                var quyenVal = $('#filter_phan_quyen').val() || "";
                var trangThaiVal = $('#filter_trang_thai').val() || "";
                
                var lopHocUrl = $('#filter_lop_hoc_reload').val();
                var selectedLopHoc = new URLSearchParams(lopHocUrl.split('?')[1]).get('view_by_lop');
                var currentLopHoc = new URLSearchParams(window.location.search).get('view_by_lop');
                
                sessionStorage.setItem('pending_filter_nhom', nhomVal);
                sessionStorage.setItem('pending_filter_quyen', quyenVal);
                sessionStorage.setItem('pending_filter_trangthai', trangThaiVal);
                
                if (selectedLopHoc !== currentLopHoc) {
                    window.location.href = lopHocUrl;
                    return;
                }
                
                table.column(3).search(nhomVal ? '^' + $.fn.dataTable.util.escapeRegex(nhomVal) + '$' : '', true, false)
                     .column(4).search(quyenVal ? '^' + $.fn.dataTable.util.escapeRegex(quyenVal) + '$' : '', true, false)
                     .column(5).search(trangThaiVal ? trangThaiVal : '', true, false)
                     .draw();
                $('#custom-filter-menu').fadeOut(200);
            });

            $('#btn-cancel-filter').on('click', function() {
                var urlParams = new URLSearchParams(window.location.search);
                sessionStorage.removeItem('pending_filter_nhom');
                sessionStorage.removeItem('pending_filter_quyen');
                sessionStorage.removeItem('pending_filter_trangthai');
                
                if (urlParams.has('view_by_lop')) {
                    window.location.href = '?page=quan-ly-tai-khoan';
                    return;
                }
                
                $('#filter_lop_hoc_reload').val('?page=quan-ly-tai-khoan');
                $('#filter_phan_nhom').val('');
                $('#filter_phan_quyen').val('');
                $('#filter_trang_thai').val('');
                updateCascadeDropdowns();
                table.column(3).search('')
                     .column(4).search('')
                     .column(5).search('')
                     .draw();
                $('#custom-filter-menu').fadeOut(200);
            });
        }
    });
});

function togglePasswordVisibility(inputId, iconSpan) {
    var input = document.getElementById(inputId);
    var icon = iconSpan.querySelector('i');
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Nhựt sửa: Hàm bật/tắt hiển thị form thêm mới
function toggle_add_form() {
    var addForm = $('#div_add_form');
    var btn = $('#btn-toggle-add');
    
    // Đóng form cập nhật nếu đang mở
    $("#div_update").html('');
    
    addForm.slideToggle(300, function() {
        if (addForm.is(':visible')) {
            btn.html('<i class="fas fa-times"></i>').removeClass('btn-success').addClass('btn-cancel-custom');
        } else {
            btn.html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-cancel-custom').addClass('btn-success');
        }
    });
}

function update_obj(id_tai_khoan) {
    $.post('quan-ly-tai-khoan/update.php', {
        'id_tai_khoan': id_tai_khoan,
    }, function(data) {
        // Nhựt sửa: Ẩn form thêm mới khi mở form cập nhật
        $('#div_add_form').slideUp(300);
        $('#btn-toggle-add').html('<i class="fas fa-plus"></i> Thêm mới').removeClass('btn-cancel-custom').addClass('btn-success');
        $('#div_update').html(data);
    });
}

function cancel_update() {
    $("#div_update").html('');
}

function send_mail(email, password) {

    Swal.fire({
        title: 'Đang gửi Email...',
        html: 'Vui lòng chờ trong giây lát.',
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading()
        },
        willClose: () => {
            Swal.DismissReason.timer;
        }
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
            console.log('I was closed by the timer')
        }
    })
    $.post('quan-ly-tai-khoan/mail.php', {
        'email': email,
        'password': password,
    }, function(mail_data) {
        Swal.DismissReason.timer;
        if (mail_data.includes('Message has been sent')) {
            Toast.fire({
                title: 'Thành công!',
                text: 'Email đã được gửi thành công.',
                icon: 'success'
            });
        } else {
            Toast.fire({
                title: 'Lỗi!',
                text: 'Gửi email thất bại: ' + mail_data,
                icon: 'error'
            });
        }
    }).fail(function() {
        Toast.fire({
            title: 'Lỗi!',
            text: 'Không thể kết nối đến máy chủ gửi mail.',
            icon: 'error'
        });
    });
}

function send_mail_all(id_lop) {

    Swal.fire({
        title: 'Đang gửi Email...',
        html: 'Vui lòng chờ trong giây lát.',
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading()
        },
        willClose: () => {
            Swal.DismissReason.timer;
        }
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
            console.log('I was closed by the timer')
        }
    })
    $.post('quan-ly-tai-khoan/send_mail_all.php', {
        'id_lop': id_lop,
    }, function(mail_data) {
        Swal.DismissReason.timer;
        if (mail_data.includes('Message has been sent')) {
            Toast.fire({
                title: 'Thành công!',
                text: 'Tất cả Email đã được gửi thành công.',
                icon: 'success'
            });
        } else {
            Toast.fire({
                title: 'Lỗi!',
                text: 'Có lỗi trong quá trình gửi mail hàng loạt: ' + mail_data,
                icon: 'error'
            });
        }
    }).fail(function() {
        Toast.fire({
            title: 'Lỗi!',
            text: 'Không thể kết nối đến máy chủ gửi mail.',
            icon: 'error'
        });
    });
}
 </script>
<script>
window.addEventListener("load", function() {
    function updateFormLayout() {
        var select = $('#id_phan_nhom_select');
        var cap_bac = select.find(':selected').data('capbac');
        var form = $('#form-add-taikhoan');

        $('.block-admin-manager').hide();
        $('.block-lop-hoc').hide();
        $('.block-khoa').hide();
        $('.block-duallistbox').hide();
        
        $('#email, #mat_khau, #id_lop_hoc_select, #id_khoa_select, #id_nguoi_dung_select').prop('required', false);

        if (cap_bac == 0 || cap_bac == 1) {
            $('.block-admin-manager').show();
            $('#email, #mat_khau').prop('required', true);
            form.attr('action', 'quan-ly-tai-khoan/action.php?req=add_admin');
        } else if (cap_bac == 2) {
            $('.block-lop-hoc').show();
            $('#id_lop_hoc_select').prop('required', true);
            $('#lbl-chon-nguoi-dung').html('Chọn sinh viên <span class="color-crimson">*</span>');
            form.attr('action', 'quan-ly-tai-khoan/action.php?req=add_sv');
        } else if (cap_bac == 3) {
            $('.block-khoa').show();
            $('#id_khoa_select').prop('required', true);
            $('#lbl-chon-nguoi-dung').html('Chọn bí thư đoàn khoa <span class="color-crimson">*</span>');
            form.attr('action', 'quan-ly-tai-khoan/action.php?req=add_btdk');
        } else if (cap_bac == 4) {
            $('.block-lop-hoc').show();
            $('#id_lop_hoc_select').prop('required', true);
            $('#lbl-chon-nguoi-dung').html('Chọn giảng viên <span class="color-crimson">*</span>');
            form.attr('action', 'quan-ly-tai-khoan/action.php?req=add_gv');
        }
    }

    $('#id_phan_nhom_select').on('change', function() {
        updateFormLayout();
        $('#id_lop_hoc_select').val('').trigger('change');
        $('#id_khoa_select').val('').trigger('change');
    });
    
    function loadUsers(req, paramName, paramValue) {
        if (!paramValue) {
            $('#id_nguoi_dung_select').empty();
            $('.block-duallistbox').hide();
            $('#id_nguoi_dung_select').prop('required', false);
            $('#id_nguoi_dung_select').bootstrapDualListbox('refresh');
            return;
        }
        
        $.ajax({
            url: 'quan-ly-tai-khoan/ajax_get_users.php',
            data: { req: req, [paramName]: paramValue },
            dataType: 'json',
            success: function(data) {
                var html = '';
                $.each(data, function(i, item) {
                    html += '<option value="' + item.id + '">' + item.name + '</option>';
                });
                $('#id_nguoi_dung_select').html(html);
                $('.block-duallistbox').show();
                $('#id_nguoi_dung_select').prop('required', true);
                $('#id_nguoi_dung_select').bootstrapDualListbox('refresh');
            }
        });
    }

    $('#id_lop_hoc_select').on('change', function() {
        var cap_bac = $('#id_phan_nhom_select').find(':selected').data('capbac');
        if (cap_bac == 2) {
            loadUsers('get_sv', 'id_lop_hoc', $(this).val());
        } else if (cap_bac == 4) {
            loadUsers('get_gv', 'id_lop_hoc', $(this).val());
        }
    });

    $('#id_khoa_select').on('change', function() {
        var cap_bac = $('#id_phan_nhom_select').find(':selected').data('capbac');
        if (cap_bac == 3) {
            loadUsers('get_btdk', 'id_khoa', $(this).val());
        }
    });

    updateFormLayout();
});
</script>



