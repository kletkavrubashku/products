<?
    $host = getenv("MYSQL_HOST");
    $user = getenv("MYSQL_USER");
    $pass = getenv("MYSQL_PASSWORD");
    $db   = getenv("MYSQL_DATABASE");
    $retry_count = getenv("APP_MYSQL_RETRY_COUNT");

    $mysql = false;
    for ($try = 0; ; $try++)
    {
        $mysql = mysqli_connect($host, $user, $pass, $db);
        if (!$mysql)
        {
            $msg = "Connection failed: " . mysqli_connect_error() . "\n";
            if ($try >= $retry_count)
            {
                exit($msg);
            }
            echo($msg);
            sleep(1);
        }        
    }