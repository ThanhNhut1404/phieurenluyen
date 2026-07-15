    <?php 
        require '../../models/getModel.php';
        $id_khoa_hoc = $_POST['id_khoa_hoc'];
        $khoahoc__Get_By_Id = $khoahoc->khoahoc__Get_By_Id($id_khoa_hoc);
    ?>

    <form class="row form" action="quan-ly-khoa-hoc/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_khoa_hoc" value="<?=$khoahoc__Get_By_Id->id_khoa_hoc?>">
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
                        <label for="">Tên khóa học <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_khoa_hoc" name="ten_khoa_hoc" class="form-control" required
                            placeholder="Nhập tên khóa học" value="<?=$khoahoc__Get_By_Id->ten_khoa_hoc?>">
                    </div>
                    <div class="form-group">
                        <label for="">Năm nhập học <span class="color-crimson">(*)</span></label>
                        <input type="number" id="nam_nhap_hoc" name="nam_nhap_hoc" class="form-control" required
                            min="2006" max="2099" maxlength="4" minlength="4" placeholder="Nhập năm nhập học"
                            value="<?=$khoahoc__Get_By_Id->nam_nhap_hoc?>">
                    </div>
                    <div class="form-group">
                        <label for="">Hệ đào tạo <span class="color-crimson">(*)</span></label>
                        <input type="number" id="he_dao_tao" name="he_dao_tao" class="form-control" required min="2"
                            max="8" maxlength="1" minlength="1" placeholder="Nhập số năm đào tạo"
                            value="<?=$khoahoc__Get_By_Id->he_dao_tao?>">
                    </div>
                    <div class="form-group">
                        <label for="">Ghi chú</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                            placeholder="Nhập ghi chú"><?=$khoahoc__Get_By_Id->ghi_chu?></textarea>
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