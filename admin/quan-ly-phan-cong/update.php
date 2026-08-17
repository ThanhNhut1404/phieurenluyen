    <?php 
        require '../../models/getModel.php';
        $id_phan_cong = $_POST['id_phan_cong'];
        $phancong__Get_By_Id = $phancong->phancong__Get_By_Id($id_phan_cong);
        $giangvien__Get_All = $giangvien->giangvien__Get_All();
        $lophoc__Get_All = $lophoc->lophoc__Get_All();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $phancong_old_input = $_SESSION['phancong_old_input'] ?? [];
        function phancong_old_value_update($key, $default = '') {
            global $phancong_old_input;
            if (isset($phancong_old_input['context']) && $phancong_old_input['context'] === 'update' && isset($phancong_old_input[$key])) {
                return $phancong_old_input[$key];
            }
            return $default;
        }
        $is_update_error = isset($phancong_old_input['context']) && $phancong_old_input['context'] === 'update';
    ?>

    <form class="row form" action="quan-ly-phan-cong/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_phan_cong" value="<?=$phancong__Get_By_Id->id_phan_cong?>">
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Cập nhật Phân công</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-sidebar" for="">Giảng viên <span class="color-crimson">*</span></label>
                                <select class="form-control <?= ($is_update_error && ($_GET['status'] ?? '') == 'duplicate-phancong') ? 'is-invalid' : '' ?>" name="id_giang_vien" required>
                                    <?php foreach ($giangvien__Get_All as $item):?>
                                    <option value="<?=$item->id_giang_vien?>" <?= phancong_old_value_update('id_giang_vien', $phancong__Get_By_Id->id_giang_vien) == $item->id_giang_vien ? 'selected' : '' ?>><?=$item->ten_giang_vien?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-sidebar" for="">Lớp học <span class="color-crimson">*</span></label>
                                <select class="form-control <?= ($is_update_error && ($_GET['status'] ?? '') == 'duplicate-phancong') ? 'is-invalid' : '' ?>" name="id_lop_hoc" required>
                                    <?php foreach ($lophoc__Get_All as $item):?>
                                    <option value="<?=$item->id_lop_hoc?>" <?= phancong_old_value_update('id_lop_hoc', $phancong__Get_By_Id->id_lop_hoc) == $item->id_lop_hoc ? 'selected' : '' ?>><?=$item->ten_lop_hoc?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($is_update_error && isset($_GET['status']) && $_GET['status'] == 'duplicate-phancong'): ?>
                                    <small class="text-danger mt-1">Giảng viên đã được phân công lớp học này.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="label-sidebar" for="">Ghi chú</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                            placeholder="Nhập ghi chú"><?= htmlspecialchars(phancong_old_value_update('ghi_chu', $phancong__Get_By_Id->ghi_chu)) ?></textarea>
                    </div>
                </div>
                <!-- /.card-body -->
                 <div class="card-footer py-2">
                     <button type="submit" class="btn btn-danger float-right font-weight-bold" style="font-weight: bold;">Cập nhật</button>
                     <button type="button" class="btn btn-cancel-custom float-right mr-2 font-weight-bold" style="font-weight: bold;" onclick="cancel_update()">Hủy</button>
                 </div>
            </div>
            <!-- /.card -->
        </div>
    </form>