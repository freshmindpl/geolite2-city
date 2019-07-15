<?php
/**
 * Composer-packaged version of the free MaxMind GeoLite2 City database.
 *
 * @package   BrightNucleus\GeoLite2City
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\GeoLite2City;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

/**
 * Class DatabaseUpdater.
 *
 * @since   0.1.5
 *
 * @package BrightNucleus\GeoLite2Country
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class DatabaseUpdater implements PluginInterface, EventSubscriberInterface
{

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
        $dbFilename = Database::getLocation();

        $io = $event->getIO();

        $io->write('Making sure the DB folder exists: ' . dirname($dbFilename), true, IOInterface::VERBOSE);
        self::maybeCreateDBFolder(dirname($dbFilename));

        $oldMD5 = self::getContents($dbFilename . '.md5');
        $io->write('MD5 of existing local DB file: ' . $oldMD5, true, IOInterface::VERBOSE);

        $io->write('Fetching remote MD5 hash...');
        $io->write(
            sprintf(
                'Downloading file: %1$s => %2$s',
                Database::MD5_URL,
                $dbFilename . '.md5.new'
            ),
                true,
                IOInterface::VERBOSE
        );
        self::downloadFile($dbFilename . '.md5.new', Database::MD5_URL);

        $newMD5 = self::getContents($dbFilename . '.md5.new');
        $io->write('MD5 of current remote DB file: ' . $newMD5, true, IOInterface::VERBOSE);
        if ($newMD5 === $oldMD5) {
            $io->write(
                sprintf(
                    '<info>The local MaxMind GeoLite2 City database is already up to date</info>. (%1$s)',
                    $dbFilename
                ),
                true
            );

            return;
        }

        // If the download was corrupted, retry three times before aborting.
        // If the update is aborted, the currently active DB file stays in place, to not break a site on failed updates.
        $retry = 3;
        while ($retry > 0) {
            $io->write('Fetching new version of the MaxMind GeoLite2 City database...', true);
            $io->write(
                sprintf(
                    'Downloading file: %1$s => %2$s',
                    Database::DB_URL,
                    $dbFilename . '.gz'
                ),
                true,
                IOInterface::VERBOSE
            );
            self::downloadFile($dbFilename . '.gz', Database::DB_URL);

            // We unzip into a temporary file, so as not to destroy the DB that is known to be working.
            $io->write('Unzipping the database...', true);

            $io->write('Unzipping file: ' . $dbFilename . '.gz => ' . $dbFilename . '.tmp', true, IOInterface::VERBOSE);
            self::unzipFile($dbFilename . '.gz', $dbFilename . '.tmp');

            $io->write('Removing file: ' . $dbFilename . '.gz', true, IOInterface::VERBOSE);
            self::removeFile($dbFilename . '.gz');

            $io->write('Verifying integrity of the downloaded database file...', true);
            $downloadMD5 = self::calculateMD5($dbFilename . '.tmp');
            $io->write('MD5 of downloaded DB file: ' . $downloadMD5, true, IOInterface::VERBOSE);

            // Download was successful, so now we replace the existing DB file with the freshly downloaded one.
            if ($downloadMD5 === $newMD5) {
                $io->write('All good, replacing previous version of the database with the downloaded one...', true);
                $retry = 0;

                $io->write('Removing file: ' . $dbFilename, true, IOInterface::VERBOSE);
                self::removeFile($dbFilename);

                $io->write('Removing file: ' . $dbFilename . '.md5', true, IOInterface::VERBOSE);
                self::removeFile($dbFilename . '.md5');

                $io->write('Renaming file: ' . $dbFilename . '.tmp => ' . $dbFilename, true, IOInterface::VERBOSE);
                self::renameFile($dbFilename . '.tmp', $dbFilename);

                $io->write(
                    'Renaming file: ' . $dbFilename . '.md5.new => ' . $dbFilename . '.md5',
                    true,
                    IOInterface::VERBOSE
                );
                self::renameFile($dbFilename . '.md5.new', $dbFilename . '.md5');
                continue;
            }

            // The download was fishy, so we remove intermediate files and retry.
            $io->write('<comment>Downloaded file did not match expected MD5, retrying...</comment>', true);

            $io->write('Removing file: ' . $dbFilename . '.tmp', true, IOInterface::VERBOSE);
            self::removeFile($dbFilename . '.tmp');

            $retry--;
        }

        // Even several retries did not produce a proper download, so we remove intermediate files and let the user know
        // about the issue.
        if (! isset($downloadMD5)
            || $downloadMD5 !== $newMD5
        ) {
            $io->write('Removing file: ' . $dbFilename . '.md5.new', true, IOInterface::VERBOSE);
            self::removeFile($dbFilename . '.md5.new');

            $io->writeError('<error>Failed to download the MaxMind GeoLite2 City database! Aborting update.</error>');

            return;
        }

        $io->write(
            sprintf(
                '<info>The local MaxMind GeoLite2 City database has been updated.</info> (%1$s)',
                $dbFilename
            ),
            true
        );
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
     * Get the content from within a file.
     *
     * @since 0.2.1
     *
     * @param string $filename Filename.
     * @return string File content.
     */
    protected static function getContents($filename)
    {
        if (! is_file($filename)) {
            return '';
        }

        return file_get_contents($filename);
    }

    /**
     * Calculate the MD5 hash of a file.
     *
     * @since 0.2.1
     *
     * @param string $filename Filename of the MD5 file.
     * @return string MD5 hash contained within the file. Empty string if not found.
     */
    protected static function calculateMD5($filename)
    {
        return md5(self::getContents($filename));
    }

    /**
     * Download a file from an URL.
     *
     * @since 0.1.0
     *
     * @param string $filename Filename of the file to download.
     * @param string $url      URL of the file to download.
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
     * @param string $source      Source, zipped filename to unzip.
     * @param string $destination Destination filename to write the unzipped contents to.
     */
    protected static function unzipFile($source, $destination)
    {
        $buffer_size = 4096;

        $zippedFile   = gzopen($source, 'rb');
        $unzippedFile = fopen($destination, 'wb');

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
     * Rename a file.
     *
     * @since 0.1.2
     *
     * @param string $source      Source filename of the file to rename.
     * @param string $destination Destination filename to rename the file to.
     */
    protected static function renameFile($source, $destination)
    {
        if (is_file($source)) {
            rename($source, $destination);
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
