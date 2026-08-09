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
                         <li class="breadcrumb-item"><a href="index.php?page=thong-ke">Home</a></li>
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
                         <a href="#" type="button" class="btn  btn-primary"
                             onclick="return send_mail_all(<?= $_GET['view_by_lop'] ?>)">
                             <i class="fas fa-paper-plane"></i> Gửi mail tài khoản
                         </a>
                     </div>
                     <?php endif ?>

                 </div>

                 <div class="table-responsive">
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
                                 onclick="return confirm_sweet('quan-ly-tai-khoan/action.php?req=active&id_tai_khoan=<?= $item->id_tai_khoan ?>&trang_thai=<?= $item->trang_thai ?>')">
                                 <?= $item->trang_thai == 1 ? "<a href='#' class='btn btn-sm btn-success'><i class='ri-checkbox-circle-line'></i></a>" : "<a href='#' class='btn btn-sm btn-danger'><i class='ri-forbid-line'></i></a>" ?>

                             </td>
                             <td class="text-center" style="text-align: center !important;">
                                 <a href="#" type="button" class="btn btn-sm btn-warning"
                                     onclick="return send_mail('<?= $item->email ?>', '<?= $item->mat_khau ?>')">
                                     <i class="ri-mail-send-line"></i>
                                 </a>
                             </td>
                             <td class="text-center" style="text-align: center !important;">
                                 <a href="#" type="button" class="btn btn-sm btn-info"
                                     onclick="return confirm_sweet('quan-ly-tai-khoan/action.php?req=reset&id_tai_khoan=<?= $item->id_tai_khoan ?>')">
                                     <i class="ri-key-2-line"></i>
                                 </a>
                             </td>
                             <td class="text-center" style="text-align: center !important;">
                                 <a href="#" type="button" class="btn btn-sm btn-danger"
                                     onclick="return confirm_sweet('quan-ly-tai-khoan/action.php?req=delete&id_tai_khoan=<?= $item->id_tai_khoan ?>')">
                                     <i class="ri-delete-bin-line"></i>
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
        "dom": "<'row'<'col-sm-12'l>><'row'<'col-sm-12'B>><'row'<'col-sm-12'f>>rtip",
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
        }]
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