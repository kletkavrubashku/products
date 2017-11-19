<?
    require_once('db.php');

    // @return array("err" => error_msg, [$request])
    function validate_products_page_count_request(array $request): array
    {
        $row_count = @$request["row_count"] ?: getenv("APP_PRODUCTS_DEFAULT_PAGE_SIZE");
        if (!is_numeric($row_count) || ($row_count = intval($row_count)) < 0 || $row_count > getenv("APP_PRODUCTS_MAX_PAGE_SIZE"))
        {
            return array("err" => "Invalid row count '$row_count'");
        }
        $request["row_count"] = $row_count;

        return $request;
    }

	// @return array("err" => error_msg, ["data" => int])
	function db_products_count(mysqli $conn): array
	{
		$sql = "
		SELECT
			COUNT(*) as count
		FROM product;";

		$result = mysqli_query($conn, $sql);
		if (!$result)
		{
			return array("err" => mysqli_error($conn));
		}

		return array(
			"err" 	=> "",
			"data" 	=> mysqli_fetch_assoc($result)["count"]
		);
    }
    
    // @return array("code" => http_code, ["err" => error_msg], ["data" => int)
    function products_page_count(&$conn, array $request): array
    {
        $request = validate_products_page_count_request($request);
        if ($request["err"])
		{
			return array(
				"code"	=> 400,
				"err" 	=> $request["err"]
			);
		}

        $conn = @$conn ?: db_connect();
		$resp = db_products_count($conn);
		
		if ($err = $resp["err"])
		{
			return array(
				"code"	=> 500,
				"err" 	=> $err
			);
        }
        
        $page_count = ceil($resp["data"] / $request["row_count"]);
        return array(
			"code"	=> 200,
			"data" 	=> intval($page_count)
		);
    }