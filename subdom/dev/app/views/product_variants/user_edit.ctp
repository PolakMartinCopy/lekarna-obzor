<h1>Upravit zboží</h1>
<?php
	echo $this->Form->create('ProductVariant');
	echo $this->element('product_variants/add_edit_form');
	echo $this->Form->hidden('Product.id');
	echo $this->Form->hidden('ProductVariant.id');
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>