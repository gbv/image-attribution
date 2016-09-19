<?php

include_once __DIR__.'/identify-open-content-license.php';

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

    static $baseURL      = 'https://commons.wikipedia.org/w/api.php';
    static $commonsRegex = '!^https?://(commons\.wikimedia\.org/wiki/File:|upload\.wikimedia\.org.+/)(.+)!';

    $query = [
        'action' => 'query',
        'prop'   => 'imageinfo',
        'iiprop' => 'url|dimensions|mime|extmetadata',
        'format' => 'json',
#        'iiextmetadatalanguage' => 'de',
        'iiextmetadatafilter' => 'LicenseShortName|UsageTerms|AttributionRequired|Restrictions|Artist|ImageDescription|DateTimeOriginal'
    ];

    if (isset($file)) {
        if (preg_match($commonsRegex, $file, $match)) {
            $file = $match[2];
        }
        $query['titles'] = "File:$file";
    } else {
        $query['generator']    = 'random';
        $query['grnnamespace'] = '6';
        $query['grnlimit']     = '1';
    }

    // just use plain old file_get_contents
    $url = $baseURL.'?'.http_build_query($query);
    try {
        $json = @file_get_contents($url);
        $data = @json_decode($json,true);
        $data = array_values($data['query']['pages'])[0];
        if (!isset($data['missing']) && isset($data['imageinfo'])) {
            $image = $data['imageinfo'][0];
        } else {
            return;
        }
    } catch(Exception $e) {
        return;
    }

    if (!$image['url']) return;

    $meta = $image['extmetadata'];

    // strip/convert HTML tags/entities
    foreach( $meta as $key => $value) {
        $value = strip_tags($value['value']);
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML401 | ENT_XHTML | ENT_HTML5, 'UTF-8');
        $meta[$key] = trim($value);
    }


    # attribution
    $attrib = [
        'src'         => $image['url'],
        'url'         => $image['descriptionurl'],
        'mime'        => $image['mime'],
        'size'        => $image['size'],
        'wdith'       => $image['width'],
        'height'      => $image['height'],
        'name'        => preg_replace('/^http.+\/File:/','', $image['descriptionurl']),
        'attribution' => isset($meta['AttributionRequired'])
            ? !!preg_match('/true/i',$meta['AttributionRequired'])
            : null,
        #'usage'       => $meta['UsageTerms'], # not required actually
    ];

    // these fields happen to be missing in some cases
    $map = [
        'DateTimeOriginal' => 'date',
        'ImageDescription' => 'description',
        'Artist'           => 'creator',
        'LicenseShortName' => 'license',
        'Restrictions'     => 'restrictions', # e.g. trademarked (multiple combined by '|')
    ];

    foreach ($map as $from => $to) {
        $attrib[$to] = isset($meta[$from]) ? $meta[$from] : null;
    }

    // treat empty strings as missing fields
    foreach( $attrib as $key => $value) {
        if ($value === "") {
            $attrib[$key] = null;
        }
    }

    // normalize license short name
    if ($attrib['license']) {
        $attrib['license'] = open_content_license_uri($attrib['license']);
    }

    if ($attrib['license'] && preg_match('/http:\/\/creativecommons.org\/licenses\/([^\/]*sa)?\//',$attrib['license'], $match)) {
        $attrib['sharealike'] = !!$match[1];
    }

    if ($attrib['creator'] === null && $attrib['attribution']) {
        $attrib['creator'] = "Wikimedia Commons";
    }

    $attrib['license_name'] = open_content_license_name($attrib['license']);

    // see <https://commons.wikimedia.org/wiki/Commons:Credit_line>
    if ($attrib['license'] && $attrib['creator'] !== null) {
        $attrib['credit'] = $attrib['creator'] . " / " .
            $attrib[ $attrib['license_name'] ? 'license_name' : 'license' ];
    } 
    // attribution unknown
    elseif ($attrib['attribution'] === null and $attrib['creator'] !== null) {
        $attrib['credit'] = $attrib['creator'] . " / see license at Wikimedia Commons";
    }

    return $attrib;
}

