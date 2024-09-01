<?php
header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");

// get_html/rest_v1_page.php?title=&revision=

if (isset($_GET['test'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}
require_once __DIR__ . "/post.php";
require_once __DIR__ . "/helps.php";

$title = filter_input(INPUT_GET, 'title', FILTER_SANITIZE_STRING) ?? '';
$revision = filter_input(INPUT_GET, 'revision', FILTER_SANITIZE_STRING) ?? '';

$HTML_text = "";

$domain = "";

if (isset($_GET['wmcloud'])) {
    $domain = "https://mdwiki.wmcloud.org";
};

if ($title != '' || $revision != '') {
    $HTML_text = get_text_html($title, $revision, $domain = $domain);
    $jsonData = [
        "text" => $HTML_text
    ];

    // Encode data as JSON with appropriate options
    // $jsonOutput = json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $jsonOutput = json_encode($jsonData);

    // Output the JSON
    echo $jsonOutput;
}
