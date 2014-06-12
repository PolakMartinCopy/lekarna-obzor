<div class="options">
<h2>Názvy výrobců - nastavení</h2>
<table class="topHeading" cellpadding="5" cellspacing="3">
<tr>
	<th><?php echo $paginator->sort('ID', 'id');?></th>
	<th><?php echo $paginator->sort('Výrobce', 'name');?></th>
	<th>Logo</th>
	<th>WWW stránky</th>
	<th class="actions">&nbsp;</th>
</tr>
<?php
$i = 0;
foreach ($manufacturers as $manufacturer):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $manufacturer['Manufacturer']['id'] ?>
		</td>
		<td>
			<?php echo $manufacturer['Manufacturer']['name'] ?>
		</td>
		<td>
			<?php echo $manufacturer['Manufacturer']['logo_url'] ?>
		</td>
		<td>
			<?php echo $manufacturer['Manufacturer']['www_address'] ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('Upravit', true), array('action'=>'edit', $manufacturer['Manufacturer']['id'])); ?>
<!--			<?php echo $html->link(__('Smazat', true), array('action'=>'delete', $manufacturer['Manufacturer']['id']), null, sprintf(__('Opravdu chcete smazat tento název atributu?', true), $manufacturer['Manufacturer']['id'])); ?>-->
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('předchozí', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('další', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Vložit nového výrobce', true), array('action'=>'add')); ?></li>
	</ul>
</div>