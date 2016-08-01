<?php
/**
 * Composer-packaged version of the free MaxMind GeoLite2 Country database.
 *
 * @package   BrightNucleus\GeoLite2Country
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\GeoLite2Country;

/**
 * Class Database.
 *
 * @since   0.1.0
 *
 * @package BrightNucleus\GeoLite2Country
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class Database
{

    const DB_FILENAME = 'GeoLite2-Country.mmdb';
    const DB_FOLDER   = 'data';
    const DB_URL      = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.mmdb.gz';
    const MD5_URL     = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.md5';

    /**
     * Get the location of the database file.
     *
     * @since 0.1.0
     *
     * @param bool $array   Optional. Whether to return the location as an array. Defaults to false.
     * @return string|array Either a string, containing the absolute path to the file, or an array with the location
     *                      split up into two keys named 'folder' and 'filename'
     */
    public static function getLocation($array = false)
    {
        $folder   = realpath(__DIR__ . '/../') . '/' . self::DB_FOLDER;
        $filepath = $folder . '/' . self::DB_FILENAME;
        if (! $array) {
            return $filepath;
        }

        return [
            'folder' => $folder,
            'file'   => self::DB_FILENAME,
        ];
    }
}
