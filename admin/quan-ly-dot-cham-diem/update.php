    <?php 
        require '../../models/getModel.php';
        $id_dot = $_POST['id_dot'];
        $dotchamdiem__Get_By_Id = $dotchamdiem->dotchamdiem__Get_By_Id($id_dot);
        $hocky__Get_All = $hocky->hocky__Get_All();
    ?>

    <form class="row form" action="quan-ly-dot-cham-diem/action.php?req=update" method="post"
        enctype="multipart/form-data">
        <input type="hidden" name="id_dot" value="<?=$dotchamdiem__Get_By_Id->id_dot?>">
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
                        <label for="">Tên đợt <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_dot" name="ten_dot" class="form-control" required
                            placeholder="Nhập tên điều" value="<?=$dotchamdiem__Get_By_Id->ten_dot?>">
                    </div>
                    <div class="form-group">
                        <label for="">Thời gian bắt đầu<span class="color-crimson">(*)</span></label>
                        <input type="date" id="thoi_gian_bat_dau_up" name="thoi_gian_bat_dau" class="form-control"
                            required placeholder="Nhập thời gian bắt đầu"
                            value="<?=$dotchamdiem__Get_By_Id->thoi_gian_bat_dau?>">
                    </div>
                    <div class="form-group">
                        <label for="">Thời gian kết thúc<span class="color-crimson">(*)</span></label>
                        <input type="date" id="thoi_gian_ket_thuc_up" name="thoi_gian_ket_thuc" class="form-control"
                            required placeholder="Nhập thời gian kết thúc" required placeholder="Nhập thời gian bắt đầu"
                            value="<?=$dotchamdiem__Get_By_Id->thoi_gian_ket_thuc?>">
                    </div>
                    <div class="form-group">
                        <label for="">Học kỳ <span class="color-crimson">(*)</span></label>
                        <select class="form-control" name="id_hoc_ky" required>
                            <option value="<?=$dotchamdiem__Get_By_Id->id_hoc_ky?>">
                                <?=$hocky->hocky__Get_By_Id($dotchamdiem__Get_By_Id->id_hoc_ky)->ten_hoc_ky?> -
                                <?=$namhoc->namhoc__Get_By_Id($hocky->hocky__Get_By_Id($dotchamdiem__Get_By_Id->id_hoc_ky)->id_nam_hoc)->ten_nam_hoc?>
                            </option>
                            <?php foreach ($hocky__Get_All as $item):?>
                            <?php if($item->id_hoc_ky != $dotchamdiem__Get_By_Id->id_hoc_ky):?>
                            <option value="<?=$item->id_hoc_ky?>"><?=$item->ten_hoc_ky?> -
                                <?=$namhoc->namhoc__Get_By_Id($item->id_nam_hoc)->ten_nam_hoc?></option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Ghi chú</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                            placeholder="Nhập Ghi chú"><?=$dotchamdiem__Get_By_Id->ghi_chu?></textarea>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <input type="submit" value="Cập nhật" class="btn btn-danger float-right">
                    </div>
                </div>
                <!-- /.card -->
            </div>
    </form>

    <script>
thoi_gian_bat_dau_up = document.getElementById('thoi_gian_bat_dau_up');
$("#thoi_gian_bat_dau_up").change(function() {
    $('#thoi_gian_ket_thuc_up').attr({
        "min": thoi_gian_bat_dau_up.value
    });
});
    </script>