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
				http_response_code($code = $item["code"]);
				if ($code != 200)
				{
					$err = $item["err"];
					require_once("templates/products/error.html");
					break;
				}

				$product = $item["data"];
				
				if ($_REQUEST["edit"])
				{
					require_once("templates/products/edit.html");
				}
				else
				{
					require_once("templates/products/view.html");	
				}
			}
			else
			{
				require_once("src/products/list.php");
				require_once("src/products/paging.php");

				$products_res = select_products($lazy_conn, $_REQUEST);
				http_response_code($code = $products_res["code"]);
				if ($code != 200)
				{
					$err = $products_res["err"];
					require_once("templates/products/error.html");
					break;
				}

				$pages_count_res = products_pages_count($lazy_conn, $_REQUEST);
				http_response_code($code = $pages_count_res["code"]);
				if ($code != 200)
				{
					$err = $pages_count_res["err"];
					require_once("templates/products/error.html");
					break;
				}

				$products = $products_res["data"];

				$prev = NULL;
				$next = NULL;
				$page = $products_res["page"];
				if ($page > 1)
				{
					$query = $_GET;
					$query["page"] = $page - 1;
					$prev = strtok($_SERVER["REQUEST_URI"],'?') . "?" . http_build_query($query);
				}
				if ($page < $pages_count_res["data"])
				{
					$query = $_GET;
					$query["page"] = $page + 1;
					$next = strtok($_SERVER["REQUEST_URI"],'?') . "?" . http_build_query($query);
				}
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