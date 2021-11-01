# MySQL-Dump

![Banner](./banner.svg)

> Very straightforward script to dump a MySQL database and import it back; locally f.i.

Database backup and import scripts.

1. Run `DB_Dump.php` from your production site and obtain your .sql.gz compressed backup
2. Save the backup file onto your localhost development site
3. Run `DB_Import.php` from there and your database will now contains a copy of your production site.

## Table of Contents

- [Install](#install)
- [Usage](#usage)
- [License](#license)

## Install

1. Get a copy of `DB_Dump.php` and `MySQLDump.php` scripts and store them in your production site (best, in a protected folder; f.i. with a `.htpasswd`). The two files should be stored in the same folder.
2. Edit `DB_Dump.php` and adjust the five constants you will see at the beginning of the file: a password that the user needs to know before running the file and the database credentials (server, name, login & password).
3. Get a copy of `DB_Import.php` and `MySQLImport.php` scripts and store them in your localhost development site. The two files should be stored in the same folder.
4. Edit `DB_Import.php` and adjust the five constants you will see at the beginning of the file: a password that the user needs to know before running the file and the database credentials (server, name, login & password).

Tip: you can use [https://www.avonture.be/php_password/](https://www.avonture.be/php_password/) for easily get the hash for the password of your choice.

### Security

**Don't forget to secure the folder where you'll store `DB_Dump.php` script on your production server. Make sure that no-one can access to that folder except you since backup files will be stored there. Think to a `.htaccess` protection using an IP protection for instance.**

## Usage

### Make a backup

Go to your production site and access to URL to the `DB_Dump.php` script. If correctly initialized, the backup will be started and you'll get an hyperlink to the backup file once the process is done.

Click on the hyperlink to download a copy of the backup file and copy that file in your localhost development folder; where you've save the `DB_Import.php` script.

### Restore the backup

Go to your localhost site and access to URL to the `DB_Import.php` script. If correctly initialized, you'll get a screen with the list of all backup files found, the most recent file first. Just click on the desired file and the importation will start..

## Source

`MySQLDump` and `MySQLImport` classes are part of the `[MySQL Dump Utility](https://github.com/dg/MySQL-dump)` of [David Grudl](https://github.com/dg)

## License

[MIT](LICENSE)
