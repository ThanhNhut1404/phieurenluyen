    <?php 
        require '../../models/getModel.php';
        $id_mau_phieu = $_POST['id_mau_phieu'];
        $mauphieu__Get_By_Id = $mauphieu->mauphieu__Get_By_Id($id_mau_phieu);
        $dieu__Get_All = $dieu->dieu__Get_All();
    ?>

    <form class="row form" action="quan-ly-mau-phieu/action.php?req=update_dieu" method="post"
        enctype="multipart/form-data">
        <input type="hidden" name="id_mau_phieu" value="<?=$mauphieu__Get_By_Id->id_mau_phieu?>">
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
                        <label for="">Tên mẫu phiếu <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_mau_phieu" name="ten_mau_phieu" class="form-control" required
                            placeholder="Nhập tên mẫu phiếu" value="<?=$mauphieu__Get_By_Id->ten_mau_phieu?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Ghi chú</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control" placeholder="Nhập ghi chú"
                            readonly><?=$mauphieu__Get_By_Id->ghi_chu?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Chọn lại điều</label>
                        <select class="duallistboxs" multiple="multiple" name="id_dieu[]" required>
                            <?php foreach($dieu__Get_All as $item):?>
                            <option value="<?=$item->id_dieu?>"><?=$item->ten_dieu?></option>
                            <?php endforeach; ?>
                        </select>
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
    <script>
$('.duallistboxs').bootstrapDualListbox()
    </script>