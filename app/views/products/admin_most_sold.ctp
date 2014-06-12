<h1>Nejprodávanější</h1>

<script>
	$(document).ready(function(){
		data = <?php echo $active_products?>;
		$('input.ProductName').each(function() {
			var autoCompleteElement = this;
			var formElementName = $(this).attr('name');
			var formElementId = $(this).attr('id');
			var hiddenElementID  = 'ProductId';
			var hiddenElementName = 'data[Product][id]';
			/* create new hidden input with name of orig input */
			$(this).after("<input type=\"hidden\" name=\"" + hiddenElementName + "\" id=\"" + hiddenElementID + "\" />");
			$(this).autocomplete({
				source: data, 
				select: function(event, ui) {
					var selectedObj = ui.item;
					$(autoCompleteElement).val(selectedObj.label);
					$('#'+hiddenElementID).val(selectedObj.value);
					return false;
				}
			});
		});
	});
</script>

<div class="demo">

<div class="ui-widget">
	<?php echo $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'most_sold_add')))?>
	<?php echo $form->input('Product.name', array('label' => false, 'type' => 'text', 'class' => 'ProductName', 'size' => 70))?>
	<?php echo $form->submit('Přidat do seznamu')?>
	<?php echo $form->end() ?>
</div>

</div><!-- End demo -->


<?php if (empty($most_sold)) { ?>
<p>Nejsou vybrány žádné produkty jako nejprodávanější.</p>
<?php } else { ?>
<table class="topHeading" cellpadding="5" cellspacing="3">
<tr>
	<th>ID</th>
	<th>Název</th>
	<th>Aktivní?</th>
	<th>MO cena s DPH</th>
	<th>&nbsp;</th>
</tr>
<?
	foreach ( $most_sold as $product ){
		$style = '';
		if (!$product['Product']['active']) {
			$style = ' style="color:grey"';
		} elseif (!$product['Availability']['cart_allowed']) {
			$style = ' style="color:orange"';
		}
?>
	<tr <?php echo  $style?>>
		<td><?=$html->link($product['Product']['id'], '/' . $product['Product']['url']);?></td>
		<td><?=$product['Product']['name']?></td>
		<td><?php echo ($product['Product']['active'] ? 'ano' : 'ne') ?></td>
		<td><?=$product['Product']['retail_price_with_dph']?></td>
		<td style="font-size:12px;">
			<?php echo $html->link('Odstranit ze seznamu', array('controller' => 'products', 'action' => 'most_sold_delete', $product['Product']['id']))?>
		</td>
	</tr>
<?
	}
?>
</table>
<?php } ?>