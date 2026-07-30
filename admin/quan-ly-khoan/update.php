    <?php 
        require '../../models/getModel.php';
        $id_khoan = $_POST['id_khoan'];
        $khoan__Get_By_Id = $khoan->khoan__Get_By_Id($id_khoan);
        $dieu__Get_All = $dieu->dieu__Get_All();

        // quân sửa: Lấy danh sách id_dieu đã được sử dụng (ngoại trừ cái hiện tại)
        $khoan__Get_All = $khoan->khoan__Get_All();
        $used_dieu = [];
        foreach ($khoan__Get_All as $k) {
            if ($k->id_khoan != $id_khoan) {
                $used_dieu[] = $k->id_dieu;
            }
        }
    ?>

    <form class="row form" action="quan-ly-khoan/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_khoan" value="<?=$khoan__Get_By_Id->id_khoan?>">
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
                        <label for="">Điều <span class="color-crimson">(*)</span></label>
                        <select class="form-control" name="id_dieu" required>
                            <option value="<?=$khoan__Get_By_Id->id_dieu?>">
                                <?=$dieu->dieu__Get_By_Id($khoan__Get_By_Id->id_dieu)->ten_dieu?>
                            </option>
                            <?php foreach ($dieu__Get_All as $item):?>
                                <?php if($item->id_dieu != $khoan__Get_By_Id->id_dieu && !in_array($item->id_dieu, $used_dieu)):?>
                                    <option value="<?=$item->id_dieu?>"><?=$item->ten_dieu?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tên khoản <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_khoan" name="ten_khoan" class="form-control" required
                            placeholder="Nhập tên khoản" value="<?=$khoan__Get_By_Id->ten_khoan?>">
                    </div>
                    <div class="form-group">
                        <label for="">Nội dung chi tiết</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control" placeholder="Nhập nội dung chi tiết"
                            required><?=$khoan__Get_By_Id->ghi_chu?></textarea>
                    </div>
                    <!-- quân sửa: Xoá ô nhập Thứ tự ở đây vì đã tự động đồng bộ theo Điều -->
                    <div class="form-group">
                        <label for="">Điểm tối đa</label>
                        <input type="number" id="can_tren" name="can_tren" class="form-control"
                            placeholder="Nhập điểm tối đa" value="<?=$khoan__Get_By_Id->can_tren?>">
                    </div>
                    <!-- quân sửa: Bổ sung ô nhập số lượng mục -->
                    <div class="form-group">
                        <label for="">Số lượng mục tối đa</label>
                        <input type="number" id="so_luong_muc" name="so_luong_muc" class="form-control" required
                            placeholder="Nhập giới hạn số lượng mục" value="<?=isset($khoan__Get_By_Id->so_luong_muc) ? $khoan__Get_By_Id->so_luong_muc : 10?>">
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