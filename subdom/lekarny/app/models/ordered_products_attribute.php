<?php
class OrderedProductsAttribute extends AppModel {

	var $name = 'OrderedProductsAttribute';

	var $belongsTo = array('OrderedProduct', 'Attribute');
}
?>