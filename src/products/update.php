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

        // id    
        $id = $request["id"];
		if (!is_numeric($id) || ($id = intval($id)) <= 0)
		{
            $request["err"] = "Invalid id '$id'";
            return;
		}
        $request["id"] = $id;        
        
        unset($request["err"]);
    }

    // @return array("err" => error_msg, "found" => bool)
    function db_update_product(mysqli $conn, int $id, string $name, string $descr, float $price): array
    {
        $found = false;
        $res = db_transact($conn, function() use($conn, $id, $name, $descr, $price, &$found): string {
            $select = db_select_product_by_id($conn, $id);
            if ($err = $select["err"])
            {
                return array("err" => $err);
            }
            $found = isset($select["data"]);
            if (!$found)
            {
                return "";
            }

            $update = "
            UPDATE product SET
                name='$name',
                description='$descr',
                price=$price
            WHERE
                id=$id;";
            mysqli_query($conn, $update);
            return mysqli_error($conn);            
        });

        return array(
            "err" => $res,
            "found" => $found
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