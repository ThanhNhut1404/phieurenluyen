<?php
    session_start();
    // Nhựt sửa lỗi: chặn gọi trực tiếp form sửa khi chưa đăng nhập admin.
    if (!isset($_SESSION['admin'])) {
        http_response_code(403);
        exit('Forbidden');
    }

    require '../../models/getModel.php';

    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    $hocky_old_input = isset($_SESSION['hocky_old_input']) && is_array($_SESSION['hocky_old_input']) ? $_SESSION['hocky_old_input'] : array();

    if (!function_exists('hocky_update_escape')) {
        function hocky_update_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('hocky_update_old_value')) {
        function hocky_update_old_value($field, $id_hoc_ky, $default = '') {
            global $hocky_old_input;
            if (isset($hocky_old_input) && is_array($hocky_old_input)) {
                $old = $hocky_old_input;
                if (isset($old['context'], $old['id_hoc_ky']) && $old['context'] === 'update' && (int)$old['id_hoc_ky'] === (int)$id_hoc_ky && isset($old[$field])) {
                    return $old[$field];
                }
            }
            return $default;
        }
    }

    $id_hoc_ky = filter_input(INPUT_POST, 'id_hoc_ky', FILTER_VALIDATE_INT);
    if (!$id_hoc_ky || $id_hoc_ky < 1) {
        http_response_code(400);
        exit('<div class="alert alert-danger">ID học kỳ không hợp lệ.</div>');
    }

    $hocky__Get_By_Id = $hocky->hocky__Get_By_Id($id_hoc_ky);
    if (!$hocky__Get_By_Id) {
        http_response_code(404);
        exit('<div class="alert alert-danger">Không tìm thấy học kỳ cần cập nhật.</div>');
    }

    if (isset($hocky_old_input['context'], $hocky_old_input['id_hoc_ky']) && $hocky_old_input['context'] === 'update' && (int)$hocky_old_input['id_hoc_ky'] === (int)$hocky__Get_By_Id->id_hoc_ky) {
        unset($_SESSION['hocky_old_input']);
    }

    if (!function_exists('hocky_format_namhoc_combobox')) {
        function hocky_format_namhoc_combobox($item) {
            $bd = DateTime::createFromFormat('Y-m-d', (string)$item->ngay_bat_dau);
            $kt = DateTime::createFromFormat('Y-m-d', (string)$item->ngay_ket_thuc);
            if ($bd && $kt) {
                return $item->ten_nam_hoc . ' (' . $bd->format('j/n/y') . ' - ' . $kt->format('j/n/y') . ')';
            }
            return $item->ten_nam_hoc;
        }
    }

    $namhoc__Get_All = $namhoc->namhoc__Get_All();
    
    $status = isset($_POST['error_status']) ? $_POST['error_status'] : '';
    $is_update_error = !empty($status);
?>

<form class="row form" action="quan-ly-hoc-ky/action.php?req=update" method="post">
    <input type="hidden" name="id_hoc_ky" value="<?=(int)$hocky__Get_By_Id->id_hoc_ky?>">
    <input type="hidden" name="csrf_token" value="<?=hocky_update_escape($_SESSION['csrf_token'])?>">
    <div class="col-12">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Cập nhật Học kỳ</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="label-sidebar" for="">Năm học <span class="color-crimson">*</span></label>
                        <select class="form-control" name="id_nam_hoc" required>
                            <option value="">Chọn năm học</option>
                            <?php foreach ($namhoc__Get_All as $item):?>
                            <option value="<?=(int)$item->id_nam_hoc?>" <?=(int)hocky_update_old_value('id_nam_hoc', $hocky__Get_By_Id->id_hoc_ky, $hocky__Get_By_Id->id_nam_hoc) === (int)$item->id_nam_hoc ? 'selected' : ''?>><?=hocky_update_escape(hocky_format_namhoc_combobox($item))?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="label-sidebar" for="ten_hoc_ky_update">Tên học kỳ <span class="color-crimson">*</span></label>
                        <input type="text" id="ten_hoc_ky_update" name="ten_hoc_ky" class="form-control <?= ($is_update_error && in_array($status, ['duplicate', 'invalid-ten-hoc-ky'])) ? 'is-invalid' : '' ?>" required maxlength="50"
                            placeholder="Nhập tên học kỳ" value="<?=hocky_update_escape(hocky_update_old_value('ten_hoc_ky', $hocky__Get_By_Id->id_hoc_ky, $hocky__Get_By_Id->ten_hoc_ky))?>">
                        <?php if ($is_update_error): ?>
                            <?php if ($status == 'duplicate'): ?>
                                <small class="text-danger mt-1">Học kỳ này đã tồn tại trong năm học được chọn.</small>
                            <?php elseif ($status == 'invalid-ten-hoc-ky'): ?>
                                <small class="text-danger mt-1">Tên học kỳ không được để trống và tối đa 50 ký tự.</small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="label-sidebar" for="ngay_bat_dau_update">Ngày bắt đầu <span class="color-crimson">*</span></label>
                        <input type="date" id="ngay_bat_dau_update" name="ngay_bat_dau" class="form-control <?= ($is_update_error && $status == 'invalid-ngay') ? 'is-invalid' : '' ?>" required
                            value="<?=hocky_update_escape(hocky_update_old_value('ngay_bat_dau', $hocky__Get_By_Id->id_hoc_ky, $hocky__Get_By_Id->ngay_bat_dau))?>">
                        <?php if ($is_update_error && $status == 'invalid-ngay'): ?>
                            <small class="text-danger mt-1">Ngày bắt đầu phải nhỏ hơn ngày kết thúc.</small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="label-sidebar" for="ngay_ket_thuc_update">Ngày kết thúc <span class="color-crimson">*</span></label>
                        <input type="date" id="ngay_ket_thuc_update" name="ngay_ket_thuc" class="form-control <?= ($is_update_error && $status == 'invalid-ngay') ? 'is-invalid' : '' ?>" required
                            value="<?=hocky_update_escape(hocky_update_old_value('ngay_ket_thuc', $hocky__Get_By_Id->id_hoc_ky, $hocky__Get_By_Id->ngay_ket_thuc))?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="label-sidebar" for="ghi_chu_update">Ghi chú</label>
                    <textarea id="ghi_chu_update" name="ghi_chu" class="form-control <?= ($is_update_error && $status == 'invalid-ghichu') ? 'is-invalid' : '' ?>" maxlength="2000"
                        placeholder="Nhập ghi chú"><?=hocky_update_escape(hocky_update_old_value('ghi_chu', $hocky__Get_By_Id->id_hoc_ky, $hocky__Get_By_Id->ghi_chu))?></textarea>
                    <?php if ($is_update_error && $status == 'invalid-ghichu'): ?>
                        <small class="text-danger mt-1">Ghi chú không được vượt quá 2000 ký tự.</small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer py-2">
                <input type="submit" value="Cập nhật" class="btn btn-danger float-right font-weight-bold">
                <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" onclick="cancel_update()">Hủy</button>
            </div>
        </div>
    </div>
</form>
