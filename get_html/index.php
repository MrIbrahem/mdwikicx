<?php
header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");

require __DIR__ . "/m.php";
require __DIR__ . "/post.php";

if (isset($_GET['test'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

function get_text_html($title, $revision)
{
    // ---
    // replace " " by "_"
    $title = str_replace(" ", "_", $title);
    // fix / in title
    $title = str_replace("/", "%2F", $title);
    // ---
    $url = "https://mdwiki.org/w/rest.php/v1/page/" . $title . "/html";
    // ---
    if ($revision != '') {
        $url = "https://mdwiki.org/w/rest.php/v1/revision/" . $revision . "/html";
    }
    // ---
    $text = "";
    // ---
    try {
        $res = get_url_params_result($url);
        if ($res) {
            $text = $res;
        }
    } catch (Exception $e) {
        $text = "";
    };
    // ---
    return $text;
}


function fix_it($text)
{
    $url = 'https://ncc2c.toolforge.org/textp';

    if ($_SERVER['SERVER_NAME'] == 'localhost') {
        $url = 'http://localhost:8000/textp';
    }

    $data = ['html' => $text];
    $response = post_url_params_result($url, $data);

    // Handle the response from your API
    if ($response === false) {
        return 'Error: Could not reach API.';
    }

    $data = json_decode($response, true);
    if (isset($data['error'])) {
        return 'Error: ' . $data['error'];
    }

    // Extract the result from the API response
    if (isset($data['result'])) {
        return $data['result'];
    } else {
        return 'Error: Unexpected response format.';
    }
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

$sourcelanguage = $_GET['sourcelanguage'] ?? 'en';
$title    = $_GET['title'] ?? '';
$revision = $_GET['revision'] ?? '';
$no_fix = $_GET['nofix'] ?? false;
$section0 = $_GET['section0'] ?? true;

$HTML_text = "";

if ($title != '' || $revision != '') {
    $HTML_text = get_text_html($title, $revision);
}

if ($revision == '') {
    $revision = get_revision($HTML_text);
}

if ($HTML_text != '') {
    $HTML_text = do_changes($HTML_text, $section0);
}

if (!$no_fix) {
    $HTML_text = fix_it($HTML_text);
}

// Decode HTML_text using htmlentities
$HTML_text = utf8_encode($HTML_text);

$jsonData = [
    "sourceLanguage" => $sourcelanguage,
    "title" => $title,
    "revision" => $revision,
    "segmentedContent" => $HTML_text,
    "categories" => []
];

// Encode data as JSON with appropriate options
// $jsonOutput = json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$jsonOutput = json_encode($jsonData);

// Output the JSON
echo $jsonOutput;
