<div class="subproduct">
<?php echo $form->create('Subproduct');?>
	<fieldset>
 		<legend><?php echo sprintf(__('Add %s', true), __('Subproduct', true));?></legend>
	<?php
		echo $form->input('product_id');
		echo $form->input('attribute_id');
		echo $form->input('price');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(sprintf(__('List %s', true), __('Subproducts', true)), array('action'=>'index'));?></li>
		<li><?php echo $html->link(sprintf(__('List %s', true), __('Products', true)), array('controller'=> 'products', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(sprintf(__('New %s', true), __('Product', true)), array('controller'=> 'products', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(sprintf(__('List %s', true), __('Attributes', true)), array('controller'=> 'attributes', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(sprintf(__('New %s', true), __('Attribute', true)), array('controller'=> 'attributes', 'action'=>'add')); ?> </li>
	</ul>
</div>
