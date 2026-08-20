    <?php 
        require '../../models/getModel.php';
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $muc_old_input = isset($_SESSION['muc_old_input']) && is_array($_SESSION['muc_old_input']) ? $_SESSION['muc_old_input'] : array();
        
        if (!function_exists('muc_update_escape')) {
            function muc_update_escape($value) {
                return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            }
        }
    
        if (!function_exists('muc_update_old_value')) {
            function muc_update_old_value($field, $id_muc, $default = '') {
                global $muc_old_input;
                if (isset($muc_old_input['context']) && $muc_old_input['context'] === 'update' && 
                    isset($muc_old_input['id_muc']) && $muc_old_input['id_muc'] == $id_muc && 
                    isset($muc_old_input[$field])) {
                    return $muc_old_input[$field];
                }
                return $default;
            }
        }

        $id_muc = isset($_POST['id_muc']) ? $_POST['id_muc'] : (isset($muc_old_input['id_muc']) ? $muc_old_input['id_muc'] : 0);
        $muc__Get_By_Id = $muc->muc__Get_By_Id($id_muc);
        $khoan__Get_All = $khoan->khoan__Get_All();
        
        $is_update_error = isset($muc_old_input['context']) && $muc_old_input['context'] === 'update' && $muc_old_input['id_muc'] == $id_muc;
        $status = isset($_GET['status']) ? $_GET['status'] : '';
    ?>

    <form class="row form" action="quan-ly-muc/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?=muc_update_escape($_SESSION['csrf_token'])?>">
        <input type="hidden" name="id_muc" value="<?=$muc__Get_By_Id->id_muc?>">
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
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-sidebar" for="id_khoan_update">Khoản <span class="color-crimson">*</span></label>
                                <?php $old_khoan_up = muc_update_old_value('id_khoan', $muc__Get_By_Id->id_muc, $muc__Get_By_Id->id_khoan); ?>
                                <select class="form-control <?= ($is_update_error && $status == 'invalid-khoan') ? 'is-invalid' : '' ?>" name="id_khoan" id="id_khoan_update" required onchange="loadThuTu(this.value, <?=muc_update_old_value('thu_tu', $muc__Get_By_Id->id_muc, $muc__Get_By_Id->thu_tu)?>, '#thu_tu_update')">
                                    <option value="<?=$muc__Get_By_Id->id_khoan?>" <?= $old_khoan_up == $muc__Get_By_Id->id_khoan ? 'selected' : '' ?>>
                                        <?=$khoan->khoan__Get_By_Id($muc__Get_By_Id->id_khoan)->ten_khoan?>
                                    </option>
                                    <?php foreach ($khoan__Get_All as $item):?>
                                    <?php if($item->id_khoan != $muc__Get_By_Id->id_khoan):?>
                                    <option value="<?=$item->id_khoan?>" <?= $old_khoan_up == $item->id_khoan ? 'selected' : '' ?>><?=$item->ten_khoan?></option>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($is_update_error && $status == 'invalid-khoan'): ?>
                                    <small class="text-danger mt-1">Khoản không hợp lệ.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-sidebar" for="ten_muc">Tên mục <span class="color-crimson">*</span></label>
                                <input type="text" id="ten_muc" name="ten_muc" class="form-control <?= ($is_update_error && $status == 'invalid-ten') ? 'is-invalid' : '' ?>" required
                                    placeholder="Nhập tên mục" value="<?=muc_update_escape(muc_update_old_value('ten_muc', $muc__Get_By_Id->id_muc, $muc__Get_By_Id->ten_muc))?>">
                                <?php if ($is_update_error && $status == 'invalid-ten'): ?>
                                    <small class="text-danger mt-1">Tên mục không được để trống.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-sidebar" for="thu_tu_update">Thứ tự <span class="color-crimson">*</span></label>
                                <select id="thu_tu_update" name="thu_tu" class="form-control <?= ($is_update_error && $status == 'invalid-thutu') ? 'is-invalid' : '' ?>" required>
                                    <option value="<?=muc_update_old_value('thu_tu', $muc__Get_By_Id->id_muc, $muc__Get_By_Id->thu_tu)?>"><?=muc_update_old_value('thu_tu', $muc__Get_By_Id->id_muc, $muc__Get_By_Id->thu_tu)?></option>
                                </select>
                                <?php if ($is_update_error && $status == 'invalid-thutu'): ?>
                                    <small class="text-danger mt-1">Thứ tự không hợp lệ.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <!-- quân sửa: Bổ sung input Điểm tối đa -->
                            <div class="form-group">
                                <label class="label-sidebar" for="diem_toi_da">Điểm tối đa <span class="color-crimson">*</span></label>
                                <input type="number" id="diem_toi_da" name="diem_toi_da" class="form-control <?= ($is_update_error && $status == 'invalid-diem') ? 'is-invalid' : '' ?>" required
                                    placeholder="Nhập điểm tối đa của mục" min="0" value="<?=muc_update_escape(muc_update_old_value('diem_toi_da', $muc__Get_By_Id->id_muc, isset($muc__Get_By_Id->diem_toi_da) ? $muc__Get_By_Id->diem_toi_da : 0))?>">
                                <?php if ($is_update_error && $status == 'invalid-diem'): ?>
                                    <small class="text-danger mt-1">Điểm tối đa không hợp lệ.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="label-sidebar" for="ghi_chu_update">Nội dung chi tiết</label>
                        <textarea id="ghi_chu_update" name="ghi_chu" class="form-control" placeholder="Nhập nội dung chi tiết"><?=muc_update_escape(muc_update_old_value('ghi_chu', $muc__Get_By_Id->id_muc, $muc__Get_By_Id->ghi_chu))?></textarea>
                    </div>

                    <script>
                        $('#ghi_chu_update').summernote({
                            height: 150,
                            toolbar: [
                                ['font', ['bold', 'italic', 'underline', 'clear']],
                                ['color', ['color']],
                                ['para', ['ul', 'ol', 'paragraph']],
                                ['insert', ['link']],
                                ['view', ['fullscreen', 'codeview']]
                            ]
                        });
                        
                        // Cập nhật load thứ tự
                        loadThuTu($('#id_khoan_update').val(), <?=muc_update_old_value('thu_tu', $muc__Get_By_Id->id_muc, $muc__Get_By_Id->thu_tu)?>, '#thu_tu_update');
                    </script>

                    <!-- quân sửa: Thêm tuỳ chọn Yêu cầu minh chứng -->
                    <div class="form-group">
                        <div class="icheck-danger d-inline">
                            <?php $old_co_minh_chung = muc_update_old_value('co_minh_chung', $muc__Get_By_Id->id_muc, $muc__Get_By_Id->co_minh_chung); ?>
                            <input type="checkbox" id="co_minh_chung_update" name="co_minh_chung" value="1" <?=($old_co_minh_chung == 1) ? 'checked' : ''?>>
                            <label for="co_minh_chung_update" class="text-danger">Yêu cầu sinh viên nộp minh chứng cho Mục này</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="label-sidebar">Quyền chấm điểm</label>
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="quyen_sv_update" name="quyen_sv" value="1" <?= muc_update_old_value('quyen_sv', $muc__Get_By_Id->id_muc, $muc__Get_By_Id->quyen_sv) == 1 ? 'checked' : '' ?>>
                                    <label for="quyen_sv_update">Sinh viên</label>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="quyen_lt_update" name="quyen_lt" value="1" <?= muc_update_old_value('quyen_lt', $muc__Get_By_Id->id_muc, $muc__Get_By_Id->quyen_lt) == 1 ? 'checked' : '' ?>>
                                    <label for="quyen_lt_update">Lớp trưởng/BCS</label>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="quyen_btdk_update" name="quyen_btdk" value="1" <?= muc_update_old_value('quyen_btdk', $muc__Get_By_Id->id_muc, $muc__Get_By_Id->quyen_btdk) == 1 ? 'checked' : '' ?>>
                                    <label for="quyen_btdk_update">Bí thư đoàn khoa</label>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="quyen_gv_update" name="quyen_gv" value="1" <?= muc_update_old_value('quyen_gv', $muc__Get_By_Id->id_muc, $muc__Get_By_Id->quyen_gv) == 1 ? 'checked' : '' ?>>
                                    <label for="quyen_gv_update">Giảng viên/CVHT</label>
                                </div>
                            </div>
                        </div>
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