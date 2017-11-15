<?
    require_once("/www/src/db.php");

    function init_0001($conn, $current_version)
    {
        $version = "0001_init";

        if ($current_version >= $version)
        {
            echo $version . " version already installed\n";
            return NULL;
        }

        echo $version . " version try install\n";

        $res = db_transact($conn, function() use($conn, $version) {
            $upgrade = "
            SELECT 1 FROM version;
            ";
            if (!mysqli_query($conn, $upgrade))
            {
                return mysqli_error($conn);
            }
    
            $up_version = "
            INSERT INTO version
            VALUES (
                '$version',
                NOW(),
                'Create product table'
            );";
            if (!mysqli_query($conn, $up_version))
            {
                return mysqli_error($conn);
            }
            return NULL;
        });

        if ($res)
        {
            return $res;
        }

        echo $version . " version successfully installed\n";
        return NULL;
    }