# image-attribution

This PHP library provides a method to information required for proper attribution of images from [Wikimedia Commons](https://commons.wikimedia.org/).

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
* url (URL of image information page)
* attribution (boolean value whether attribution is required)
* license (license or "Public domain")
* creator
* credit (license and creator)
* description
* date

## Examples

Directory `example` includes a web service to query image attribution information as JSON.

