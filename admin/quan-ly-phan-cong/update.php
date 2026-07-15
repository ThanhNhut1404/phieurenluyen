    <?php 
        require '../../models/getModel.php';
        $id_phan_cong = $_POST['id_phan_cong'];
        $phancong__Get_By_Id = $phancong->phancong__Get_By_Id($id_phan_cong);
        $giangvien__Get_All = $giangvien->giangvien__Get_All();
        $lophoc__Get_All = $lophoc->lophoc__Get_All();
    ?>

    <form class="row form" action="quan-ly-phan-cong/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_phan_cong" value="<?=$phancong__Get_By_Id->id_phan_cong?>">
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
                        <label for="">Giảng viên<span class="color-crimson">(*)</span></label>
                        <select class="form-control" name="id_giang_vien" required>
                            <option value="<?=$phancong__Get_By_Id->id_giang_vien?>">
                                <?=$giangvien->giangvien__Get_By_Id($phancong__Get_By_Id->id_giang_vien)->ten_giang_vien?>
                            </option>
                            <?php foreach ($giangvien__Get_All as $item):?>
                            <?php if($item->id_giang_vien != $phancong__Get_By_Id->id_giang_vien):?>
                            <option value="<?=$item->id_giang_vien?>"><?=$item->ten_giang_vien?></option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Lớp học<span class="color-crimson">(*)</span></label>
                        <select class="form-control" name="id_lop_hoc" required>
                            <option value="<?=$phancong__Get_By_Id->id_lop_hoc?>">
                                <?=$lophoc->lophoc__Get_By_Id($phancong__Get_By_Id->id_lop_hoc)->ten_lop_hoc?>
                            </option>
                            <?php foreach ($lophoc__Get_All as $item):?>
                            <?php if($item->id_lop_hoc != $phancong__Get_By_Id->id_lop_hoc):?>
                            <option value="<?=$item->id_lop_hoc?>"><?=$item->ten_lop_hoc?></option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Ghi chú</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                            placeholder="Nhập ghi chú"><?=$phancong__Get_By_Id->ghi_chu?></textarea>
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