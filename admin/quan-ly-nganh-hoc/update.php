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

        // Nhựt sửa lỗi: chụp dữ liệu lỗi ra biến cục bộ để dùng đúng một lần rồi dọn session.
        $nganhhoc_old_input = isset($_SESSION['nganhhoc_old_input']) && is_array($_SESSION['nganhhoc_old_input']) ? $_SESSION['nganhhoc_old_input'] : array();

        if (!function_exists('nganhhoc_update_escape')) {
            function nganhhoc_update_escape($value) {
                return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            }
        }

        // Nhựt sửa lỗi: giữ lại dữ liệu đã nhập cho form sửa nếu trước đó có validate lỗi.
        if (!function_exists('nganhhoc_update_old_value')) {
            function nganhhoc_update_old_value($field, $id_nganh_hoc, $default = '') {
                global $nganhhoc_old_input;
                if (isset($nganhhoc_old_input) && is_array($nganhhoc_old_input)) {
                    $old = $nganhhoc_old_input;
                    if (isset($old['context'], $old['id_nganh_hoc']) && $old['context'] === 'update' && (int)$old['id_nganh_hoc'] === (int)$id_nganh_hoc && isset($old[$field])) {
                        return $old[$field];
                    }
                }
                return $default;
            }
        }

        // Nhựt sửa lỗi: kiểm tra id_nganh_hoc hợp lệ trước khi lấy dữ liệu.
        $id_nganh_hoc = filter_input(INPUT_POST, 'id_nganh_hoc', FILTER_VALIDATE_INT);
        if (!$id_nganh_hoc || $id_nganh_hoc < 1) {
            http_response_code(400);
            exit('<div class="alert alert-danger">ID ngành học không hợp lệ.</div>');
        }

        $nganhhoc__Get_By_Id = $nganhhoc->nganhhoc__Get_By_Id($id_nganh_hoc);
        if (!$nganhhoc__Get_By_Id) {
            http_response_code(404);
            exit('<div class="alert alert-danger">Không tìm thấy ngành học cần cập nhật.</div>');
        }

        $khoa__Get_All = $khoa->khoa__Get_All();
        $old_id_khoa = nganhhoc_update_old_value('id_khoa', $nganhhoc__Get_By_Id->id_nganh_hoc, $nganhhoc__Get_By_Id->id_khoa);
        if (isset($nganhhoc_old_input['context'], $nganhhoc_old_input['id_nganh_hoc']) && $nganhhoc_old_input['context'] === 'update' && (int)$nganhhoc_old_input['id_nganh_hoc'] === (int)$nganhhoc__Get_By_Id->id_nganh_hoc) {
            unset($_SESSION['nganhhoc_old_input']);
        }

        $status = isset($_POST['error_status']) ? $_POST['error_status'] : '';
        $is_update_error = !empty($status);
    ?>

    <form class="row form" action="quan-ly-nganh-hoc/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_nganh_hoc" value="<?=(int)$nganhhoc__Get_By_Id->id_nganh_hoc?>">
        <input type="hidden" name="csrf_token" value="<?=nganhhoc_update_escape($_SESSION['csrf_token'])?>">
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Cập nhật Ngành học</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="label-sidebar" for="">Khoa <span class="color-crimson">*</span></label>
                        <select class="form-control <?= ($is_update_error && $status == 'invalid-khoa') ? 'is-invalid' : '' ?>" name="id_khoa" required>
                            <?php foreach ($khoa__Get_All as $item):?>
                            <option value="<?=(int)$item->id_khoa?>" <?=((int)$old_id_khoa === (int)$item->id_khoa) ? "selected" : ""?>><?=nganhhoc_update_escape($item->ten_khoa)?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($is_update_error && $status == 'invalid-khoa'): ?>
                            <small class="text-danger mt-1">Khoa không hợp lệ.</small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="label-sidebar" for="ten_nganh_hoc_update">Tên ngành học <span class="color-crimson">*</span></label>
                        <!-- Nhựt sửa lỗi: giới hạn tên ngành học ở client khớp với validate server-side và DB. -->
                        <input type="text" id="ten_nganh_hoc_update" name="ten_nganh_hoc" class="form-control <?= ($is_update_error && in_array($status, ['duplicate-nganh-hoc', 'invalid-ten-nganh-hoc'])) ? 'is-invalid' : '' ?>" required maxlength="50"
                            placeholder="Nhập tên ngành học" value="<?=nganhhoc_update_escape(nganhhoc_update_old_value('ten_nganh_hoc', $nganhhoc__Get_By_Id->id_nganh_hoc, $nganhhoc__Get_By_Id->ten_nganh_hoc))?>">
                        <?php if ($is_update_error): ?>
                            <?php if ($status == 'duplicate-nganh-hoc'): ?>
                                <small class="text-danger mt-1">Tên ngành học đã tồn tại trong khoa đã chọn.</small>
                            <?php elseif ($status == 'invalid-ten-nganh-hoc'): ?>
                                <small class="text-danger mt-1">Tên ngành học không được để trống và tối đa 50 ký tự.</small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="label-sidebar" for="ghi_chu_update">Ghi chú</label>
                        <textarea id="ghi_chu_update" name="ghi_chu" class="form-control <?= ($is_update_error && $status == 'invalid-ghichu') ? 'is-invalid' : '' ?>" maxlength="2000"
                            placeholder="Nhập ghi chú"><?=nganhhoc_update_escape(nganhhoc_update_old_value('ghi_chu', $nganhhoc__Get_By_Id->id_nganh_hoc, $nganhhoc__Get_By_Id->ghi_chu))?></textarea>
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
