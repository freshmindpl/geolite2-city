# Bright Nucleus GeoLite2 Country Database

[![Latest Stable Version](https://poser.pugx.org/brightnucleus/geolite2-country/v/stable)](https://packagist.org/packages/brightnucleus/geolite2-country)
[![Total Downloads](https://poser.pugx.org/brightnucleus/geolite2-country/downloads)](https://packagist.org/packages/brightnucleus/geolite2-country)
[![Latest Unstable Version](https://poser.pugx.org/brightnucleus/geolite2-country/v/unstable)](https://packagist.org/packages/brightnucleus/geolite2-country)
[![License](https://poser.pugx.org/brightnucleus/geolite2-country/license)](https://packagist.org/packages/brightnucleus/geolite2-country)

This is a Composer plugin that provides an automated binary version of the free MaxMind GeoLite2 Country database.

The main advantage is that the downloaded database will be checked for updates on each `composer install` and `composer update`.

## Table Of Contents

* [Attribution](#attribution)
* [Installation](#installation)
* [Basic Usage](#basic-usage)
* [Example](#example)
* [Contributing](#contributing)

## Attribution

This product includes GeoLite2 data created by MaxMind, available from
<a href="http://www.maxmind.com">http://www.maxmind.com</a>.

## Installation

The only thing you need to do to make this work is adding this package as a dependency to your project:

```BASH
composer require brightnucleus/geolite2-country
```

## Basic Usage

On each `composer install` or `composer update`, a check will be made to see whether there's a new version of the database available. If there is, that new version is downloaded.

To retrieve the path to the binary database file from within your project, you can use the `Database::getLocation()` method:

```PHP
<?php

use BrightNucleus\GeoLite2Country\Database;

$dbLocation = Database::getLocation();
```

You can pass this location on to the [`GeoIp2\Database\Reader`](https://github.com/maxmind/GeoIP2-php/blob/master/src/Database/Reader.php) class that is provided with the [`geoip2/geoip2`](https://packagist.org/packages/geoip2/geoip2) Composer package.

## Example

The following example assumes that you have added the [`geoip2/geoip2`](https://packagist.org/packages/geoip2/geoip2) Composer package as a dependency to your project, so that it is available to the autoloader.

```PHP
<?php

use GeoIp2\Database\Reader;
use BrightNucleus\GeoLite2Country\Database;

function getCountry($ip) {
    $dbLocation = Database::getLocation();
    $reader = new Reader($dbLocation);

    return $reader->country($ip);
}
```

## Contributing

All feedback / bug reports / pull requests are welcome.
