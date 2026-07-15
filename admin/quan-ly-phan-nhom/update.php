    <?php 
        require '../../models/getModel.php';
        $id_phan_nhom = $_POST['id_phan_nhom'];
        $phannhom__Get_By_Id = $phannhom->phannhom__Get_By_Id($id_phan_nhom);
    ?>

    <form class="row form" action="quan-ly-phan-nhom/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_phan_nhom" value="<?=$phannhom__Get_By_Id->id_phan_nhom?>">
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
                        <label for="">Tên phân nhóm <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_phan_nhom" name="ten_phan_nhom" class="form-control" required
                            placeholder="Nhập tên phân nhóm" value="<?=$phannhom__Get_By_Id->ten_phan_nhom?>">
                    </div>
                    <div class="form-group">
                        <label for="">Cấp bậc</label>
                        <input type="number" id="cap_bac" name="cap_bac" class="form-control" placeholder="Nhập cấp bậc"
                            value="<?=$phannhom__Get_By_Id->cap_bac?>">
                    </div>
                    <div class="form-group">
                        <label for="">Ghi chú</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                            placeholder="Nhập ghi chú"><?=$phannhom__Get_By_Id->ghi_chu?></textarea>
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