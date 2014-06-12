<?
class ProductDocument extends AppModel{
	var $name = 'ProductDocument';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Product');
}
?>