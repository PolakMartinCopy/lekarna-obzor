<h2>Editace způsobu dopravy</h2>
<?php echo $form->Create('Shipping')?>
	<fieldset>
 		<legend>Způsob dopravy</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Název způsobu dopravy
				</th>
				<td>
					<?php echo $form->input('Shipping.name', array('label' => false, 'size' => 80))?>
				</td>
			</tr>
			<tr>
				<th>
					Cena za dopravu
				</th>
				<td>
					<?php echo $form->input('Shipping.price', array('label' => false, 'size' => 80))?>
				</td>
			</tr>
			<tr>
				<th>
					Zdarma od
				</th>
				<td>
					<?php echo $form->input('Shipping.free', array('label' => false, 'size' => 80))?>
				</td>
			</tr>
			<tr>
				<th>
					URL prefix
				</th>
				<td>
					<?php echo $form->input('Shipping.tracker_prefix', array('label' => false, 'size' => 80))?>
				</td>
			</tr>
			<tr>
				<th>
					URL postfix
				</th>
				<td>
					<?php echo $form->input('Shipping.tracker_postfix', array('label' => false, 'size' => 80))?>
				</td>
			</tr>
		</table>
	</fieldset>
<?
	echo $form->hidden('Shipping.id');
	echo $form->end('upravit')
?>
<div class="actions">
	<ul>
		<li><?php echo $html->link('Zpět na seznam způsobů dopravy', array('controller' => 'shippings', 'action' => 'index'))?></li>
	</ul>
</div>