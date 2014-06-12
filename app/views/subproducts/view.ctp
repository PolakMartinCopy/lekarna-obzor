<div class="subproduct">
<h2><?php  __('Subproduct');?></h2>
	<dl>
		<dt class="altrow"><?php __('Id') ?></dt>
		<dd class="altrow">
			<?php echo $subproduct['Subproduct']['id'] ?>
			&nbsp;
		</dd>
		<dt><?php __('Product') ?></dt>
		<dd>
			<?php echo $html->link(__($subproduct['Product']['name'], true), array('controller'=> 'products', 'action'=>'view', $subproduct['Product']['id'])); ?>
			&nbsp;
		</dd>
		<dt class="altrow"><?php __('Attribute') ?></dt>
		<dd class="altrow">
			<?php echo $html->link(__($subproduct['Attribute']['id'], true), array('controller'=> 'attributes', 'action'=>'view', $subproduct['Attribute']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php __('Created') ?></dt>
		<dd>
			<?php echo $subproduct['Subproduct']['created'] ?>
			&nbsp;
		</dd>
		<dt class="altrow"><?php __('Modified') ?></dt>
		<dd class="altrow">
			<?php echo $subproduct['Subproduct']['modified'] ?>
			&nbsp;
		</dd>
		<dt><?php __('Price') ?></dt>
		<dd>
			<?php echo $subproduct['Subproduct']['price'] ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(sprintf(__('Edit %s', true), __('Subproduct', true)), array('action'=>'edit', $subproduct['Subproduct']['id'])); ?> </li>
		<li><?php echo $html->link(sprintf(__('Delete %s', true), __('Subproduct', true)), array('action'=>'delete', $subproduct['Subproduct']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $subproduct['Subproduct']['id'])); ?> </li>
		<li><?php echo $html->link(sprintf(__('List %s', true), __('Subproducts', true)), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(sprintf(__('New %s', true), __('Subproduct', true)), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(sprintf(__('List %s', true), __('Products', true)), array('controller'=> 'products', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(sprintf(__('New %s', true), __('Product', true)), array('controller'=> 'products', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(sprintf(__('List %s', true), __('Attributes', true)), array('controller'=> 'attributes', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(sprintf(__('New %s', true), __('Attribute', true)), array('controller'=> 'attributes', 'action'=>'add')); ?> </li>
	</ul>
</div>
