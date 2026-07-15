    <?php 
        require '../../models/getModel.php';
        $id_nam_hoc = $_POST['id_nam_hoc'];
        $namhoc__Get_By_Id = $namhoc->namhoc__Get_By_Id($id_nam_hoc);
    ?>

    <form class="row form" action="quan-ly-nam-hoc/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_nam_hoc" value="<?=$namhoc__Get_By_Id->id_nam_hoc?>">
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
                        <label for="">Tên năm học <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_nam_hoc" name="ten_nam_hoc" class="form-control" required
                            placeholder="Nhập tên năm học" value="<?=$namhoc__Get_By_Id->ten_nam_hoc?>">
                    </div>
                    <div class="form-group">
                        <label for="">Ghi chú</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                            placeholder="Nhập ghi chú"><?=$namhoc__Get_By_Id->ghi_chu?></textarea>
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