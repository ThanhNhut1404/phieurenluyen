    <?php 
        require '../../models/getModel.php';
        $id_dieu = isset($_POST['id_dieu']) ? trim($_POST['id_dieu']) : "";
        // Nhựt sửa lỗi: Ajax update thiếu id_dieu hoặc id_dieu sai kiểu thì quay về danh sách.
        if (!preg_match('/^[1-9][0-9]*$/', $id_dieu)) {
            echo "<script>location.href = 'index.php?page=quan-ly-dieu&status=not-found'</script>";
            exit();
        }
        $dieu__Get_By_Id = $dieu->dieu__Get_By_Id($id_dieu);
        // Nhựt sửa lỗi: id_dieu không tồn tại thì không render form update để tránh lỗi object rỗng.
        if (!$dieu__Get_By_Id) {
            echo "<script>location.href = 'index.php?page=quan-ly-dieu&status=not-found'</script>";
            exit();
        }
        // Nhựt sửa lỗi: Giới hạn thứ tự trên form cập nhật từ 1 đến max hiện tại để chặn nhập số âm, 0 hoặc vượt giới hạn ở client.
        $dieu__Max_Thu_Tu = $dieu->dieu__Get_Max_Thu_Tu();

        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        $dieu_old_input = isset($_SESSION['dieu_old_input']) && is_array($_SESSION['dieu_old_input']) ? $_SESSION['dieu_old_input'] : array();

        if (!function_exists('dieu_update_escape')) {
            function dieu_update_escape($value) {
                return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            }
        }

        if (!function_exists('dieu_update_old_value')) {
            function dieu_update_old_value($field, $id_dieu, $default = '') {
                global $dieu_old_input;
                if (isset($dieu_old_input) && is_array($dieu_old_input)) {
                    $old = $dieu_old_input;
                    if (isset($old['context'], $old['id_dieu']) && $old['context'] === 'update' && (int)$old['id_dieu'] === (int)$id_dieu && isset($old[$field])) {
                        return $old[$field];
                    }
                }
                return $default;
            }
        }

        if (isset($dieu_old_input['context'], $dieu_old_input['id_dieu']) && $dieu_old_input['context'] === 'update' && (int)$dieu_old_input['id_dieu'] === (int)$dieu__Get_By_Id->id_dieu) {
            unset($_SESSION['dieu_old_input']);
        }

        $status = isset($_POST['error_status']) ? $_POST['error_status'] : '';
        $is_update_error = !empty($status);
    ?>

    <form class="row form" action="quan-ly-dieu/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_dieu" value="<?=$dieu__Get_By_Id->id_dieu?>">
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Cập nhật</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="label-sidebar" for="ten_dieu">Tên điều <span class="color-crimson">*</span></label>
                        <input type="text" id="ten_dieu" name="ten_dieu" class="form-control <?= ($is_update_error && in_array($status, ['duplicate-name', 'invalid'])) ? 'is-invalid' : '' ?>" required
                            placeholder="Nhập tên điều" value="<?=dieu_update_escape(dieu_update_old_value('ten_dieu', $dieu__Get_By_Id->id_dieu, $dieu__Get_By_Id->ten_dieu))?>">
                        <?php if ($is_update_error): ?>
                            <?php if ($status == 'duplicate-name'): ?>
                                <small class="text-danger mt-1">Tên điều đã tồn tại trong hệ thống.</small>
                            <?php elseif ($status == 'invalid'): ?>
                                <small class="text-danger mt-1">Tên điều không được để trống.</small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="label-sidebar" for="ghi_chu">Nội dung chi tiết</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control" placeholder="Nhập nội dung chi tiết"
                            required><?=dieu_update_escape(dieu_update_old_value('ghi_chu', $dieu__Get_By_Id->id_dieu, $dieu__Get_By_Id->ghi_chu))?></textarea>
                    </div>
                    <div class="form-group">
                        <!-- quân sửa: Cập nhật lời nhắc Thứ tự tự động -->
                        <label class="label-sidebar" for="thu_tu">Thứ tự</label>
                        <input type="number" id="thu_tu" name="thu_tu" class="form-control" min="1"
                            max="<?=$dieu__Max_Thu_Tu?>" step="1"
                            placeholder="Nhập thứ tự (Có thể để trống)" value="<?=dieu_update_escape(dieu_update_old_value('thu_tu', $dieu__Get_By_Id->id_dieu, abs($dieu__Get_By_Id->thu_tu)))?>">
                        <small class="form-text text-muted">Mẹo: Để trống để giữ nguyên. Nếu nhập trùng, hệ thống sẽ tự động hoán đổi 2 Điều.</small>
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
