<?
class OrderedProduct extends AppModel{
	var $name = 'OrderedProduct';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array(
		'OrderedProductsAttribute' => array(
			'dependent' => true
		));
	var $belongsTo = array('Product', 'Order');
}
?>