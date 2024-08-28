<?php

// get_html/rest_v1_page.php?title=&revision=

header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");

require __DIR__ . "/post.php";

if (isset($_GET['test'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

function get_revision($HTML_text)
{
    if ($HTML_text != '') {
        // Special:Redirect/revision/1417517\
        // find revision from HTML_text

        preg_match('/Redirect\/revision\/(\d+)/', $HTML_text, $matches);
        if (isset($matches[1])) {
            $revision = $matches[1];
            return $revision;
        }
    }
    return "";
};

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
