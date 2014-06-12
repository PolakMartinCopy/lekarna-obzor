<h2>Upravit atribut</h2>
<div class="subproduct">
<?php echo $form->create('Subproduct', array('url' => array_merge(   array('action' => 'edit', 'id' => null), $this->params['pass']   )));?>
	<fieldset>
 		<legend>Atribut</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Cena:
				</th>
				<td>
					<?=$form->text('price')?>
				</td>
			</tr>
		</table>
	<?php
		echo $form->input('id');
		echo $form->hidden('product_id');
		echo $form->hidden('attribute_id');
	?>
	</fieldset>
<?php echo $form->end('Upravit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Zpět na seznam atributů', true), array('controller'=> 'products', 'action'=>'attributes_list', 'id' => $form->value('Product.id'))); ?> </li>
	</ul>
</div>