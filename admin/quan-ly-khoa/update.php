    <?php 
        session_start();
        // Nhựt sửa lỗi: chặn gọi trực tiếp form sửa khi chưa đăng nhập admin.
        if (!isset($_SESSION['admin'])) {
            http_response_code(403);
            exit('Forbidden');
        }

        require '../../models/getModel.php';

        // Nhựt sửa lỗi: tạo CSRF token an toàn hơn, có fallback nếu random_bytes() lỗi.
        if (empty($_SESSION['csrf_token'])) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Throwable $e) {
                $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
            }
        }

        if (!function_exists('khoa_update_escape')) {
            function khoa_update_escape($value) {
                return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            }
        }

        // Nhựt sửa lỗi: giữ lại dữ liệu đã nhập cho form sửa nếu trước đó có validate lỗi.
        if (!function_exists('khoa_update_old_value')) {
            function khoa_update_old_value($field, $id_khoa, $default = '') {
                if (isset($_SESSION['khoa_old_input']) && is_array($_SESSION['khoa_old_input'])) {
                    $old = $_SESSION['khoa_old_input'];
                    if (isset($old['context'], $old['id_khoa']) && $old['context'] === 'update' && (int)$old['id_khoa'] === (int)$id_khoa && isset($old[$field])) {
                        return $old[$field];
                    }
                }
                return $default;
            }
        }

        // Nhựt sửa lỗi: kiểm tra id_khoa hợp lệ trước khi lấy dữ liệu.
        $id_khoa = filter_input(INPUT_POST, 'id_khoa', FILTER_VALIDATE_INT);
        if (!$id_khoa || $id_khoa < 1) {
            http_response_code(400);
            exit('<div class="alert alert-danger">ID khoa không hợp lệ.</div>');
        }

        $khoa__Get_By_Id = $khoa->khoa__Get_By_Id($id_khoa);
        if (!$khoa__Get_By_Id) {
            http_response_code(404);
            exit('<div class="alert alert-danger">Không tìm thấy khoa cần cập nhật.</div>');
        }
    ?>

    <form class="row form" action="quan-ly-khoa/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_khoa" value="<?=(int)$khoa__Get_By_Id->id_khoa?>">
        <input type="hidden" name="csrf_token" value="<?=khoa_update_escape($_SESSION['csrf_token'])?>">
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
                        <!-- Nhựt sửa lỗi: giới hạn tên khoa ở client khớp với validate server-side và DB. -->
                        <input type="text" id="ten_khoa" name="ten_khoa" class="form-control" required maxlength="50"
                            value="<?=khoa_update_escape(khoa_update_old_value('ten_khoa', $khoa__Get_By_Id->id_khoa, $khoa__Get_By_Id->ten_khoa))?>" placeholder="Nhập tên khoa">
                    </div>
                    <div class="form-group">
                        <label for="">Ghi chú</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control" maxlength="2000"
                            placeholder="Nhập ghi chú"><?=khoa_update_escape(khoa_update_old_value('ghi_chu', $khoa__Get_By_Id->id_khoa, $khoa__Get_By_Id->ghi_chu))?></textarea>
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
