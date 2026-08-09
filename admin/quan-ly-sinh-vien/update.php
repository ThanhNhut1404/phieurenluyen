    <?php 
        require '../../models/getModel.php';
        $id_sinh_vien = $_POST['id_sinh_vien'];
        $sinhvien__Get_By_Id = $sinhvien->sinhvien__Get_By_Id($id_sinh_vien);
        $lophoc__Get_All = $lophoc->lophoc__Get_All();
    ?>

    <form class="row form" action="quan-ly-sinh-vien/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_sinh_vien" value="<?=$sinhvien__Get_By_Id->id_sinh_vien?>">
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Cập nhật</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Mã sinh viên <span class="color-crimson">(*)</span></label>
                                <input type="text" id="ma_sinh_vien" name="ma_sinh_vien" class="form-control" required
                                    value="<?=$sinhvien__Get_By_Id->ma_sinh_vien?>" placeholder="Nhập mã sinh viên">
                            </div>
                            <div class="form-group">
                                <label for="">Tên sinh viên <span class="color-crimson">(*)</span></label>
                                <input type="text" id="ten_sinh_vien" name="ten_sinh_vien" class="form-control" required
                                    value="<?=$sinhvien__Get_By_Id->ten_sinh_vien?>" placeholder="Nhập tên sinh viên">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Giới tính <span class="color-crimson">(*)</span></label>
                                <select class="form-control" name="gioi_tinh" required>
                                    <!-- quân sửa: Sửa lại lỗi hiển thị ngược giới tính (1 là Nam, 0 là Nữ) -->
                                    <option value="0" <?=$sinhvien__Get_By_Id->gioi_tinh == 0 ? "selected" : ""?>>Nữ
                                    </option>
                                    <option value="1" <?=$sinhvien__Get_By_Id->gioi_tinh == 1 ? "selected" : ""?>>Nam
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Ngày sinh <span class="color-crimson">(*)</span></label>
                                <input type="date" id="ngay_sinh" name="ngay_sinh" class="form-control" required
                                    value="<?=$sinhvien__Get_By_Id->ngay_sinh?>"
                                    min="<?=date('Y-m-d', strtotime('-100 years'))?>"
                                    max="<?=date('Y-m-d', strtotime('-10 years'))?>" placeholder="Nhập ngày sinh">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Email <span class="color-crimson">(*)</span></label>
                                <input type="email" id="email" name="email" class="form-control" required
                                    value="<?=$sinhvien__Get_By_Id->email?>" placeholder="Nhập email">
                            </div>
                            <div class="form-group">
                                <label for="">Số điện thoại 1 <span class="color-crimson">(*)</span></label>
                                <input type="text" id="so_dien_thoai_1" name="so_dien_thoai_1"
                                    pattern="0[0-9]{9,10}" class="form-control" required
                                    value="<?=$sinhvien__Get_By_Id->so_dien_thoai_1?>"
                                    title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 1"
                                    minlength="10" maxlength="11">
                            </div>
                            <div class="form-group">
                                <label for="">Số điện thoại 2 <span class="color-crimson">(*)</span></label>
                                <input type="text" id="so_dien_thoai_2" name="so_dien_thoai_2"
                                    pattern="0[0-9]{9,10}" class="form-control" required
                                    value="<?=$sinhvien__Get_By_Id->so_dien_thoai_2?>"
                                    title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 2"
                                    minlength="10" maxlength="11">
                            </div>
                        </div>

                        <div class="col-6">
                            <!-- quân sửa: Tách chuỗi địa chỉ để gán vào 4 ô nhập -->
                             <?php
                                $dia_chi_ll = $sinhvien__Get_By_Id->dia_chi_lien_lac ?? '';
                                $arr_dc_ll = preg_split('/,\s*/', $dia_chi_ll);
                                if (count($arr_dc_ll) < 4) {
                                    $dc_ll_so_nha = $dia_chi_ll;
                                    $dc_ll_ap = '';
                                    $dc_ll_xa = '';
                                    $dc_ll_tinh = '';
                                } else {
                                    $dc_ll_so_nha = $arr_dc_ll[0] ?? '';
                                    $dc_ll_ap = $arr_dc_ll[1] ?? '';
                                    $dc_ll_xa = $arr_dc_ll[2] ?? '';
                                    $dc_ll_tinh = $arr_dc_ll[3] ?? '';
                                }

                                $dia_chi_tt = $sinhvien__Get_By_Id->dia_chi_thuong_tru ?? '';
                                $arr_dc_tt = preg_split('/,\s*/', $dia_chi_tt);
                                if (count($arr_dc_tt) < 4) {
                                    $dc_tt_so_nha = $dia_chi_tt;
                                    $dc_tt_ap = '';
                                    $dc_tt_xa = '';
                                    $dc_tt_tinh = '';
                                } else {
                                    $dc_tt_so_nha = $arr_dc_tt[0] ?? '';
                                    $dc_tt_ap = $arr_dc_tt[1] ?? '';
                                    $dc_tt_xa = $arr_dc_tt[2] ?? '';
                                    $dc_tt_tinh = $arr_dc_tt[3] ?? '';
                                }
                            ?>
                            <div class="form-group">
                                <label for="">Địa chỉ liên lạc <span class="color-crimson">(*)</span></label>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_ll_so_nha" class="form-control" required value="<?=$dc_ll_so_nha?>" placeholder="Số nhà, đường">
                                    </div>
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_ll_ap" class="form-control" required value="<?=$dc_ll_ap?>" placeholder="Ấp / Khu phố">
                                    </div>
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_ll_xa" class="form-control" required value="<?=$dc_ll_xa?>" placeholder="Xã / Phường">
                                    </div>
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_ll_tinh" class="form-control" required value="<?=$dc_ll_tinh?>" placeholder="Tỉnh / Thành phố">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Địa chỉ thường trú <span class="color-crimson">(*)</span></label>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_tt_so_nha" class="form-control" required value="<?=$dc_tt_so_nha?>" placeholder="Số nhà, đường">
                                    </div>
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_tt_ap" class="form-control" required value="<?=$dc_tt_ap?>" placeholder="Ấp / Khu phố">
                                    </div>
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_tt_xa" class="form-control" required value="<?=$dc_tt_xa?>" placeholder="Xã / Phường">
                                    </div>
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_tt_tinh" class="form-control" required value="<?=$dc_tt_tinh?>" placeholder="Tỉnh / Thành phố">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Chức vụ <span class="color-crimson">(*)</span></label>
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
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Lớp học <span class="color-crimson">(*)</span></label>
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
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <input type="submit" value="Cập nhật" class="btn btn-danger float-right">
                    <button type="button" class="btn btn-default float-right mr-2" onclick="cancel_update()">Hủy</button>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </form>