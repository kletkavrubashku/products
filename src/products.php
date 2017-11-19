<?
	require_once('db.php');

	// @return array("err" => error_msg, [$request])
	function validate_products_request(array $request): array
	{
		// order_by
		$order_by = @$request["order_by"] ?: "id";
		$support_ordering = array("price", "id");
		if (!in_array($order_by, $support_ordering))
		{
			return array("err" => "Ordering supported only by '" . implode("', '", $support_ordering) . "' columns");
		}
		$request["order_by"] = $order_by;
		
		// asc_desc
		$asc_desc = @$request["asc_desc"] ?: "";
		$support_asc_desc = array("", "asc", "desc");
		if (!in_array($asc_desc, $support_asc_desc))
		{
			return array("err" => "Invalid ordering '$asc_desc'");
		}
		$request["asc_desc"] = $asc_desc;
		
		// page
		$page = @$request["page"] ?: 1;
		if (!is_numeric($page) || ($page = intval($page)) < 0)
		{
			return array("err" => "Invalid page '$page'");
		}
		$request["page"] = $page;

		// row_count
		$row_count = @$request["row_count"] ?: getenv("APP_PRODUCTS_DEFAULT_PAGE_SIZE");
		if (!is_numeric($row_count) || ($row_count = intval($row_count)) < 0 || $row_count > getenv("APP_PRODUCTS_MAX_PAGE_SIZE"))
		{
			return array("err" => "Invalid row count '$row_count'");
		}
		$request["row_count"] = $row_count;

		return $request;
	}

	// @return array("err" => error_msg, ["data" => array(product1, ...)])
	function db_select_products(mysqli $conn, string $order_by, string $asc_desc, int $offset, int $row_count): array
	{
		$sql = "
		SELECT
			id,
			name,
			description,
			price
		FROM product
		ORDER BY $order_by $asc_desc
		LIMIT $offset, $row_count;";

		$result = mysqli_query($conn, $sql);
		if (!$result)
		{
			return array("err" => mysqli_error($conn));
		}

		$products = array();
		while ($row = mysqli_fetch_assoc($result))
		{
			$row["id"] = intval($row["id"]);
			$products[] = $row;
		}
		return array(
			"err" 	=> "",
			"data" 	=> $products
		);
	}

	// @return array("code" => http_code, ["err" => error_msg], ["data" => array(product1, ...)])
	function select_products(&$conn, array $request): array
	{
		$request = validate_products_request($request);
		if ($request["err"])
		{
			return array(
				"code"	=> 400,
				"err" 	=> $request["err"]
			);
		}

		$offset = ($request["page"] - 1) * $request["row_count"];

		$conn = @$conn ?: db_connect();
		$resp = db_select_products($conn, $request["order_by"], $request["asc_desc"], $offset, $request["row_count"]);
		
		if ($err = $resp["err"])
		{
			return array(
				"code"	=> 500,
				"err" 	=> $err
			);
		}
		return array(
			"code"	=> 200,
			"data" 	=> $resp["data"]
		);
	}