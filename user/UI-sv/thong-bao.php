<?php
    // Bỏ qua lấy logic vì header.php đã gọi fetch_notifications.php
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Thông báo</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách thông báo của bạn</h3>
                    </div>
                    <div class="card-body">
                        <?php if (count($list_thong_bao) > 0): ?>
                            <div class="list-group">
                                <?php foreach ($list_thong_bao as $tb): 
                                    $time = date('H:i d/m/Y', strtotime($tb->ngay_tao));
                                ?>
                                    <a href="#" class="list-group-item list-group-item-action mb-2 shadow-sm rounded notif-item" 
                                       data-id="<?php echo $tb->id_thong_bao; ?>"
                                       data-title="<?php echo htmlspecialchars($tb->tieu_de); ?>"
                                       data-sender="<?php echo htmlspecialchars($tb->nguoi_gui); ?>"
                                       data-time="<?php echo $time; ?>"
                                       data-content="<?php echo htmlspecialchars($tb->noi_dung); ?>"
                                       style="<?php echo $tb->is_read ? 'background-color: #f8f9fa; opacity: 0.8;' : 'background-color: #ffffff; border-left: 4px solid #1d4ed8;'; ?>">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1" style="<?php echo !$tb->is_read ? 'font-weight: bold; color: #111827;' : ''; ?>">
                                                <?php echo htmlspecialchars($tb->tieu_de); ?>
                                            </h5>
                                            <small><?php echo $time; ?></small>
                                        </div>
                                        <p class="mb-1" style="color: #4b5563; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.4;">
                                            <?php echo htmlspecialchars($tb->noi_dung); ?>
                                        </p>
                                        <small class="text-muted">Gửi bởi: <?php echo htmlspecialchars($tb->nguoi_gui); ?></small>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center p-5 text-muted">
                                <i class="ri-notification-off-line" style="font-size: 48px; color: #cbd5e1;"></i>
                                <p class="mt-3">Bạn không có thông báo nào.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Hiển thị thông báo chi tiết -->
<div class="modal fade" id="modal-thong-bao" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="notif-modal-title">Tiêu đề thông báo</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3 border-bottom pb-2">
                    <small class="text-muted"><i class="ri-time-line"></i> <span id="notif-modal-time"></span></small><br>
                    <small class="text-muted"><i class="ri-user-line"></i> Người gửi: <span id="notif-modal-sender"></span></small>
                </div>
                <div id="notif-modal-content" style="white-space: pre-line; line-height: 1.6; font-size: 15px;">
                    Nội dung chi tiết...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notifItems = document.querySelectorAll('.notif-item');
        notifItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                const sender = this.getAttribute('data-sender');
                const time = this.getAttribute('data-time');
                const content = this.getAttribute('data-content');
                
                document.getElementById('notif-modal-title').textContent = title;
                document.getElementById('notif-modal-sender').textContent = sender;
                document.getElementById('notif-modal-time').textContent = time;
                document.getElementById('notif-modal-content').textContent = content;
                
                $('#modal-thong-bao').modal('show');
                
                // Đánh dấu đã đọc bằng AJAX
                if (this.style.backgroundColor !== 'rgb(248, 249, 250)' && this.style.backgroundColor !== '#f8f9fa') {
                    const self = this;
                    fetch('../../api/read_thong_bao.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id_thong_bao=' + id
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.status === 'success') {
                            self.style.backgroundColor = '#f8f9fa';
                            self.style.opacity = '0.8';
                            self.style.borderLeft = 'none';
                            const h5 = self.querySelector('h5');
                            if(h5) {
                                h5.style.fontWeight = 'normal';
                            }
                        }
                    });
                }
            });
        });
    });
</script>
