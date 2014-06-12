<div class="options">
<h2>Daňové třídy - nastavení</h2>
<table class="topHeading" cellpadding="5" cellspacing="3">
<tr>
	<th><?php echo $paginator->sort('ID', 'id');?></th>
	<th><?php echo $paginator->sort('Název daňové třídy', 'name');?></th>
	<th><?php echo $paginator->sort('Hodnota daně', 'name');?></th>
	<th class="actions">&nbsp;</th>
</tr>
<?php
$i = 0;
foreach ($taxClasses as $taxClass):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $taxClass['TaxClass']['id'] ?>
		</td>
		<td>
			<?php echo $taxClass['TaxClass']['name'] ?>
		</td>
		<td>
			<?php echo $taxClass['TaxClass']['value'] ?>%
		</td>
		<td class="actions">
			<?php echo $html->link(__('Upravit', true), array('action'=>'edit', $taxClass['TaxClass']['id'])); ?>
<!--			<?php echo $html->link(__('Smazat', true), array('action'=>'delete', $taxClass['TaxClass']['id']), null, sprintf(__('Opravdu chcete smazat tento název atributu?', true), $taxClass['TaxClass']['id'])); ?>-->
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
		<li><?php echo $html->link(__('Vložit novou daňovou třídu', true), array('action'=>'add')); ?></li>
	</ul>
</div>