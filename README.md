# Bright Nucleus GeoLite2 City Database

This is a Composer plugin that provides an automated binary version of the free MaxMind GeoLite2 City database.

The main advantage is that the downloaded database will be checked for updates on each `composer install` and `composer update`.

## Table Of Contents

* [Attribution](#attribution)
* [Installation](#installation)
* [Basic Usage](#basic-usage)
* [Example](#example)
* [Contributing](#contributing)
* [License](#license)

## Attribution

This product includes GeoLite2 data created by MaxMind, available from
<a href="http://www.maxmind.com">http://www.maxmind.com</a>.

## Installation

The only thing you need to do to make this work is adding this package as a dependency to your project:

```BASH
composer require freshmindpl/geolite2-city
```

## Basic Usage

On each `composer install` or `composer update`, a check will be made to see whether there's a new version of the database available. If there is, that new version is downloaded.

To retrieve the path to the binary database file from within your project, you can use the `Database::getLocation()` method:

```PHP
<?php

use BrightNucleus\GeoLite2City\Database;

$dbLocation = Database::getLocation();
```

You can pass this location on to the [`GeoIp2\Database\Reader`](https://github.com/maxmind/GeoIP2-php/blob/master/src/Database/Reader.php) class that is provided with the [`geoip2/geoip2`](https://packagist.org/packages/geoip2/geoip2) Composer package.

## Example

The following example assumes that you have added the [`geoip2/geoip2`](https://packagist.org/packages/geoip2/geoip2) Composer package as a dependency to your project, so that it is available to the autoloader.

```PHP
<?php

use GeoIp2\Database\Reader;
use BrightNucleus\GeoLite2Country\Database;

function getCity($ip) {
    $dbLocation = Database::getLocation();
    $reader = new Reader($dbLocation);

    return $reader->city($ip);
}
```

## Contributing

All feedback / bug reports / pull requests are welcome.

## License

This code is released under the MIT license.

For the full copyright and license information, please view the [`LICENSE`](LICENSE) file distributed with this source code.
