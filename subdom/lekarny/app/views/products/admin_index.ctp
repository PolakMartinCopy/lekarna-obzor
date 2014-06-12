<h1><?php echo $category['Category']['name']?> - seznam produktů</h1>

<?
	if ( !empty($products) ){
?>
	<table class="top_headed" cellpadding="5" cellspacing="3">
		<tr>
			<th>
				název
			</th>
		</tr>
<?
	$odd = ' class="odd"';
	foreach ( $products as $product ){
?>
		<tr<?php echo $odd?>>
			<td>
				<?=$html->link($product['Product']['name'], array('controller' => 'products', 'action' => 'view', $product['Product']['id']))?><br />
				<?
					$categories_list = array();
					foreach ( $product['Category'] as $category ){
						$categories_list[] = $category['name'];
					}
				?>
				<span style="font-size:11px;">(<?=implode('<br />', $categories_list) ?>)</span>
			</td>
		</tr>
<?
		$odd = $odd == ' class="odd"' ? '' : ' class="odd"';
	}
?>
	</table>
<?
		//echo $paginator->counter(array('format' => 'Page %page% of %pages%, showing %current% records out of%count% total, starting on record %start%, ending on %end%'));
	}
?>