<?PHP

function remove_unlinkedwikibase($html)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $elements = $dom->getElementsByTagName('span');
    foreach ($elements as $element) {
        $nhtml = $dom->saveHTML($element);
        if (stripos($nhtml, 'unlinkedwikibase') !== false) {
            // echo $nhtml;
            $element->parentNode->removeChild($element);
            $html = str_replace($nhtml, '', $html);
        }
    }
    // return $dom->saveHTML();
    return $html;
}

function get_references_section($html)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $elements = $dom->getElementsByTagName('section');
    foreach ($elements as $element) {
        $nhtml = $dom->saveHTML($element);
        if (stripos($nhtml, 'mw:Extension/references') !== false) {
            return $nhtml;
        }
    }
    // return $dom->saveHTML();
    return "";
}

function get_section0($HTML_text)
{
    // split before <section data-mw-section-id="1" then add </body></html>
    $refs = get_references_section($HTML_text);

    $pos = strpos($HTML_text, '<section data-mw-section-id="1"');

    if ($pos !== false) {
        $HTML_text = substr($HTML_text, 0, $pos) . $refs . '</body></html>';
    }
    return $HTML_text;
}

function remove_templatestyles($html)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $elements = $dom->getElementsByTagName('style');
    foreach ($elements as $element) {
        // $nhtml = $dom->saveHTML($element);
        $typeof = $element->getAttribute('typeof');
        if (stripos($typeof, 'mw:Extension/templatestyles') !== false) {
            $element->parentNode->removeChild($element);
            // $html = str_replace($nhtml, '', $html);
        }
    }
    $content = $dom->saveHTML($dom->documentElement);
    return $content;
}

function remove_temp_Distinguish($html)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $elements = $dom->getElementsByTagName('div');
    foreach ($elements as $element) {
        $nhtml = $dom->saveHTML($element);
        $class = $element->getAttribute('class');
        if (stripos($class, 'hatnote navigation-not-searchable') !== false) {
            $html = str_replace($nhtml, '', $html);
            $element->parentNode->removeChild($element);
        }
    }
    $content = $dom->saveHTML($dom->documentElement);
    return $content;
}

function do_changes($HTML_text, $section0)
{
    if ($section0) {
        $HTML_text = get_section0($HTML_text);
    }

    // $HTML_text = remove_unlinkedwikibase($HTML_text);

    $HTML_text = remove_templatestyles($HTML_text);

    $HTML_text = remove_temp_Distinguish($HTML_text);

    return $HTML_text;
}
