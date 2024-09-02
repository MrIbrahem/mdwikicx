<?php
header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");

if (isset($_GET['test'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

require_once __DIR__ . "/m.php";
require_once __DIR__ . "/fixiit.php";
require_once __DIR__ . "/post.php";
require_once __DIR__ . "/helps.php";

$sourcelanguage = $_GET['sourcelanguage'] ?? 'en';
$title = $_GET['title'] ?? '';
$revision = $_GET['revision'] ?? '';
$section0 = $_GET['section0'] ?? '';

$no_fix = $_GET['nofix'] ?? '';
$printetxt = $_GET['printetxt'] ?? '';
$rmstyle = $_GET['rmstyle'] ?? '';


$HTML_text = "";

if ($title != '' || $revision != '') {
    $HTML_text = get_text_html($title, $revision);
    $test_js = json_decode($HTML_text, true);
    // {"errorKey":"rest-nonexistent-title","messageTranslations":{"en":"The specified title (Sympathetic_crasxhing_acute_pulmonary_edema) does not exist"},"httpCode":404,"httpReason":"Not Found"}
    if ($test_js != false && isset($test_js['errorKey'])) {
        $HTML_text = "";
        $message = $test_js['messageTranslations']['en'] ?? 'The specified title does not exist';
        print_data($revision, $HTML_text, $sourcelanguage, $title, $error = $message);
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
        $HTML_text = fix_it($HTML_text, $revision);

        // if (is_bad_fix($HTML_text)) {
        //     $error = "Fixing failed";
        // }
    }
    // Decode HTML_text using htmlentities
    // $HTML_text = utf8_encode($HTML_text);

    $HTML_text = dom_it($HTML_text);

    // $HTML_text = str_replace("<section", "\n<section", $HTML_text);
}

if ($printetxt != '') {
    echo $HTML_text;
    return;
}
print_data($revision, $HTML_text, $sourcelanguage, $title, $error = $error);
