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
    }
    ?>


 <style>
select#bootstrap-duallistbox-nonselected-list_id_nguoi_dung\[\],
select#bootstrap-duallistbox-nonselected-list_,
select#bootstrap-duallistbox-selected-list_id_nguoi_dung\[\],
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
                     <h1>Quản lý tài khoản</h1>
                 </div>
                 <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-right">
                         <li class="breadcrumb-item"><a href="#">Home</a></li>
                         <li class="breadcrumb-item active">Quản lý tài khoản</li>
                     </ol>
                 </div>
             </div>
         </div><!-- /.container-fluid -->
     </section>

     <section class="content">
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
                         <label for="">Phân nhóm <span class="color-crimson">(*)</span></label>
                         <select class="form-control" name="id_phan_nhom" required onchange="location.href=this.value">
                             <?php if (isset($_GET['id_phan_nhom'])) : ?>
                             <?php $id_phan_nhom = $_GET['id_phan_nhom']; ?>
                             <option value="<?= $id_phan_nhom ?>">
                                 <?= $phannhom->phannhom__Get_By_Id($id_phan_nhom)->ten_phan_nhom ?>
                             </option>
                             <?php foreach ($phannhom__Get_All as $item) : ?>
                             <?php if ($item->id_phan_nhom != $id_phan_nhom) : ?>
                             <option value="?page=quan-ly-tai-khoan&id_phan_nhom=<?= $item->id_phan_nhom ?>">
                                 <?= $item->ten_phan_nhom ?>
                             </option>
                             <?php endif ?>
                             <?php endforeach; ?>
                             <?php else : ?>
                             <option value="">Chọn Phân nhóm</option>
                             <?php foreach ($phannhom__Get_All as $item) : ?>
                             <option value="?page=quan-ly-tai-khoan&id_phan_nhom=<?= $item->id_phan_nhom ?>">
                                 <?= $item->ten_phan_nhom ?>
                             </option>
                             <?php endforeach; ?>
                             <?php endif ?>

                         </select>
                     </div>
                 </div>

                 <?php if (isset($_GET['id_phan_nhom'])) : ?>
                 <?php $id_phan_nhom = $_GET['id_phan_nhom']; ?>


                 <!-- ADMIN -->
                 <?php if ($phannhom->phannhom__Get_By_Id($id_phan_nhom)->cap_bac == 0) : ?>
                 <form action="quan-ly-tai-khoan/action.php?req=add_admin" method="post" enctype="multipart/form-data">
                     <input type="hidden" name="id_phan_nhom" value="<?= $id_phan_nhom ?>">
                     <input type="hidden" name="id_phan_quyen"
                         value="<?= $phanquyen->phanquyen__Get_By_Cap_Bac(0)->id_phan_quyen ?>">
                     <div class="card-body">
                         <div class="form-group">
                             <label for="">Email <span class="color-crimson">(*)</span></label>
                             <input type="email" id="email" name="email" class="form-control" required
                                 placeholder="Nhập email">
                         </div>
                         <div class="form-group">
                             <label for="">Mật khẩu <span class="color-crimson">(*)</span></label>
                             <input type="password" id="mat_khau" name="mat_khau" class="form-control" required
                                 placeholder="Nhập mật khẩu">
                         </div>
                     </div>
                     <div class="card-footer">
                         <input type="submit" value="Thêm mới" class="btn btn-success float-right">
                     </div>
                 </form>
                 <?php endif ?>
                 <!-- END ADMIN -->

                 <!-- MANAGER  -->
                 <?php if ($phannhom->phannhom__Get_By_Id($id_phan_nhom)->cap_bac == 1) : ?>
                 <form action="quan-ly-tai-khoan/action.php?req=add_admin" method="post" enctype="multipart/form-data">
                     <input type="hidden" name="id_phan_nhom" value="<?= $id_phan_nhom ?>">
                     <input type="hidden" name="id_phan_quyen"
                         value="<?= $phanquyen->phanquyen__Get_By_Cap_Bac(1)->id_phan_quyen ?>">
                     <div class="card-body">
                         <div class="form-group">
                             <label for="">Email <span class="color-crimson">(*)</span></label>
                             <input type="email" id="email" name="email" class="form-control" required
                                 placeholder="Nhập email">
                         </div>
                         <div class="form-group">
                             <label for="">Mật khẩu <span class="color-crimson">(*)</span></label>
                             <input type="password" id="mat_khau" name="mat_khau" class="form-control" required
                                 placeholder="Nhập mật khẩu">
                         </div>
                     </div>
                     <div class="card-footer">
                         <input type="submit" value="Thêm mới" class="btn btn-success float-right">
                     </div>
                 </form>
                 <?php endif ?>
                 <!-- END MANAGER -->

                 <!-- SINHVIEN -->
                 <?php if ($phannhom->phannhom__Get_By_Id($id_phan_nhom)->cap_bac == 2) : ?>
                 <div class="card-body">
                     <div class="form-group">
                         <label for="">Chọn lớp <span class="color-crimson">(*)</span></label>
                         <select class="form-control" name="id_lop_hoc" required onchange="location.href=this.value">
                             <?php if (isset($_GET['id_lop_hoc'])) : ?>
                             <?php $id_lop_hoc = $_GET['id_lop_hoc']; ?>
                             <option value="<?= $id_lop_hoc ?>">
                                 <?= $lophoc->lophoc__Get_By_Id($id_lop_hoc)->ten_lop_hoc ?>
                             </option>
                             <?php foreach ($lophoc__Get_All as $item) : ?>
                             <?php if ($item->id_lop_hoc != $id_lop_hoc) : ?>
                             <option
                                 value="?page=quan-ly-tai-khoan&id_phan_nhom=<?= $id_phan_nhom ?>&id_lop_hoc=<?= $item->id_lop_hoc ?>">
                                 <?= $item->ten_lop_hoc ?>
                             </option>
                             <?php endif ?>
                             <?php endforeach; ?>
                             <?php else : ?>
                             <option value="">Chọn Lớp</option>
                             <?php foreach ($lophoc__Get_All as $item) : ?>
                             <option
                                 value="?page=quan-ly-tai-khoan&id_phan_nhom=<?= $id_phan_nhom ?>&id_lop_hoc=<?= $item->id_lop_hoc ?>">
                                 <?= $item->ten_lop_hoc ?>
                             </option>
                             <?php endforeach; ?>
                             <?php endif ?>

                         </select>
                     </div>
                 </div>

                 <?php if (isset($_GET['id_lop_hoc'])) : ?>
                 <?php
                                $id_lop_hoc = $_GET['id_lop_hoc'];
                                $sinhvien__Get_All_Not_Exits = $sinhvien->sinhvien__Get_All_Not_Exits($id_lop_hoc);

                                ?>

                 <form action="quan-ly-tai-khoan/action.php?req=add_sv" method="post" enctype="multipart/form-data">
                     <input type="hidden" name="id_phan_nhom" value="<?= $id_phan_nhom ?>">
                     <input type="hidden" name="id_phan_quyen"
                         value="<?= $phanquyen->phanquyen__Get_By_Cap_Bac(2)->id_phan_quyen ?>">
                     <div class="card-body">
                         <div class="form-group">
                             <label for="">Chọn sinh viên <span class="color-crimson">(*)</span></label>
                             <select class="duallistbox" multiple="multiple" name="id_nguoi_dung[]" required>
                                 <?php foreach ($sinhvien__Get_All_Not_Exits as $item) : ?>
                                 <option value="<?= $item->id_sinh_vien ?>">
                                     <?= $item->ten_sinh_vien ?>
                                 </option>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                     </div>
                     <div class="card-footer">
                         <input type="submit" value="Thêm mới" class="btn btn-success float-right">
                     </div>
                 </form>
                 <?php endif ?>
                 <?php endif ?>
                 <!--END SINHVIEN -->

                 <!-- BITHUDOANKHOA  -->
                 <?php if ($phannhom->phannhom__Get_By_Id($id_phan_nhom)->cap_bac == 3) : ?>
                 <div class="card-body">
                     <div class="form-group">
                         <label for="">Chọn khoa <span class="color-crimson">(*)</span></label>
                         <select class="form-control" name="id_khoa" required onchange="location.href=this.value">
                             <?php if (isset($_GET['id_khoa'])) : ?>
                             <?php $id_khoa = $_GET['id_khoa']; ?>
                             <option value="<?= $id_khoa ?>">
                                 <?= $khoa->khoa__Get_By_Id($id_khoa)->ten_khoa ?>
                             </option>
                             <?php foreach ($khoa__Get_All as $item) : ?>
                             <?php if ($item->id_khoa != $id_khoa) : ?>
                             <option
                                 value="?page=quan-ly-tai-khoan&id_phan_nhom=<?= $id_phan_nhom ?>&id_khoa=<?= $item->id_khoa ?>">
                                 <?= $item->ten_khoa ?>
                             </option>
                             <?php endif ?>
                             <?php endforeach; ?>
                             <?php else : ?>
                             <option value="">Chọn khoa</option>
                             <?php foreach ($khoa__Get_All as $item) : ?>
                             <option
                                 value="?page=quan-ly-tai-khoan&id_phan_nhom=<?= $id_phan_nhom ?>&id_khoa=<?= $item->id_khoa ?>">
                                 <?= $item->ten_khoa ?>
                             </option>
                             <?php endforeach; ?>
                             <?php endif ?>

                         </select>
                     </div>
                 </div>

                 <?php if (isset($_GET['id_khoa'])) : ?>
                 <?php
                                $id_khoa = $_GET['id_khoa'];
                                $bithudoankhoa__Get_All_Not_Exits = $bithudoankhoa->bithudoankhoa__Get_All_Not_Exits($id_khoa);

                                ?>

                 <form action="quan-ly-tai-khoan/action.php?req=add_btdk" method="post" enctype="multipart/form-data">
                     <input type="hidden" name="id_phan_nhom" value="<?= $id_phan_nhom ?>">
                     <input type="hidden" name="id_phan_quyen"
                         value="<?= $phanquyen->phanquyen__Get_By_Cap_Bac(2)->id_phan_quyen ?>">
                     <div class="card-body">
                         <div class="form-group">
                             <label for="">Chọn bí thư đoàn khoa <span class="color-crimson">(*)</span></label>
                             <select class="duallistbox" multiple="multiple" name="id_nguoi_dung[]" required>
                                 <?php foreach ($bithudoankhoa__Get_All_Not_Exits as $item) : ?>
                                 <option value="<?= $item->id_bi_thu ?>">
                                     <?= $item->ten_bi_thu ?>
                                 </option>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                     </div>
                     <div class="card-footer">
                         <input type="submit" value="Thêm mới" class="btn btn-success float-right">
                     </div>
                 </form>
                 <?php endif ?>
                 <?php endif ?>
                 <!-- END BITHUDOANKHOA -->

                 <!-- COVANHOCTAP  -->
                 <?php if ($phannhom->phannhom__Get_By_Id($id_phan_nhom)->cap_bac == 4) : ?>
                 <div class="card-body">
                     <div class="form-group">
                         <label for="">Chọn lớp <span class="color-crimson">(*)</span></label>
                         <select class="form-control" name="id_lop_hoc" required onchange="location.href=this.value">
                             <?php if (isset($_GET['id_lop_hoc'])) : ?>
                             <?php $id_lop_hoc = $_GET['id_lop_hoc']; ?>
                             <option value="<?= $id_lop_hoc ?>">
                                 <?= $lophoc->lophoc__Get_By_Id($id_lop_hoc)->ten_lop_hoc ?>
                             </option>
                             <?php foreach ($lophoc__Get_All as $item) : ?>
                             <?php if ($item->id_lop_hoc != $id_lop_hoc) : ?>
                             <option
                                 value="?page=quan-ly-tai-khoan&id_phan_nhom=<?= $id_phan_nhom ?>&id_lop_hoc=<?= $item->id_lop_hoc ?>">
                                 <?= $item->ten_lop_hoc ?>
                             </option>
                             <?php endif ?>
                             <?php endforeach; ?>
                             <?php else : ?>
                             <option value="">Chọn Lớp</option>
                             <?php foreach ($lophoc__Get_All as $item) : ?>
                             <option
                                 value="?page=quan-ly-tai-khoan&id_phan_nhom=<?= $id_phan_nhom ?>&id_lop_hoc=<?= $item->id_lop_hoc ?>">
                                 <?= $item->ten_lop_hoc ?>
                             </option>
                             <?php endforeach; ?>
                             <?php endif ?>

                         </select>
                     </div>
                 </div>

                 <?php if (isset($_GET['id_lop_hoc'])) : ?>
                 <?php
                                $id_lop_hoc = $_GET['id_lop_hoc'];
                                $giangvien__Get_All_Not_Exits = $giangvien->giangvien__Get_All_Not_Exits($id_lop_hoc);

                                ?>

                 <!-- quân sửa: Trỏ form về đúng luồng add_gv và truyền thêm id_lop_hoc -->
                 <form action="quan-ly-tai-khoan/action.php?req=add_gv" method="post" enctype="multipart/form-data">
                     <input type="hidden" name="id_phan_nhom" value="<?= $id_phan_nhom ?>">
                     <input type="hidden" name="id_lop_hoc" value="<?= $id_lop_hoc ?>">
                     <input type="hidden" name="id_phan_quyen"
                         value="<?= $phanquyen->phanquyen__Get_By_Cap_Bac(2)->id_phan_quyen ?>">
                     <div class="card-body">
                         <div class="form-group">
                             <label for="">Chọn giảng viên <span class="color-crimson">(*)</span></label>
                             <select class="duallistbox" multiple="multiple" name="id_nguoi_dung[]" required>
                                 <?php foreach ($giangvien__Get_All_Not_Exits as $item) : ?>
                                 <option value="<?= $item->id_giang_vien ?>">
                                     <?= $item->ten_giang_vien ?>
                                 </option>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                     </div>
                     <div class="card-footer">
                         <input type="submit" value="Thêm mới" class="btn btn-success float-right">
                     </div>
                 </form>
                 <?php endif ?>
                 <?php endif ?>
                 <!-- END COVANHOCTAP -->


                 <?php endif ?>

             </div>
             <!-- /.card -->
         </div>
     </section>

     <section class="content" id="div_update">
     </section>

     <section class="content">
         <div class="card card-primary">
             <div class="card-header">
                 <h3 class="card-title">Danh sách [</h3>
                 <a href="?page=quan-ly-tai-khoan&view=view_all" type="button" class="btn btn-tool">
                     Tất cả <?= count($taikhoan__Get_All_All) ?>
                 </a> |
                 <a href="?page=quan-ly-tai-khoan&view=view_admin" type="button" class="btn btn-tool">
                     Admin <?= count($taikhoan__Get_All_Admin) ?>
                 </a> |
                 <a href="?page=quan-ly-tai-khoan&view=view_manager" type="button" class="btn btn-tool">
                     Manager <?= count($taikhoan__Get_All_Manager) ?>
                 </a> |
                 <a href="?page=quan-ly-tai-khoan&view=view_sv" type="button" class="btn btn-tool">
                     Sinh viên <?= count($taikhoan__Get_All_Sv) ?>
                 </a>|
                 <a href="?page=quan-ly-tai-khoan&view=view_btdk" type="button" class="btn btn-tool">
                     Bí thư đoàn khoa <?= count($taikhoan__Get_All_Btdk) ?>
                 </a>|
                 <a href="?page=quan-ly-tai-khoan&view=view_cvht" type="button" class="btn btn-tool">
                     Cố vấn <?= count($taikhoan__Get_All_Cvht) ?>
                 </a>]
                 <div class="card-tools">
                     <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                         <i class="fas fa-minus"></i>
                     </button>
                 </div>


             </div>
             <!-- /.card-header -->

             <div class="card-body">
                 <div class="row">
                     <div class="col-6">
                         <div class="form-group">
                             <select name="" id="" class="form-control" onchange="location.href=this.value">
                                 <option value="?page=quan-ly-tai-khoan">Xem tất cả</option>
                                 <?php foreach ($lophoc__Get_All as $item) : ?>
                                 <option value="?page=quan-ly-tai-khoan&view_by_lop=<?= $item->id_lop_hoc ?>"
                                     <?= isset($_GET['view_by_lop']) && $_GET['view_by_lop'] == $item->id_lop_hoc ? 'selected' : '' ?>>
                                     <?= $item->ten_lop_hoc ?>
                                 </option>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                     </div>
                     <?php if (isset($_GET['view_by_lop'])) : ?>
                     <div class="col-6">
                         <a href="#" type="button" class="btn  btn-success"
                             onclick="return send_mail_all(<?= $_GET['view_by_lop'] ?>)">
                             <i class="fas fa-paper-plane"></i> Gửi mail tài khoản
                         </a>
                         <progress id="progressBar" value="0" max="100" style="width:300px;display:none"></progress>
                         <p id="loaded_n_total"></p>
                         <?php endif ?>
                     </div>

                 </div>

                 <div class="table-responsive">
                     <table id="tablejs" class="table table-bordered table-striped display responsive" width="100%">
                         <thead>
                         <tr>
                             <th>#</th>
                             <th>Email</th>
                             <th>Họ và tên</th>
                             <th>Mật khẩu</th>
                             <th>Phân nhóm</th>
                             <th>Phân quyền</th>
                             <th>Trạng thái</th>
                             <th>Gửi email</th>
                             <th>Đặt lại mật khẩu (123456)</th>
                             <th>Thao tác</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php $num = 0; ?>
                         <?php foreach ($taikhoan__Get_All as $item) : ?>
                         <tr>
                             <td><?= ++$num ?></td>
                             <td><?= $item->email ?></td>
                             <?php
                                // quân sửa: Lấy họ tên dựa trên cấp bậc phân nhóm
                                $ho_ten = '<span class="text-secondary">N/A</span>';
                                $pn = $phannhom->phannhom__Get_By_Id($item->id_phan_nhom);
                                if ($pn) {
                                    if ($pn->cap_bac == 0) $ho_ten = "Admin";
                                    elseif ($pn->cap_bac == 1) $ho_ten = "Manager";
                                    elseif ($pn->cap_bac == 2) {
                                        $sv = $sinhvien->sinhvien__Get_By_Id($item->id_nguoi_dung);
                                        if ($sv) $ho_ten = htmlspecialchars($sv->ten_sinh_vien);
                                    } elseif ($pn->cap_bac == 3) {
                                        $bt = $bithudoankhoa->bithudoankhoa__Get_By_Id($item->id_nguoi_dung);
                                        if ($bt) $ho_ten = htmlspecialchars($bt->ten_bi_thu);
                                    } elseif ($pn->cap_bac == 4) {
                                        $gv = $giangvien->giangvien__Get_By_Id($item->id_nguoi_dung);
                                        if ($gv) $ho_ten = htmlspecialchars($gv->ten_giang_vien);
                                    }
                                }
                             ?>
                             <td><b><?= $ho_ten ?></b></td>
                             <td><?= $item->mat_khau ?></td>
                             <!-- quân sửa: Bổ sung kiểm tra dữ liệu tồn tại để tránh lỗi báo Attempt to read property on bool -->
                             <td><?= $pn ? $pn->ten_phan_nhom : '<span class="text-danger">Chưa xác định</span>' ?></td>
                             <td><?= ($pq = $phanquyen->phanquyen__Get_By_Id($item->id_phan_quyen)) ? $pq->ten_phan_quyen : '<span class="text-danger">Chưa xác định</span>' ?></td>
                             <td
                                 onclick="return confirm_sweet('quan-ly-tai-khoan/action.php?req=active&id_tai_khoan=<?= $item->id_tai_khoan ?>&trang_thai=<?= $item->trang_thai ?>')">
                                 <?= $item->trang_thai == 1 ? "<button class='btn btn-success'><i class='fas fa-user-check'></i></button>" : "<button class='btn btn-danger'><i class='fas fa-user-slash'></i></button>" ?>

                             </td>
                             <td>
                                 <a href="#" type="button" class="btn  btn-success"
                                     onclick="return send_mail('<?= $item->email ?>', '<?= $item->mat_khau ?>')">
                                     <i class="fas fa-paper-plane"></i>
                                 </a>
                                 <progress id="progressBar" value="0" max="100"
                                     style="width:300px;display:none"></progress>
                                 <p id="loaded_n_total"></p>
                             </td>
                             <td>
                                 <a href="#" type="button" class="btn  btn-danger"
                                     onclick="return confirm_sweet('quan-ly-tai-khoan/action.php?req=reset&id_tai_khoan=<?= $item->id_tai_khoan ?>')">
                                     <i class="fas fa-user-cog"></i>
                                 </a>
                             </td>
                             <td>
                                 <a href="#" type="button" class="btn  btn-danger"
                                     onclick="return confirm_sweet('quan-ly-tai-khoan/action.php?req=delete&id_tai_khoan=<?= $item->id_tai_khoan ?>')">
                                     <i class="fas fa-trash"></i>
                                 </a>
                             </td>

                         </tr>
                         <?php endforeach ?>
                     </tbody>
                 </table>
                 </div>
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
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#tablejs_wrapper .col-md-6:eq(0)');
});

function update_obj(id_tai_khoan) {
    $.post('quan-ly-tai-khoan/update.php', {
        'id_tai_khoan': id_tai_khoan,
    }, function(data) {
        $(".card.card-success").addClass('collapsed-card');
        $('#div_update').html(data);
    });
}

function send_mail(email, password) {

    Swal.fire({
        title: 'Sending Email!',
        html: 'Email will be sent in seconds',
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
    }, function(data) {
        Swal.DismissReason.timer;
        Swal.fire(
            'Good job!',
            'The email is sent!',
            'success'
        )
    });
}

function send_mail_all(id_lop) {

    Swal.fire({
        title: 'Sending Email!',
        html: 'Email will be sent in seconds',
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
    }, function(data) {
        Swal.DismissReason.timer;
        Swal.fire(
            'Good job!',
            'The email is sent!',
            'success'
        )
    });
}
 </script>