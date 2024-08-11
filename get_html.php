<?php
header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");

// https://mdwiki.org/w/rest.php/v1/page/Sympathetic_crashing_acute_pulmonary_edema/html
// https://mdwiki.org/w/rest.php/v1/revision/1420795/html

if (isset($_GET['test'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

$usr_agent = 'WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)';

function get_url_params_result(string $url): string
{
    global $usr_agent;
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    // curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function get_text_html($title, $revision)
{
    // ---
    // replace " " by "_"
    $title = str_replace(" ", "_", $title);
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

function post_url_params_result(string $endPoint, array $params = []): string
{
    global $usr_agent;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $endPoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    // curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $output = curl_exec($ch);
    $url = "{$endPoint}?" . http_build_query($params);
    if ($output === FALSE) {
        echo ("<br>cURL Error: " . curl_error($ch) . "<br>$url");
    }

    curl_close($ch);
    return $output;
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

$sourcelanguage = $_GET['sourcelanguage'] ?? 'en';
$title    = $_GET['title'] ?? '';
$revision = $_GET['revision'] ?? '';
$no_fix = $_GET['nofix'] ?? false;

$HTML_text = "";

if ($title != '' || $revision != '') {
    $HTML_text = get_text_html($title, $revision);
}

if ($HTML_text != '') {
    // ---
    if ($revision == '') {
        // --
        // Special:Redirect/revision/1417517\
        // find revision from HTML_text

        preg_match('/Redirect\/revision\/(\d+)/', $HTML_text, $matches);
        if (isset($matches[1])) {
            $revision = $matches[1];
        }
    }
    // ---
    if (!$no_fix) {
        $HTML_text = fix_it($HTML_text);
    }
    // ---
};


// --

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
