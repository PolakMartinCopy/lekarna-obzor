<?
	foreach ( $products as $product ){
		echo '<strong><a href="/' . $product['url'] . '">' . $product['name'] . '</a></strong><br />';
		echo $product['short_description'] . '<br /><br />';
	}
?>