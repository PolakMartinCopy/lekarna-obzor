<h1>Seznam produktů s atributem '<?=$products[0]['Attribute']['Option']['name'] . ': ' . $products[0]['Attribute']['Value']['name'] ?>'</h1>

<?
	if ( !empty($products) ){
?>
	<table class="top_headed" cellpadding="5" cellspacing="3">
		<tr>
			<th>
				název
			</th>
			<th>
				&nbsp;
			</th>
		</tr>
<?
	foreach ( $products as $product ){
?>
		<tr>
			<td>
				<?=$product['Product']['name']?><br />
				<?
					$categories_list = array();
					foreach ( $product['Product']['Category'] as $category ){
						$categories_list[] = $category['name'];
					}
				?>
				<span style="font-size:11px;">(<?=implode('<br />', $categories_list) ?>)</span>
			</td>
			<td>
				<?=$html->link('atributy', array('controller' => 'products', 'action' => 'attributes_list', 'id' => $product['Product']['id'])) ?> 
			</td>
		</tr>
<?
	}
?>
	</table>
<?
		//echo $paginator->counter(array('format' => 'Page %page% of %pages%, showing %current% records out of%count% total, starting on record %start%, ending on %end%'));
	}
?>
