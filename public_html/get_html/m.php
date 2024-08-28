<?PHP

function fix_links($html)
{

    // find and remove from this: (if href has (action=edit))

    // <a rel="mw:WikiLink" href="./Cancer_signs_and_symptoms?action=edit&amp;redlink=1" title="Cancer signs and symptoms" class="new" typeof="mw:LocalizedAttrs" data-mw-i18n='{"title":{"lang":"x-page","key":"red-link-title","params":["Cancer signs and symptoms"]}}' id="mwBA">symptoms</a>

    // to this:

    // <a rel="mw:WikiLink" href="./Cancer_signs_and_symptoms" title="Cancer signs and symptoms" id="mwBg">symptoms</a>

    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    // Find all link elements with href attributes containing "action=edit"
    $links = $dom->getElementsByTagName('a');

    // Loop through each anchor element
    foreach ($links as $link) {
        // Check if the href attribute contains 'action=edit'
        $href = $link->getAttribute('href');
        if (strpos($href, 'action=edit') !== false) {
            $newHref = preg_replace('/\?action=edit.*?/', '', $href);
            $link->setAttribute('href', $newHref);

            // Remove unwanted attributes: typeof, data-mw-i18n
            $link->removeAttribute('typeof');
            $link->removeAttribute('data-mw-i18n');
            $link->removeAttribute('class');

            // remove class new
            $link->setAttribute('class', 'cx-link');
        }
    }

    // Return the modified HTML content
    return $dom->saveHTML();
}

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

    // $HTML_text = fix_links($HTML_text);

    return $HTML_text;
}

function dom_it($html)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $content = $dom->saveHTML($dom->documentElement);

    return $content;
}
