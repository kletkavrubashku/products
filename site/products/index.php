<?
	$lazy_conn = NULL;
	var_dump($_SERVER["REQUEST_METHOD"]);

	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		if ($_GET["id"])
		{
			require_once("src/products/view.php");
			
			$item = select_product_by_id($lazy_conn, $_GET);
			
			require_once("templates/products/view.html");
		}
		else
		{
			require_once("src/products/list.php");
			require_once("src/products/paging.php");

			$items = select_products($lazy_conn, $_GET);
			$page_count = products_page_count($lazy_conn, $_GET);
			
			require_once("templates/products/list.html");
		}		
	}
	else if ($_SERVER["REQUEST_METHOD"] === "POST") {
		require_once("src/products/create.php");

		$res = create_product($lazy_conn, $_POST);
		var_dump($res);
   	}

	$lazy_conn and mysqli_close($lazy_conn);