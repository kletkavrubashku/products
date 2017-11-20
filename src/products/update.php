<?
    require_once('src/db.php');
    require_once('view.php');
    require_once('create.php');

    // @set $request["err"] = error_msg
    function prepare_update_product_request(array &$request)
    {
        prepare_create_product_request($request);
        if ($request["err"])
        {
            return;
        }
        prepare_view_product_request($request);
    }

    // @return array("err" => error_msg, "found" => bool)
    function db_update_product(mysqli $conn, int $id, string $name, string $descr, float $price): array
    {
        $select = db_select_product_by_id($conn, $id);
        if ($err = $select["err"])
        {
            return array("err" => $err);
        }
        if (!isset($select["data"]))
        {
            return array("found" => FALSE);
        }

        $update = "
        UPDATE product SET
            name='$name',
            description='$descr',
            price=$price
        WHERE
            id=$id;";

        mysqli_query($conn, $update);
        return array(
            "err" => mysqli_error($conn),
            "found" => TRUE
        );
    }

    // @return array("code" => http_code, "err" => error_msg)
    function update_product(&$conn, array $request): array
    {
        prepare_update_product_request($request);
        if ($request["err"])
        {
            return array(
                "code"	=> 400,
                "err" 	=> $request["err"]
            );
        }

        $conn = @$conn ?: db_connect();       
        $res = db_update_product($conn, $request["id"], $request["name"], $request["description"], $request["price"]);        

        if ($err = $res["err"])
        {
            return array("err" => $err);    
        }
        else if (!$res["found"])
        {
            return array(
                "code" => 404,
                "err" => "Product not found"
            );
        }

        return array("code"	=> 204);
    }