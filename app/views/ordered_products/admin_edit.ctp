<h2>Úprava objednávky č. <?=$id ?></h2>
<p><?=$html->link('zpět na objednávku', array('controller' => 'orders', 'action' => 'view', $order['Order']['id'])) ?></p>
<table id="productList">
	<tr>
		<th>
			Objednaný produkt
		</th>
		<th>
			Změna atributů
		</th>
		<th>
			Množství
		</th>
		<th>
			Cena<br />
			za kus
		</th>
		<th>
			&nbsp;
		</th>
	</tr>
	<?
	foreach ( $products as $product ){
		// celkova cena za pocet kusu krat jednotkova cena
		$total_products_price = $product['OrderedProduct']['product_quantity'] * $product['OrderedProduct']['product_price_with_dph'];
	?>
				<tr style="background-color:silver">
					<td>
						<?=$product['Product']['name'] ?>
						<? 
						// musim vyhodit atributy, pokud nejake produkt ma
						if ( !empty( $product['OrderedProductsAttribute'] ) ){
						?>
							<div class="orderedProductAttributes">
							<?
								foreach( $product['OrderedProductsAttribute'] as $attribute ){
							?>
									<span>- <strong> <?=$attribute['Attribute']['Option']['name'] ?></strong>: <?=$attribute['Attribute']['value'] ?></span><br /> 
							<?
								}
							?>
							</div>
						<? 
						}

						echo '<br /><span style="font-size:11px">cena za kus: <strong>' . $product['OrderedProduct']['product_price_with_dph'] . ' Kč</strong></span>';
						?>
					</td>
					<td>
						<?
							if ( !empty($product['Subs']) ){
								echo $form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id'])));
						?>
							<table style="font-size:10px">
								<?
								foreach ( $product['Subs'] as $sub ){
									if ( !empty($sub['Value']) ){
										echo '<tr><th align="right">' . $sub['Option']['name'] . '</th>';
										echo '<td><select name="data[OrderedProduct][Option][' . $sub['Option']['id'] . ']" style="font-size:10px;">';
										foreach ( $sub['Value'] as $value ){
											$selected = '';
											foreach ( $product['OrderedProductsAttribute'] as $attr ){
												if ( $attr['attribute_id'] == $value['id']){
													$selected = ' selected="selected"';
												}
											}
											echo '<option value="' . $value['id'] . '"' . $selected . '>' . $value['value'] . '</option>';
										}
										echo '</select></td></tr>';
									}
								}
								?>
								<tr>
									<th>&nbsp;</th>
									<td><?=$form->submit('změnit atributy') ?></td>
								</tr>
							</table>
						<?
								echo $form->hidden('OrderedProduct.id', array('value' => $product['OrderedProduct']['id']));
								echo $form->hidden('OrderedProduct.change_switch', array('value' => 'attributes_change'));
								echo $form->end();
							} else {
								echo '&nbsp;';
							}
						?>
					</td>
					<td>
						<?
							echo $form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id'])));
							echo $form->input('OrderedProduct.product_quantity', array('value' => $product['OrderedProduct']['product_quantity'], 'label' => false, 'div' => false, 'size' => 3)) . ' ks';
							echo $form->input('OrderedProduct.id', array('value' => $product['OrderedProduct']['id']));
							echo $form->hidden('OrderedProduct.change_switch', array('value' => 'quantity_change'));
						?>
							<br />
						<?
							echo $form->submit('změnit počet');
							echo $form->end();
						?>
							
					</td>
					<td>
					<?
						echo $form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id'])));
					?>
						<select name="data[OrderedProduct][product_price_with_dph]">
							<option value="<?=$product['Product']['retail_price_with_dph'] ?>"<?=( $product['Product']['retail_price_with_dph'] == $product['OrderedProduct']['product_price_with_dph'] ? ' selected="selected"' : "" ) ?>>
								základní cena: <?=$product['Product']['retail_price_with_dph'] ?> Kč
							</option>
						<?php if ($product['Product']['discount_common'] > 0) { ?>
							<option value="<?php echo $product['Product']['discount_common'] ?>"<?php echo ($product['Product']['discount_common'] == $product['OrderedProduct']['product_price_with_dph'] ? ' selected="selected"' : "") ?>>
								běžná sleva: <?php echo $product['Product']['discount_common']?> Kč
							</option>
						<?php } ?>
						<?php if ($product['Product']['discount_member'] > 0) { ?>
							<option value="<?php echo $product['Product']['discount_member'] ?>"<?php echo ($product['Product']['discount_member'] == $product['OrderedProduct']['product_price_with_dph'] ? ' selected="selected"' : "") ?>>
								členská sleva: <?php echo $product['Product']['discount_member']?> Kč
							</option>
						<?php } ?>
						</select>
						<br />
						<span style="font-size:10px">ručně:</span> <?=$form->input('OrderedProduct.custom_price', array('label' => false, 'size' => 5)); ?>
						<?
							echo $form->input('OrderedProduct.id', array('value' => $product['OrderedProduct']['id']));
							echo $form->hidden('OrderedProduct.change_switch', array('value' => 'price_change'));
							echo $form->submit('změnit cenu');
							echo $form->end();
						?>
					</td>
					<td>
						<?=$html->link('smazat produkt', array('controller' => 'ordered_products', 'action' => 'delete', $product['OrderedProduct']['id'])) ?>
					</td>
				</tr>
	<?	
	}
	?>
	<tr>
		<th colspan="2" align="right">
			cena za zboží celkem:
		</th>
		<td colspan="2" align="right">
			<?=$order['Order']['subtotal_with_dph']?> Kč
		</td>
	</tr>
	<tr>
		<td colspan="2" align="right">
			způsob doručení:
		</td>
		<td colspan="2" align="right">
			<?=$form->create('Order', array('url' => array('action' => 'edit_shipping', $order['Order']['id'])));?>
			<?=$form->select('Order.shipping_id', $shipping_choices, $order['Order']['shipping_id'], array('empty' => false));?>
			<?=$form->submit('změnit');?>
			<?=$form->end();?>
		</td>
	</tr>
	<tr>
		<th colspan="2" align="right">
			celková cena objednávky:
		</th>
		<td colspan="2" align="right">
			<?=( $order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost'])?> Kč
		</td>
	</tr>
</table>

<h3>Přidat nový produkt</h3>
<?=$form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id']))); ?>
<table>
	<tr>
		<td colspan="2">
			<p style="font-size:12px">Napište jakoukoliv část názvu produktu. ("metr" vyhledá např. gluko<strong>metr</strong> i tono<strong>metr</strong> ).</p>
		</td>
	</tr>
	<tr>
		<th>
			vyhledat produkt
		</th>
		<th>
			<?=$form->input('OrderedProduct.query', array('label' => false)) ?>
		</th>
	</tr>
	<tr>
		<td>
			<?=$form->submit('vyhledat') ?>
		</td>
	</tr>
</table>
<?=$form->hidden('OrderedProduct.change_switch', array('value' => 'product_query')); ?>
<?=$form->end(); ?>
<?
if ( isset($query_products) ){
	echo $form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id'])));
?>
<table>
<?
	foreach ( $query_products as $product ){
?>
				<tr style="background-color:silver">
					<td>
						<?=$product['Product']['name'] ?>
					</td>
					<td>
						<?
							if ( !empty($product['Subs']) ){
						?>
							<table style="font-size:10px">
								<?
								foreach ( $product['Subs'] as $sub ){
									if ( !empty($sub['Value']) ){
								?>
								<tr>
									<th align="right"><?=$sub['Option']['name']?></th>
									<td>
										<select name="data[OrderedProduct][<?=$product['Product']['id']?>][Option][<?=$sub['Option']['id']?>]" style="font-size:10px;">';
										<? foreach ( $sub['Value'] as $value ){ ?>
											<option value="<?=$value['id']?>"><?=$value['value']?></option>;
										<? } ?>
										</select>
									</td>
								</tr>
								<?	}
								}
								?>
							</table>
						<?
							} else {
								echo '&nbsp;';
							}
						?>
					</td>
					<td>
						<?
							echo $form->input('OrderedProduct.' . $product['Product']['id'] . '.product_quantity', array('value' => '1', 'label' => false, 'div' => false, 'size' => 3)) . ' ks';
						?>
					</td>
					<td>
						<select name="data[OrderedProduct][<?=$product['Product']['id'] ?>][product_price_with_dph]">
							<option value="<?=$product['Product']['retail_price_with_dph'] ?>">
								základní cena: <?=$product['Product']['retail_price_with_dph'] ?> Kč
							</option>
						<?php if ($product['Product']['discount_common'] > 0) { ?>
							<option value="<?php echo $product['Product']['discount_common'] ?>">
								běžná sleva: <?php echo $product['Product']['discount_common']?> Kč
							</option>
						<?php } ?>
						<?php if ($product['Product']['discount_member'] > 0) { ?>
							<option value="<?php echo $product['Product']['discount_member'] ?>">
								členská sleva: <?php echo $product['Product']['discount_member']?> Kč
							</option>
						<?php } ?>
						</select>
						<br />
						<span style="font-size:10px">ručně:</span> <?=$form->input('OrderedProduct.' . $product['Product']['id'] . '.custom_price', array('label' => false, 'size' => 5)); ?>
					</td>
					<td>
						<?=$form->hidden('OrderedProduct.' . $product['Product']['id'] . '.product_id', array('value' => $product['Product']['id'])) ?>
						<?=$form->submit('přidat', array('name' => 'data[OrderedProduct][' . $product['Product']['id'] . '][add_it]', 'value' => $product['Product']['id'])) ?>
					</td>
				</tr>
	<?	
	}
?>
</table>
<?
	echo $form->hidden('OrderedProduct.change_switch', array('value' => 'add_product'));
	echo $form->end();
}
?>