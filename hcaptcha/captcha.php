<?php
// 2Captcha API key
$apiKey = '79d22e863cecd27334887009668b252a';

// hCaptcha site key and page URL
$siteKey = '463b917e-e264-403f-ad34-34af0ee10294';
$pageUrl = 'https://givesome.org';

// Initialize cURL for 2Captcha request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://2captcha.com/in.php?key=$apiKey&method=hcaptcha&sitekey=$siteKey&pageurl=$pageUrl");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

// Check if the request was successful
if (strpos($response, 'OK|') === 0) {
    $captchaId = substr($response, 3);

    // Poll for the captcha response
    $captchaSolved = false;
    while (!$captchaSolved) {
        sleep(10); // Wait for 10 seconds before polling again

        // Initialize cURL for polling 2Captcha result
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://2captcha.com/res.php?key=$apiKey&action=get&id=$captchaId");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        if (strpos($response, 'OK|') === 0) {
            $captchaSolved = true;
            $captchaToken = substr($response, 3);
        } elseif ($response !== 'CAPCHA_NOT_READY') {
            echo "Error solving captcha: $response\n";
            exit;
        }
    }
} else {
    echo "Error sending captcha request: $response\n";
    exit;
}
?>