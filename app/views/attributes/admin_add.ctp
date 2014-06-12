<div class="attribute">
<h2>Vložit nový atribut</h2>
<?php echo $form->create('Attribute');?>
	<fieldset>
 		<legend>Atribut</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>Název atributu</th>
				<td>
					<?=$form->select('option_id', $options_options);?>
					<?=$form->error('option_id');?>
				</td>
			</tr>
			<tr>
				<th>Hodnota atributu</th>
				<td><?=$form->input('Attribute.value', array('label' => false));?></td>
			</tr>
		</table>
	</fieldset>
<?php echo $form->end('Vložit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Zpět na seznam atributů', true), array('action'=>'index'));?></li>
	</ul>
</div>