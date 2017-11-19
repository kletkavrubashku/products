<?
	require_once("src/products.php");
	require_once("src/products_paging.php");
	$lazy_conn = NULL;

	$items = select_products($lazy_conn, $_GET);
	$page_count = products_page_count($lazy_conn, $_GET);

	$lazy_conn and mysqli_close($lazy_conn);
	
	require_once("product_list.html.php");