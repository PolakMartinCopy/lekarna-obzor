<?php
class CartsProductsController extends AppController {
	var $name = 'CartsProducts';

	var $helpers = array('Html', 'Form', 'Javascript');

	function beforeFilter(){
		$this->Session->read();
		$this->CartsProduct->cart_id = $this->CartsProduct->Cart->get_id();
	}

	function index(){
		// kosik nesouvisi s kategoriemi,
		// menu necham zavrene
		$opened_category_id = 5;
		
		$this->layout = 'content';

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
		$cart_products = $this->CartsProduct->findAllByCart_id($this->CartsProduct->cart_id);
		foreach ( $cart_products as $index => $cart_product ){
			// u produktu si pridam jmenne atributy
			$cart_products[$index]['CartsProduct']['product_attributes'] = $this->requestAction('subproducts/to_names', array('attributes' => $cart_product['CartsProduct']['product_attributes']));
		}
		
		$this->set('cart_products', $cart_products);
		$this->set('sess', $this->Session->read());
	}

	function add(){
		// musim si vytahnout info o produktu
		$this->CartsProduct->Product->id = $this->data['Product']['id'];
		$this->CartsProduct->Product->recursive = -1;
		$product = $this->CartsProduct->Product->read();

		// vytahnu si info o slevach daneho produktu,
		// pokud existuji a zjistim zda se mi nezmeni zakladni cena
		
		// zakladni cena produktu, je zatim povazovana za nejnizsi
		$total_price = $product['Product']['price'];
		
		$discounts = $this->CartsProduct->Product->DiscountModelsProduct->find('all', array('conditions' => array('product_id' => $this->data['Product']['id']), 'recursive' => 2));
		if ( !empty( $discounts ) ){
			foreach ( $discounts as $discount ){
				if ( $discount['DiscountModelsProduct']['price'] < $total_price ){
					// cena je nizsi, nez prozatimni nejnizsi cena
					// musim kontrolovat, zda ma clovek splnene podminky
					// pro tuto cenu - na zacatku prepokladam, ze ma
					$allowed_discount = true;
					foreach ( $discount['DiscountModel']['DiscountConditions'] as $condition ){
						switch ( $condition['id'] ){
							case "1":
								// 1 je, ze musi byt prihlaseny
								if ( !$this->Session->check('Customer.id') ){
									$allowed_discount = false;
								}
							break;
							default:
								$allowed_discount = false;
							break;
						}
					}
					
					if ( $allowed_discount ){
						$total_price = $discount['DiscountModelsProduct']['price'];
					}
				}
			}
		}
		
		// vytahnu si info o subproduktech pokud
		// nejake existuji a pripoctu jejich prirustkovou cenu
		if ( isset($this->data['Product']['Option']) && !empty($this->data['Product']['Option']) ){
			$options_condition = array();
			// prodjdu si zvolene options a vytahnu si idecka
			foreach ( $this->data['Product']['Option'] as $option ){
				$options_condition[] = $option;
			}

			// vytahnu si ceny pro dane options
			$this->CartsProduct->Product->Subproduct->recursive = -1;
			$options = $this->CartsProduct->Product->Subproduct->findAll(array('id' => $options_condition), 'price');

			// k celkove cene pripoctu ceny options
			foreach ( $options as $option ){
				$total_price = $total_price + $option['Subproduct']['price'];
			}
		} else {
			$this->data['Product']['Option'] = array();
		}



		// inicializuju si objekt
		$this->CartsProduct->create();
		// vytvorim si data, ktera ulozim
		$this->data['CartsProduct']['product_id'] = $this->data['Product']['id'];
		$this->data['CartsProduct']['cart_id'] = $this->requestAction('carts/get_id');
		$this->data['CartsProduct']['product_attributes'] = serialize($this->data['Product']['Option']);
		$this->data['CartsProduct']['quantity'] = $this->data['Product']['quantity'];
		$this->data['CartsProduct']['price'] = $total_price;

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
//			$this->CartsProduct->updateAll(array('quantity' => '`quantity` + ' . $this->data['CartsProduct']['quantity']), array('Cart.id' => $cpID));
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
		// potrebuju vedet, ktery kosik mam zobrazit
		$cart_id = $this->requestAction('/carts/get_id');
		
		// odpojim nepotrebne modely
		$this->CartsProduct->unbindModel(array('belongsTo' => array('Cart')));

		$this->CartsProduct->Product->unbindModel(
			array(
				'hasAndBelongsToMany' => array('Category', 'Cart'),
				'hasMany' => array('Subproduct', 'CartsProduct'),
				'belongsTo' => array('Manufacturer')
			)
		);

		$this->CartsProduct->recursive = 2;

		// vytahnu si vsechny produkty, ktere patri
		// do zakaznikova kose
		$cart_products = $this->CartsProduct->findAllByCart_id($cart_id);
		foreach ( $cart_products as $index => $cart_product ){
			// u produktu si pridam jmenne atributy
			$cart_products[$index]['CartsProduct']['product_attributes'] = $this->requestAction('subproducts/to_names', array('attributes' => $cart_product['CartsProduct']['product_attributes']));
		}
		return $cart_products;
	}


	function get_out($id){
		return $this->CartsProduct->delete($id);
	}
	
}
?>