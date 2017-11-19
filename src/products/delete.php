<?
    require_once('src/db.php');
    require_once('view.php');    

    // @set $request["err"] = error_msg
    function prepare_delete_product_request(array &$request)
    {
        prepare_view_product_request($request);
    }

    // @return array("err" => error_msg, "found" => bool)
    function db_delete_product(mysqli $conn, int $id): array
    {
        $sql = "
        DELETE FROM product
        WHERE id=$id;";
        mysqli_query($conn, $sql);
        return array(
            "err" => mysqli_error($conn),
            "found" => mysqli_affected_rows($conn) > 0
        );
    }

    // @return array("code" => http_code, "err" => error_msg)
    function delete_product(&$conn, array $request): array
    {
        prepare_delete_product_request($request);
        if ($request["err"])
        {
            return array(
                "code"	=> 400,
                "err" 	=> $request["err"]
            );
        }

        $conn = @$conn ?: db_connect();       
        $res = db_delete_product($conn, $request["id"]);

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