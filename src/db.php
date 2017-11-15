<?
    function db_connect()
    {
        $host = getenv("MYSQL_HOST");
        $user = getenv("MYSQL_USER");
        $pass = getenv("MYSQL_PASSWORD");
        $db   = getenv("MYSQL_DATABASE");
        $retry_count = getenv("APP_MYSQL_RETRY_COUNT");
    
        $conn = false;
        for ($try = 0; ; $try++)
        {
            $conn = mysqli_connect($host, $user, $pass, $db);
            if (!$conn)
            {
                $msg = "Connection failed: " . mysqli_connect_error() . "\n";
                if ($try >= $retry_count)
                {
                    exit($msg);
                }
                echo($msg);
                sleep(1);
            }
            else {
                break;
            }
        }
        return $conn;
    }

    function db_transact(mysqli $conn, callable $block, int $flags = MYSQLI_TRANS_START_READ_WRITE)
    {
        if (!mysqli_autocommit($conn, FALSE))
        {
            return mysqli_error($conn);
        }

        if (!mysqli_begin_transaction($conn, $flags))
        {
            $err = mysqli_error($conn);
            mysqli_autocommit($conn, TRUE);
            return $err ;
        }

        if ($err = $block())
        {
            mysqli_rollback($conn);
            mysqli_autocommit($conn, TRUE);
            return $err;
        }

        if (!mysqli_commit($conn))
        {
            $err = mysqli_error($conn);
            mysqli_rollback($conn);
            mysqli_autocommit($conn, TRUE);
            return $err;
        }
        
        mysqli_autocommit($conn, TRUE);
        return NULL;
    }