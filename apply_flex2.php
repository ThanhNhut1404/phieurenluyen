<?php
$files = [
    'user/sinh-vien/index.php',
    'user/lop-truong/index.php',
    'user/co-van-hoc-tap/index.php',
    'user/bi-thu-doan-khoa/index.php',
    'user/bi-thu-chi-doan/index.php'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        
        // Mã số sinh viên (flex: 1 -> flex: 0.6)
        $content = preg_replace(
            '/<div class="info-item" style="flex: 1;">\s*<span class="info-label">Mã số sinh viên<\/span>/',
            '<div class="info-item" style="flex: 0.6;">\n                                <span class="info-label">Mã số sinh viên</span>',
            $content
        );

        // Khóa học (flex: 0.7; min-width: 80px -> flex: 0.5; min-width: 60px)
        $content = preg_replace(
            '/<div class="info-item" style="flex: 0\.7; min-width: 80px;">\s*<span class="info-label">Khóa học<\/span>/',
            '<div class="info-item" style="flex: 0.5; min-width: 60px;">\n                                <span class="info-label">Khóa học</span>',
            $content
        );

        // Năm học (flex: 0.7; min-width: 80px -> flex: 0.5; min-width: 60px)
        $content = preg_replace(
            '/<div class="info-item" style="flex: 0\.7; min-width: 80px;">\s*<span class="info-label">Năm học<\/span>/',
            '<div class="info-item" style="flex: 0.5; min-width: 60px;">\n                                <span class="info-label">Năm học</span>',
            $content
        );

        // Học kỳ (flex: 0.6; min-width: 70px -> flex: 0.4; min-width: 50px)
        $content = preg_replace(
            '/<div class="info-item" style="flex: 0\.6; min-width: 70px;">\s*<span class="info-label">Học kỳ<\/span>/',
            '<div class="info-item" style="flex: 0.4; min-width: 50px;">\n                                <span class="info-label">Học kỳ</span>',
            $content
        );

        // Khoa/ Bộ môn (flex: 1.5 -> flex: 2.2)
        $content = preg_replace(
            '/<div class="info-item" style="flex: 1\.5;">\s*<span class="info-label">Khoa\/ Bộ môn<\/span>/',
            '<div class="info-item" style="flex: 2.2;">\n                                <span class="info-label">Khoa/ Bộ môn</span>',
            $content
        );

        // Lớp (flex: 1.2 -> flex: 1.6)
        $content = preg_replace(
            '/<div class="info-item" style="flex: 1\.2;">\s*<span class="info-label">Lớp<\/span>/',
            '<div class="info-item" style="flex: 1.6;">\n                                <span class="info-label">Lớp</span>',
            $content
        );
        
        file_put_contents($path, $content);
        echo "Updated $file\n";
    }
}
