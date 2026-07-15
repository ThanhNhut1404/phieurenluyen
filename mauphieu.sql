-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.28-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.4.0.6659
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for phieurenluyen
CREATE DATABASE IF NOT EXISTS `phieurenluyen` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `phieurenluyen`;

-- Dumping structure for table phieurenluyen.bocauhoi
CREATE TABLE IF NOT EXISTS `bocauhoi` (
  `id_bo_cau_hoi` int(11) NOT NULL AUTO_INCREMENT,
  `id_mau_phieu` int(11) NOT NULL DEFAULT 0,
  `id_dieu` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_bo_cau_hoi`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table phieurenluyen.bocauhoi: ~5 rows (approximately)
INSERT INTO `bocauhoi` (`id_bo_cau_hoi`, `id_mau_phieu`, `id_dieu`) VALUES
	(1, 1, 1),
	(2, 1, 2),
	(3, 1, 3),
	(4, 1, 4),
	(5, 1, 5);

-- Dumping structure for table phieurenluyen.dieu
CREATE TABLE IF NOT EXISTS `dieu` (
  `id_dieu` int(11) NOT NULL AUTO_INCREMENT,
  `ten_dieu` text DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `thu_tu` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_dieu`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table phieurenluyen.dieu: ~5 rows (approximately)
INSERT INTO `dieu` (`id_dieu`, `ten_dieu`, `ghi_chu`, `thu_tu`) VALUES
	(1, 'Điều 1', '(Điều 4 và Điều 8 TT 16)', 1),
	(2, 'Điều 2', '(Điều 5 TT 16)', 2),
	(3, 'Điều 3', '(Điều 6 và Điều 8 TT 16)', 3),
	(4, 'Điều 4', '(Điều 7 TT 16)', 4),
	(5, 'Điều 5', '(Điều 8 TT 16)', 5);

-- Dumping structure for table phieurenluyen.khoan
CREATE TABLE IF NOT EXISTS `khoan` (
  `id_khoan` int(11) NOT NULL AUTO_INCREMENT,
  `ten_khoan` text DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `can_tren` int(11) DEFAULT NULL,
  `thu_tu` int(11) DEFAULT NULL,
  `id_dieu` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_khoan`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table phieurenluyen.khoan: ~5 rows (approximately)
INSERT INTO `khoan` (`id_khoan`, `ten_khoan`, `ghi_chu`, `can_tren`, `thu_tu`, `id_dieu`) VALUES
	(1, 'Đánh giá về ý thức tham gia học tập (0 - 20 điểm):', 'Khoản 1', 20, 1, 1),
	(2, 'Đánh giá về ý thức chấp hành nội quy, quy định trong nhà trường (0 - 25 điểm):', 'Khoản 2', 25, 2, 2),
	(3, 'Đánh giá về ý thức tham gia các hoạt động chính trị, xã hội (đền ơn đáp nghĩa, cứu trợ, tình thương…), văn hóa, văn nghệ, thể thao, phòng chống tội phạm và các tệ nạn xã hội (0 - 20 điểm):', 'Khoản 3', 20, 3, 3),
	(4, 'Đánh giá về ý thức công dân trong quan hệ cộng đồng (0 - 25 điểm):', 'Khoản 4', 25, 4, 4),
	(5, 'Đánh giá về ý thức và kết quả tham gia công tác cán bộ lớp, đoàn thể trong nhà trường (0 - 10 điểm):', 'Khoản 5', 10, 5, 5);

-- Dumping structure for table phieurenluyen.mauphieu
CREATE TABLE IF NOT EXISTS `mauphieu` (
  `id_mau_phieu` int(11) NOT NULL AUTO_INCREMENT,
  `ten_mau_phieu` text DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  PRIMARY KEY (`id_mau_phieu`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table phieurenluyen.mauphieu: ~1 rows (approximately)
INSERT INTO `mauphieu` (`id_mau_phieu`, `ten_mau_phieu`, `ghi_chu`) VALUES
	(1, 'PHIẾU ĐÁNH GIÁ KẾT QUẢ RÈN LUYỆN CỦA SINH VIÊN', 'Căn cứ Thông tư số 16/2015/TT-BGDĐT và Quyết định số 216/QĐ-UBND');

-- Dumping structure for table phieurenluyen.muc
CREATE TABLE IF NOT EXISTS `muc` (
  `id_muc` int(11) NOT NULL AUTO_INCREMENT,
  `ten_muc` text DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `thu_tu` int(11) DEFAULT NULL,
  `id_khoan` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_muc`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table phieurenluyen.muc: ~20 rows (approximately)
INSERT INTO `muc` (`id_muc`, `ten_muc`, `ghi_chu`, `thu_tu`, `id_khoan`) VALUES
	(1, 'Đi học và thực tập đầy đủ các môn học theo lịch học và thực tập (5đ); nếu vi phạm bị trừ 2đ/môn học.', NULL, 1, 1),
	(2, 'Thực hiện đúng quy chế thi, kiểm tra (5đ); nếu vi phạm bị kỷ luật từ khiển trách trở lên (0đ).', NULL, 2, 1),
	(3, 'Không bị cấm thi môn học nào (3đ); nếu bị cấm thi 1 môn trở lên (0đ).', NULL, 3, 1),
	(4, 'Vượt khó, phấn đấu trong học tập, xếp loại học tập học kỳ gần nhất từ khá trở lên (3đ).', NULL, 4, 1),
	(5, 'Tham gia một trong các hoạt động học thuật, nghiên cứu khoa học, các kỳ thi về học thuật hoặc đạt một trong các kỹ năng chuẩn đầu ra theo quy định của trường (riêng kỹ năng chuẩn đầu ra được cộng điểm qua các học kỳ) (4đ).', NULL, 5, 1),
	(6, 'Kết quả sinh hoạt đầu khóa, giữa khóa, cuối khóa (ĐK,GK,CK) đạt và sinh hoạt lớp đầy đủ (9đ); kết quả sinh hoạt ĐK,GK,CK đạt nhưng có bỏ sinh hoạt lớp 1 buổi (7đ), 2 buổi (4đ); kết quả sinh hoạt ĐK,GK,CK không đạt hoặc đạt nhưng bỏ sinh hoạt lớp 3 buổi trở lên (0đ).', NULL, 6, 2),
	(7, 'Thực hiện tốt nội quy, quy định của nhà trường (5đ); nếu vi phạm: làm mất trật tự lớp học trừ (1đ), không mặc đồng phục trừ (2đ), làm mất vệ sinh lớp học trừ (2đ); nếu vi phạm bị kỷ luật từ khiển trách trở lên (0đ).', NULL, 7, 2),
	(8, 'Đóng học phí đúng thời gian quy định hoặc có đơn xin gia hạn được Ban Giám hiệu duyệt (6đ), đóng học phí trễ hạn (0đ).', NULL, 8, 2),
	(9, 'Tham gia nghiêm túc việc lấy ý kiến phản hồi về hoạt động giảng dạy của giảng viên học kỳ 1 năm học 2019 - 2020 (5đ).', NULL, 9, 2),
	(10, 'Tham gia các hoạt động công ích, tình nguyện, công tác xã hội (4đ).', NULL, 10, 3),
	(11, 'Tham gia tuyên truyền, phòng chống tội phạm và các tệ nạn xã hội (3đ).', NULL, 11, 3),
	(12, 'Tham gia từ 3 hoạt động do lớp, chi đoàn, chi hội phát động (2đ).', NULL, 12, 3),
	(13, 'Tham gia từ 3 hoạt động cấp khoa (bao gồm cả đoàn thể) phát động (3đ).', NULL, 13, 3),
	(14, 'Tham gia từ 2 hoạt động cấp trường (bao gồm cả đoàn thể) phát động (4đ).', NULL, 14, 3),
	(15, 'Có giấy khen hoặc giải thưởng các hoạt động cấp khoa (bao gồm cả đoàn thể) (3đ), hoặc cấp trường trở lên (bao gồm cả đoàn thể) (4đ).', NULL, 15, 3),
	(16, 'Chấp hành pháp luật của Nhà nước, quy định của địa phương thường trú, tạm trú, có cập nhật thông tin sinh viên trên cổng sinh viên để quản lý thông tin ngoại trú (10đ), nếu không nộp hoặc khi thay đổi địa chỉ mà không khai báo lại (0đ); nếu vi phạm hành chính (0đ); nếu đang bị truy cứu trách nhiệm hình sự thì không được đánh giá kết quả rèn luyện.', NULL, 16, 4),
	(17, 'Tham gia các hoạt động xã hội có giấy xác nhận thành tích, được biểu dương hoặc khen thưởng (4đ).', NULL, 17, 4),
	(18, 'Có tinh thần chia sẻ, giúp đỡ người có khó khăn, hoạn nạn (đóng góp, ủng hộ về vật chất hoặc tinh thần) (2đ).', NULL, 18, 4),
	(19, 'Tham gia bảo hiểm y tế (BHYT) (7đ), bảo hiểm tai nạn (BHTN) (2đ); không tham gia BHYT và BHTN (0đ).', NULL, 19, 4),
	(20, 'Tham gia công tác lớp là tổ trưởng, tổ phó (1đ) hoặc tham gia ban cán sự lớp, ban chấp hành chi đoàn, chi hội và hoàn thành nhiệm vụ (3đ).', NULL, 20, 5),
	(21, 'Tham gia ban chấp hành đoàn thể cấp khoa (2đ) hoặc cấp trường (3đ).', NULL, 21, 5),
	(22, 'Tập thể lớp hoặc chi đoàn, chi hội được khen thưởng từ cấp khoa trở lên (bao gồm cả đoàn thể) (4đ).', NULL, 22, 5);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
