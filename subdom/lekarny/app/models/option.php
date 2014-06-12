<?php
class Option extends AppModel {
	var $name = 'Option';

	var $actsAs = array('Containable');
	
	var $hasMany = array('Attribute');
	
	var $validate = array(
		'name' => VALID_NOT_EMPTY,
	);
}
?>