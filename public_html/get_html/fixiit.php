<?php

require_once __DIR__ . "/post.php";

function do_fix_it($text)
{
    $url = 'https://ncc2c.toolforge.org/textp';

    if ($_SERVER['SERVER_NAME'] == 'localhost') {
        $url = 'http://localhost:8000/textp';
    }

    $data = ['html' => $text];
    $response = post_url_params_result($url, $data);

    // Handle the response from your API
    if ($response === false) {
        return ['error' => 'Error: Could not reach API.'];
    }

    $data = json_decode($response, true);
    if (isset($data['error'])) {
        return ['error' => 'Error: ' . $data['error']];
    }

    // Extract the result from the API response
    if (isset($data['result'])) {
        return ['result' => $data['result']];
    } else {
        return ['error' => 'Error: Unexpected response format.'];
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
    if ($elements->length > 2) {
        return false;
    }
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

function fix_it($text, $revision)
{
    // ---
    $file = __DIR__ . "/revisions/$revision.html";
    // ---
    if (file_exists($file)) {
        $new_text = file_get_contents($file);
        return $new_text;
    }
    // ---
    $fixed = do_fix_it($text);
    // ---
    $error  = $error['error'] ?? '';
    $result = $fixed['result'] ?? $text;
    // ---
    // $result = str_replace("https://medwiki.toolforge.org/md/", "https://en.wikipedia.org/w/", $result);
    // $result = str_replace("https://medwiki.toolforge.org/w/", "https://en.wikipedia.org/w/", $result);
    // $result = str_replace("https://medwiki.toolforge.org/wiki/", "https://en.wikipedia.org/wiki/", $result);
    // ---
    if ($error == "" && is_bad_fix($result) == false && $revision != '') {
        file_put_contents($file, $result);
    }
    // ---
    return $result;
}
