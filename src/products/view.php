<?
    require_once('src/db.php');

    // @set $request["err"] = error_msg
    function prepare_view_product_request(array &$request)
    {
        $id = $request["id"];
		if (!is_numeric($id) || ($id = intval($id)) <= 0)
		{
            $request["err"] = "Invalid id '$id'";
            return;
		}
        $request["id"] = $id;
        
        unset($request["err"]);
    }

    // @return array("err" => error_msg, "data" => product)
	function db_select_product_by_id(mysqli $conn, int $id): array
	{
		$sql = "
		SELECT
			id,
			name,
			description,
			price,
			image
		FROM product
        WHERE
            id = $id;";

		$rows = mysqli_query($conn, $sql);
		if (!$rows)
		{
			return array("err" => mysqli_error($conn));
		}

		$product = array();
		if ($row = mysqli_fetch_assoc($rows))
		{
			$row["id"] = intval($row["id"]);          
            return array(
                "data" 	=> $row
            );
		}
		return array();
    }
    

	// @return array("code" => http_code, "err" => error_msg, "data" => product)
    function select_product_by_id(&$conn, array $request): array
    {
        prepare_view_product_request($request);
		if ($request["err"])
		{
			return array(
				"code"	=> 400,
				"err" 	=> $request["err"]
			);
		}

		$conn = @$conn ?: db_connect();
		$resp = db_select_product_by_id($conn, $request["id"]);
		
		if ($err = $resp["err"])
		{
			return array("err" => $err);
        }
        if (!$resp["data"])
        {
            return array(
				"code"	=> 404,
				"err" 	=> "Product not found"
			);
        }

		return array(
			"code"	=> 200,
			"data" 	=> $resp["data"]
		);
    }