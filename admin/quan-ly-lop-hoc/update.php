    <?php 
        require '../../models/getModel.php';
        $id_lop_hoc = $_POST['id_lop_hoc'];
        $lophoc__Get_By_Id = $lophoc->lophoc__Get_By_Id($id_lop_hoc);
        $khoahoc__Get_All = $khoahoc->khoahoc__Get_All();
        $nganhhoc__Get_All = $nganhhoc->nganhhoc__Get_All();
    ?>

    <form class="row form" action="quan-ly-lop-hoc/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_lop_hoc" value="<?=$lophoc__Get_By_Id->id_lop_hoc?>">
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
                        <label for="">Khóa học <span class="color-crimson">(*)</span></label>

                        <select class="form-control" name="id_khoa_hoc" required>
                            <option value="<?=$lophoc__Get_By_Id->id_khoa_hoc?>">
                                <?=$khoahoc->khoahoc__Get_By_Id($lophoc__Get_By_Id->id_khoa_hoc)->ten_khoa_hoc?>
                            </option>
                            <?php foreach ($khoahoc__Get_All as $item):?>
                            <?php if($item->id_khoa_hoc != $lophoc__Get_By_Id->id_khoa_hoc):?>
                            <option value="<?=$item->id_khoa_hoc?>"><?=$item->ten_khoa_hoc?></option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Ngành học <span class="color-crimson">(*)</span></label>
                        <select class="form-control" name="id_nganh_hoc" required>
                            <option value="<?=$lophoc__Get_By_Id->id_nganh_hoc?>">
                                <?=$nganhhoc->nganhhoc__Get_By_Id($lophoc__Get_By_Id->id_nganh_hoc)->ten_nganh_hoc?>
                            </option>
                            <?php foreach ($nganhhoc__Get_All as $item):?>
                            <?php if($item->id_nganh_hoc != $lophoc__Get_By_Id->id_nganh_hoc):?>
                            <option value="<?=$item->id_nganh_hoc?>"><?=$item->ten_nganh_hoc?></option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tên lớp học <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_lop_hoc" name="ten_lop_hoc" class="form-control" required
                            placeholder="Nhập tên lớp học" value="<?=$lophoc__Get_By_Id->ten_lop_hoc?>">
                    </div>

                    <div class="form-group">
                        <label for="">Ghi chú</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                            placeholder="Nhập ghi chú"><?=$lophoc__Get_By_Id->ghi_chu?></textarea>
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