<?
class Shipping extends AppModel {
	var $name = 'Shipping';
	
	var $actsAs = array('Containable');

	var $hasMany = array('Order');
	
	var $validate = array(
		'name' => array(
			'minLength' => array(
				'rule' => array('minLength', 1),
				'required' => true,
				'message' => 'Vyplňte prosím název způsobu dopravy.'
			),
			'isUnique' => array(
				'rule' => array('isUnique', 'name'),
				'required' => true,
				'message' => 'Tento způsob dopravy již existuje! Zvolte prosím jiný název způsobu dopravy.'
			)
		),
		'price' => array(
	        'rule' => 'numeric',  
	        'message' => 'Uveďte prosím cenu za dopravu v korunách.',
			'required' => true
	    ),
	    'free' => array(
	        'rule' => 'numeric',  
	        'message' => 'Uveďte prosím cenu objednávky v korunách, od které je doprava zdarma.',
			'required' => true
	    )
	);
	

	function get_data($id){
		$this->recursive = -1;
		return $this->read(null, $id);
	}

	function get_cost($id, $order_total){
		$price = 0;
		$this->recursive = -1;
		$shipping = $this->read(null, $id);
		if ( $order_total <= intval($shipping['Shipping']['free']) ){
			$price = $shipping['Shipping']['price'];
		}
		return $price;
	}

}
?>