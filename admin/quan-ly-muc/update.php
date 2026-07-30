    <?php 
        require '../../models/getModel.php';
        $id_muc = $_POST['id_muc'];
        $muc__Get_By_Id = $muc->muc__Get_By_Id($id_muc);
        $khoan__Get_All = $khoan->khoan__Get_All();
    ?>

    <form class="row form" action="quan-ly-muc/action.php?req=update" method="post" enctype="multipart/form-data">
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
                    <div class="form-group">
                        <label for="">Khoản <span class="color-crimson">(*)</span></label>
                        <select class="form-control" name="id_khoan" id="id_khoan_update" required onchange="loadThuTu(this.value, <?=$muc__Get_By_Id->thu_tu?>, '#thu_tu_update')">
                            <option value="<?=$muc__Get_By_Id->id_khoan?>">
                                <?=$khoan->khoan__Get_By_Id($muc__Get_By_Id->id_khoan)->ten_khoan?>
                            </option>
                            <?php foreach ($khoan__Get_All as $item):?>
                            <?php if($item->id_khoan != $muc__Get_By_Id->id_khoan):?>
                            <option value="<?=$item->id_khoan?>"><?=$item->ten_khoan?></option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tên khoản <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_muc" name="ten_muc" class="form-control" required
                            placeholder="Nhập tên khoản" value="<?=$muc__Get_By_Id->ten_muc?>">
                    </div>
                    <div class="form-group">
                        <label for="">Nội dung chi tiết</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control" placeholder="Nhập nội dung chi tiết"
                            required><?=$muc__Get_By_Id->ghi_chu?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Thứ tự <span class="color-crimson">(*)</span></label>
                        <select id="thu_tu_update" name="thu_tu" class="form-control" required>
                            <option value="<?=$muc__Get_By_Id->thu_tu?>"><?=$muc__Get_By_Id->thu_tu?></option>
                        </select>
                    </div>

                    <script>
                    $(document).ready(function() {
                        loadThuTu($('#id_khoan_update').val(), <?=$muc__Get_By_Id->thu_tu?>, '#thu_tu_update');
                    });
                    </script>
                    <!-- quân sửa: Bổ sung input Điểm tối đa -->
                    <div class="form-group">
                        <label for="">Điểm tối đa <span class="color-crimson">(*)</span></label>
                        <input type="number" id="diem_toi_da" name="diem_toi_da" class="form-control" required
                            placeholder="Nhập điểm tối đa của mục" min="0" value="<?=isset($muc__Get_By_Id->diem_toi_da) ? $muc__Get_By_Id->diem_toi_da : 0?>">
                    </div>
                    <div class="form-group">
                        <label for="">Quyền chấm điểm</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="quyen_sv_update" name="quyen_sv" value="1" <?=$muc__Get_By_Id->quyen_sv == 1 ? 'checked' : ''?>>
                                    <label for="quyen_sv_update">Sinh viên</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="quyen_lt_update" name="quyen_lt" value="1" <?=$muc__Get_By_Id->quyen_lt == 1 ? 'checked' : ''?>>
                                    <label for="quyen_lt_update">Lớp trưởng/BCS</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="quyen_btdk_update" name="quyen_btdk" value="1" <?=$muc__Get_By_Id->quyen_btdk == 1 ? 'checked' : ''?>>
                                    <label for="quyen_btdk_update">Bí thư đoàn khoa</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="quyen_gv_update" name="quyen_gv" value="1" <?=$muc__Get_By_Id->quyen_gv == 1 ? 'checked' : ''?>>
                                    <label for="quyen_gv_update">Giảng viên/CVHT</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <input type="submit" value="Cập nhật" class="btn btn-danger float-right">
                </div>
            </div>
            <!-- /.card -->
        </div>
    </form>