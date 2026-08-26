<?php
require_once 'config_ai.php';

class AIService {
    public static function checkMinhChung($base64_image, $ten_sinh_vien, $mssv, $ten_tieu_chi) {
        if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY)) {
            return ['hop_le' => false, 'ly_do' => 'Chưa cấu hình API Key'];
        }

        // Xác định mime_type và loại bỏ tiền tố base64
        $mime_type = "image/jpeg"; // default
        if (strpos($base64_image, 'data:') === 0 && strpos($base64_image, ';base64,') !== false) {
            $parts = explode(';base64,', $base64_image);
            $mime_type = str_replace('data:', '', $parts[0]);
            $base64_image = $parts[1];
        } else if (strpos($base64_image, ',') !== false) {
            $base64_image = explode(',', $base64_image)[1];
        }

        $prompt = "Đây là minh chứng của sinh viên $ten_sinh_vien (MSSV: $mssv) nộp cho mục '$ten_tieu_chi'. \nHãy trả lời chuẩn định dạng JSON với đúng 2 key:\n- 'hop_le' (boolean): true nếu có mộc đỏ (hoặc chữ ký/giáp lai hợp lệ) VÀ nội dung giấy khen/chứng nhận liên quan trực tiếp đến '$ten_tieu_chi'. Nếu sai người, sai nội dung hoặc không có dấu hiệu chứng thực thì là false.\n- 'ly_do' (string): Giải thích rất ngắn gọn (VD: 'Đúng tên, nội dung phù hợp, có mộc đỏ' hoặc 'Sai nội dung chuyên mục').\nKhông output thêm bất kỳ text nào ngoài JSON (không có markdown).";

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt],
                        [
                            "inline_data" => [
                                "mime_type" => $mime_type,
                                "data" => $base64_image
                            ]
                        ]
                    ]
                ]
            ],
            "generationConfig" => [
                "response_mime_type" => "application/json"
            ]
        ];

        $ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-goog-api-key: ' . GEMINI_API_KEY
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout 30s
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Fix SSL for XAMPP

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode == 200 && $response) {
            $result = json_decode($response, true);
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $text = $result['candidates'][0]['content']['parts'][0]['text'];
                // Clean markdown if AI ignored the prompt instruction
                $text = str_replace(['```json', '```'], '', $text);
                $json_res = json_decode(trim($text), true);
                
                if ($json_res && isset($json_res['hop_le'])) {
                    return [
                        'hop_le' => (bool)$json_res['hop_le'],
                        'ly_do' => $json_res['ly_do'] ?? ''
                    ];
                }
            }
        }
        
        return [
            'hop_le' => false, 
            'ly_do' => 'Lỗi kết nối. Mã lỗi: ' . $httpcode . '. Chi tiết: ' . $response
        ];
    }
}
?>
