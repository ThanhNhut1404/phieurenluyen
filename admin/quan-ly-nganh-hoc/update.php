    <?php 
        require '../../models/getModel.php';
        $id_nganh_hoc = $_POST['id_nganh_hoc'];
        $nganhhoc__Get_By_Id = $nganhhoc->nganhhoc__Get_By_Id($id_nganh_hoc);
        $khoa__Get_All = $khoa->khoa__Get_All();
    ?>

    <form class="row form" action="quan-ly-nganh-hoc/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_nganh_hoc" value="<?=$nganhhoc__Get_By_Id->id_nganh_hoc?>">
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
                        <label for="">Khoa <span class="color-crimson">(*)</span></label>
                        <select class="form-control" name="id_khoa" required>
                            <option value="<?=$nganhhoc__Get_By_Id->id_khoa?>">
                                <?=$khoa->khoa__Get_By_Id($nganhhoc__Get_By_Id->id_khoa)->ten_khoa?>
                            </option>
                            <?php foreach ($khoa__Get_All as $item):?>
                            <?php if($item->id_khoa != $nganhhoc__Get_By_Id->id_khoa):?>
                            <option value="<?=$item->id_khoa?>"><?=$item->ten_khoa?></option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tên ngành học <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_nganh_hoc" name="ten_nganh_hoc" class="form-control" required
                            placeholder="Nhập tên ngành học" value="<?=$nganhhoc__Get_By_Id->ten_nganh_hoc?>">
                    </div>
                    <div class="form-group">
                        <label for="">Ghi chú</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                            placeholder="Nhập ghi chú"><?=$nganhhoc__Get_By_Id->ghi_chu?></textarea>
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