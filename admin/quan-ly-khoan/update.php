    <?php 
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
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

        $khoan_old_input = isset($_SESSION['khoan_old_input']) && is_array($_SESSION['khoan_old_input']) ? $_SESSION['khoan_old_input'] : array();

        if (!function_exists('khoan_update_escape')) {
            function khoan_update_escape($value) {
                return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            }
        }

        if (!function_exists('khoan_update_old_value')) {
            function khoan_update_old_value($field, $id_khoan, $default = '') {
                global $khoan_old_input;
                if (isset($khoan_old_input) && is_array($khoan_old_input)) {
                    $old = $khoan_old_input;
                    if (isset($old['context'], $old['id_khoan']) && $old['context'] === 'update' && (int)$old['id_khoan'] === (int)$id_khoan && isset($old[$field])) {
                        return $old[$field];
                    }
                }
                return $default;
            }
        }

        $id_khoan = isset($_POST['id_khoan']) ? trim($_POST['id_khoan']) : "";
        if (!preg_match('/^[1-9][0-9]*$/', $id_khoan)) {
            echo "<script>location.href = 'index.php?page=quan-ly-khoan&status=not-found'</script>";
            exit();
        }
        $khoan__Get_By_Id = $khoan->khoan__Get_By_Id($id_khoan);
        if (!$khoan__Get_By_Id) {
            echo "<script>location.href = 'index.php?page=quan-ly-khoan&status=not-found'</script>";
            exit();
        }

        if (isset($khoan_old_input['context'], $khoan_old_input['id_khoan']) && $khoan_old_input['context'] === 'update' && (int)$khoan_old_input['id_khoan'] === (int)$khoan__Get_By_Id->id_khoan) {
            unset($_SESSION['khoan_old_input']);
        }

        $dieu__Get_All = $dieu->dieu__Get_All();
        $khoan__Get_All = $khoan->khoan__Get_All();
        $used_dieu = [];
        foreach ($khoan__Get_All as $k) {
            if ($k->id_khoan != $id_khoan) {
                $used_dieu[] = $k->id_dieu;
            }
        }

        $status = isset($_POST['error_status']) ? $_POST['error_status'] : '';
        $is_update_error = !empty($status);
    ?>

    <form class="row form" action="quan-ly-khoan/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_khoan" value="<?=$khoan__Get_By_Id->id_khoan?>">
        <input type="hidden" name="csrf_token" value="<?=khoan_update_escape($_SESSION['csrf_token'])?>">
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Cập nhật</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-sidebar" for="id_dieu_up">Điều <span class="color-crimson">*</span></label>
                                <select class="form-control <?= ($is_update_error && in_array($status, ['invalid-dieu', 'duplicate-dieu'])) ? 'is-invalid' : '' ?>" name="id_dieu" id="id_dieu_up" required>
                                    <?php $selected_dieu = khoan_update_old_value('id_dieu', $khoan__Get_By_Id->id_khoan, $khoan__Get_By_Id->id_dieu); ?>
                                    <option value="<?=$khoan__Get_By_Id->id_dieu?>" <?= $selected_dieu == $khoan__Get_By_Id->id_dieu ? 'selected' : '' ?>>
                                        <?php $ban_sao_text_hien_tai = preg_match('/\(Bản sao thứ \d+\)/', $dieu->dieu__Get_By_Id($khoan__Get_By_Id->id_dieu)->ghi_chu ?? '', $m) ? ' ' . $m[0] : ''; ?>
                                        <?=khoan_update_escape($dieu->dieu__Get_By_Id($khoan__Get_By_Id->id_dieu)->ten_dieu ?? '') . $ban_sao_text_hien_tai?>
                                    </option>
                                    <?php foreach ($dieu__Get_All as $item):?>
                                        <?php if($item->id_dieu != $khoan__Get_By_Id->id_dieu && !in_array($item->id_dieu, $used_dieu)):?>
                                            <?php $ban_sao_text = preg_match('/\(Bản sao thứ \d+\)/', $item->ghi_chu, $m) ? ' ' . $m[0] : ''; ?>
                                            <option value="<?=$item->id_dieu?>" <?= $selected_dieu == $item->id_dieu ? 'selected' : '' ?>><?=khoan_update_escape($item->ten_dieu) . $ban_sao_text?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($is_update_error): ?>
                                    <?php if ($status == 'invalid-dieu'): ?>
                                        <small class="text-danger mt-1">Vui lòng chọn Điều hợp lệ.</small>
                                    <?php elseif ($status == 'duplicate-dieu'): ?>
                                        <small class="text-danger mt-1">Điều này đã được sử dụng.</small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-sidebar" for="ten_khoan_up">Tên khoản <span class="color-crimson">*</span></label>
                                <input type="text" id="ten_khoan_up" name="ten_khoan" class="form-control <?= ($is_update_error && $status == 'invalid-ten') ? 'is-invalid' : '' ?>" required
                                    placeholder="Nhập tên khoản" value="<?=khoan_update_escape(khoan_update_old_value('ten_khoan', $khoan__Get_By_Id->id_khoan, $khoan__Get_By_Id->ten_khoan))?>">
                                <?php if ($is_update_error && $status == 'invalid-ten'): ?>
                                    <small class="text-danger mt-1">Tên khoản không được để trống.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <!-- quân sửa: Xoá ô nhập Thứ tự ở đây vì đã tự động đồng bộ theo Điều -->
                            <div class="form-group">
                                <label class="label-sidebar" for="can_tren_up">Điểm tối đa <span class="color-crimson">*</span></label>
                                <input type="number" id="can_tren_up" name="can_tren" class="form-control <?= ($is_update_error && $status == 'invalid-diem') ? 'is-invalid' : '' ?>" required
                                    placeholder="Nhập điểm tối đa" value="<?=khoan_update_escape(khoan_update_old_value('can_tren', $khoan__Get_By_Id->id_khoan, $khoan__Get_By_Id->can_tren))?>">
                                <?php if ($is_update_error && $status == 'invalid-diem'): ?>
                                    <small class="text-danger mt-1">Điểm tối đa không hợp lệ.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <!-- quân sửa: Bổ sung ô nhập số lượng mục -->
                            <div class="form-group">
                                <label class="label-sidebar" for="so_luong_muc_up">Số lượng mục tối đa <span class="color-crimson">*</span></label>
                                <input type="number" id="so_luong_muc_up" name="so_luong_muc" class="form-control <?= ($is_update_error && $status == 'invalid-soluong') ? 'is-invalid' : '' ?>" required
                                    placeholder="Nhập giới hạn số lượng mục" min="1" value="<?=khoan_update_escape(khoan_update_old_value('so_luong_muc', $khoan__Get_By_Id->id_khoan, isset($khoan__Get_By_Id->so_luong_muc) ? $khoan__Get_By_Id->so_luong_muc : 10))?>">
                                <?php if ($is_update_error && $status == 'invalid-soluong'): ?>
                                    <small class="text-danger mt-1">Số lượng mục không hợp lệ.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="label-sidebar" for="ghi_chu_up">Nội dung chi tiết</label>
                        <textarea id="ghi_chu_up" name="ghi_chu" class="form-control" placeholder="Nhập nội dung chi tiết"
                            ><?=khoan_update_escape(khoan_update_old_value('ghi_chu', $khoan__Get_By_Id->id_khoan, $khoan__Get_By_Id->ghi_chu))?></textarea>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer py-2">
                    <input type="submit" value="Cập nhật" class="btn btn-danger float-right font-weight-bold">
                    <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" onclick="cancel_update()">Hủy</button>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </form>