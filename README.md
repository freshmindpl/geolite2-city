# Bright Nucleus GeoLite2 Country Database

[![Latest Stable Version](https://poser.pugx.org/brightnucleus/geolite2-country/v/stable)](https://packagist.org/packages/brightnucleus/geolite2-country)
[![Total Downloads](https://poser.pugx.org/brightnucleus/geolite2-country/downloads)](https://packagist.org/packages/brightnucleus/geolite2-country)
[![Latest Unstable Version](https://poser.pugx.org/brightnucleus/geolite2-country/v/unstable)](https://packagist.org/packages/brightnucleus/geolite2-country)
[![License](https://poser.pugx.org/brightnucleus/geolite2-country/license)](https://packagist.org/packages/brightnucleus/geolite2-country)

This is a Composer-packaged version of the binary downloadable version of the free MaxMind GeoLite2 Country database.

The main advantage is that the downloaded database will be checked for updates on each `composer install` and `composer update`.

## Table Of Contents

* [Attribution](#attribution)
* [Installation](#installation)
    * [Add Package As A Requirement](#add-package-as-a-requirement)
    * [Add A Hook To The Update Method](#add-a-hook-to-the-update-method)
* [Basic Usage](#basic-usage)
* [Contributing](#contributing)

## Attribution

This product includes GeoLite2 data created by MaxMind, available from
<a href="http://www.maxmind.com">http://www.maxmind.com</a>.

## Installation

To make this work, you need to add this package as a requirement and add a hook to the update mechanism to your own package.

### Add Package As A Requirement

You can either do this via the command-line with the following command:
```BASH
composer require brightnucleus/geolite2-country
```

Or, you can add the package directly to your `require` section in your `composer.json` file:

```JSON
{
  "name": "<vendor>/<package>",
  // [ ... ]
  "require": {
    // [ ... ]
    "brightnucleus/geolite2-country": "*"
  }
}
```

### Add A Hook To The Update Method

You need to add the following hooks to your `scripts` section of your `composer.json` file:

```JSON
{
  "name": "<vendor>/<package>",
  // [ ... ]
  "scripts": {
    // [ ... ]
    "post-update-cmd": "BrightNucleus\\GeoLite2Country\\Database::update",
    "post-install-cmd": "BrightNucleus\\GeoLite2Country\\Database::update"
  }
}
```

## Basic Usage

On each `composer install` or `composer update`, a check will be made to see whether there's a new version of the database available. If there is, that new version is downloaded.

To retrieve the path to the binary database file from within your project, you can use the `getLocation()` method:

```PHP
<?php

use BrightNucleus\GeoLite2Country\Database;

$dbLocation = Database::getLocation();
```

You can pass this location on to the [`GeoIp2\Database\Reader`](https://github.com/maxmind/GeoIP2-php/blob/master/src/Database/Reader.php) class that is provided with the [`geoip2/geoip2`](https://packagist.org/packages/geoip2/geoip2) Composer package.

## Contributing

All feedback / bug reports / pull requests are welcome.
