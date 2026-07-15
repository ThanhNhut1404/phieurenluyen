    <?php 
        require '../../models/getModel.php';
        $id_phan_quyen = $_POST['id_phan_quyen'];
        $phanquyen__Get_By_Id = $phanquyen->phanquyen__Get_By_Id($id_phan_quyen);
    ?>

    <form class="row form" action="quan-ly-phan-quyen/action.php?req=update" method="post"
        enctype="multipart/form-data">
        <input type="hidden" name="id_phan_quyen" value="<?=$phanquyen__Get_By_Id->id_phan_quyen?>">
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
                        <label for="">Tên phân quyền <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_phan_quyen" name="ten_phan_quyen" class="form-control" required
                            placeholder="Nhập tên phân quyền" value="<?=$phanquyen__Get_By_Id->ten_phan_quyen?>">
                    </div>
                    <div class="form-group">
                        <label for="">Cấp bậc</label>
                        <select name="cap_bac" class="form-control" required>
                            <option value="0" <?=$phanquyen__Get_By_Id->cap_bac == 0 ? "selected" : ""?>>Admin</option>
                            <option value="1" <?=$phanquyen__Get_By_Id->cap_bac == 1 ? "selected" : ""?>>Manager
                            </option>
                            <option value="2" <?=$phanquyen__Get_By_Id->cap_bac == 2 ? "selected" : ""?>>Sinh viên
                            </option>
                            <option value="3" <?=$phanquyen__Get_By_Id->cap_bac == 3 ? "selected" : ""?>>Giảng viên
                            </option>
                        </select>

                    </div>
                    <div class="form-group">
                        <label for="">Ghi chú</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                            placeholder="Nhập ghi chú"><?=$phanquyen__Get_By_Id->ghi_chu?></textarea>
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