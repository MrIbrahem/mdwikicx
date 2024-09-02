<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../m.php";

// get old.html
$old = file_get_contents(__DIR__ . "/old.html");
$new = $old;
file_put_contents(__DIR__ . "/new.html", $new);

// $new = do_changes($old, false);
$new = dom_it($old);

// $new = remove_unlinkedwikibase($new);
// <span about="#mwt1" typeof="mw:Transclusion" data-mw='{"parts":[{"template":{"target":{"wt":"#unlinkedwikibase:id=Q117768481","function":"unlinkedwikibase"},"params":{},"i":0}}]}' id="mwAg"></span>
// $new = preg_replace('/^<span[^>]>/', '', $new);

file_put_contents(__DIR__ . "/new.html", $new);
