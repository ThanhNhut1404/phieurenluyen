    <?php 
        session_start();

    if (!isset($_SESSION['admin'])) {
        header('location: ../../auth/');
        exit();
    }
        $mauphieu_old_input = isset($_SESSION['mauphieu_old_input']) && is_array($_SESSION['mauphieu_old_input']) ? $_SESSION['mauphieu_old_input'] : array();

        if (!function_exists('mauphieu_update_escape')) {
            function mauphieu_update_escape($value) {
                return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            }
        }

        if (!function_exists('mauphieu_update_old_value')) {
            function mauphieu_update_old_value($field, $id_mau_phieu, $default = '') {
                global $mauphieu_old_input;
                if (isset($mauphieu_old_input['context']) && $mauphieu_old_input['context'] === 'update' 
                    && isset($mauphieu_old_input['id_mau_phieu']) && $mauphieu_old_input['id_mau_phieu'] == $id_mau_phieu 
                    && isset($mauphieu_old_input[$field])) {
                    return $mauphieu_old_input[$field];
                }
                return $default;
            }
        }

        require '../../models/getModel.php';
        $id_mau_phieu = $_POST['id_mau_phieu'];
        $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        $mauphieu__Get_By_Id = $mauphieu->mauphieu__Get_By_Id($id_mau_phieu);
        
        $is_update_error = isset($mauphieu_old_input['context']) && $mauphieu_old_input['context'] === 'update' && isset($mauphieu_old_input['id_mau_phieu']) && $mauphieu_old_input['id_mau_phieu'] == $id_mau_phieu;
        $status = isset($_GET['status']) ? $_GET['status'] : '';
    ?>

    <form class="row form" action="quan-ly-mau-phieu/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_mau_phieu" value="<?=$mauphieu__Get_By_Id->id_mau_phieu?>">
        <input type="hidden" name="csrf_token" value="<?=mauphieu_update_escape($csrf_token)?>">
        <div class="col-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Cập nhật Mẫu phiếu</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-sidebar" for="ten_mau_phieu_up">Tên mẫu phiếu <span class="color-crimson">*</span></label>
                                <input type="text" id="ten_mau_phieu_up" name="ten_mau_phieu" class="form-control <?= ($is_update_error && $status == 'invalid-ten') ? 'is-invalid' : '' ?>" required
                                    placeholder="Nhập tên mẫu phiếu" value="<?=mauphieu_update_escape(mauphieu_update_old_value('ten_mau_phieu', $id_mau_phieu, $mauphieu__Get_By_Id->ten_mau_phieu))?>">
                                <?php if ($is_update_error && $status == 'invalid-ten'): ?>
                                    <small class="text-danger mt-1">Tên mẫu phiếu không được để trống.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-sidebar" for="ghi_chu_up">Ghi chú</label>
                                <textarea id="ghi_chu_up" name="ghi_chu" class="form-control" rows="2"
                                    placeholder="Nhập ghi chú"><?=mauphieu_update_escape(mauphieu_update_old_value('ghi_chu', $id_mau_phieu, $mauphieu__Get_By_Id->ghi_chu))?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer py-2">
                    <input type="submit" value="Cập nhật" class="btn btn-warning float-right font-weight-bold" style="color: #fff;">
                    <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" onclick="cancel_update()">Hủy</button>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </form>