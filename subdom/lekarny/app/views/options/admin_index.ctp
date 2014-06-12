<h1>Názvy atributů - nastavení</h1>
<? if (!empty($options)) { ?>
<table class="top_headed" cellpadding="5" cellspacing="3">
<tr>
	<th>název</th>
	<th>&nbsp;</th>
</tr>
<?php
$odd = ' class="odd"';
foreach ($options as $option) {
?>
	<tr<?php echo $odd?>>
		<td>
			<?php echo $option['Option']['name'] ?>
		</td>
		<td>
			<?php echo $html->link(__('Upravit', true), array('action'=>'edit', $option['Option']['id'])); ?>
		</td>
	</tr>
<?php
	$odd = $odd == ' class="odd"' ? '' : ' class="odd"'; 
} ?>
</table>
<? } ?>