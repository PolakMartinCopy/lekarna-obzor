<?php
class CartsProduct extends AppModel {
	var $name = 'CartsProduct';

	var $actsAs = array('Containable');

	var $belongsTo = array(
		'Cart' => array(
			'className' => 'Cart',
		),
		'Product' => array(
			'className' => 'Product'
		),

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
		$data = $this->find('first', array(
			'conditions' => $conditions,
			'contain' => array()
		));

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
		$contents = $this->find('all', array(
			'conditions' => array('cart_id' => $cart_id),
			'fields' => array('quantity', 'price_with_dph')
		));

		foreach ( $contents as $item ){
			$products_count = $products_count + $item['CartsProduct']['quantity'];
			$total_price = $total_price + $item['CartsProduct']['price_with_dph'] * $item['CartsProduct']['quantity'];
		}

		// vratim pole s vysledkem
		$carts_stats = array(
			'products_count' => $products_count,
			'total_price' => $total_price
		);

		return $carts_stats;
	}
	
	function getProducts() {
		// potrebuju vedet, ktery kosik mam zobrazit
		$cart_id = $this->Cart->get_id();

		// vytahnu si vsechny produkty, ktere patri
		// do zakaznikova kose
		$cart_products = $this->find('all', array(
			'conditions' => array('CartsProduct.cart_id' => $cart_id),
			'contain' => array(
				'Product' => array(
					'Flag',
					'TaxClass',
					'fields' => array('id', 'name')
				),
				'Subproduct' => array(
					'AttributesSubproduct' => array(
						'fields' => array('attribute_id')
					)
				)
			)
		));

		foreach ($cart_products as $index => $cart_product) {		
			$cart_products[$index]['CartsProduct']['product_attributes'] = array();
			if (!empty($cart_product['CartsProduct']['subproduct_id'])) {
				$this->Product->Subproduct->id = $cart_product['CartsProduct']['subproduct_id'];
				$this->Product->Subproduct->contain(array(
					'AttributesSubproduct' => array(
						'Attribute' => array(
							'Option'
						)
					)
				));
				$subproduct = $this->Product->Subproduct->read();
				$product_attributes = array();
				foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) {
					$product_attributes[$attributes_subproduct['Attribute']['Option']['name']] = $attributes_subproduct['Attribute']['value'];
				}
				$cart_products[$index]['CartsProduct']['product_attributes'] = $product_attributes;
			}
		}
		return $cart_products;
	}
}
?>