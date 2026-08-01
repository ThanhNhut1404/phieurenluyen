-- 1. Thêm cột Giới hạn số lượng mục vào bảng Khoản
ALTER TABLE `khoan` ADD COLUMN `so_luong_muc` INT NOT NULL DEFAULT 10 COMMENT 'Số lượng mục tối đa';

-- 2. Thêm cột Điểm tối đa vào bảng Mục
ALTER TABLE `muc` ADD COLUMN `diem_toi_da` INT NOT NULL DEFAULT 0 COMMENT 'Điểm tối đa của mục';

-- 3. Các cột phục vụ chức năng Xoá mềm (Soft Delete) để bảo toàn lịch sử Mẫu phiếu
ALTER TABLE `dieu` ADD COLUMN `is_deleted` TINYINT(1) DEFAULT 0 COMMENT '0: Bình thường, 1: Đã xoá mềm';
ALTER TABLE `khoan` ADD COLUMN `is_deleted` TINYINT(1) DEFAULT 0 COMMENT '0: Bình thường, 1: Đã xoá mềm';
ALTER TABLE `muc` ADD COLUMN `is_deleted` TINYINT(1) DEFAULT 0 COMMENT '0: Bình thường, 1: Đã xoá mềm';

-- 4. Thêm cột cờ báo hiệu yêu cầu sinh viên nộp Minh chứng cho bảng Mục
ALTER TABLE `muc` ADD COLUMN `co_minh_chung` TINYINT(1) DEFAULT 0 COMMENT '0: Không yêu cầu, 1: Bắt buộc nộp minh chứng';

-- 5. Thêm cột id_muc vào bảng minhchung để liên kết minh chứng với Mục
ALTER TABLE `minhchung` ADD COLUMN `id_muc` INT NULL COMMENT 'Mục tương ứng với minh chứng, NULL nếu là minh chứng chung';
