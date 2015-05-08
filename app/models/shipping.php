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
	
	var $GP_shipping_id = array(12, 13);
	// kategorie definovane pro zpusob dopravy doporucenym psanim maji id 77, 162
	var $recommended_letter_category_ids = array(77, 162, 163);
	// maximalni pocet produktu v objednavce, ktere je mozne takto poslat, je 2
	var $recommended_letter_max_count = 2;
	

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

	function geis_point_url($session) {
		$address = $session->read('Address');
		if (!$address) {
			return false;
		}
		$cust_address = $address['street'];
		if (!empty($address['street_no'])) {
			$cust_address .= ' ' . $address['street_no'];
		}
		$cust_address .= ';' . $address['city'] . ';' . $address['zip'];
		$cust_address = urlencode($cust_address);
		$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . '/orders/recapitulation';
		$redirect_url = urlencode($redirect_url);
		$service_url = 'http://plugin.geispoint.cz/map.php';
		$service_url = $service_url . '?CustAddress=' . $cust_address . '&ReturnURL=' . $redirect_url;
	
		return $service_url;
	}
	
	function getRecommendedLetterShippingIds() {
		$shippings = $this->find('all', array(
			'conditions' => array('Shipping.special' => 'recommended_letter'),
			'contain' => array(),
			'fields' => array('Shipping.id')	
		));
		return Set::extract('/Shipping/id', $shippings);
	}
	
	function isRecommendedLetterShipping($shipping_id) {
		return in_array($shipping_id, $this->getRecommendedLetterShippingIds());
	}
}
?>