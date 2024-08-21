<?php
// ضبط نوع المحتوى
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// عنوان API الهدف
$api_url = "https://mdwiki.org/w/api.php";

// الحصول على طريقة الطلب (GET أو POST)
$request_method = $_SERVER['REQUEST_METHOD'];

// تهيئة بيانات POST إن كانت موجودة
$post_data = ($request_method === 'POST') ? file_get_contents('php://input') : '';

// تهيئة طلب cURL
$ch = curl_init();

// إذا كان الطلب GET، نضيف المعاملات إلى URL
if ($request_method === 'GET') {
    $query_string = $_SERVER['QUERY_STRING'];
    $url = $api_url . '?' . $query_string;
    curl_setopt($ch, CURLOPT_URL, $url);
} else {
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
}

$usr_agent = "WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)";

// إعدادات cURL العامة
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);

// تنفيذ الطلب
$response = curl_exec($ch);

// التأكد من عدم وجود أخطاء في التنفيذ
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($http_code !== 200) {
    echo 'Error: API request failed with status code ' . $http_code;
}

// إغلاق طلب cURL
curl_close($ch);

// عرض الاستجابة
echo $response;

?>
