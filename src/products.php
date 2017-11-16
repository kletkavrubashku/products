<?
	require_once('db.php');

	// @return error_msg
	function validate_products_query(string $order_by, string $asc_desc, string $offset, string $row_count)
	{
		$support_ordering = array("price", "id");
		if (!in_array($order_by, $support_ordering))
		{
			return "Ordering supported only by $support_ordering columns";
		}

		$support_asc_desc = array("", "asc", "desc");
		if (!in_array($asc_desc, $support_asc_desc))
		{
			return "Invalid ordering '$asc_desc'";
		}
		
		if (!is_numeric($offset) || intval($offset) < 0)
		{
			return "Invalid offset '$offset'";
		}

		if (!is_numeric($row_count) || intval($row_count) < 0)
		{
			return "Invalid row count '$row_count'";
		}

		return "";
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

	// @return array("code" => http_code, ["err" => error_msg,] ["data" => array(product1, ...)])
	function select_products(string $order_by, string $asc_desc, string $offset, string $row_count): array
	{
		if ($err = validate_products_query($order_by, $asc_desc, $offset, $row_count))
		{
			return array(
				"code"	=> 400,
				"err" 	=> $err
			);
		}
		$conn = db_connect();
		$resp = db_select_products($conn, $order_by, $asc_desc, $offset, $row_count);
		mysqli_close($conn);
		
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
