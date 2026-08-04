    <?php
    require '../../models/getModel.php';
    $id_giang_vien = $_POST['id_giang_vien'];
    $giangvien__Get_By_Id = $giangvien->giangvien__Get_By_Id($id_giang_vien);
    $trinhdo__Get_All = $trinhdo->trinhdo__Get_All();
    ?>

    <form class="row form" action="quan-ly-giang-vien/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_giang_vien" value="<?= $giangvien__Get_By_Id->id_giang_vien ?>">
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
                                <label for="">Mã giảng viên <span class="color-crimson">(*)</span></label>
                                <input type="text" id="ma_giang_vien" name="ma_giang_vien" class="form-control" required value="<?= $giangvien__Get_By_Id->ma_giang_vien ?>" placeholder="Nhập mã giảng viên">
                            </div>
                            <div class="form-group">
                                <label for="">Tên giảng viên <span class="color-crimson">(*)</span></label>
                                <input type="text" id="ten_giang_vien" name="ten_giang_vien" class="form-control" required value="<?= $giangvien__Get_By_Id->ten_giang_vien ?>" placeholder="Nhập tên giảng viên">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Giới tính <span class="color-crimson">(*)</span></label>
                                <select class="form-control" name="gioi_tinh" required>
                                    <option value="0" <?= $giangvien__Get_By_Id->gioi_tinh == 0 ? "selected" : "" ?>>Nữ
                                    </option>
                                    <option value="1" <?= $giangvien__Get_By_Id->gioi_tinh == 1 ? "selected" : "" ?>>Nam
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Ngày sinh <span class="color-crimson">(*)</span></label>
                                <input type="date" id="ngay_sinh" name="ngay_sinh" class="form-control" required value="<?= $giangvien__Get_By_Id->ngay_sinh ?>" min="<?= date('Y-m-d', strtotime('-100 years')) ?>" max="<?= date('Y-m-d', strtotime('-10 years')) ?>" placeholder="Nhập ngày sinh">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Email <span class="color-crimson">(*)</span></label>
                                <input type="email" id="email" name="email" class="form-control" required value="<?= $giangvien__Get_By_Id->email ?>" placeholder="Nhập email">
                            </div>
                             <div class="form-group">
                                 <label for="">Số điện thoại 1 <span class="color-crimson">(*)</span></label>
                                 <input type="text" id="so_dien_thoai_1" name="so_dien_thoai_1" pattern="0[0-9]{9,10}" class="form-control" required value="<?= $giangvien__Get_By_Id->so_dien_thoai_1 ?>" title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 1" minlength="10" maxlength="11">
                             </div>
                             <div class="form-group">
                                 <label for="">Số điện thoại 2 <span class="color-crimson">(*)</span></label>
                                 <input type="text" id="so_dien_thoai_2" name="so_dien_thoai_2" pattern="0[0-9]{9,10}" class="form-control" required value="<?= $giangvien__Get_By_Id->so_dien_thoai_2 ?>" title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 2" minlength="10" maxlength="11">
                             </div>
                        </div>

                        <div class="col-6">
                            <!-- quân sửa: Tách chuỗi địa chỉ để gán vào 4 ô nhập (Sửa) -->
                             <?php
                                 $dia_chi_ll = $giangvien__Get_By_Id->dia_chi_lien_lac ?? '';
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

                                 $dia_chi_tt = $giangvien__Get_By_Id->dia_chi_thuong_tru ?? '';
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
                                <label for="">Trình độ <span class="color-crimson">(*)</span></label>
                                <select class="form-control" name="id_trinh_do" required>
                                    <option value="<?= $giangvien__Get_By_Id->id_trinh_do ?>">
                                        <?= $trinhdo->trinhdo__Get_By_Id($giangvien__Get_By_Id->id_trinh_do)->ten_trinh_do ?>
                                    </option>
                                    <?php foreach ($trinhdo__Get_All as $item) : ?>
                                        <?php if ($item->id_trinh_do != $giangvien__Get_By_Id->id_trinh_do) : ?>
                                            <option value="<?= $item->id_trinh_do ?>"><?= $item->ten_trinh_do ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
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