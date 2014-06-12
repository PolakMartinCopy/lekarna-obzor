<?php
class CartsProductsController extends AppController {
	var $name = 'CartsProducts';

	var $helpers = array('Html', 'Form', 'Javascript');

	function beforeFilter(){
		$this->Session->read();
		$this->CartsProduct->cart_id = $this->requestAction('/carts/get_id');
	}

	function index() {
		// kosik nesouvisi s kategoriemi,
		// menu necham zavrene
		$opened_category_id = 1;
		
		$this->layout = 'content';
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Košík', 'href' => '/kosik')
		);
		$this->set('breadcrumbs', $breadcrumbs);

		// nastavim si titulek stranky
		$this->set('page_heading', 'Obsah nákupního košíku');

		// potrebuju vedet, ktery kosik mam zobrazit

		// odpojim nepotrebne modely
		$this->CartsProduct->unbindModel(array('belongsTo' => array('Cart')));
		$this->CartsProduct->Product->unbindModel(
			array(
				'hasAndBelongsToMany' => array('Category', 'Cart'),
				'hasMany' => array('Subproduct', 'CartsProduct'),
				'belongsTo' => array('Manufacturer', 'TaxClass')
			)
		);

		$this->CartsProduct->recursive = 2;

		// vytahnu si vsechny produkty, ktere patri
		// do zakaznikova kose
		$cart_products = $this->CartsProduct->find('all', array(
			'conditions' => array('CartsProduct.cart_id' => $this->CartsProduct->cart_id)
		));
		foreach ( $cart_products as $index => $cart_product ){
			// u produktu si pridam jmenne atributy
			// chci tam dostat pole napr (barva -> bila, velikost -> S) ... takze (option_name -> value)
			// pokud znam id subproduktu, tak ma produkt varianty a muzu si je jednoduse vytahnout
			$cart_products[$index]['CartsProduct']['product_attributes'] = array();
			if (!empty($cart_product['CartsProduct']['subproduct_id'])) {
				$this->CartsProduct->Product->Subproduct->id = $cart_product['CartsProduct']['subproduct_id'];
				$this->CartsProduct->Product->Subproduct->contain(array(
					'AttributesSubproduct' => array(
						'Attribute' => array(
							'Option'
						)
					)
				));
				$subproduct = $this->CartsProduct->Product->Subproduct->read();
				$product_attributes = array();
				if ($subproduct) {
					foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) {
						$product_attributes[$attributes_subproduct['Attribute']['Option']['name']] = $attributes_subproduct['Attribute']['value'];
					}
				}
				$cart_products[$index]['CartsProduct']['product_attributes'] = $product_attributes;
			}
			//$cart_products[$index]['CartsProduct']['product_attributes'] = $this->requestAction('subproducts/to_names', array('attributes' => $cart_product['CartsProduct']['product_attributes']));
		}

		$this->set('cart_products', $cart_products);
		$this->set('sess', $this->Session->read());
	}
	
	function add() {
		$this->data['CartsProduct'] = $this->params['CartsProduct'];

		// musim si vytahnout info o produktu
		$product = $this->CartsProduct->Product->find('first', array(
			'conditions' => array('Product.id' => $this->data['CartsProduct']['product_id']),
			'contain' => array(
				'Subproduct',
				'Availability' => array(
					'fields' => array('Availability.id', 'Availability.name', 'Availability.cart_allowed')
				)
			),
		));
		
		// stav produktu musi umoznovat vlozeni do kosiku (cart_allowed == true)
		if (!$product['Availability']['cart_allowed']) {
			$this->Session->setFlash('Produkt je ve stavu <strong>' . $product['Availability']['name'] . '</strong> a nyní ho nelze objednat.');
			$this->redirect('/' . $product['Product']['url']);
		}

		// pokud ma produkt varianty a neni zadna zvolena, musim ho poslat na detail produktu a vyhodit flash
		if (!empty($product['Subproduct']) && !(isset($this->data['Subproduct']) || isset($this->data['Product']['Option']))) {
			$this->Session->setFlash('Vyberte prosím variantu produktu a vložte do košíku');
			$this->redirect('/' . $product['Product']['url']);
		}
		
		// nejnizsi cena je povazovana za zakladni cenu produktu
		$total_price_with_dph = $this->CartsProduct->Product->assign_discount_price($product);
		
		// vytahnu si info o subproduktech pokud
		// nejake existuji a pripoctu jejich prirustkovou cenu
		if ( isset($this->data['CartsProduct']['subproduct_id']) && !empty($this->data['CartsProduct']['subproduct_id']) ){
			$options_condition = array();

			// najdu si subprodukt
			$this->CartsProduct->Product->Subproduct->id = $this->data['CartsProduct']['subproduct_id'];
			$this->CartsProduct->Product->Subproduct->contain();
			$subproduct = $this->CartsProduct->Product->Subproduct->read();

			// k celkove cene pripoctu ceny options
			$total_price_with_dph = $total_price_with_dph + $subproduct['Subproduct']['price_with_dph'];
		}

		// inicializuju si objekt
		$this->CartsProduct->create();
		// vytvorim si data, ktera ulozim
		$this->data['CartsProduct']['cart_id'] = $this->CartsProduct->Cart->get_id();
		$this->data['CartsProduct']['price_with_dph'] = $total_price_with_dph;

		// nejdriv zkontroluju, jestli uz produkt nemam
		// v kosiku
		$cpID = $this->CartsProduct->is_in_cart($this->data['CartsProduct']); 

		if ( $cpID === false ){
			// produkt v kosiku neni,
			// vlozim ho
			if ( !$this->CartsProduct->save($this->data) ){
				return false;
			}
		} else {
			// produkt uz v kosiku je,
			// zvysim jenom qunatity
			$this->CartsProduct->id = $cpID;
			$this->CartsProduct->recursive = -1;
			$c = $this->CartsProduct->read(array('CartsProduct.quantity'));
			$c['CartsProduct']['quantity'] = $c['CartsProduct']['quantity'] + $this->data['CartsProduct']['quantity'];
			$this->CartsProduct->save($c);
		}
		return true;
	}
	
	function edit($id){
		// predpoklad ze se to nepodari
		$this->Session->setFlash('Košík daný produkt neobsahuje, nelze jej proto vymazat.');

		// najdu si produkt a upravim ho
		if ( $this->CartsProduct->findByIds($this->CartsProduct->cart_id, $id) ){
			$this->CartsProduct->id = $this->data['CartsProduct']['id'];
			unset($this->data['CartsProduct']['id']);
			$this->CartsProduct->save($this->data['CartsProduct'], false, array('quantity'));
			$this->Session->setFlash('Množství bylo upraveno');
		}
		$this->redirect(array('action' => 'index'), null, true);
	}

	function delete($id){
		// predpoklad ze se to nepodari
		$this->Session->setFlash('Košík daný produkt neobsahuje, nelze jej proto vymazat.');

		// najdu si produkt a smazu ho
		if ( $this->CartsProduct->findByIds($this->CartsProduct->cart_id, $id) ){
			$this->CartsProduct->delete($id);
			$this->Session->setFlash('Produkt byl z košíku vymazán.');
		}
		$this->redirect(array('action' => 'index'), null, true);
	}
	
	function stats(){
		$carts_stats = $this->CartsProduct->getStats($this->CartsProduct->cart_id);
		return array('carts_stats' => $carts_stats);
	}


	function getProducts(){
		return $this->CartsProduct->getProducts();
	}

	function get_out($id){
		return $this->CartsProduct->delete($id);
	}
}
?>