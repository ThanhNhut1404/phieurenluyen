    <?php 
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Nhựt sửa lỗi: chặn gọi trực tiếp form sửa khi chưa đăng nhập admin.
        if (!isset($_SESSION['admin'])) {
            http_response_code(403);
            exit('Forbidden');
        }
        require '../../models/getModel.php';
        if (empty($_SESSION['csrf_token'])) {
            // Nhựt sửa lỗi: Tạo CSRF token cho form cập nhật Xếp loại.
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Throwable $e) {
                $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
            }
        }

        $xep_loai_old_input = isset($_SESSION['xep_loai_old_input']) && is_array($_SESSION['xep_loai_old_input']) ? $_SESSION['xep_loai_old_input'] : array();

        if (!function_exists('xep_loai_update_escape')) {
            function xep_loai_update_escape($value) {
                return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            }
        }

        if (!function_exists('xep_loai_update_old_value')) {
            function xep_loai_update_old_value($field, $id_xep_loai, $default = '') {
                global $xep_loai_old_input;
                if (isset($xep_loai_old_input) && is_array($xep_loai_old_input)) {
                    $old = $xep_loai_old_input;
                    if (isset($old['context'], $old['id_xep_loai']) && $old['context'] === 'update' && (int)$old['id_xep_loai'] === (int)$id_xep_loai && isset($old[$field])) {
                        return $old[$field];
                    }
                }
                return $default;
            }
        }

        $id_xep_loai = isset($_POST['id_xep_loai']) ? trim($_POST['id_xep_loai']) : "";
        // Nhựt sửa lỗi: Ajax update thiếu id_xep_loai hoặc id sai kiểu thì quay về danh sách.
        if (!preg_match('/^[1-9][0-9]*$/', $id_xep_loai)) {
            echo "<script>location.href = 'index.php?page=quan-ly-xep-loai&status=not-found'</script>";
            exit();
        }
        $xeploai__Get_By_Id = $xeploai->xeploai__Get_By_Id($id_xep_loai);
        // Nhựt sửa lỗi: id_xep_loai không tồn tại thì không render form update để tránh lỗi object rỗng.
        if (!$xeploai__Get_By_Id) {
            echo "<script>location.href = 'index.php?page=quan-ly-xep-loai&status=not-found'</script>";
            exit();
        }

        if (isset($xep_loai_old_input['context'], $xep_loai_old_input['id_xep_loai']) && $xep_loai_old_input['context'] === 'update' && (int)$xep_loai_old_input['id_xep_loai'] === (int)$xeploai__Get_By_Id->id_xep_loai) {
            unset($_SESSION['xep_loai_old_input']);
        }

        $status = isset($_POST['error_status']) ? $_POST['error_status'] : '';
        $is_update_error = !empty($status);
    ?>

    <form class="row form" action="quan-ly-xep-loai/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_xep_loai" value="<?=$xeploai__Get_By_Id->id_xep_loai?>">
        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>">
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Cập nhật Xếp loại</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="label-sidebar" for="ten_xep_loai_up">Tên xếp loại <span class="color-crimson">*</span></label>
                        <!-- Nhựt sửa lỗi: Đổi id form cập nhật để tránh trùng id HTML với form thêm. -->
                        <input type="text" id="ten_xep_loai_up" name="ten_xep_loai" class="form-control <?= ($is_update_error && in_array($status, ['duplicate-name', 'invalid-ten'])) ? 'is-invalid' : '' ?>" required
                            placeholder="Nhập tên xếp loại" value="<?=xep_loai_update_escape(xep_loai_update_old_value('ten_xep_loai', $xeploai__Get_By_Id->id_xep_loai, $xeploai__Get_By_Id->ten_xep_loai))?>">
                        <?php if ($is_update_error): ?>
                            <?php if ($status == 'duplicate-name'): ?>
                                <small class="text-danger mt-1">Tên xếp loại đã tồn tại trong hệ thống.</small>
                            <?php elseif ($status == 'invalid-ten'): ?>
                                <small class="text-danger mt-1">Tên xếp loại không được để trống.</small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-sidebar" for="can_duoi_up">Điểm tối thiểu <span class="color-crimson">*</span></label>
                                <input type="number" id="can_duoi_up" name="can_duoi"
                                    value="<?=xep_loai_update_escape(xep_loai_update_old_value('can_duoi', $xeploai__Get_By_Id->id_xep_loai, abs($xeploai__Get_By_Id->can_duoi)))?>" class="form-control <?= ($is_update_error && in_array($status, ['invalid-diem', 'overlap-xep-loai'])) ? 'is-invalid' : '' ?>" required
                                    title="Thấp nhất là 0, lớn nhất là 100" placeholder="Nhập điểm tối thiểu" min="0" max="100"
                                    step="1">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-sidebar" for="can_tren_up">Điểm tối đa <span class="color-crimson">*</span></label>
                                <input type="number" id="can_tren_up" name="can_tren"
                                    value="<?=xep_loai_update_escape(xep_loai_update_old_value('can_tren', $xeploai__Get_By_Id->id_xep_loai, abs($xeploai__Get_By_Id->can_tren)))?>" class="form-control <?= ($is_update_error && in_array($status, ['invalid-diem', 'overlap-xep-loai'])) ? 'is-invalid' : '' ?>" required
                                    title="Thấp nhất là 0, lớn nhất là 100" placeholder="Nhập điểm tối đa" min="0" max="100"
                                    step="1">
                            </div>
                        </div>
                        <?php if ($is_update_error && in_array($status, ['invalid-diem', 'overlap-xep-loai'])): ?>
                            <div class="col-12">
                            <?php if ($status == 'invalid-diem'): ?>
                                <small class="text-danger mt-1">Điểm không hợp lệ (từ 0 đến 100, tối thiểu <= tối đa).</small>
                            <?php elseif ($status == 'overlap-xep-loai'): ?>
                                <small class="text-danger mt-1">Khoảng điểm bị trùng lặp với xếp loại khác.</small>
                            <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-sidebar" for="ha_bac_up">Hạ bậc <span class="color-crimson">*</span></label>
                                <!-- Nhựt sửa lỗi: Đổi id form cập nhật để tránh trùng id HTML với form thêm. -->
                                <input type="number" id="ha_bac_up" name="ha_bac" min="10" max="15" step="1" class="form-control <?= ($is_update_error && $status == 'invalid-habac') ? 'is-invalid' : '' ?>"
                                    required title="Thấp nhất là 10, lớn nhất là 15" placeholder="Nhập điểm hạ bậc"
                                    value="<?=xep_loai_update_escape(xep_loai_update_old_value('ha_bac', $xeploai__Get_By_Id->id_xep_loai, abs($xeploai__Get_By_Id->ha_bac)))?>">
                                <?php if ($is_update_error && $status == 'invalid-habac'): ?>
                                    <small class="text-danger mt-1">Điểm hạ bậc không hợp lệ (từ 10 đến 15).</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-sidebar" for="ghi_chu_up">Ghi chú</label>
                                <!-- Nhựt sửa lỗi: Đổi id form cập nhật để tránh trùng id HTML với form thêm. -->
                                <textarea id="ghi_chu_up" name="ghi_chu" class="form-control" rows="1"
                                    placeholder="Nhập ghi chú"><?=xep_loai_update_escape(xep_loai_update_old_value('ghi_chu', $xeploai__Get_By_Id->id_xep_loai, $xeploai__Get_By_Id->ghi_chu))?></textarea>
                            </div>
                        </div>
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

    <script>
can_duoi_up = document.getElementById('can_duoi_up');
$("#can_duoi_up").change(function() {
    $('#can_tren_up').attr({
        "min": can_duoi_up.value
    });
});
    </script>
