<div class="dashboard-container">
    <form class="row form" action="../../auth/action.php?req=doi-mat-khau" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_tai_khoan" value="<?=$_SESSION['user']->id_tai_khoan?>">
        <div class="col-12 mx-auto" style="max-width: 550px;">
            <div class="ketqua-card custom-card">
                <div class="ketqua-header">
                    <h3 class="ketqua-title">Đổi mật khẩu</h3>
                </div>
                <div class="p-4">
                
                <div class="form-group">
                    <label class="label-sidebar" style="color: #1d4ed8; font-weight: 600;">Email</label>
                    <input type="text" readonly class="form-control" id="staticEmail2"
                        value="<?=$_SESSION['user']->email?>">
                </div>
                <?php if(isset($_GET['status'])): ?>
                    <?php if($_GET['status'] == 'success'): ?>
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                if(window.Toast) {
                                    Toast.fire({
                                        icon: 'success',
                                        title: 'Đổi mật khẩu thành công! Vui lòng đăng nhập lại.'
                                    }).then(() => {
                                        window.location.href = '../../auth/action.php?req=dang-xuat&role=user';
                                    });
                                }
                            });
                        </script>
                    <?php elseif($_GET['status'] == 'failed'): ?>
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                if(window.Toast) {
                                    Toast.fire({
                                        icon: 'error',
                                        title: 'Có lỗi xảy ra, vui lòng thử lại!'
                                    });
                                }
                            });
                        </script>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="form-group">
                    <label class="label-sidebar" for="mat_khau_cu" style="color: #1d4ed8; font-weight: 600;">Mật khẩu cũ <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="mat_khau_cu" placeholder="Nhập mật khẩu hiện tại" name="mat_khau_cu" required>
                        <div class="input-group-append" style="cursor: pointer;" onclick="togglePassword('mat_khau_cu', 'icon_cu')">
                            <span class="input-group-text"><i class="fas fa-eye" id="icon_cu"></i></span>
                        </div>
                    </div>
                    <?php if(isset($_GET['status']) && $_GET['status'] == 'wrong_old_password'): ?>
                        <small class="text-danger mt-1 d-block">Mật khẩu cũ không chính xác!</small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="label-sidebar" for="mat_khau_moi" style="color: #1d4ed8; font-weight: 600;">Mật khẩu mới <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="mat_khau_moi" placeholder="Nhập mật khẩu mới" name="mat_khau_moi" required>
                        <div class="input-group-append" style="cursor: pointer;" onclick="togglePassword('mat_khau_moi', 'icon_moi')">
                            <span class="input-group-text"><i class="fas fa-eye" id="icon_moi"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label-sidebar" for="xac_nhan_mat_khau" style="color: #1d4ed8; font-weight: 600;">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="xac_nhan_mat_khau" placeholder="Nhập lại mật khẩu mới" name="xac_nhan_mat_khau" required>
                        <div class="input-group-append" style="cursor: pointer;" onclick="togglePassword('xac_nhan_mat_khau', 'icon_xac_nhan')">
                            <span class="input-group-text"><i class="fas fa-eye" id="icon_xac_nhan"></i></span>
                        </div>
                    </div>
                    <?php if(isset($_GET['status']) && $_GET['status'] == 'password_mismatch'): ?>
                        <small class="text-danger mt-1 d-block">Mật khẩu mới và xác nhận mật khẩu không khớp!</small>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn-submit-success mt-2">Cập nhật</button>
                </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function togglePassword(inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon = document.getElementById(iconId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>
