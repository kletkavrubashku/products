<?
    require_once('src/db.php');

    // @set $request["err"] = error_msg
    function prepare_create_product_request(array &$request)
    {
        // name    
        $name = $request["name"];
        if (empty($request["name"]))
        {
            $request["err"] = "Empty name '$name'";
            return;
        }

        // description
        $request["description"] = @$request["description"] ?: "";

        // price
        $price = $request["price"];
        if (!preg_match("/^\d+(\.\d+)?$/", $price))
        {
            $request["err"] = "Invalid price format '$price'";
            return;
        }
        $request["price"] = floatval($price);
        
        unset($request["err"]);
    }

    // @return error_msg
    function db_create_product(mysqli $conn, string $name, string $descr, float $price): string
    {
        $sql = "
        INSERT INTO product (
            name,
            description,
            price
        ) VALUES (
            '$name',
            '$descr',
            $price
        );";

        mysqli_query($conn, $sql);
        return mysqli_error($conn);
    }
    
    // @return array("code" => http_code, "err" => error_msg)
    function create_product(&$conn, array $request): array
    {
        prepare_create_product_request($request);
        if ($request["err"])
        {
            return array(
                "code"	=> 400,
                "err" 	=> $request["err"]
            );
        }

        $conn = @$conn ?: db_connect();       
        $err = db_create_product($conn, $request["name"], $request["description"], $request["price"]);
        if ($err)
        {
            return array("err" => $err);
        }

        return array("code"	=> 204);
    }