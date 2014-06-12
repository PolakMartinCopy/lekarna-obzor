<?php
class OrderedProductsAttribute extends AppModel {
	var $name = 'OrderedProductsAttribute';
	
	var $actsAs = array('Containable');

	var $belongsTo = array('OrderedProduct', 'Attribute');
}
?>