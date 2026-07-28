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

    $khoahoc_old_input = isset($_SESSION['khoahoc_old_input']) && is_array($_SESSION['khoahoc_old_input']) ? $_SESSION['khoahoc_old_input'] : array();

    if (!function_exists('khoahoc_update_escape')) {
        function khoahoc_update_escape($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('khoahoc_update_old_value')) {
        function khoahoc_update_old_value($field, $id_khoa_hoc, $default = '') {
            global $khoahoc_old_input;
            if (isset($khoahoc_old_input) && is_array($khoahoc_old_input)) {
                $old = $khoahoc_old_input;
                if (isset($old['context'], $old['id_khoa_hoc']) && $old['context'] === 'update' && (int)$old['id_khoa_hoc'] === (int)$id_khoa_hoc && isset($old[$field])) {
                    return $old[$field];
                }
            }
            return $default;
        }
    }

    $id_khoa_hoc = filter_input(INPUT_POST, 'id_khoa_hoc', FILTER_VALIDATE_INT);
    if (!$id_khoa_hoc || $id_khoa_hoc < 1) {
        http_response_code(400);
        exit('<div class="alert alert-danger">ID khóa học không hợp lệ.</div>');
    }

    $khoahoc__Get_By_Id = $khoahoc->khoahoc__Get_By_Id($id_khoa_hoc);
    if (!$khoahoc__Get_By_Id) {
        http_response_code(404);
        exit('<div class="alert alert-danger">Không tìm thấy khóa học cần cập nhật.</div>');
    }

    if (isset($khoahoc_old_input['context'], $khoahoc_old_input['id_khoa_hoc']) && $khoahoc_old_input['context'] === 'update' && (int)$khoahoc_old_input['id_khoa_hoc'] === (int)$khoahoc__Get_By_Id->id_khoa_hoc) {
        unset($_SESSION['khoahoc_old_input']);
    }
?>

<form class="row form" action="quan-ly-khoa-hoc/action.php?req=update" method="post">
    <input type="hidden" name="id_khoa_hoc" value="<?=(int)$khoahoc__Get_By_Id->id_khoa_hoc?>">
    <input type="hidden" name="csrf_token" value="<?=khoahoc_update_escape($_SESSION['csrf_token'])?>">
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
                    <label for="">Tên khóa học <span class="color-crimson">(*)</span></label>
                    <input type="text" id="ten_khoa_hoc" name="ten_khoa_hoc" class="form-control" required maxlength="50"
                        placeholder="Nhập tên khóa học" value="<?=khoahoc_update_escape(khoahoc_update_old_value('ten_khoa_hoc', $khoahoc__Get_By_Id->id_khoa_hoc, $khoahoc__Get_By_Id->ten_khoa_hoc))?>">
                </div>
                <div class="form-group">
                    <label for="">Năm nhập học <span class="color-crimson">(*)</span></label>
                    <input type="number" id="nam_nhap_hoc" name="nam_nhap_hoc" class="form-control" required
                        min="2006" max="2099" placeholder="Nhập năm nhập học"
                        value="<?=khoahoc_update_escape(khoahoc_update_old_value('nam_nhap_hoc', $khoahoc__Get_By_Id->id_khoa_hoc, $khoahoc__Get_By_Id->nam_nhap_hoc))?>">
                </div>
                <div class="form-group">
                    <label for="">Hệ đào tạo <span class="color-crimson">(*)</span></label>
                    <input type="number" id="he_dao_tao" name="he_dao_tao" class="form-control" required min="2"
                        max="8" step="0.5" placeholder="Nhập số năm đào tạo"
                        value="<?=khoahoc_update_escape(khoahoc_update_old_value('he_dao_tao', $khoahoc__Get_By_Id->id_khoa_hoc, $khoahoc__Get_By_Id->he_dao_tao))?>">
                </div>
                <div class="form-group">
                    <label for="">Ghi chú</label>
                    <textarea id="ghi_chu" name="ghi_chu" class="form-control" maxlength="2000"
                        placeholder="Nhập ghi chú"><?=khoahoc_update_escape(khoahoc_update_old_value('ghi_chu', $khoahoc__Get_By_Id->id_khoa_hoc, $khoahoc__Get_By_Id->ghi_chu))?></textarea>
                </div>
            </div>
            <div class="card-footer">
                <input type="submit" value="Cập nhật" class="btn btn-danger float-right">
            </div>
        </div>
    </div>
</form>
