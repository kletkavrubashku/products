<?
    // @return error_msg
    function ensure_versioning(mysqli $conn): string
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS version
        (
            id              VARCHAR(100)    NOT NULL PRIMARY KEY,
            date            DATETIME        NOT NULL, 
            description     TEXT            NOT NULL
        );";

        mysqli_query($conn, $sql);
        return mysqli_error($conn);
    }

    // @return error_msg
    function set_version(mysqli $conn, string $version, string $descr): string
    {
        $up_version = "
        INSERT INTO version
        VALUES (
            '$version',
            NOW(),
            '$descr'
        );";

        mysqli_query($conn, $up_version);
        return mysqli_error($conn);
    }

    // @return error_msg
    function upgrade(mysqli $conn): string
    {
        require_once('0001_init.php');

        $sql = "
        SELECT MAX(id) as id
        FROM version;
        ";
        $result = mysqli_query($conn, $sql);
        if (!$result)
        {
            return $result;
        }

        $current_version = "";
        if ($row = mysqli_fetch_assoc($result))
        {
            $current_version = @$row["id"] ?: "";
        }
        echo("Current version is '$current_version'\n");

        if ($err = init_0001($conn, $current_version)) { return $err; }
        
        return "";
    }