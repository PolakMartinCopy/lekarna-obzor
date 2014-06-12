<h2>Editace názvu</h2>
<div class="option">
<?php echo $form->create('Option');?>
	<fieldset>
 		<legend>Název</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Název atributu
				</th>
				<td>
					<?=$form->text('name')?>
				</td>
			</tr>
		</table>
	</fieldset>
	<?=$form->hidden('id')?>
	<?=$form->end('Upravit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Zpět na seznam názvů atributů', true), array('action'=>'index'));?></li>
	</ul>
</div>
