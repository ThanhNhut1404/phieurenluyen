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

    $trinhdo_old_input = isset($_SESSION['trinhdo_old_input']) && is_array($_SESSION['trinhdo_old_input']) ? $_SESSION['trinhdo_old_input'] : array();

    if (!function_exists('trinhdo_update_escape')) {
        function trinhdo_update_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('trinhdo_update_old_value')) {
        function trinhdo_update_old_value($field, $id_trinh_do, $default = '') {
            global $trinhdo_old_input;
            if (isset($trinhdo_old_input) && is_array($trinhdo_old_input)) {
                $old = $trinhdo_old_input;
                if (isset($old['context'], $old['id_trinh_do']) && $old['context'] === 'update' && (int)$old['id_trinh_do'] === (int)$id_trinh_do && isset($old[$field])) {
                    return $old[$field];
                }
            }
            return $default;
        }
    }

    $id_trinh_do = filter_input(INPUT_POST, 'id_trinh_do', FILTER_VALIDATE_INT);
    if (!$id_trinh_do || $id_trinh_do < 1) {
        http_response_code(400);
        exit('<div class="alert alert-danger">ID trình độ không hợp lệ.</div>');
    }

    $trinhdo__Get_By_Id = $trinhdo->trinhdo__Get_By_Id($id_trinh_do);
    if (!$trinhdo__Get_By_Id) {
        http_response_code(404);
        exit('<div class="alert alert-danger">Không tìm thấy trình độ cần cập nhật.</div>');
    }

    if (isset($trinhdo_old_input['context'], $trinhdo_old_input['id_trinh_do']) && $trinhdo_old_input['context'] === 'update' && (int)$trinhdo_old_input['id_trinh_do'] === (int)$trinhdo__Get_By_Id->id_trinh_do) {
        unset($_SESSION['trinhdo_old_input']);
    }

    $status = isset($_POST['error_status']) ? $_POST['error_status'] : '';
    $is_update_error = !empty($status);
?>

<form class="row form" action="quan-ly-trinh-do/action.php?req=update" method="post">
    <input type="hidden" name="id_trinh_do" value="<?=(int)$trinhdo__Get_By_Id->id_trinh_do?>">
    <input type="hidden" name="csrf_token" value="<?=trinhdo_update_escape($_SESSION['csrf_token'])?>">
    <div class="col-12">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Cập nhật Trình độ</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="label-sidebar" for="ten_trinh_do_update">Tên trình độ <span class="color-crimson">*</span></label>
                    <input type="text" id="ten_trinh_do_update" name="ten_trinh_do" class="form-control <?= ($is_update_error && in_array($status, ['duplicate', 'invalid-ten-trinh-do'])) ? 'is-invalid' : '' ?>" required maxlength="50"
                        placeholder="Nhập tên trình độ" value="<?=trinhdo_update_escape(trinhdo_update_old_value('ten_trinh_do', $trinhdo__Get_By_Id->id_trinh_do, $trinhdo__Get_By_Id->ten_trinh_do))?>">
                    <?php if ($is_update_error): ?>
                        <?php if ($status == 'duplicate'): ?>
                            <small class="text-danger mt-1">Tên trình độ đã tồn tại trong hệ thống.</small>
                        <?php elseif ($status == 'invalid-ten-trinh-do'): ?>
                            <small class="text-danger mt-1">Tên trình độ không được để trống và tối đa 50 ký tự.</small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="label-sidebar" for="ghi_chu_update">Ghi chú</label>
                    <textarea id="ghi_chu_update" name="ghi_chu" class="form-control <?= ($is_update_error && $status == 'invalid-ghichu') ? 'is-invalid' : '' ?>" maxlength="2000"
                        placeholder="Nhập ghi chú"><?=trinhdo_update_escape(trinhdo_update_old_value('ghi_chu', $trinhdo__Get_By_Id->id_trinh_do, $trinhdo__Get_By_Id->ghi_chu))?></textarea>
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
