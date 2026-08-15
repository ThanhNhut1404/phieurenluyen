    <?php 
        session_start();
        // Nhựt sửa lỗi: chặn gọi trực tiếp form sửa khi chưa đăng nhập admin.
        if (!isset($_SESSION['admin'])) {
            http_response_code(403);
            exit('Forbidden');
        }

        require '../../models/getModel.php';

        // Nhựt sửa lỗi: tạo CSRF token an toàn hơn, có fallback nếu random_bytes() lỗi.
        if (empty($_SESSION['csrf_token'])) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Throwable $e) {
                $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
            }
        }

        $lophoc_old_input = isset($_SESSION['lophoc_old_input']) && is_array($_SESSION['lophoc_old_input']) ? $_SESSION['lophoc_old_input'] : array();

        if (!function_exists('lophoc_update_escape')) {
            function lophoc_update_escape($value) {
                return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            }
        }

        // Nhựt sửa lỗi: giữ lại dữ liệu đã nhập cho form sửa nếu trước đó có validate lỗi.
        if (!function_exists('lophoc_update_old_value')) {
            function lophoc_update_old_value($field, $id_lop_hoc, $default = '') {
                global $lophoc_old_input;
                if (isset($lophoc_old_input) && is_array($lophoc_old_input)) {
                    $old = $lophoc_old_input;
                    if (isset($old['context'], $old['id_lop_hoc']) && $old['context'] === 'update' && (int)$old['id_lop_hoc'] === (int)$id_lop_hoc && isset($old[$field])) {
                        return $old[$field];
                    }
                }
                return $default;
            }
        }

        // Nhựt sửa lỗi: kiểm tra id_lop_hoc hợp lệ trước khi lấy dữ liệu.
        $id_lop_hoc = filter_input(INPUT_POST, 'id_lop_hoc', FILTER_VALIDATE_INT);
        if (!$id_lop_hoc || $id_lop_hoc < 1) {
            http_response_code(400);
            exit('<div class="alert alert-danger">ID lớp học không hợp lệ.</div>');
        }

        $lophoc__Get_By_Id = $lophoc->lophoc__Get_By_Id($id_lop_hoc);
        if (!$lophoc__Get_By_Id) {
            http_response_code(404);
            exit('<div class="alert alert-danger">Không tìm thấy lớp học cần cập nhật.</div>');
        }

        $khoahoc__Get_All = $khoahoc->khoahoc__Get_All();
        $nganhhoc__Get_All = $nganhhoc->nganhhoc__Get_All();
        $old_id_khoa_hoc = lophoc_update_old_value('id_khoa_hoc', $lophoc__Get_By_Id->id_lop_hoc, $lophoc__Get_By_Id->id_khoa_hoc);
        $old_id_nganh_hoc = lophoc_update_old_value('id_nganh_hoc', $lophoc__Get_By_Id->id_lop_hoc, $lophoc__Get_By_Id->id_nganh_hoc);
        if (isset($lophoc_old_input['context'], $lophoc_old_input['id_lop_hoc']) && $lophoc_old_input['context'] === 'update' && (int)$lophoc_old_input['id_lop_hoc'] === (int)$lophoc__Get_By_Id->id_lop_hoc) {
            unset($_SESSION['lophoc_old_input']);
        }

        $status = isset($_POST['error_status']) ? $_POST['error_status'] : '';
        $is_update_error = !empty($status);
    ?>

    <form class="row form" action="quan-ly-lop-hoc/action.php?req=update" method="post">
        <input type="hidden" name="id_lop_hoc" value="<?=(int)$lophoc__Get_By_Id->id_lop_hoc?>">
        <input type="hidden" name="csrf_token" value="<?=lophoc_update_escape($_SESSION['csrf_token'])?>">
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Cập nhật Lớp học</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="label-sidebar" for="">Khóa học <span class="color-crimson">*</span></label>

                        <select class="form-control <?= ($is_update_error && $status == 'invalid-khoa-hoc') ? 'is-invalid' : '' ?>" name="id_khoa_hoc" required>
                            <?php foreach ($khoahoc__Get_All as $item):?>
                            <option value="<?=(int)$item->id_khoa_hoc?>" <?=((int)$old_id_khoa_hoc === (int)$item->id_khoa_hoc) ? 'selected' : ''?>><?=lophoc_update_escape($item->ten_khoa_hoc)?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($is_update_error && $status == 'invalid-khoa-hoc'): ?>
                            <small class="text-danger mt-1">Khóa học không hợp lệ.</small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="label-sidebar" for="">Ngành học <span class="color-crimson">*</span></label>
                        <select class="form-control <?= ($is_update_error && $status == 'invalid-nganh-hoc') ? 'is-invalid' : '' ?>" name="id_nganh_hoc" required>
                            <?php foreach ($nganhhoc__Get_All as $item):?>
                            <option value="<?=(int)$item->id_nganh_hoc?>" <?=((int)$old_id_nganh_hoc === (int)$item->id_nganh_hoc) ? 'selected' : ''?>><?=lophoc_update_escape($item->ten_nganh_hoc)?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($is_update_error && $status == 'invalid-nganh-hoc'): ?>
                            <small class="text-danger mt-1">Ngành học không hợp lệ.</small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="label-sidebar" for="ten_lop_hoc_update">Tên lớp học <span class="color-crimson">*</span></label>
                        <input type="text" id="ten_lop_hoc_update" name="ten_lop_hoc" class="form-control <?= ($is_update_error && in_array($status, ['duplicate-lop-hoc', 'invalid-ten-lop-hoc'])) ? 'is-invalid' : '' ?>" required maxlength="50"
                            placeholder="Nhập tên lớp học" value="<?=lophoc_update_escape(lophoc_update_old_value('ten_lop_hoc', $lophoc__Get_By_Id->id_lop_hoc, $lophoc__Get_By_Id->ten_lop_hoc))?>">
                        <?php if ($is_update_error): ?>
                            <?php if ($status == 'duplicate-lop-hoc'): ?>
                                <small class="text-danger mt-1">Tên lớp học đã tồn tại trong khóa học và ngành học đã chọn.</small>
                            <?php elseif ($status == 'invalid-ten-lop-hoc'): ?>
                                <small class="text-danger mt-1">Tên lớp học không được để trống và tối đa 50 ký tự.</small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="label-sidebar" for="ghi_chu_update">Ghi chú</label>
                        <textarea id="ghi_chu_update" name="ghi_chu" class="form-control <?= ($is_update_error && $status == 'invalid-ghichu') ? 'is-invalid' : '' ?>" maxlength="2000"
                            placeholder="Nhập ghi chú"><?=lophoc_update_escape(lophoc_update_old_value('ghi_chu', $lophoc__Get_By_Id->id_lop_hoc, $lophoc__Get_By_Id->ghi_chu))?></textarea>
                        <?php if ($is_update_error && $status == 'invalid-ghichu'): ?>
                            <small class="text-danger mt-1">Ghi chú không được vượt quá 2000 ký tự.</small>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <input type="submit" value="Cập nhật" class="btn btn-danger float-right font-weight-bold">
                    <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" onclick="cancel_update()">Hủy</button>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </form>
