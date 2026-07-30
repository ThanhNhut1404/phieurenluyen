-- Thêm các cột phân quyền vào bảng muc
ALTER TABLE `muc` 
ADD COLUMN `quyen_sv` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1: Sinh viên được chấm, 0: Không',
ADD COLUMN `quyen_lt` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1: Lớp trưởng/BCS được chấm, 0: Không',
ADD COLUMN `quyen_btdk` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1: Bí thư đoàn khoa được chấm, 0: Không',
ADD COLUMN `quyen_gv` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1: Giảng viên được chấm, 0: Không';
