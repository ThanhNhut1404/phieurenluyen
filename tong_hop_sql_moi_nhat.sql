-- 1. Thêm cột Giới hạn số lượng mục vào bảng Khoản
ALTER TABLE `khoan` ADD COLUMN `so_luong_muc` INT NOT NULL DEFAULT 10 COMMENT 'Số lượng mục tối đa';

-- 2. Thêm cột Điểm tối đa vào bảng Mục
ALTER TABLE `muc` ADD COLUMN `diem_toi_da` INT NOT NULL DEFAULT 0 COMMENT 'Điểm tối đa của mục';
