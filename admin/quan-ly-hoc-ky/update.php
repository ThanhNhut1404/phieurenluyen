    <?php 
        require '../../models/getModel.php';
        $id_hoc_ky = $_POST['id_hoc_ky'];
        $hocky__Get_By_Id = $hocky->hocky__Get_By_Id($id_hoc_ky);
        $namhoc__Get_All = $namhoc->namhoc__Get_All();
    ?>

    <form class="row form" action="quan-ly-hoc-ky/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_hoc_ky" value="<?=$hocky__Get_By_Id->id_hoc_ky?>">
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
                        <label for="">Năm học <span class="color-crimson">(*)</span></label>
                        <select class="form-control" name="id_nam_hoc" required>
                            <option value="<?=$hocky__Get_By_Id->id_nam_hoc?>">
                                <?=$namhoc->namhoc__Get_By_Id($hocky__Get_By_Id->id_nam_hoc)->ten_nam_hoc?>
                            </option>
                            <?php foreach ($namhoc__Get_All as $item):?>
                            <?php if($item->id_nam_hoc != $hocky__Get_By_Id->id_nam_hoc):?>
                            <option value="<?=$item->id_nam_hoc?>"><?=$item->ten_nam_hoc?></option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tên học kỳ <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_hoc_ky" name="ten_hoc_ky" class="form-control" required
                            placeholder="Nhập tên học kỳ" value="<?=$hocky__Get_By_Id->ten_hoc_ky?>">
                    </div>
                    <div class="form-group">
                        <label for="">Ghi chú</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                            placeholder="Nhập ghi chú"><?=$hocky__Get_By_Id->ghi_chu?></textarea>
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