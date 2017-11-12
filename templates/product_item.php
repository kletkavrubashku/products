<?
    require_once("src/product_item.php");
    $item = get_product_by_id($_GET["id"]);
?>
<html>
	<body>
	<?
		echo "<p>$item</p>";
	?>
	</body>
</html>
