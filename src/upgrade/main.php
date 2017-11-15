<?
    function ensure_versioning($conn)
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS version
        (
            id              VARCHAR(100)    NOT NULL PRIMARY KEY,
            date            DATETIME        NOT NULL, 
            description     TEXT            NOT NULL
        );";

        return mysqli_query($conn, $sql);
    }

    function upgrade($conn)
    {
        require_once('0001_init.php');

        $sql = "
        SELECT id
        FROM version
        ORDER BY id DESC
        LIMIT 1;
        ";
        $result = mysqli_query($conn, $sql);
        if (!$result)
        {
            return $result;
        }

        $current_version = "";
        if ($row = mysqli_fetch_assoc($result))
        {
            $current_version = $row['id'];
        }

        if ($err = init_0001($conn, $current_version)) { return $err; }
        
        return NULL;
    }