<?php

/**
 * Get image attribution information for files at Wikimedia Commons.
 *
 * @see https://github.com/wikimedia/mediawiki-extensions-CommonsMetadata
 *
 * @param string $file
 *  file to get information about.
 * @return array|null
 *  image attribution information on success.
 */
function commons_image_attribution($file) {
    if (!$file) return;

    if (preg_match(
        '!^https?://(commons\.wikimedia\.org/wiki/File:|upload\.wikimedia\.org.+/)(.+)!',
        $file, $match)) {
        $file = $match[2];
    }

    $baseURL = "https://commons.wikipedia.org/w/api.php";
    $query   = http_build_query([
        "action" => "query",
        "prop"   => "imageinfo",
        "iiprop" => "url|extmetadata",
        "titles" => "File:$file",
        "format" => "json",
#        "iiextmetadatalanguage" => "de",
        "iiextmetadatafilter" => "LicenseShortName|UsageTerms|AttributionRequired|Restrictions|Artist|ImageDescription|DateTimeOriginal"
    ]);

    // just use plain old file_get_contents
    try {
        $json = @file_get_contents($baseURL.'?'.$query);
        $data = @json_decode($json,true);
        $image = array_values($data['query']['pages'])[0]['imageinfo'][0];
    } catch(Exception $e) {
        return;
    }

    if (!$image['url']) return;

    $meta = $image['extmetadata'];

    foreach( $meta as $key => $value) {
        $meta[$key] = strip_tags($value['value']);
    }

    # attribution
    $atb = [
        'src'         => $image['url'],
        'url'         => $image['descriptionurl'],
        'description' => $meta['ImageDescription'],
        'creator'     => $meta['Artist'],
        'date'        => $meta['DateTimeOriginal'],
        'attribution' => !!preg_match('/true/i',$meta['AttributionRequired']),
        'license'     => $meta['LicenseShortName'],
        #'usage'       => $meta['UsageTerms'], # not required actually
        'restriction' => $meta['Restrictions'], # e.g. trademarked (multiple combined by '|')
    ];

    if (!$atb['license']) {
        return;
    }

    foreach( $atb as $key => $value) {
        if ($value === null || $value === "") {
            unset($atb[$key]);
        }
    }

    if (!$atb['creator'] && $atb['attribution']) {
        $atb['creator'] = "Wikimedia Commons";
    }

    $atb['credit'] = $atb['license'] . ': ' . $atb['creator'];

    return $atb;
}
