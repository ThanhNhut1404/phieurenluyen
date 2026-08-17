    <?php
    require '../../models/getModel.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $id_bi_thu = filter_input(INPUT_POST, 'id_bi_thu', FILTER_VALIDATE_INT);
    if (!$id_bi_thu || $id_bi_thu < 1) {
        http_response_code(400);
        exit('<div class="alert alert-danger">ID bí thư không hợp lệ.</div>');
    }

    $bithudoankhoa__Get_By_Id = $bithudoankhoa->bithudoankhoa__Get_By_Id($id_bi_thu);
    if (!$bithudoankhoa__Get_By_Id) {
        http_response_code(404);
        exit('<div class="alert alert-danger">Không tìm thấy bí thư cần cập nhật.</div>');
    }

    $khoa__Get_All = $khoa->khoa__Get_All();
    
    $bithu_old_input = isset($_SESSION['bithu_old_input']) && is_array($_SESSION['bithu_old_input']) ? $_SESSION['bithu_old_input'] : array();

    if (!function_exists('bithu_update_escape')) {
        function bithu_update_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('bithu_old_value_update')) {
        function bithu_old_value_update($key, $id_bi_thu, $default = '') {
            global $bithu_old_input;
            if (isset($bithu_old_input['context'], $bithu_old_input['id_bi_thu']) && $bithu_old_input['context'] === 'update' && (int)$bithu_old_input['id_bi_thu'] === (int)$id_bi_thu && isset($bithu_old_input[$key])) {
                return $bithu_old_input[$key];
            }
            return $default;
        }
    }

    if (isset($bithu_old_input['context'], $bithu_old_input['id_bi_thu']) && $bithu_old_input['context'] === 'update' && (int)$bithu_old_input['id_bi_thu'] === (int)$bithudoankhoa__Get_By_Id->id_bi_thu) {
        unset($_SESSION['bithu_old_input']);
    }

    $status = isset($_POST['error_status']) ? $_POST['error_status'] : '';
    $is_update_error = !empty($status);
    ?>

    <form class="row form" action="quan-ly-bi-thu-doan-khoa/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_bi_thu" value="<?= (int)$bithudoankhoa__Get_By_Id->id_bi_thu ?>">
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Cập nhật Bí thư Đoàn khoa</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-sidebar" for="">Tên bí thư <span class="color-crimson">*</span></label>
                                <input type="text" id="ten_bi_thu" name="ten_bi_thu" class="form-control" required value="<?= bithu_update_escape(bithu_old_value_update('ten_bi_thu', $bithudoankhoa__Get_By_Id->id_bi_thu, $bithudoankhoa__Get_By_Id->ten_bi_thu)) ?>" placeholder="Nhập tên bí thư">
                            </div>
                            <div class="form-group">
                                <label class="label-sidebar" for="">Ngày sinh <span class="color-crimson">*</span></label>
                                <input type="date" id="ngay_sinh" name="ngay_sinh" class="form-control <?= ($is_update_error && $status == 'invalid-ngay') ? 'is-invalid' : '' ?>" required value="<?= bithu_update_escape(bithu_old_value_update('ngay_sinh', $bithudoankhoa__Get_By_Id->id_bi_thu, $bithudoankhoa__Get_By_Id->ngay_sinh)) ?>" min="<?= date('Y-m-d', strtotime('-100 years')) ?>" max="<?= date('Y-m-d', strtotime('-10 years')) ?>" placeholder="Nhập ngày sinh">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-sidebar" for="">Giới tính <span class="color-crimson">*</span></label>
                                <select class="form-control" name="gioi_tinh" required>
                                    <option value="0" <?= bithu_old_value_update('gioi_tinh', $bithudoankhoa__Get_By_Id->id_bi_thu, $bithudoankhoa__Get_By_Id->gioi_tinh) == '0' ? "selected" : "" ?>>
                                        Nữ
                                    </option>
                                    <option value="1" <?= bithu_old_value_update('gioi_tinh', $bithudoankhoa__Get_By_Id->id_bi_thu, $bithudoankhoa__Get_By_Id->gioi_tinh) == '1' ? "selected" : "" ?>>
                                        Nam
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="label-sidebar" for="">Email <span class="color-crimson">*</span></label>
                                <input type="email" id="email" name="email" class="form-control <?= ($is_update_error && $status == 'duplicate-bithu') ? 'is-invalid' : '' ?>" required value="<?= bithu_update_escape(bithu_old_value_update('email', $bithudoankhoa__Get_By_Id->id_bi_thu, $bithudoankhoa__Get_By_Id->email)) ?>" placeholder="Nhập email">
                                <?php if ($is_update_error && $status == 'duplicate-bithu'): ?>
                                    <small class="text-danger mt-1">Email bí thư đoàn khoa đã tồn tại.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-6">

                            <!-- quân sửa: Tách chuỗi địa chỉ để gán vào 4 ô nhập (Sửa) -->
                             <?php
                                $dia_chi_ll = $bithudoankhoa__Get_By_Id->dia_chi_lien_lac ?? '';
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

                                $dia_chi_tt = $bithudoankhoa__Get_By_Id->dia_chi_thuong_tru ?? '';
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
                                <label class="label-sidebar" for="">Địa chỉ liên lạc <span class="color-crimson">*</span></label>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_ll_so_nha" class="form-control" required value="<?= bithu_update_escape(bithu_old_value_update('dc_ll_so_nha', $bithudoankhoa__Get_By_Id->id_bi_thu, $dc_ll_so_nha)) ?>" placeholder="Số nhà, đường">
                                    </div>
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_ll_ap" class="form-control" required value="<?= bithu_update_escape(bithu_old_value_update('dc_ll_ap', $bithudoankhoa__Get_By_Id->id_bi_thu, $dc_ll_ap)) ?>" placeholder="Ấp / Khu phố">
                                    </div>
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_ll_xa" class="form-control" required value="<?= bithu_update_escape(bithu_old_value_update('dc_ll_xa', $bithudoankhoa__Get_By_Id->id_bi_thu, $dc_ll_xa)) ?>" placeholder="Xã / Phường">
                                    </div>
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_ll_tinh" class="form-control" required value="<?= bithu_update_escape(bithu_old_value_update('dc_ll_tinh', $bithudoankhoa__Get_By_Id->id_bi_thu, $dc_ll_tinh)) ?>" placeholder="Tỉnh / Thành phố">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="label-sidebar" for="">Số điện thoại 1 <span class="color-crimson">*</span></label>
                                <input type="text" id="so_dien_thoai_1" name="so_dien_thoai_1" pattern="0[0-9]{9,10}" class="form-control <?= ($is_update_error && $status == 'invalid-sdt') ? 'is-invalid' : '' ?>" required value="<?= bithu_update_escape(bithu_old_value_update('so_dien_thoai_1', $bithudoankhoa__Get_By_Id->id_bi_thu, $bithudoankhoa__Get_By_Id->so_dien_thoai_1)) ?>" title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 1" minlength="10" maxlength="11">
                            </div>
                            <div class="form-group">
                                <label class="label-sidebar" for="">Chọn Khoa <span class="color-crimson">*</span></label>
                                <select class="form-control <?= ($is_update_error && $status == 'invalid-khoa') ? 'is-invalid' : '' ?>" name="id_khoa" required>
                                    <?php foreach ($khoa__Get_All as $item) : ?>
                                        <option value="<?= $item->id_khoa ?>" <?= bithu_old_value_update('id_khoa', $bithudoankhoa__Get_By_Id->id_bi_thu, $bithudoankhoa__Get_By_Id->id_khoa) == $item->id_khoa ? "selected" : "" ?>><?= $item->ten_khoa ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-sidebar" for="">Địa chỉ thường trú <span class="color-crimson">*</span></label>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_tt_so_nha" class="form-control" required value="<?= bithu_update_escape(bithu_old_value_update('dc_tt_so_nha', $bithudoankhoa__Get_By_Id->id_bi_thu, $dc_tt_so_nha)) ?>" placeholder="Số nhà, đường">
                                    </div>
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_tt_ap" class="form-control" required value="<?= bithu_update_escape(bithu_old_value_update('dc_tt_ap', $bithudoankhoa__Get_By_Id->id_bi_thu, $dc_tt_ap)) ?>" placeholder="Ấp / Khu phố">
                                    </div>
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_tt_xa" class="form-control" required value="<?= bithu_update_escape(bithu_old_value_update('dc_tt_xa', $bithudoankhoa__Get_By_Id->id_bi_thu, $dc_tt_xa)) ?>" placeholder="Xã / Phường">
                                    </div>
                                    <div class="col-6 mb-2">
                                        <input type="text" name="dc_tt_tinh" class="form-control" required value="<?= bithu_update_escape(bithu_old_value_update('dc_tt_tinh', $bithudoankhoa__Get_By_Id->id_bi_thu, $dc_tt_tinh)) ?>" placeholder="Tỉnh / Thành phố">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="label-sidebar" for="">Số điện thoại 2 <span class="color-crimson">*</span></label>
                                <input type="text" id="so_dien_thoai_2" name="so_dien_thoai_2" pattern="0[0-9]{9,10}" class="form-control <?= ($is_update_error && $status == 'invalid-sdt') ? 'is-invalid' : '' ?>" required value="<?= bithu_update_escape(bithu_old_value_update('so_dien_thoai_2', $bithudoankhoa__Get_By_Id->id_bi_thu, $bithudoankhoa__Get_By_Id->so_dien_thoai_2)) ?>" title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số" placeholder="Nhập số điện thoại 2" minlength="10" maxlength="11">
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