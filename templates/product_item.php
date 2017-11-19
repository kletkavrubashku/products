<?
	require_once("src/products/view.php");
	
	$lazy_conn = NULL;
	$item = select_product_by_id($lazy_conn, $_GET);
	$lazy_conn and mysqli_close($lazy_conn);
	
	require_once("product_item.html.php");
