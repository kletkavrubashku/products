<?
    require_once('db.php');
    require_once('upgrade/main.php');

    $conn = db_connect();

    echo("Initialize...\n");
    if ($err = ensure_versioning($conn))
    {
        echo("Install versioning failed: $err\n");
        exit(1);
    }
    echo("Ensure versioning completed successfully\n");
    
    echo("Start upgrade\n");
    if ($err = upgrade($conn))
    {
        echo("Upgrade failed: $err\n");
        exit(1);
    }
    echo("Upgrade completed successfully\n");
    mysqli_close($conn);