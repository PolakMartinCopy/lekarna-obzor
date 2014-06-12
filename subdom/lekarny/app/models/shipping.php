<?
class Shipping extends AppModel{
	var $name = 'Shipping';
	
	var $actsAs = array('Containable');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Pole Název musí být vyplněno'
			)
		),
		'price' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Pole Cena musí být vyplněno číslem'
			)
		),
		'free' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Hodnota Doprava zdarma od musí být celočíselná'
			)
		)
	);
}
?>