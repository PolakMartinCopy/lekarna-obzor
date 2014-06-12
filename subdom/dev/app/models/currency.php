<?php 
class Currency extends AppModel {
	var $name = 'Currency';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('CSTransactionItem');
	
	var $displayField = 'shortcut';
	
	var $order = array('Currency.order' => 'asc');
}
?>