<?
    require_once("/www/src/db.php");
    require_once("main.php");

    // @return error_msg
    function init_0001(mysqli $conn, string $current_version): string
    {
        $version = "0001_init";

        if ($current_version >= $version)
        {
            echo $version . " version already installed\n";
            return "";
        }

        echo $version . " version try install\n";

        $res = db_transact($conn, function() use($conn, $version): string {
            $upgrade = "
            CREATE TABLE product
            (
                id              INT             NOT NULL    AUTO_INCREMENT,
                name            VARCHAR(200)    NOT NULL, 
                description     TEXT            NOT NULL,
                price           DECIMAL(10, 2)  NOT NULL,

                PRIMARY KEY(id),
                INDEX(price)
            );";
            if (!mysqli_query($conn, $upgrade))
            {
                return mysqli_error($conn);
            }
    
            return set_version($conn, $version, "Create product table");
        });

        if ($res)
        {
            return $res;
        }

        echo $version . " version successfully installed\n";
        return "";
    }