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

function get_section_0_and_html($title)
{
    $params = array(
        "action" => "parse",
        "format" => "json",
        "page" => $title,
        "section" => "0",
        "prop" => "wikitext|revid"
    );
    $url = "https://mdwiki.org/w/api.php?" . http_build_query($params);

    $req = get_url_params_result($url);
    $json1 = json_decode($req, true);

    $first = $json1["parse"]["wikitext"]["*"] ?? '';
    $revid = $json1["parse"]["revid"] ?? '';
    // ---
    if ($first == '') {
        return ['', ''];
    }
    // ---
    $first .= "\n==References==\n<references />";
    // ---
    $params2 = [
        'action' => 'flow-parsoid-utils',
        'format' => 'json',
        'from' => 'wikitext',
        'to' => 'html',
        'content' => $first,
        'title' => 'Main_Page',
        'utf8' => 1,
        'formatversion' => '2'
    ];
    // ---
    $url2 = "https://www.mediawiki.org/w/api.php?" . http_build_query($params2);
    // ---
    $req2 = get_url_params_result($url2);
    // ---
    $json2 = json_decode($req2, true);
    // ---
    $html = $json2['flow-parsoid-utils']['content'] ?? '';
    // ---
    return [$html, $revid];
}

$HTML_text = "";

if ($title != '') {
    $d = get_section_0_and_html($title);
    // ---
    $HTML_text = $d[0];
    $revision = $d[1];
    // ---
}
$error = '';

if ($HTML_text != '') {
    // {\"wt\":\"Drugbox\\n\",\"href\":\".\/Template:Drugbox\"}
    // replace Drugbox with Infobox drug
    $HTML_text = preg_replace("/\bDrugbox\b/", "Infobox drug", $HTML_text);

    if ($revision == '') {
        $revision = get_revision($HTML_text);
    }

    $HTML_text = do_changes($HTML_text, false);

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
