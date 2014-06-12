<?php
class OrderedProduct extends AppModel {
	var $name = 'OrderedProduct';

	var $actsAs = array('Containable');

	var $belongsTo = array('Order', 'Product');

	var $hasMany = array(
		'OrderedProductsAttribute' => array(
			'dependent' => true
		)
	);
}
?>