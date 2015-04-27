<?
class Export extends AppModel{
	var $name = 'Export';
	
	var $useTable = false;
	
	function getProductShipping($product_id) {
		// udaje o moznych variantach dopravy
		App::import('Model', 'Shipping');
		$this->Shipping = new Shipping;
		
		App::import('Model', 'Product');
		$this->Product = new Product;
		
		$shipping_conditions = array('NOT' => array('Shipping.heureka_id' => null));
		if (!$this->Product->isRecommendedLetterPossible($product_id)) {
			$shipping_conditions[] = 'Shipping.id NOT IN (' . implode(',', $this->Shipping->getRecommendedLetterShippingIds()) . ')';
		}
		
		$shippings = $this->Shipping->find('all', array(
			'conditions' => $shipping_conditions,
			'contain' => array(),
			'group' => array('Shipping.heureka_id'),
			'fields' => array('Shipping.heureka_id', 'Shipping.free', 'MIN(Shipping.price) AS min_price')
		));
		
		return $shippings;
	}
}
?>