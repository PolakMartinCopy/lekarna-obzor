<?php
class Cart extends AppModel {
	var $name = 'Cart';

	var $actsAs = array('Containable');
	
	var $hasMany = array(
		'Product',
		'CartsProduct' => array(
			'className' => 'CartsProduct',
			'dependent' => true
		)
	);

	function _create($company_id){
		// kosik pridelim podle id spolecnosti,
		// nachystam si data na ulozeni
		$cart['Cart']['company_id'] = $company_id;

		// kosik ulozim do db
		$this->save($cart);
		
		// znam ID kosiku, zalozim automaticke pojmenovani
		$name = 'nepojmenovana' . $this->id . '_' . date('d-m-Y');
		$this->save(array('name' => $name));
		
		// vratim id kosiku
		return $this->id;
	}
	
	function add($data){
		// projdu si vsechny varianty produktu
		// sleduju, jestli se snazi pridat vubec nejaky produkt
		$any_product_added = false;
		foreach ( $data['CartsProduct'] as $cp ){
			if ( $cp['quantity'] > 0 ){
				// kontrola zda se nejedna o pridani zbozi
				// ktere uz je v objednavce, pokud je v objednavce nalezeno
				// stary zaznam se vymaze a je nahrazen novym
				$this->check_cart($cp);
				
				// potrebuju znat cenu produktu
				$product = $this->Product->find('first', array(
					'conditions' => array(
						'id' => $cp['product_id']
					),
					'fields' => array('price'),
					'contain' => array()
				));
				
				// nachystam si data pro ulozeni do kosiku
				$save_data = array(
					'cart_id' => $this->get_id(),
					'product_id' => $cp['product_id'],
					'product_attributes' => $cp['product_attributes'],
					'quantity' => $cp['quantity'],
					'price' => $product['Product']['price'],
					'subproduct_id' => (isset($cp['subproduct_id']) ? $cp['subproduct_id'] : null)
				);
				
				if (isset($cp['subproduct_id'])) {
					$subproduct = $this->CartsProduct->Subproduct->find('first', array(
						'conditions' => array('id' => $cp['subproduct_id']),
						'contain' => array()
					));
					
					$save_data['price'] += $subproduct['Subproduct']['price'];
				}
			
				// zresetuju si id, abych neupdatoval jiz vytvorene zaznamy
				unset($this->CartsProduct->id);
				
				// nachystana data vlozime do databaze
				if ( !$this->CartsProduct->save($save_data) ){
					debug($save_data); debug($cp);
					die();
					return 'Produkty se nepodařilo přidat do objednávky, zkuste to prosím znovu.';
				}

				// pridam priznak, ze nejaky produkt byl
				// do kosiku pridan
				$any_product_added = true;
			}
		}

		// musim zjistit, zda jsem pridal nejake produkty a podle toho
		// prizpusobit hlasku
		$return = 'Žádné zboží nebylo přidáno do objednávky. Zřejmě jste nechal(a) pole pro počet kusů prázdné.';
		if ( $any_product_added ){
			$return = 'Úspěšně jste přidal(a) zboží do objednávky.';
		}
		
		return $return;
	}
	
	function check_cart($data){
		$cp = $this->CartsProduct->find('all', array(
			'conditions' => array(
				'cart_id' => $this->get_id(),
				'product_id' => $data['product_id'],
				'product_attributes' => $data['product_attributes']
			),
			'contain' => array()
		));

		// pokud produkt v kosiku je, musim ho vymazat
		if ( !empty($cp) ){
			$this->delete_from_cart($cp);
		}
	}
	
	function delete_from_cart($data){
		foreach ( $data as $cp ){
			$this->CartsProduct->del($cp['CartsProduct']['id']);
		}
	}
	
	function get_id() {
		// zkusim najit v databazi kosik
		// pro daneho uzivatele
		// potrebuju operovat se Session promennymi,
		// tak si to nadefinuju
		App::import('Component', 'Session');
		$this->Session = &new SessionComponent;
		
		// nejdriv hledam id kosiku v sessions
		if ( !$this->Session->check('Cart.id') ){
			// kdyz ho nenaleznu, automaticky se snazim
			// najit v databazi posledni rozpracovanou objednavku (kosik)
			$data = $this->find('first', array(
				'conditions' => array(
					'company_id' => $this->Session->read('Company.id')
				),
				'order' => array(
					'created' => 'desc'
				)
			));
			
			if ( empty($data) ){
				// kosik jsem v databazi nenasel,
				// musim ho zalozit
				return $this->_create();
			}

			// kosik jsem nasel v databazi, zapisu si ho do session
			// pro pristi pouziti
			$this->Session->write('Cart', $data['Cart']);
			
			// vratim id kosiku
			return $data['Cart']['id'];
		}
		
		// id kosiku mam v session, takze ho vratim
		return $this->Session->read('Cart.id');
	}
}
?>