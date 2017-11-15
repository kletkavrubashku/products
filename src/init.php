<?
    require_once('db.php');
    require_once('upgrade/main.php');

    $conn = db_connect();

    echo("Initialize...\n");
    if (!ensure_versioning($conn))
    {
        exit("Install versioning failed: " . mysqli_error($conn) . "\n");
    }
    echo("Ensure versioning completed successfully\n");
    
    echo("Start upgrade\n");
    if ($err = upgrade($conn))
    {
        exit("Upgrade failed: $err\n");
    }
    echo("Upgrade completed successfully\n");
    mysqli_close($conn);