<?php
/**
 * Composer-packaged version of the free MaxMind GeoLite2 Country database.
 *
 * @package   BrightNucleus\GeoLite2Country
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\GeoLite2Country;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

/**
 * Class Database.
 *
 * @since   0.1.0
 *
 * @package BrightNucleus\GeoLite2Country
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class Database implements PluginInterface, EventSubscriberInterface
{

    const DB_FILENAME = 'GeoLite2-Country.mmdb';
    const DB_FOLDER   = 'data';
    const DB_URL      = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.mmdb.gz';
    const MD5_URL     = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.md5';

    /**
     * Get the event subscriber configuration for this plugin.
     *
     * @return array<string,string> The events to listen to, and their associated handlers.
     */
    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_INSTALL_CMD => 'update',
            ScriptEvents::POST_UPDATE_CMD  => 'update',
        );
    }

    /**
     * Update the stored database.
     *
     * @since 0.1.0
     *
     * @param Event $event
     */
    public static function update(Event $event)
    {
        $dbFilename = self::getLocation();

        self::maybeCreateDBFolder(dirname($dbFilename));

        $oldMD5 = self::getMD5($dbFilename . '.md5');
        self::downloadFile($dbFilename . '.md5', self::MD5_URL);

        $newMD5 = self::getMD5($dbFilename . '.md5');
        if ($newMD5 === $oldMD5) {
            return;
        }

        $io = $event->getIO();
        $io->write('Fetching new version of the MaxMind GeoLite2 Country database...', true);
        self::downloadFile($dbFilename . '.gz', self::DB_URL);

        $io->write('Unzipping the database...', true);
        self::unzipFile($dbFilename);

        $io->write('Removing zipped file...', true);
        self::removeFile($dbFilename . '.gz');

        $io->write('The MaxMind GeoLite2 Country database has been updated.', true);
    }

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

    /**
     * Create the DB folder if it does not exist yet.
     *
     * @since 0.1.0
     *
     * @param string $folder Name of the DB folder.
     */
    protected static function maybeCreateDBFolder($folder)
    {
        if (! is_dir($folder)) {
            mkdir($folder);
        }
    }

    /**
     * Get the MD5 string contained within a file.
     *
     * @since 0.1.0
     *
     * @param string $filename Filename of the MD5 file.
     * @return string MD5 hash contained within the file. Empty string if not found.
     */
    protected static function getMD5($filename)
    {
        if (! is_file($filename)) {
            return '';
        }

        return file_get_contents($filename);
    }

    /**
     * Download a file from an URL.
     *
     * @since 0.1.0
     *
     * @param string $filename Filename of the file to download.
     */
    protected static function downloadFile($filename, $url)
    {
        $fileHandle = fopen($filename, 'w');
        $options    = [
            CURLOPT_FILE    => $fileHandle,
            CURLOPT_TIMEOUT => 600,
            CURLOPT_URL     => $url,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        curl_exec($curl);
        curl_close($curl);
    }

    /**
     * Unzip a gzipped file.
     *
     * @since 0.1.0
     *
     * @param string $filename Filename of the database, without .gz extension.
     */
    protected static function unzipFile($filename)
    {
        $buffer_size = 4096;

        $zippedFile   = gzopen($filename . '.gz', 'rb');
        $unzippedFile = fopen($filename, 'wb');

        while (! gzeof($zippedFile)) {
            fwrite($unzippedFile, gzread($zippedFile, $buffer_size));
        }

        fclose($unzippedFile);
        gzclose($zippedFile);
    }

    /**
     * Delete a file.
     *
     * @since 0.1.2
     *
     * @param string $filename Filename of the file to delete.
     */
    protected static function removeFile($filename)
    {
        if (is_file($filename)) {
            unlink($filename);
        }
    }

    /**
     * Activate the plugin.
     *
     * @since 0.1.3
     *
     * @param Composer    $composer The main Composer object.
     * @param IOInterface $io       The i/o interface to use.
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        // no action required
    }
}
