<?
	require_once("src/products.php");

	$order_by = @$_GET["order_by"] ?: "id";
	$asc_desc = @$_GET["asc_desc"] ?: "";

	$items = select_products($order_by, $asc_desc, 0, 100);
	
	require_once("product_list.html.php");