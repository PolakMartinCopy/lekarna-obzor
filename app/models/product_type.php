<?php 
class ProductType extends AppModel {
	var $name = 'ProductType';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('Product');
}
?>