<?php 
class Subscriber extends AppModel {
	var $name = 'Subscriber';
	
	var $actsAs = array('Containable');
	
	var $validate = array(
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'message' =>  'Zadejte emailovou adresu, např. email@email.cz',
				'allowEmpty' => false
			),
			'isUnique' => array(
				'rule' => array('myIsUnique'),
				'message' => 'Zadaná emailová adresa je již přihlášena. Zvolte prosím jinou.'
			)
		)
	);
	
	// email je unikatni, jestlize neni v subscribers a zaroven jestlize neni registrovany a prihlasen pro newslettery v customers
	function myIsUnique($check) {
		App::import('Model', 'Customer');
		$this->Customer = new Customer;
		
		$customer_conditions = $check + array('newsletter' => true);
		return (!($this->hasAny($check) || ($this->Customer->hasAny($customer_conditions))));
	}
}
?>