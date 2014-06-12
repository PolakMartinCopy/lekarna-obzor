<?
class CategoriesProduct extends AppModel{
	var $name = 'CategoriesProduct';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Category', 'Product');
}
?>