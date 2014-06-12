<div class="subproducts">
<h2><?php __('Subproducts');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('product_id');?></th>
	<th><?php echo $paginator->sort('attribute_id');?></th>
	<th><?php echo $paginator->sort('created');?></th>
	<th><?php echo $paginator->sort('modified');?></th>
	<th><?php echo $paginator->sort('price');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($subproducts as $subproduct):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $subproduct['Subproduct']['id'] ?>
		</td>
		<td>
			<?php echo $html->link(__($subproduct['Product']['name'], true), array('controller'=> 'products', 'action'=>'view', $subproduct['Product']['id'])); ?>
		</td>
		<td>
			<?php echo $html->link(__($subproduct['Attribute']['id'], true), array('controller'=> 'attributes', 'action'=>'view', $subproduct['Attribute']['id'])); ?>
		</td>
		<td>
			<?php echo $subproduct['Subproduct']['created'] ?>
		</td>
		<td>
			<?php echo $subproduct['Subproduct']['modified'] ?>
		</td>
		<td>
			<?php echo $subproduct['Subproduct']['price'] ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $subproduct['Subproduct']['id'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $subproduct['Subproduct']['id'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $subproduct['Subproduct']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $subproduct['Subproduct']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(sprintf(__('New %s', true), __('Subproduct', true)), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(sprintf(__('List %s', true), __('Products', true)), array('controller'=> 'products', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(sprintf(__('New %s',  true), __('Product', true)), array('controller'=> 'products', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(sprintf(__('List %s', true), __('Attributes', true)), array('controller'=> 'attributes', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(sprintf(__('New %s',  true), __('Attribute', true)), array('controller'=> 'attributes', 'action'=>'add')); ?> </li>
	</ul>
</div>
