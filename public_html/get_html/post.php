<?php

// https://mdwiki.org/w/rest.php/v1/page/Sympathetic_crashing_acute_pulmonary_edema/html
// https://mdwiki.org/w/rest.php/v1/revision/1420795/html

$usr_agent = 'WikiProjectMed Translation Dashboard/1.0 (https://medwiki.toolforge.org/; tools.medwiki@toolforge.org)';

function get_url_params_result(string $url): string
{
    global $usr_agent;
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function post_url_params_result(string $endPoint, array $params = []): string
{
    global $usr_agent;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $endPoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $output = curl_exec($ch);
    $url = "{$endPoint}?" . http_build_query($params);
    if ($output === FALSE) {
        echo ("<br>cURL Error: " . curl_error($ch) . "<br>$url");
    }

    curl_close($ch);
    return $output;
}


function get_text_html($title, $revision, $domain = "https://mdwiki.org")
{
    // ---
    // replace " " by "_"
    $title = str_replace(" ", "_", $title);
    // fix / in title
    $title = str_replace("/", "%2F", $title);
    // ---
    $domain = ($domain != "") ? $domain : "https://mdwiki.org";
    // ---
    // $domain = "https://mdwiki.org";
    // $domain = "https://mdwiki.wmcloud.org";
    // ---
    $url = "$domain/w/rest.php/v1/page/" . $title . "/html";
    // ---
    if ($revision != '') {
        $url = "$domain/w/rest.php/v1/revision/" . $revision . "/html";
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
