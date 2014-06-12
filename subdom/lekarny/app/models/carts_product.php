<?php
class CartsProduct extends AppModel {
	var $name = 'CartsProduct';

	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'Cart',
		'Product',
		'Subproduct'
	);
		
	function is_in_cart($conditions){
		// quantity nepotrebuju do podminek
		unset($conditions['quantity']);
		// odpojim modely, ktere nepotrebuju
		$this->unbindModel(
			array(
				'belongsTo' => array('Cart', 'Product')
			)
		);

		// vyhledam si produkt
		$data = $this->find($conditions);

		// pokud se mi podarilo nacist jej,
		// vratim jeho id, ktere se pouzije
		// pro upravu quantity
		if ( !empty($data) ){
			return $data['CartsProduct']['id'];
		}

		// produkt neexistuje
		return false;
	}
	
	function findByIds($cart_id, $product_id){
		// zkontroluje podle id kosiku a produktu,
		// zda jsou validni
		$conditions = array(
			'CartsProduct.cart_id' => $cart_id,
			'CartsProduct.id' => $product_id
		);
		if ( $this->find($conditions) ){
			return true;
		}
		return false;
	}

	function getStats($cart_id){
		// inicializace
		$products_count = 0;
		$total_price = 0;

		// vytahnu si vsechny produkty z db a spoctu si to
		$contents = $this->findAll(array('cart_id' => $cart_id), array('quantity', 'price'));
		foreach ( $contents as $item ){
			$products_count = $products_count + $item['CartsProduct']['quantity'];
			$total_price = $total_price + $item['CartsProduct']['price'] * $item['CartsProduct']['quantity'];
		}

		// vratim pole s vysledkem
		$carts_stats = array(
			'products_count' => $products_count,
			'total_price' => $total_price
		);

		return $carts_stats;
	}
}
?>