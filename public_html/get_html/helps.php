<?php


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

function print_data($revision, $HTML_text, $sourcelanguage, $title, $error = "")
{
    // global $sourcelanguage, $title;
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
