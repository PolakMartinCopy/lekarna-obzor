<div>
	<h2>Smazané produkty</h2>
	<?
		if ( empty($products) ){
	?>
		<p>Žádné proukty, které nejsou přiřazeny do žádné kategorie, nebyly nalezeny.
	<?
		} else{
		// vypisu produkty, ktere byly vymazane
	?>
		<table class="topHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>Id</th>
				<th>Název</th>
				<th>Cena</th>
				<th>&nbsp;</th>
			</tr>
		<?
			foreach ( $products as $product ){
		?>
			<tr>
				<td><?=$product['Product']['id']?></td>
				<td><?=$product['Product']['name']?></td>
				<td><?=$product['Product']['retail_price_with_dph']?></td>
				<td style="font-size:12px;">
					<?=$html->link('Zařadit do prodeje', array('controller' => 'categories_products', 'action' => 'add', $product['Product']['id'])) ?> |
					<?=$html->link('Smazat úplně', array('controller' => 'products', 'action' => 'delete_total', $product['Product']['id']), array(), 'Opravdu chcete tento produkt smazat?')?>
				</td>
			</tr>
		<?	
			}
		?>
		</table>
	<?
		} 
	?>
</div>