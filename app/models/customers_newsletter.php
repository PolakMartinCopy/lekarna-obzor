<?
class CustomersNewsletter extends AppModel{
	var $name = 'CustomersNewsletter';
	
	var $belongsTo = array('Customer');
	
	var $actsAs = array('Containable');
}
?>