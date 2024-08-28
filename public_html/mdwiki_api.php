<?php


header("Access-Control-Allow-Origin: *");

$wmcloud = $_GET['wmcloud'] ?? ($_POST['wmcloud'] ?? '');

$api_url = "https://mdwiki.org/w/api.php";

if ($wmcloud !== '') {
    $api_url = "https://mdwiki.wmcloud.org/w/api.php";
}

$request_method = $_SERVER['REQUEST_METHOD'];

$post_data = ($request_method === 'POST') ? file_get_contents('php://input') : '';
$query_string = $_SERVER['QUERY_STRING'] ?? '';

// echo "query_string: " . $query_string . "<br>";
// echo "post_data: " . $post_data . "<br>";
$ch = curl_init();

if ($request_method === 'GET') {
    $url = $api_url . '?' . $query_string;
    curl_setopt($ch, CURLOPT_URL, $url);
} else {
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
}
$usr_agent = "WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)";

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($http_code !== 200) {
    echo 'Error: API request failed with status code ' . $http_code;
}

curl_close($ch);

// get Content-Type from header
$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

header('Content-Type: ' . $content_type);

echo $response;
