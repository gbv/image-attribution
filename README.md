# image-attribution

[![Latest Stable Version](https://poser.pugx.org/gbv/image-attribution/v/stable)](https://packagist.org/packages/gbv/image-attribution)

[![Build Status](https://travis-ci.org/gbv/image-attribution.svg?branch=master)](https://travis-ci.org/gbv/image-attribution)

This PHP library provides a method to retrieve information required for proper attribution of images from [Wikimedia Commons](https://commons.wikimedia.org/).

## Motivation

Wikimedia Commons contains images and other freely usable media files. Some files can be used without any conditions (public domain), others are licensed under an open content license. Depending on the particular license one has to give proper credit to the original author and/or name the specific license. Wikimedia Commons includes guidelines [how to give proper credit](https://commons.wikimedia.org/wiki/Commons:Credit_line) but there is no easy way to look up attribution requirements for a given image in machine-readable format.

There is a method to retrieve some license information via Wikimedia Commons API, but the API is difficult to find and query, its return format is too verbose, and the response is not usable without further processing. For instance licenses are spelled in several different ways ("CC-BY-SA-3.0", "CC BY-SA 3.0"...) and field values may contain HTML tags.

## Requirements

Requires PHP 5.6 or above and `allow_url_fopen` enabled.

## Installation 

Use composer:

~~~sh
$ composer require gbv/image-attribution
~~~

or just copy files `src/image-attribution.php` and `src/identify-open-content-license.php` by hand.

## Usage

Use autoload:

~~~php
require 'vendor/autoload.php';
~~~

or include the library file the old way:

~~~php
require_once 'src/image-attribution.php';
~~~

Then make use of the function `commons_image_attribution`:

~~~php
$attribution = commons_image_attribution("Loewe_frontal.JPG");
~~~

The image can be passed as file name, full image URL or URL of the image page. The value `null` will return a random image.  If the image is found with a license, an array is returned with the following fields:

 field        | content 
--------------|---------------------------------------------------------------------
`src`         | URL of the image file
`url`         | URL of the image description page
`license`     | URI of the license or `null` if unknown
`license_name`| short name of the license or `null` if unknown
`attribution` | boolean value whether attribution is required, or `null` if unknown
`sharealike`  | boolean value whether share-alike is required, or `null` if unknown
`creator`     | creator of the image, or `null` if unknown
`credit`      | credit string to give attribution with creator and license
`description` | image description as plain string, or `null` if unknown
`date`        | date of the image as plain string, or `null` if unknown
`restrictions`| additional restrictions, such as "trademarked", or `null`
`name`        | (file)name of the image
`mime`        | image mime type
`size`        | image size in bytes
`width`       | image width in pixels
`height`      | image height in pixels

## Examples

Directory `example` includes a web service (`index.php`) to query image attribution information as JSON. The service is deployed at <https://image-attribution.herokuapp.com/> but may be slow or down from time to time. Please install at your own server if you need to use it frequently! To give an example:

* <https://image-attribution.herokuapp.com/?api.php?image=Wikimedia-logo.svg>

Directory `bin` contains a command line script to get image attribution:

    ./bin/image-attribution Wikimedia-logo.svg 

