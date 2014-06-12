<h2>Editace daňové třídy</h2>
<div class="option">
<?php echo $form->create('TaxClass');?>
	<fieldset>
 		<legend>Daňová třída</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Název daňové třídy
				</th>
				<td>
					<?=$form->text('name')?>
					<?=$form->error('name')?>
				</td>
			</tr>
			<tr>
				<th>
					Hodnota daně
				</th>
				<td>
					<?=$form->text('value')?>
					<?=$form->error('value')?>
				</td>
			</tr>
		</table>
	</fieldset>
	<?=$form->hidden('id')?>
	<?=$form->end('Upravit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Zpět na seznam daňových tříd', true), array('controller' => 'tax_classes', 'action'=>'index'));?></li>
	</ul>
</div>