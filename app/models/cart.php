<?php
class Cart extends AppModel {
	var $name = 'Cart';

	var $hasMany = array(
		'CartsProduct' => array(
			'className' => 'CartsProduct',
			'dependent' => true
		)
	);
	
	function get_id() {
		App::import('Model', 'CakeSession');
		$this->Session = &new CakeSession;
		
		// zkusim najit v databazi kosik
		// pro daneho uzivatele
		$data = $this->find(array('rand' => $this->Session->read('Config.rand'), 'userAgent' => $this->Session->read('Config.userAgent')));

		// kosik jsem v databazi nenasel,
		// musim ho zalozit
		if ( empty($data) ){
			return $this->_create();
		} else {
			return $data['Cart']['id'];
		}
	}

	function _create(){
		App::import('Model', 'CakeSession');
		$this->Session = &new CakeSession;
		
		$this->data['Cart']['rand'] = $this->Session->read('Config.rand');
		$this->data['Cart']['userAgent'] = $this->Session->read('Config.userAgent');
		$this->save($this->data);
		return $this->getLastInsertID();
	}
}
?>