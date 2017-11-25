<?
	function print_error_page(int $code, string $msg)
	{
		require_once("templates/products/error.html");
	}	

	function print_view_page(string $id, string $name, string $description, float $price, string $image)
	{
		require_once("templates/products/view.html");
	}

	function print_edit_page(string $id, string $name, string $description, float $price, string $image)
	{
		require_once("templates/products/edit.html");
	}

	function print_list_page(array $products, $prev, $next)
	{
		require_once("templates/products/list.html");
	}

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
				if ($code >= 300)
				{
					print_error_page($code, $item["err"]);
					break;
				}

				$product = $item["data"];				
				if ($_REQUEST["edit"])
				{
					print_edit_page($product["id"], $product["name"], $product["description"], $product["price"], $product["image"]);
				}
				else
				{
					print_view_page($product["id"], $product["name"], $product["description"], $product["price"], $product["image"]);
				}
			}
			else
			{
				require_once("src/products/list.php");
				require_once("src/products/paging.php");

				$products_res = select_products($lazy_conn, $_REQUEST);
				http_response_code($code = $products_res["code"]);
				if ($code >= 300)
				{
					print_error_page($code, $products_res["err"]);
					break;
				}

				$pages_count_res = products_pages_count($lazy_conn, $_REQUEST);
				http_response_code($code = $pages_count_res["code"]);
				if ($code >= 300)
				{
					print_error_page($code, $pages_count_res["err"]);
					break;
				}

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
				print_list_page($products_res["data"], $prev, $next);
			}
			break;
		case "POST":			
			if ($_REQUEST["edit"])
			{
				require_once("src/products/update.php");
				
				$item = update_product($lazy_conn, $_REQUEST);
				http_response_code($code = $item["code"]);
				if ($code >= 300)
				{
					print_error_page($code, $item["err"]);
					break;
				}
				header("Location: /products/" . $item["id"] . "/", TRUE, 303);
			}
			else if ($_REQUEST["delete"])
			{
				require_once("src/products/delete.php");
				
				$res = delete_product($lazy_conn, $_REQUEST);
				http_response_code($code = $res["code"]);
				if ($code >= 300)
				{
					print_error_page($code, $res["err"]);
					break;
				}
				header("Location: /products/", TRUE, 303);
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