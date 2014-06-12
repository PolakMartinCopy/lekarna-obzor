<h1>Nové zboží</h1>
<?php
	echo $this->Form->create('ProductVariant');
	echo $this->element('product_variants/add_edit_form');
	echo $this->Form->hidden('Product.active', array('value' => true));
	echo $this->Form->hidden('ProductVariant.active', array('value' => true));
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>