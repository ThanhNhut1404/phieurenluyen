    <?php 
        require '../../models/getModel.php';
        $id_dieu = $_POST['id_dieu'];
        $dieu__Get_By_Id = $dieu->dieu__Get_By_Id($id_dieu);
    ?>

    <form class="row form" action="quan-ly-dieu/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_dieu" value="<?=$dieu__Get_By_Id->id_dieu?>">
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
                        <label for="">Tên điều <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_dieu" name="ten_dieu" class="form-control" required
                            placeholder="Nhập tên điều" value="<?=$dieu__Get_By_Id->ten_dieu?>">
                    </div>
                    <div class="form-group">
                        <label for="">Nội dung chi tiết</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control" placeholder="Nhập nội dung chi tiết"
                            required><?=$dieu__Get_By_Id->ghi_chu?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Thứ tự <span class="color-crimson">(*)</span></label>
                        <input type="text" id="thu_tu" name="thu_tu" class="form-control" required
                            placeholder="Nhập thứ tự" value="<?=$dieu__Get_By_Id->thu_tu?>">
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