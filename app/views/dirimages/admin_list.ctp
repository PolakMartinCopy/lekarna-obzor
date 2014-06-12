<h2>Seznam uploadových obrázků</h2>
<?=$html->link('zpět na obrázky produktu', array('controller' => 'products', 'action' => 'images_list', $product_id))?>
<a href=""></a>
<table class="topHeading" cellpadding="5" cellspacing="3">
	<tr>
		<th>náhled</th>
		<th>název</th>
		<th>&nbsp;</th>
	</tr>
<?
	foreach ( $files as $file ){
		echo '
		<tr>
			<td align="center">
				<a href="' . $file['destination'] . '"><img style="border:1px solid black" src="' . $file['destination'] . '" width="' . $file['width'] . '" height="' . $file['height'] . '" /></a>
			</td>
			<td>
				' . $file['name'] . '
			</td>
			<td>
				' . $html->link('přidat', array('controller' => 'images', 'action' => 'add_dir_image', 'name' => urlencode($file['name']), 'product_id' => $product_id)) . '
			</td>
		</tr>
		';
	}
?>
</table>