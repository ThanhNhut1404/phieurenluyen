<?php
    session_start();
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

    $khoa_old_input = isset($_SESSION['khoa_old_input']) && is_array($_SESSION['khoa_old_input']) ? $_SESSION['khoa_old_input'] : array();

    if (!function_exists('khoa_update_escape')) {
        function khoa_update_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('khoa_update_old_value')) {
        function khoa_update_old_value($field, $id_khoa, $default = '') {
            global $khoa_old_input;
            if (isset($khoa_old_input) && is_array($khoa_old_input)) {
                $old = $khoa_old_input;
                if (isset($old['context'], $old['id_khoa']) && $old['context'] === 'update' && (int)$old['id_khoa'] === (int)$id_khoa && isset($old[$field])) {
                    return $old[$field];
                }
            }
            return $default;
        }
    }

    $id_khoa = filter_input(INPUT_POST, 'id_khoa', FILTER_VALIDATE_INT);
    if (!$id_khoa || $id_khoa < 1) {
        http_response_code(400);
        exit('<div class="alert alert-danger">ID khoa không hợp lệ.</div>');
    }

    $khoa__Get_By_Id = $khoa->khoa__Get_By_Id($id_khoa);
    if (!$khoa__Get_By_Id) {
        http_response_code(404);
        exit('<div class="alert alert-danger">Không tìm thấy khoa cần cập nhật.</div>');
    }

    if (isset($khoa_old_input['context'], $khoa_old_input['id_khoa']) && $khoa_old_input['context'] === 'update' && (int)$khoa_old_input['id_khoa'] === (int)$khoa__Get_By_Id->id_khoa) {
        unset($_SESSION['khoa_old_input']);
    }

    $status = isset($_POST['error_status']) ? $_POST['error_status'] : '';
    $is_update_error = !empty($status);
?>

<form class="row form" action="quan-ly-khoa/action.php?req=update" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id_khoa" value="<?=(int)$khoa__Get_By_Id->id_khoa?>">
    <input type="hidden" name="csrf_token" value="<?=khoa_update_escape($_SESSION['csrf_token'])?>">
    <div class="col-12">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Cập nhật Khoa</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="label-sidebar" for="ten_khoa_update">Tên khoa <span class="color-crimson">*</span></label>
                        <input type="text" id="ten_khoa_update" name="ten_khoa" class="form-control <?= ($is_update_error && in_array($status, ['duplicate', 'invalid-ten-khoa'])) ? 'is-invalid' : '' ?>" required maxlength="50"
                            value="<?=khoa_update_escape(khoa_update_old_value('ten_khoa', $khoa__Get_By_Id->id_khoa, $khoa__Get_By_Id->ten_khoa))?>" placeholder="Nhập tên khoa">
                        <?php if ($is_update_error): ?>
                            <?php if ($status == 'duplicate'): ?>
                                <small class="text-danger mt-1">Tên khoa đã tồn tại trong hệ thống.</small>
                            <?php elseif ($status == 'invalid-ten-khoa'): ?>
                                <small class="text-danger mt-1">Tên khoa không được để trống và tối đa 50 ký tự.</small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="label-sidebar" for="ghi_chu_update">Ghi chú</label>
                        <textarea id="ghi_chu_update" name="ghi_chu" class="form-control <?= ($is_update_error && $status == 'invalid-ghichu') ? 'is-invalid' : '' ?>" maxlength="2000" rows="1"
                            placeholder="Nhập ghi chú"><?=khoa_update_escape(khoa_update_old_value('ghi_chu', $khoa__Get_By_Id->id_khoa, $khoa__Get_By_Id->ghi_chu))?></textarea>
                        <?php if ($is_update_error && $status == 'invalid-ghichu'): ?>
                            <small class="text-danger mt-1">Ghi chú không được vượt quá 2000 ký tự.</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2">
                <input type="submit" value="Cập nhật" class="btn btn-danger float-right font-weight-bold">
                <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" onclick="cancel_update()">Hủy</button>
            </div>
        </div>
    </div>
</form>
