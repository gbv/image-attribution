# image-attribution

[![Latest Stable Version](https://poser.pugx.org/gbv/image-attribution/v/stable)](https://packagist.org/packages/gbv/image-attribution)

[![Build Status](https://travis-ci.org/gbv/image-attribution.svg?branch=master)](https://travis-ci.org/gbv/image-attribution)

This PHP library provides a method to information required for proper attribution of images from [Wikimedia Commons](https://commons.wikimedia.org/).

## Requirements

Requires PHP 5.6 or above and `allow_url_fopen` enabled.

## Installation 

Use composer:

~~~sh
$ composer require gbv/image-attribution
~~~

or just copy file `src/image-attribution.php` by hand.

## Usage

Use autoload:

~~~php
require 'vendor/autoload.php';
~~~

or include the library file the old way:

~~~php
require 'src/image-attribution.php';
~~~

Then make use of the function `commons_image_attribution`:

~~~php
$attribution = commons_image_attribution("Loewe_frontal.JPG");
~~~

If the image is found with a license, an array is returned with the following fields:

* src (URL of image file)
* url (URL of image page)
* attribution (boolean value whether attribution is required)
* license (license or "Public domain")
* creator
* credit (license and creator)
* description
* date

The image can be passed as file name, full image URL or URL of the image page.

## Examples

Directory `example` includes a web service to query image attribution information as JSON. The service is deployed at <https://image-attribution.herokuapp.com> but may be slow or down from time to time. Please install at your own server if you need to use it frequently!

