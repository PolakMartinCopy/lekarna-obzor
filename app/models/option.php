<?php
class Option extends AppModel {
	var $name = 'Option';

	

	var $actsAs = array('Containable');

	
	var $validate = array(
		'name' => array(

			'rule' => array('minLength', 1)

		),
	);

	

	var $hasMany = array('Attribute');
}
?>