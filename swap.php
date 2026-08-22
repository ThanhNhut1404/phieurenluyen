<?php
$files = [
    'user/sinh-vien/index.php',
    'user/sinh-vien/chi-tiet.php',
    'user/lop-truong/index.php',
    'user/bi-thu-chi-doan/index.php',
    'user/co-van-hoc-tap/index.php',
    'user/bi-thu-doan-khoa/index.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Find the block of 3 divs
        if (preg_match('/(<div[^>]*><strong[^>]*>Điểm rèn luyện:<\/strong>.*?<\/div>)\s*(<div[^>]*><strong[^>]*>Xếp loại:<\/strong>.*?<\/div>)\s*(<div[^>]*><strong[^>]*>Ngày xếp loại:<\/strong>.*?<\/div>)/is', $content, $matches)) {
            
            $diem = $matches[1];
            $xeploai = $matches[2];
            $ngay = $matches[3];
            
            // New order: Ngày xếp loại -> Điểm rèn luyện -> Xếp loại
            $new_block = $ngay . "\n                        " . $diem . "\n                        " . $xeploai;
            
            $content = str_replace($matches[0], $new_block, $content);
            file_put_contents($file, $content);
            echo "Updated: $file\n";
        } else {
            echo "Not matched: $file\n";
        }
    }
}
?>
