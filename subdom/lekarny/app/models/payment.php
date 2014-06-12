<?
class Payment extends AppModel{
	var $name = 'Payment';
	
	var $actsAs = array('Containable');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Pole Název musí být vyplněno'
			)
		)
	);
}
?>