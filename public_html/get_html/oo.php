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

$sourcelanguage = $_GET['sourcelanguage'] ?? 'en';
$title = $_GET['title'] ?? '';
$HTML_text = "";

if ($title != '') {
    $HTML_text = get_medwiki_html($title);
    $test_js = json_decode($HTML_text, true);
    // {"errorKey":"rest-nonexistent-title","messageTranslations":{"en":"The specified title (Sympathetic_crasxhing_acute_pulmonary_edema) does not exist"},"httpCode":404,"httpReason":"Not Found"}
    if ($test_js != false && isset($test_js['errorKey'])) {
        $HTML_text = "";
        $message = $test_js['messageTranslations']['en'] ?? 'The specified title does not exist';
        print_data("", $HTML_text, $sourcelanguage, $title, $error = $message);
        // http_response_code(404);
        exit(1);
    }
}

if ($HTML_text != '') {
    $HTML_text = preg_replace("/\bDrugbox\b/", "Infobox drug", $HTML_text);

    $revision = get_revision($HTML_text);

    $HTML_text = remove_unlinkedwikibase($HTML_text, $section0);

    $HTML_text = fix_it($HTML_text, $revision);

}

print_data($revision, $HTML_text, $sourcelanguage, $title);
