<?php

/**
 * Backup interface. This script requires MySQLDump.php.
 *
 * The `MySQLDump.php` file is part of the
 * "MySQL Dump Utility" of David Grudl (https://github.com/dg/MySQL-dump)
 */

// Default password is "MyPasswordIsSecret" (hash returned with password_hash())
define('APP_PASSWORD', '$2y$10$3l7YpO1ctqmC9yc2thrsy.Pymx67JPSfdDycnv7dVv8DXZp.DFc6y');

// Please enter here the connection data to your database
define('DB_SERVER', 'localhost');
define('DB_NAME', 'myDatabaseName');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// URL to this script's repository
define('REPO', 'https://github.com/cavo789/db_dump');

/**
 * Check if the password is valid; if not, stop immediately.
 */
function checkPassword()
{
    @session_start();

    // Get the password from the session if any
    $password = $_SESSION['DBDump_password'] ?? '';

    // Get the password from the query string
    if ('' == $password) {
        $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
    }

    // verify if the filled in password is the expected one
    if (($password == '') || (!password_verify($password, APP_PASSWORD))) {
        header('HTTP/1.0 403 Forbidden');
        echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST">Password: <input type="text" name="password" /><input class="Submit" type="submit" name="submit" /></form>';
        die();
    }

    if ('' == ($_SESSION['DBDump_password'] ?? '')) {
        $_SESSION['DBDump_password'] = $password;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Database backup</title>
</head>
<body>
    <?php

        if (!file_exists(__DIR__ . '/MySQLDump.php')) {
            die('File MySQLDump.php is missing, please refers to ' .
                'installation instructions as explained on ' . REPO);
        }

        // Die if the password isn't supplied
        checkPassword();

        set_time_limit(0);
        ignore_user_abort(true);


        require __DIR__ . '/MySQLDump.php';

        $time = -microtime(true);

        $dump = new MySQLDump(new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME));
        
        $backupFileName = 'dump ' . date('Y-m-d H-i') . '.sql.gz';
        $dump->save($backupFileName);

        $time += microtime(true);
        
        echo '<a href="'.$backupFileName.'">Download the backup</a>';
    ?>
</body>
</html>

