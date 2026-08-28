    <?php 
        require '../../models/getModel.php';
        $id_sinh_vien = $_POST['id_sinh_vien'];
        $sinhvien__Get_By_Id = $sinhvien->sinhvien__Get_By_Id($id_sinh_vien);
        $lophoc__Get_All = $lophoc->lophoc__Get_All();
        
        $sinhvien_old_input = isset($_SESSION['sinhvien_old_input']) && is_array($_SESSION['sinhvien_old_input']) ? $_SESSION['sinhvien_old_input'] : array();
        if (isset($sinhvien_old_input['context']) && $sinhvien_old_input['context'] === 'update' && (int)$sinhvien_old_input['id_sinh_vien'] === (int)$id_sinh_vien) {
            unset($_SESSION['sinhvien_old_input']);
        }
        $status = isset($_POST['error_status']) ? $_POST['error_status'] : '';
        $is_update_error = !empty($status);
        
    function sinhvien_old_value_update($key, $default = '') {
        global $sinhvien_old_input;
        if (isset($sinhvien_old_input['context']) && $sinhvien_old_input['context'] === 'update' && isset($sinhvien_old_input[$key])) {
            return $sinhvien_old_input[$key];
        }
        return $default;
    }

    $dia_chi_ll = $sinhvien__Get_By_Id->dia_chi_lien_lac ?? '';
    $dc_ll = preg_split('/,\s*/', $dia_chi_ll);
    $count_ll = count($dc_ll);
    if ($count_ll >= 3) {
        $dc_ll_tinh = array_pop($dc_ll);
        $dc_ll_xa = array_pop($dc_ll);
        $dc_ll_so_nha = implode(', ', $dc_ll);
    } else {
        $dc_ll_so_nha = $dia_chi_ll;
        $dc_ll_xa = '';
        $dc_ll_tinh = '';
    }

    $dia_chi_tt = $sinhvien__Get_By_Id->dia_chi_thuong_tru ?? '';
    $dc_tt = preg_split('/,\s*/', $dia_chi_tt);
    $count_tt = count($dc_tt);
    if ($count_tt >= 3) {
        $dc_tt_tinh = array_pop($dc_tt);
        $dc_tt_xa = array_pop($dc_tt);
        $dc_tt_so_nha = implode(', ', $dc_tt);
    } else {
        $dc_tt_so_nha = $dia_chi_tt;
        $dc_tt_xa = '';
        $dc_tt_tinh = '';
    }
    ?>

    <form class="row form" action="quan-ly-sinh-vien/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_sinh_vien" value="<?=$sinhvien__Get_By_Id->id_sinh_vien?>">
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Cập nhật Sinh viên</h3>
                </div>
                <div class="card-body">
                         <div class="row">
                             <div class="col-6">
<div class="form-group">
                                 <label class="label-sidebar">Mã sinh viên <span class="color-crimson">*</span></label>
                                 <?php $val_ma = $is_update_error && isset($sinhvien_old_input['ma_sinh_vien']) ? $sinhvien_old_input['ma_sinh_vien'] : $sinhvien__Get_By_Id->ma_sinh_vien; ?>
                                 <input type="text" id="ma_sinh_vien" name="ma_sinh_vien" class="form-control <?= ($is_update_error && $status == 'duplicate-ma-sinh-vien') ? 'is-invalid' : '' ?>" required
                                     value="<?=htmlspecialchars($val_ma)?>" placeholder="Nhập mã sinh viên">
                                 <?php if ($is_update_error && $status == 'duplicate-ma-sinh-vien'): ?>
                                     <small class="text-danger mt-1">Mã sinh viên đã tồn tại trong hệ thống.</small>
                                 <?php endif; ?>
                             </div>
                             </div>
                             <div class="col-6">
<div class="form-group">
                                <label class="label-sidebar">Tên sinh viên <span class="color-crimson">*</span></label>
                                <input type="text" id="ten_sinh_vien" name="ten_sinh_vien" class="form-control" required
                                    value="<?=$sinhvien__Get_By_Id->ten_sinh_vien?>" placeholder="Nhập tên sinh viên">
                            </div>
                             </div>
                         </div>
                                        <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                 <label class="label-sidebar">Giới tính <span class="color-crimson">*</span></label>
                                 <select class="form-control" name="gioi_tinh" required>
                                     <!-- quân sửa: Sửa lại lỗi hiển thị ngược giới tính (1 là Nam, 0 là Nữ) -->
                                     <option value="0" <?=$sinhvien__Get_By_Id->gioi_tinh == 0 ? "selected" : ""?>>Nữ
                                     </option>
                                     <option value="1" <?=$sinhvien__Get_By_Id->gioi_tinh == 1 ? "selected" : ""?>>Nam
                                     </option>
                                 </select>
                             </div>
                             </div>
                             <div class="col-6">
                                 <div class="form-group">
                                 <label class="label-sidebar">Ngày sinh <span class="color-crimson">*</span></label>
                                 <input type="date" id="ngay_sinh" name="ngay_sinh" class="form-control" required
                                     value="<?=$sinhvien__Get_By_Id->ngay_sinh?>"
                                     min="<?=date('Y-m-d', strtotime('-100 years'))?>"
                                     max="<?=date('Y-m-d', strtotime('-10 years'))?>" placeholder="Nhập ngày sinh">
                             </div>
                             </div>
                         </div>
                         

                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group">
                                     <label class="label-sidebar">Email <span class="color-crimson">*</span></label>
                                     <?php $val_email = $is_update_error && isset($sinhvien_old_input['email']) ? $sinhvien_old_input['email'] : $sinhvien__Get_By_Id->email; ?>
                                     <input type="email" id="email" name="email" class="form-control <?= ($is_update_error && $status == 'duplicate-email-sinh-vien') ? 'is-invalid' : '' ?>" required readonly
                                         value="<?=htmlspecialchars($val_email)?>" placeholder="Nhập email">
                                     <?php if ($is_update_error && $status == 'duplicate-email-sinh-vien'): ?>
                                         <small class="text-danger mt-1">Email đã tồn tại trong hệ thống.</small>
                                     <?php endif; ?>
                                 </div>
                             </div>
                             <div class="col-6">
<div class="form-group">
                                <label class="label-sidebar">Lớp học <span class="color-crimson">*</span></label>
                                <select class="form-control" name="id_lop_hoc" required>
                                    <option value="<?=$sinhvien__Get_By_Id->id_lop_hoc?>">
                                        <?=$lophoc->lophoc__Get_By_Id($sinhvien__Get_By_Id->id_lop_hoc)->ten_lop_hoc?>
                                    </option>
                                    <?php foreach($lophoc__Get_All as $item):?>
                                    <?php if($item->id_lop_hoc != $sinhvien__Get_By_Id->id_lop_hoc):?>
                                    <option value="<?=$item->id_lop_hoc?>"><?=$item->ten_lop_hoc?></option>
                                    <?php endif; ?>
                                    <?php endforeach;?>
                                </select>
                            </div>
                             </div>
                         </div>
                         

                         <div class="row">
                             <div class="col-6">
<div class="form-group">
                                <label class="label-sidebar">Số điện thoại 1 <span class="color-crimson">*</span></label>
                                <input type="text" id="so_dien_thoai_1" name="so_dien_thoai_1"
                                    pattern="0[0-9]{9,10}" class="form-control" required
                                    value="<?=$sinhvien__Get_By_Id->so_dien_thoai_1?>"
                                    title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 1"
                                    minlength="10" maxlength="11">
                            </div>
                             </div>
                             <div class="col-6">
<div class="form-group">
                                <label class="label-sidebar">Số điện thoại 2 <span class="color-crimson">*</span></label>
                                <input type="text" id="so_dien_thoai_2" name="so_dien_thoai_2"
                                    pattern="0[0-9]{9,10}" class="form-control" required
                                    value="<?=$sinhvien__Get_By_Id->so_dien_thoai_2?>"
                                    title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 2"
                                    minlength="10" maxlength="11">
                            </div>
                             </div>
                         </div>
                         
                         <div class="row">
                             <div class="col-6">
<div class="form-group">
                                <label class="label-sidebar">Địa chỉ liên lạc <span class="color-crimson">*</span></label>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <select name="dc_ll_tinh" id="update_dc_ll_tinh" class="form-control" required>
                                            <option value="">Chọn Tỉnh / Thành phố</option>
                                        </select>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <select name="dc_ll_xa" id="update_dc_ll_xa" class="form-control" required>
                                            <option value="">Chọn Phường / Xã</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <input type="text" name="dc_ll_chitiet" class="form-control" required value="<?= htmlspecialchars(sinhvien_old_value_update('dc_ll_chitiet', $dc_ll_so_nha)) ?>" placeholder="Số nhà, đường, khu phố">
                                    </div>
                                </div>
                            </div>
                             </div>
                             <div class="col-6">
<div class="form-group">
                                <label class="label-sidebar">Địa chỉ thường trú <span class="color-crimson">*</span></label>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <select name="dc_tt_tinh" id="update_dc_tt_tinh" class="form-control" required>
                                            <option value="">Chọn Tỉnh / Thành phố</option>
                                        </select>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <select name="dc_tt_xa" id="update_dc_tt_xa" class="form-control" required>
                                            <option value="">Chọn Phường / Xã</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <input type="text" name="dc_tt_chitiet" class="form-control" required value="<?= htmlspecialchars(sinhvien_old_value_update('dc_tt_chitiet', $dc_tt_so_nha)) ?>" placeholder="Số nhà, đường, khu phố">
                                    </div>
                                </div>
                            </div>
                             </div>
                         </div>
                         
                         <div class="row">
                             <div class="col-6">
<div class="form-group">
                                <label class="label-sidebar">Chức vụ <span class="color-crimson">*</span></label>
                                <select class="form-control" name="chuc_vu" required>
                                    <option value="">Chọn Chức vụ</option>
                                    <option value="0" <?=$sinhvien__Get_By_Id->chuc_vu == 0 ? "selected" : ""?>>Không có
                                    </option>
                                    <option value="1" <?=$sinhvien__Get_By_Id->chuc_vu == 1 ? "selected" : ""?>>Lớp
                                        trưởng</option>
                                    <option value="2" <?=$sinhvien__Get_By_Id->chuc_vu == 2 ? "selected" : ""?>>Bí thư
                                    </option>
                                </select>
                            </div>
                             </div>
                         </div>
                     </div>
                     <!-- /.card-body -->
                 <div class="card-footer py-2">
                     <input type="submit" value="Cập nhật" class="btn btn-danger float-right font-weight-bold">
                     <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" onclick="cancel_update()">Hủy</button>
                 </div>
            </div>
            <!-- /.card -->
        </div>
    </form>
    <script>
    $(document).ready(function() {
        var updateLL = {
            tinh: "<?= htmlspecialchars(sinhvien_old_value_update('dc_ll_tinh', $dc_ll_tinh)) ?>",
            xa: "<?= htmlspecialchars(sinhvien_old_value_update('dc_ll_xa', $dc_ll_xa)) ?>"
        };
        loadVNProvinces('update_dc_ll', updateLL.tinh ? updateLL : null);
        
        var updateTT = {
            tinh: "<?= htmlspecialchars(sinhvien_old_value_update('dc_tt_tinh', $dc_tt_tinh)) ?>",
            xa: "<?= htmlspecialchars(sinhvien_old_value_update('dc_tt_xa', $dc_tt_xa)) ?>"
        };
        loadVNProvinces('update_dc_tt', updateTT.tinh ? updateTT : null);
    });
    </script>