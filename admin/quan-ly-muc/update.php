    <?php 
        require '../../models/getModel.php';
        $id_muc = $_POST['id_muc'];
        $muc__Get_By_Id = $muc->muc__Get_By_Id($id_muc);
        $khoan__Get_All = $khoan->khoan__Get_All();
    ?>

    <form class="row form" action="quan-ly-muc/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_muc" value="<?=$muc__Get_By_Id->id_muc?>">
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
                        <label for="">Khoản <span class="color-crimson">(*)</span></label>
                        <select class="form-control" name="id_khoan" required>
                            <option value="<?=$muc__Get_By_Id->id_khoan?>">
                                <?=$khoan->khoan__Get_By_Id($muc__Get_By_Id->id_khoan)->ten_khoan?>
                            </option>
                            <?php foreach ($khoan__Get_All as $item):?>
                            <?php if($item->id_khoan != $muc__Get_By_Id->id_khoan):?>
                            <option value="<?=$item->id_khoan?>"><?=$item->ten_khoan?></option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tên khoản <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_muc" name="ten_muc" class="form-control" required
                            placeholder="Nhập tên khoản" value="<?=$muc__Get_By_Id->ten_muc?>">
                    </div>
                    <div class="form-group">
                        <label for="">Nội dung chi tiết</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control" placeholder="Nhập nội dung chi tiết"
                            required><?=$muc__Get_By_Id->ghi_chu?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Thứ tự <span class="color-crimson">(*)</span></label>
                        <input type="text" id="thu_tu" name="thu_tu" class="form-control" required
                            placeholder="Nhập thứ tự" value="<?=$muc__Get_By_Id->thu_tu?>">
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