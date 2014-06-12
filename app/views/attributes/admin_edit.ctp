<div class="attribute">
<h2>Editace atributu</h2>
<? echo $form->create('Attribute', array('controller' => 'attributes', 'action' => 'edit', $id));?>
	<fieldset>
 		<legend>Atribut</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>Název atributu</th>
				<td>
					<?=$form->select('Attribute.option_id', $options_options, null, array('disabled' => 1))?>
					<?=$form->hidden('Attribute.option_id')?>
				</td>
			</tr>
			<tr>
				<th>Hodnota atributu</th>
				<td><?=$form->input('Attribute.value', array('label' => false))?></td>
			</tr>
		</table>
	<?
		echo $form->hidden('Attribute.id');
	?>
	</fieldset>
<?php echo $form->end('Upravit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Zpět na seznam atributů', true), array('action'=>'index'));?></li>
	</ul>
</div>