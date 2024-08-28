<?php
header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");

require __DIR__ . "/m.php";
require __DIR__ . "/post.php";

if (isset($_GET['test'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
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
function is_bad_fix($text)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($text);
    // ---
    $bad_tags = [
        "style",
        "link"
    ];
    foreach ($bad_tags as $tag) {
        $ems = $dom->getElementsByTagName($tag);
        // ---
        foreach ($ems as $ent) {
            $ent->parentNode->removeChild($ent);
        }
    }
    // ---
    $elements = $dom->getElementsByTagName('section');
    // ---
    foreach ($elements as $element) {
        $t = trim($element->textContent);
        if ($t == "") {
            return true;
        }
    }
    // ---
    return false;
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
$title = $_GET['title'] ?? '';
$revision = $_GET['revision'] ?? '';
$section0 = $_GET['section0'] ?? '';

$no_fix = $_GET['nofix'] ?? '';
$printetxt = $_GET['printetxt'] ?? '';
$rmstyle = $_GET['rmstyle'] ?? '';

function print_data($revision, $HTML_text, $error = "")
{
    global $sourcelanguage, $title;
    // ---
    if ($sourcelanguage == "mdwiki") {
        $sourcelanguage = "en";
    }
    // ---
    $jsonData = [
        "sourceLanguage" => $sourcelanguage,
        "title" => $title,
        "revision" => $revision,
        "segmentedContent" => $HTML_text,
        "categories" => []
    ];
    // ---
    if ($error != "") {
        $jsonData['error'] = $error;
    }
    // ---
    // Encode data as JSON with appropriate options
    // $jsonOutput = json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $jsonOutput = json_encode($jsonData);

    // Output the JSON
    echo $jsonOutput;
}

$HTML_text = "";


function get_medwiki_html($title)
{
    // ---
    // replace " " by "_"
    $title = "Md:" . str_replace(" ", "_", $title);
    // fix / in title
    $title = str_replace("/", "%2F", $title);
    // ---
    $url = "https://medwiki.toolforge.org/w/rest.php/v1/page/" . $title . "/html";
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

if ($title != '') {
    $HTML_text = get_medwiki_html($title);
    $test_js = json_decode($HTML_text, true);
    // {"errorKey":"rest-nonexistent-title","messageTranslations":{"en":"The specified title (Sympathetic_crasxhing_acute_pulmonary_edema) does not exist"},"httpCode":404,"httpReason":"Not Found"}
    if ($test_js != false && isset($test_js['errorKey'])) {
        $HTML_text = "";
        $message = $test_js['messageTranslations']['en'] ?? 'The specified title does not exist';
        print_data($revision, $HTML_text, $error = $message);
        // http_response_code(404);
        exit(1);
    }
}
$error = '';

if ($HTML_text != '') {
    // {\"wt\":\"Drugbox\\n\",\"href\":\".\/Template:Drugbox\"}
    // replace Drugbox with Infobox drug
    $HTML_text = preg_replace("/\bDrugbox\b/", "Infobox drug", $HTML_text);

    if ($revision == '') {
        $revision = get_revision($HTML_text);
    }

    $HTML_text = do_changes($HTML_text, $section0);

    if ($rmstyle != '') {
        $HTML_text = remove_all_style_tags($HTML_text);
    }

    if ($no_fix == '') {
        $HTML_text = fix_it($HTML_text);

        // if (is_bad_fix($HTML_text)) {
        //     $error = "Fixing failed";
        // }
    }
    // Decode HTML_text using htmlentities
    // $HTML_text = utf8_encode($HTML_text);

    $HTML_text = dom_it($HTML_text);
    $HTML_text = str_replace("https://medwiki.toolforge.org/md/", "https://en.wikipedia.org/w/", $HTML_text);
    $HTML_text = str_replace("https://medwiki.toolforge.org/w/", "https://en.wikipedia.org/w/", $HTML_text);
    $HTML_text = str_replace("https://medwiki.toolforge.org/wiki/", "https://en.wikipedia.org/wiki/", $HTML_text);

    // $HTML_text = str_replace("<section", "\n<section", $HTML_text);
}

if ($printetxt != '') {
    echo $HTML_text;
    return;
}
print_data($revision, $HTML_text, $error = $error);
