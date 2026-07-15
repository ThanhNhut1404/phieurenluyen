    <?php 
        require '../../models/getModel.php';
        $id_khoa = $_POST['id_khoa'];
        $khoa__Get_By_Id = $khoa->khoa__Get_By_Id($id_khoa);
    ?>

    <form class="row form" action="quan-ly-khoa/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_khoa" value="<?=$khoa__Get_By_Id->id_khoa?>">
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
                        <label for="">Tên khoa <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_khoa" name="ten_khoa" class="form-control" required
                            placeholder="Nhập tên khoa" value="<?=$khoa__Get_By_Id->ten_khoa?>">
                    </div>
                    <div class="form-group">
                        <label for="">Ghi chú</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                            placeholder="Nhập ghi chú"><?=$khoa__Get_By_Id->ghi_chu?></textarea>
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