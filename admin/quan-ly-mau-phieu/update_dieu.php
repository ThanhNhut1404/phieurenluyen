    <?php 
        session_start();
        $mauphieu_old_input = isset($_SESSION['mauphieu_old_input']) && is_array($_SESSION['mauphieu_old_input']) ? $_SESSION['mauphieu_old_input'] : array();

        if (!function_exists('mauphieu_update_dieu_escape')) {
            function mauphieu_update_dieu_escape($value) {
                return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            }
        }

        if (!function_exists('mauphieu_update_dieu_old_value')) {
            function mauphieu_update_dieu_old_value($field, $id_mau_phieu, $default = array()) {
                global $mauphieu_old_input;
                if (isset($mauphieu_old_input['context']) && $mauphieu_old_input['context'] === 'update_dieu' 
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
        $dieu__Get_All = $dieu->dieu__Get_All();
        
        $is_update_error = isset($mauphieu_old_input['context']) && $mauphieu_old_input['context'] === 'update_dieu' && isset($mauphieu_old_input['id_mau_phieu']) && $mauphieu_old_input['id_mau_phieu'] == $id_mau_phieu;
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        
        $arr_id_dieu_hien_tai = array();
        foreach($dieu->dieu__Get_All_Selected($id_mau_phieu) as $item_dieu){
            $arr_id_dieu_hien_tai[] = $item_dieu->id_dieu;
        }
    ?>

    <form class="row form" action="quan-ly-mau-phieu/action.php?req=update_dieu" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_mau_phieu" value="<?=$mauphieu__Get_By_Id->id_mau_phieu?>">
        <input type="hidden" name="csrf_token" value="<?=mauphieu_update_dieu_escape($csrf_token)?>">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Sửa điều trong Mẫu phiếu</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-sidebar" for="ten_mau_phieu_upd">Tên mẫu phiếu <span class="color-crimson">*</span></label>
                                <input type="text" id="ten_mau_phieu_upd" class="form-control"
                                    value="<?=$mauphieu__Get_By_Id->ten_mau_phieu?>" readonly>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-sidebar" for="ghi_chu_upd">Ghi chú</label>
                                <textarea id="ghi_chu_upd" class="form-control" rows="2"
                                    readonly><?=$mauphieu__Get_By_Id->ghi_chu?></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-sidebar" for="id_dieu_upd">Chọn lại điều <span class="color-crimson">*</span></label>
                                <?php $old_id_dieu_upd = mauphieu_update_dieu_old_value('id_dieu', $id_mau_phieu, $arr_id_dieu_hien_tai); ?>
                                <select class="duallistboxs <?= ($is_update_error && $status == 'invalid-dieu') ? 'is-invalid' : '' ?>" multiple="multiple" name="id_dieu[]" id="id_dieu_upd" required>
                                    <?php foreach($dieu__Get_All as $item):?>
                                    <option value="<?=$item->id_dieu?>" <?= in_array($item->id_dieu, $old_id_dieu_upd) ? 'selected' : '' ?>><?=$item->ten_dieu?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($is_update_error && $status == 'invalid-dieu'): ?>
                                    <small class="text-danger mt-1">Vui lòng chọn ít nhất một điều.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <input type="submit" value="Cập nhật" class="btn btn-info float-right font-weight-bold" style="color: #fff;">
                    <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" onclick="cancel_update()">Hủy</button>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </form>
    <script>
$('.duallistboxs').bootstrapDualListbox({
    filterTextClear: 'Hiện tất cả',
    filterPlaceHolder: 'Tìm kiếm',
    infoText: 'Hiển thị tất cả ({0})',
    infoTextFiltered: '<span class="badge badge-warning">Tìm kiếm</span> {0} từ {1}',
    infoTextEmpty: 'Danh sách trống'
});
    </script>