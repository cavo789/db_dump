<?php

/**
 * DB_Dump
 * Scripts for making a dump (DB_Dump) and reimport (DB_Import) a MySQL DB
 * php version 5.5
 *
 * Note: `MySQLDump.php` and `MySQLImport.php` files are part of the
 * "MySQL Dump Utility" of David Grudl (https://github.com/dg/MySQL-dump)
 *
 * @package   dbDump
 * @author    Christophe Avonture <christophe@avonture.be>
 * @license   MIT
 *
 * @link https://github.com/cavo789/db_dump
 */

/**
 * MySQL database dump loader.
 *
 * @author     David Grudl (http://davidgrudl.com)
 * @copyright  Copyright (c) 2008 David Grudl
 * @license    New BSD License
 */
class MySQLImport
{
    /**
     * @var callable function (int $count, ?float $percent): void
     */
    public $onProgress;

    /**
     * @var mysqli
     */
    private $connection;

    /**
     * Connects to database.
     *
     * @param  mysqli connection
     * @param mixed $charset
     */
    public function __construct(mysqli $connection, $charset = 'utf8')
    {
        $this->connection = $connection;

        if ($connection->connect_errno) {
            throw new Exception($connection->connect_error);
        } elseif (!$connection->set_charset($charset)) { // was added in MySQL 5.0.7 and PHP 5.0.5, fixed in PHP 5.1.5)
            throw new Exception($connection->error);
        }
    }

    /**
     * Loads dump from the file.
     *
     * @param  string filename
     * @param mixed $file
     *
     * @return int
     */
    public function load($file)
    {
        $handle = strcasecmp(substr($file, -3), '.gz') ? fopen($file, 'rb') : gzopen($file, 'rb');
        if (!$handle) {
            throw new Exception("ERROR: Cannot open file '$file'.");
        }

        return $this->read($handle);
    }

    /**
     * Reads dump from logical file.
     *
     * @param  resource
     * @param mixed $handle
     *
     * @return int
     */
    public function read($handle)
    {
        if (!is_resource($handle) || 'stream' !== get_resource_type($handle)) {
            throw new Exception('Argument must be stream resource.');
        }

        $stat = fstat($handle);

        $sql       = '';
        $delimiter = ';';
        $count     = $size     = 0;

        while (!feof($handle)) {
            $s = fgets($handle);
            $size += strlen($s);
            if ('DELIMITER ' === strtoupper(substr($s, 0, 10))) {
                $delimiter = trim(substr($s, 10));
            } elseif (substr($ts = rtrim($s), -strlen($delimiter)) === $delimiter) {
                $sql .= substr($ts, 0, -strlen($delimiter));
                if (!$this->connection->query($sql)) {
                    throw new Exception($this->connection->error . ': ' . $sql);
                }

                $sql = '';
                $count++;
                if ($this->onProgress) {
                    call_user_func($this->onProgress, $count, isset($stat['size']) ? $size * 100 / $stat['size'] : null);
                }
            } else {
                $sql .= $s;
            }
        }

        if ('' !== rtrim($sql)) {
            $count++;
            if (!$this->connection->query($sql)) {
                throw new Exception($this->connection->error . ': ' . $sql);
            }

            if ($this->onProgress) {
                call_user_func($this->onProgress, $count, isset($stat['size']) ? 100 : null);
            }
        }

        return $count;
    }
}
