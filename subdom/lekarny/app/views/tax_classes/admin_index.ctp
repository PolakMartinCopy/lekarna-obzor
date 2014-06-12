<h1>Daňové třídy - nastavení</h1>
<? if (!empty($taxClasses)) { ?>
<table class="top_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>název</th>
		<th>hodnota</th>
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = ' class="odd"'; 
	foreach ($taxClasses as $taxClass) {
?>
	<tr<?php echo $odd?>>
		<td>
			<?php echo $taxClass['TaxClass']['name'] ?>
		</td>
		<td>
			<?php echo $taxClass['TaxClass']['value'] ?>%
		</td>
		<td>
			<?php echo $html->link(__('Upravit', true), array('action'=>'edit', $taxClass['TaxClass']['id'])); ?>
		</td>
	</tr>
<?php 
		$odd = $odd == ' class="odd"' ? '' : ' class="odd"';
	}
?>
</table>
<? } ?>