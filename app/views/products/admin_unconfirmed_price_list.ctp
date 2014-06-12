<h1>Produkty s nepotvrzenou cenou</h1>

<?php echo $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'unconfirmed_price_list')))?>
<table class="topHeading" cellpadding="5" cellspacing="3">
	<tr>
		<th>ID</th>
		<th>Název / SN URL</th>
		<th>MO cena - obecna sleva / SN cena</th>
		<th>sleva pro prihlasene / SN sleva</th>
	</tr>
<?php foreach ($products as $product) { ?>
	<tr>
		<td><?php echo $product['Product']['id']?></td>
		<td nowrap="nowrap">
			<?php echo $html->link($product['Product']['name'], '/' . $product['Product']['url'])?> / <?php echo $html->link('SN Detail', $product['Product']['sportnutrition_url'])?><br/>
			<small><?php echo $product['Product']['compared']?></small>
		</td>
		<td nowrap="nowrap">
			<?php
			echo $form->hidden('Product.' . $product['Product']['id'] . '.id', array('value' => $product['Product']['id']));
			if ($product['Product']['sportnutrition_price'] != 0) {
				echo $form->hidden('Product.' . $product['Product']['id'] . '.sportnutrition_price', array('value' => $product['Product']['sportnutrition_price']));
				echo $form->input('Product.' . $product['Product']['id'] . '.sportnutrition_price_confirmed', array('label' => false, 'type' => 'checkbox', 'div' => false));
			}
			echo $product['Product']['retail_price_with_dph'] . ' - ' . $product['Product']['discount_common']?><br/>
			<?php echo $product['Product']['sportnutrition_price']?>
		</td>
		<td nowrap="nowrap"><?php
			if ($product['Product']['sportnutrition_discount_price'] != 0) {
				echo $form->hidden('Product.' . $product['Product']['id'] . '.sportnutrition_discount_price', array('value' => $product['Product']['sportnutrition_discount_price']));
				echo $form->input('Product.' . $product['Product']['id'] . '.sportnutrition_discount_price_confirmed', array('label' => false, 'type' => 'checkbox', 'div' => false));
			}
			echo $product['Product']['discount_member']?><br/>
			<?php echo $product['Product']['sportnutrition_discount_price']?>
		</td>
<?php } ?>
</table>

<?php echo $form->submit('Uložit')?>
<?php echo $form->end()?>