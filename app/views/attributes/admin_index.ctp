<div class="attributes">
<h2>Atributy produktů - nastavení</h2>

<h3>Řazení atributů</h3>
<ul>
<?php
foreach ($options as $option) {
	echo '<li>';
	echo $html->link($option['Option']['name'], array('controller' => 'attributes', 'action' => 'index', 'option_id' => $option['Option']['id']));
	echo '</li>';
}
?>
</ul>

<?php // jestli vypisuju jenom atributy pro zvolene option 
if (isset($this->params['named']['option_id'])) {
	// povolim sortable
	echo $form->create('Attribute', array('url' => array('controller' => 'attributes', 'action' => 'index', 'option_id' => $this->params['named']['option_id'])));
}
?>
	
<table class="topHeading" cellpadding="5" cellspacing="3" id=attribute_table>
<tr>
	<th><?php echo $paginator->sort('ID', 'id');?></th>
	<th><?php echo $paginator->sort('Název atr.', 'option_name');?></th>
	<th><?php echo $paginator->sort('Hodnota atr.', 'value_name');?></th>
	<th class="actions">&nbsp;</th>
</tr>
<tbody>
<?php
$i = 0;
foreach ($attributes as $attribute):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>

	<tr<?php echo $class;?>>
		<td>
<?php
			echo $attribute['Attribute']['id'];
	 		if (isset($this->params['named']['option_id'])) {
				echo $form->hidden($i, array('value' => $attribute['Attribute']['id']));
			}
?>
		</td>
		<td>
			<?=$attribute['Option']['name']?>
		</td>
		<td>
			<?=$attribute['Attribute']['value']?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('Upravit', true), array('action'=>'edit', $attribute['Attribute']['id'])); ?>
			<?php echo $html->link(__('Nahoru', true), array('action' => 'move_up', $attribute['Attribute']['id']))?>
			<?php echo $html->link(__('Dolů', true), array('action' => 'move_down', $attribute['Attribute']['id']))?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('předchozí', true), array('url' => array('action' => 'index')), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers(array('url' => array('action' => 'index')));?>
	<?php echo $paginator->next(__('další', true).' >>', array('url' => array('action' => 'index')), null, array('class'=>'disabled'));?>
</div>
<?php // povolim sortable
if (isset($this->params['named']['option_id'])) {
	echo $form->submit('Uspořádat');
	echo $form->end();
}
?>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Vložit nový atribut', true), array('action'=>'add')); ?></li>
	</ul>
</div>