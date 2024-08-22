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

function get_0_section($html)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $elements = $dom->getElementsByTagName('section');
    foreach ($elements as $element) {
        // if element has table with class infobox
        $table = $element->getElementsByTagName('table');
        foreach ($table as $t) {
            $class = $t->getAttribute('class');
            if ($class == 'infobox') {
                return $dom->saveHTML($element);
            }
        }
    }

    foreach ($elements as $element) {
        // if element has table with class infobox
        $nhtml = $dom->saveHTML($element);
        if (stripos($nhtml, 'infobox') !== false) {
            return $nhtml;
        }
    }
    // return $dom->saveHTML();
    return '';
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

function count_sections($html)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $elements = $dom->getElementsByTagName('section');
    return count($elements);
}

function get_section0_old($HTML_text)
{
    if (count_sections($HTML_text) < 3) {
        return $HTML_text;
    }
    // split before <section data-mw-section-id="1" then add </body></html>
    $refs = get_references_section($HTML_text);

    // $pos = strpos($HTML_text, '<section data-mw-section-id="1"');
    $pos = strpos($HTML_text, '</section>');

    if ($pos !== false) {
        $HTML_text = substr($HTML_text, 0, $pos) . "</section>" . $refs . '</body></html>';
    }
    return $HTML_text;
}

function get_section0($HTML_text)
{
    if (count_sections($HTML_text) < 3) {
        return $HTML_text;
    }
    // split before <section data-mw-section-id="1" then add </body></html>
    $refs = get_references_section($HTML_text);
    $section_0 = get_0_section($HTML_text);

    // $pos = strpos($HTML_text, '<section data-mw-section-id="1"');
    $pos = strpos($HTML_text, '<section');

    if ($pos !== false && $section_0 != '' && $refs != '') {
        $HTML_text = substr($HTML_text, 0, $pos) . $section_0 . $refs . '</body></html>';
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

function remove_all_style_tags($html)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $elements = $dom->getElementsByTagName('style');
    foreach ($elements as $element) {
        $element->parentNode->removeChild($element);
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
        if (stripos($class, 'hatnote navigation-not-searchable') !== false || $class == "Module:Sitelinks") {
            $html = str_replace($nhtml, '', $html);
            $element->parentNode->removeChild($element);
        }
    }
    $content = $dom->saveHTML($dom->documentElement);
    return $content;
}

function do_changes($HTML_text, $section0)
{
    if ($section0 != '') {
        $HTML_text = get_section0($HTML_text);
    }

    $HTML_text = remove_unlinkedwikibase($HTML_text);

    // $HTML_text = remove_templatestyles($HTML_text);

    $HTML_text = remove_temp_Distinguish($HTML_text);

    return $HTML_text;
}

function dom_it($html)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $content = $dom->saveHTML($dom->documentElement);

    return $content;
}
