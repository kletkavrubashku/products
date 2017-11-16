<?
	require_once('db.php');

	// @return array(error_msg, array(product1, ...))
	function db_list_products(mysqli $conn, string $order_by, string $asc_desc, int $offset, int $row_count): array
	{
		if ()

		$sql = "
		SELECT
			id,
			name,
			description,
			price
		FROM product
		ORDER BY $order_by $asc_desc
		LIMIT $offset, $row_count;";

        mysqli_query($conn, $up_version)
        return mysqli_error($conn);
	}
