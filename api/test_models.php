<?php
$ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['x-goog-api-key: YOUR_API_KEY']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$res = json_decode(curl_exec($ch), true);
if (isset($res['models'])) {
    foreach($res['models'] as $m) {
        if (in_array('generateContent', $m['supportedGenerationMethods'])) {
            echo $m['name'] . "\n";
        }
    }
} else {
    echo "Error parsing response or no models found\n";
    var_dump($res);
}
?>
