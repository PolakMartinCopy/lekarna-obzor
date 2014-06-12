<?
class AttributesSubproduct extends AppModel {
	var $name = 'AttributesSubproduct';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Subproduct', 'Attribute');
}
?>