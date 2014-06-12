<h2>Vložit nový název atributu</h2>
<div class="option">
<?php echo $form->create('Option');?>
	<fieldset>
 		<legend>Název</legend>
		<table>
			<tr>
				<th>Název atributu</th>
				<td><?=$form->text('name')?></td>
			</tr>
		</table>
	</fieldset>
<?php echo $form->end('Vložit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Zpět na seznam názvů atributů', true), array('action'=>'index'));?></li>
	</ul>
</div>
