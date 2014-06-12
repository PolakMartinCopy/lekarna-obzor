<?php 
class Language extends AppModel {
	var $name = 'Language';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array(
		'CSInvoice',
		'CSCreditNote'	
	);
	
	var $order = array('Language.order' => 'asc');
}
?>