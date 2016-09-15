<?php

/**
 * Image attribution webservice.
 */

require_once __DIR__.'/../src/image-attribution.php';

$image = @$_GET['image'];
$attribution = commons_image_attribution($image);

if ($attribution) {
    $attribution = [$attribution];
} else {
    $attribution = [];
}

$json = json_encode($attribution, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

header('Access-Control-Allow-Origin: *');

$callback = @$_GET['callback'];
if (preg_match('/^[$A-Z_][0-9A-Z_$.]*$/i', $callback)) {
    $json = "/**/$callback($json);";
    header('Content-Type: application/javascript; charset=utf-8');
} else {
    header('Content-Type: application/json');
}

print $json;
