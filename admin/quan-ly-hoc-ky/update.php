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

    $namhoc__Get_All = $namhoc->namhoc__Get_All();
?>

<form class="row form" action="quan-ly-hoc-ky/action.php?req=update" method="post">
    <input type="hidden" name="id_hoc_ky" value="<?=(int)$hocky__Get_By_Id->id_hoc_ky?>">
    <input type="hidden" name="csrf_token" value="<?=hocky_update_escape($_SESSION['csrf_token'])?>">
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
                <div class="form-group">
                    <label for="">Năm học <span class="color-crimson">(*)</span></label>
                    <select class="form-control" name="id_nam_hoc" required>
                        <option value="">Chọn năm học</option>
                        <?php foreach ($namhoc__Get_All as $item):?>
                        <option value="<?=(int)$item->id_nam_hoc?>" <?=(int)hocky_update_old_value('id_nam_hoc', $hocky__Get_By_Id->id_hoc_ky, $hocky__Get_By_Id->id_nam_hoc) === (int)$item->id_nam_hoc ? 'selected' : ''?>><?=hocky_update_escape($item->ten_nam_hoc)?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="">Tên học kỳ <span class="color-crimson">(*)</span></label>
                    <input type="text" id="ten_hoc_ky" name="ten_hoc_ky" class="form-control" required maxlength="50"
                        placeholder="Nhập tên học kỳ" value="<?=hocky_update_escape(hocky_update_old_value('ten_hoc_ky', $hocky__Get_By_Id->id_hoc_ky, $hocky__Get_By_Id->ten_hoc_ky))?>">
                </div>
                <div class="form-group">
                    <label for="">Ghi chú</label>
                    <textarea id="ghi_chu" name="ghi_chu" class="form-control" maxlength="2000"
                        placeholder="Nhập ghi chú"><?=hocky_update_escape(hocky_update_old_value('ghi_chu', $hocky__Get_By_Id->id_hoc_ky, $hocky__Get_By_Id->ghi_chu))?></textarea>
                </div>
            </div>
            <div class="card-footer">
                <input type="submit" value="Cập nhật" class="btn btn-danger float-right">
            </div>
        </div>
    </div>
</form>
