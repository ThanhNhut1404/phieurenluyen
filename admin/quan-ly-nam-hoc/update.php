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

    $namhoc_old_input = isset($_SESSION['namhoc_old_input']) && is_array($_SESSION['namhoc_old_input']) ? $_SESSION['namhoc_old_input'] : array();

    if (!function_exists('namhoc_update_escape')) {
        function namhoc_update_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('namhoc_update_old_value')) {
        function namhoc_update_old_value($field, $id_nam_hoc, $default = '') {
            global $namhoc_old_input;
            if (isset($namhoc_old_input) && is_array($namhoc_old_input)) {
                $old = $namhoc_old_input;
                if (isset($old['context'], $old['id_nam_hoc']) && $old['context'] === 'update' && (int)$old['id_nam_hoc'] === (int)$id_nam_hoc && isset($old[$field])) {
                    return $old[$field];
                }
            }
            return $default;
        }
    }

    $id_nam_hoc = filter_input(INPUT_POST, 'id_nam_hoc', FILTER_VALIDATE_INT);
    if (!$id_nam_hoc || $id_nam_hoc < 1) {
        http_response_code(400);
        exit('<div class="alert alert-danger">ID năm học không hợp lệ.</div>');
    }

    $namhoc__Get_By_Id = $namhoc->namhoc__Get_By_Id($id_nam_hoc);
    if (!$namhoc__Get_By_Id) {
        http_response_code(404);
        exit('<div class="alert alert-danger">Không tìm thấy năm học cần cập nhật.</div>');
    }

    if (isset($namhoc_old_input['context'], $namhoc_old_input['id_nam_hoc']) && $namhoc_old_input['context'] === 'update' && (int)$namhoc_old_input['id_nam_hoc'] === (int)$namhoc__Get_By_Id->id_nam_hoc) {
        unset($_SESSION['namhoc_old_input']);
    }

    $status = isset($_POST['error_status']) ? $_POST['error_status'] : '';
    $is_update_error = !empty($status);
?>

<form class="row form" action="quan-ly-nam-hoc/action.php?req=update" method="post">
    <input type="hidden" name="id_nam_hoc" value="<?=(int)$namhoc__Get_By_Id->id_nam_hoc?>">
    <input type="hidden" name="csrf_token" value="<?=namhoc_update_escape($_SESSION['csrf_token'])?>">
    <div class="col-12">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Cập nhật Năm học</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="label-sidebar" for="ten_nam_hoc_update">Tên năm học <span class="color-crimson">*</span></label>
                    <input type="text" id="ten_nam_hoc_update" name="ten_nam_hoc" class="form-control <?= ($is_update_error && in_array($status, ['duplicate', 'invalid-ten-nam-hoc'])) ? 'is-invalid' : '' ?>" required maxlength="50"
                        placeholder="Nhập tên năm học" value="<?=namhoc_update_escape(namhoc_update_old_value('ten_nam_hoc', $namhoc__Get_By_Id->id_nam_hoc, $namhoc__Get_By_Id->ten_nam_hoc))?>">
                    <?php if ($is_update_error): ?>
                        <?php if ($status == 'duplicate'): ?>
                            <small class="text-danger mt-1">Tên năm học đã tồn tại trong hệ thống.</small>
                        <?php elseif ($status == 'invalid-ten-nam-hoc'): ?>
                            <small class="text-danger mt-1">Tên năm học không được để trống và tối đa 50 ký tự.</small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="label-sidebar" for="ngay_bat_dau_update">Ngày bắt đầu <span class="color-crimson">*</span></label>
                    <input type="date" id="ngay_bat_dau_update" name="ngay_bat_dau" class="form-control <?= ($is_update_error && $status == 'invalid-ngay') ? 'is-invalid' : '' ?>" required
                        value="<?=namhoc_update_escape(namhoc_update_old_value('ngay_bat_dau', $namhoc__Get_By_Id->id_nam_hoc, $namhoc__Get_By_Id->ngay_bat_dau))?>">
                    <?php if ($is_update_error && $status == 'invalid-ngay'): ?>
                        <small class="text-danger mt-1">Ngày bắt đầu phải nhỏ hơn ngày kết thúc.</small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="label-sidebar" for="ngay_ket_thuc_update">Ngày kết thúc <span class="color-crimson">*</span></label>
                    <input type="date" id="ngay_ket_thuc_update" name="ngay_ket_thuc" class="form-control <?= ($is_update_error && $status == 'invalid-ngay') ? 'is-invalid' : '' ?>" required
                        value="<?=namhoc_update_escape(namhoc_update_old_value('ngay_ket_thuc', $namhoc__Get_By_Id->id_nam_hoc, $namhoc__Get_By_Id->ngay_ket_thuc))?>">
                </div>
                <div class="form-group">
                    <label class="label-sidebar" for="ghi_chu_update">Ghi chú</label>
                    <textarea id="ghi_chu_update" name="ghi_chu" class="form-control <?= ($is_update_error && $status == 'invalid-ghichu') ? 'is-invalid' : '' ?>" maxlength="2000"
                        placeholder="Nhập ghi chú"><?=namhoc_update_escape(namhoc_update_old_value('ghi_chu', $namhoc__Get_By_Id->id_nam_hoc, $namhoc__Get_By_Id->ghi_chu))?></textarea>
                    <?php if ($is_update_error && $status == 'invalid-ghichu'): ?>
                        <small class="text-danger mt-1">Ghi chú không được vượt quá 2000 ký tự.</small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer">
                <input type="submit" value="Cập nhật" class="btn btn-danger float-right font-weight-bold">
                <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" onclick="cancel_update()">Hủy</button>
            </div>
        </div>
    </div>
</form>
