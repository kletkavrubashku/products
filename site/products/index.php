<?
	$lazy_conn = NULL;

	$_REQUEST = array_merge($_REQUEST, $_FILES);

	switch ($_SERVER["REQUEST_METHOD"])
	{
		case "GET":
			if ($_REQUEST["id"])
			{
				require_once("src/products/view.php");
				
				$item = select_product_by_id($lazy_conn, $_REQUEST);
				
				require_once("templates/products/view.html");
			}
			else
			{
				require_once("src/products/list.php");
				require_once("src/products/paging.php");

				$items = select_products($lazy_conn, $_REQUEST);
				$page_count = products_page_count($lazy_conn, $_REQUEST);
				
				require_once("templates/products/list.html");
			}
			break;
		case "POST":
			if ($_REQUEST["edit"])
			{
				require_once("src/products/update.php");
				
				$res = update_product($lazy_conn, $_REQUEST);
				var_dump($res);
			}
			else if ($_REQUEST["delete"])
			{
				require_once("src/products/delete.php");
				
				$res = delete_product($lazy_conn, $_REQUEST);
				var_dump($res);
			}
			else
			{
				require_once("src/products/create.php");
				
				$res = create_product($lazy_conn, $_REQUEST);
				var_dump($res);
			}			
			break;
   	}

	$lazy_conn and mysqli_close($lazy_conn);