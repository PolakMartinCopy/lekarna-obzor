<div class="options">
<h2>Názvy atributů - nastavení</h2>
<table class="topHeading" cellpadding="5" cellspacing="3">
<tr>
	<th><?php echo $paginator->sort('ID', 'id');?></th>
	<th><?php echo $paginator->sort('Název atr.', 'name');?></th>
	<th class="actions">&nbsp;</th>
</tr>
<?php
$i = 0;
foreach ($options as $option):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $option['Option']['id'] ?>
		</td>
		<td>
			<?php echo $option['Option']['name'] ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('Upravit', true), array('action'=>'edit', $option['Option']['id'])); ?>
<!--			<?php echo $html->link(__('Smazat', true), array('action'=>'delete', $option['Option']['id']), null, sprintf(__('Opravdu chcete smazat tento název atributu?', true), $option['Option']['id'])); ?>-->
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
		<li><?php echo $html->link(__('Vložit nový název', true), array('action'=>'add')); ?></li>
	</ul>
</div>
