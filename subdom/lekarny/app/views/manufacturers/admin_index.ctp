<h1>Názvy výrobců - nastavení</h1>
<?
	if ( !empty($manufacturers) ){
?>
<table class="top_headed" cellpadding="5" cellspacing="3">
<tr>
	<th>název</th>
	<th>&nbsp;</th>
</tr>
<?php
$odd = ' class="odd"';
foreach ($manufacturers as $manufacturer) {
?>
	<tr<?php echo $odd?>>
		<td>
			<?php echo $manufacturer['Manufacturer']['name'] ?>
		</td>
		<td>
			<?php echo $html->link(__('Upravit', true), array('action'=>'edit', $manufacturer['Manufacturer']['id'])); ?>
		</td>
	</tr>
<?php
	$odd = $odd == ' class="odd"' ? '' : ' class="odd"'; 
} ?>
</table>
<? } ?>
</div>