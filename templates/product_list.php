<?
	require_once("src/product_list.php");
	$items = list_products();
?>
<html>
	<body>
	<?
		foreach ($items as $item)
		{
			echo "<p>$item</p>";
		}
	?>
	</body>
</html>
 