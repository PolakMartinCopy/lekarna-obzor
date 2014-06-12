<?php
class Value extends AppModel {
	var $name = 'Value';

	var $validate = array(
		'name' => array(
			'not_empty' => array(
				'rule' => array('minLength', 1),
				'message' => 'Pole nesmí zůstat prázdné!'
			)
		)
	);
}
?>