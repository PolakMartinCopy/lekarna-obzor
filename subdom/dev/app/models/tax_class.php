<?php 
class TaxClass extends AppModel {
	var $name = 'TaxClass';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('Product');
}
?>