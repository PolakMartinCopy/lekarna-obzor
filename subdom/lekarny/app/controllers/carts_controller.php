<?php
class CartsController extends AppController {
	var $name = 'Carts';
	
	function count($id){
		$count = $this->Cart->find('count', array(
			'conditions' => array(
				'company_id' => $id
			)
		));
		return $count;
	}
	
	function get_stats(){
		$cart = $this->Cart->find('first', array(
			'conditions' => array(
				'id' => $this->Session->read('Cart.id')
			),
			'contain' => array('CartsProduct')
		));
		
		$total = 0;
		$count = count($cart['CartsProduct']);
		
		for ( $i = 0; $i < $count; $i++ ){
			$total = $total + ( $cart['CartsProduct'][$i]['quantity'] * $cart['CartsProduct'][$i]['price'] );
		}
		
		return array('products_quantity' => $count, 'price' => $total);
	}
	
	function users_add(){
		// vytvorim novy kosik (objednavku)
		$cart_id = $this->Cart->_create($this->Session->read('Company.id'));
		$cart = $this->Cart->find('first', array(
			'conditions' => array(
				'id' => $cart_id
			),
			'contain' => array()
		));
		
		$this->Session->write('Cart', $cart['Cart']);
		
		// presmeruju na seznam produktu v objednavce
		$this->Session->setFlash('Nová objednávka byla založena.');
		$this->redirect(array('users' => true, 'controller' => 'carts', 'action' => 'view'), null, true);
	}

	function users_delete($id){
		// musim zkontrolovat, zda ma pravo mazat objednavku
		$cart = $this->Cart->find('first', array(
			'conditions' => array(
				'id' => $id,
				'company_id' => $this->Session->read('Company.id')
			),
			'contain' => array()
		));
		
		if ( !empty($cart) ){
			$this->Cart->del($id);
			
			// musim zkontrolovat, jestli se nejedna o objednavku, ktera byla nastavena jako aktivni,
			// v tom pripade ji musim vymazat ze session
			if ( $id == $this->Session->read('Cart.id') ){
				$this->Session->del('Cart');
			}
			
			$this->Session->setFlash('Rozpracovaná objednávka byla vymazána.');
			$this->redirect(array('users' => true, 'controller' => 'carts', 'action' => 'index'), null, true);
		}
		
		$this->Session->setFlash('Snažíte se vymazat neexistující rozpracovanou objednávku.');
		$this->redirect(array('users' => true, 'controller' => 'carts', 'action' => 'index'), null, true);
	}
	
	function users_edit(){
		if ( isset($this->data) ){
			foreach ( $this->data['CartsProduct'] as $id => $cp ){
				// pokud tam zada nulu, nebo cokoliv jineho nez kladne cislo,
				// musi se zaznam odstranit
				if ( $cp['quantity'] < 1 ){
					$this->Cart->CartsProduct->del($id);
					continue;
				} 
				
				$this->Cart->CartsProduct->id = $id;
				if ( !$this->Cart->CartsProduct->save($cp) ){
					$this->Session->setFlash('Při úpravě položek došlo k chybě, zkuste to prosím znovu.');
					$this->redirect(array('users' => true, 'controller' => 'carts', 'action' => 'view'), null, true);
				}
			}
			$this->Session->setFlash('Položky objednávky byly upraveny.');
			$this->redirect(array('users' => true, 'controller' => 'carts', 'action' => 'view'), null, true);
		}
	}
	
	function users_edit_name($id){
		$cart = $this->Cart->find('first', array(
			'conditions' => array(
				'id' => $id,
				'company_id' => $this->Session->read('Company.id')
			),
			'contain' => array()
		));
		
		// test zda ma zadany uzivatel pravo pracovat s touto objednavkou
		if ( empty($cart) ){
			$this->Session->setFlash('Snažíte se pracovat s neexistující objenávkou.');
			$this->redirect(array('users' => true, 'controller' => 'carts', 'action' => 'index'), null, true);
		}

		$this->layout = 'users';
		
		if ( isset($this->data) ){
			// budeme ukladat
			if ( $this->Cart->save($this->data) ){
				// musim zkontrolovat, zda neprejmenoval aktualne zpracovavanou objednavku a upravit session
				if ( $this->Session->check('Cart') AND $this->Session->read('Cart.id') == $id ){
					$this->Session->write('Cart.name', $this->data['Cart']['name']);
				}
				
				$this->Session->setFlash('Objednávka byla přejmenována.');
				$this->redirect(array('users' => true, 'controller' => 'carts', 'action' => 'index'), null, true);
			}
			$this->setFlash('Objednávku se nepodařilo přejmenovat, zkuste to prosím znovu.');
		}
		$this->data = $cart;
	}
	
	function users_index(){
		$this->layout = 'users';
		
		$carts = $this->Cart->find('all', array(
			'conditions' => array(
				'company_id' => $this->Session->read('Company.id')
			),
			'order' => array(
				'modified' => 'desc'
			),
			'contain' => array(),
		));
		
		$this->set('carts', $carts);
	}
	
	function users_set_active($id){
		$cart = $this->Cart->find('first', array(
			'conditions' => array(
				'id' => $id,
				'company_id' => $this->Session->read('Company.id')
			),
			'contain' => array()
		));
		
		// test zda ma zadany uzivatel pravo pracovat s touto objednavkou
		if ( empty($cart) ){
			$this->Session->setFlash('Snažíte se pracovat s neexistující objenávkou.');
			$this->redirect(array('users' => true, 'controller' => 'carts', 'action' => 'index'), null, true);
		}
		
		// nastavim si tuto objednavku jako aktivni a presmeruju
		$this->Session->write('Cart', $cart['Cart']);
		$this->Session->setFlash('Objednávka byla vybrána jako aktivní, nyní můžete pokračovat v úpravách.');
		$this->redirect(array('users' => true, 'controller' => 'carts', 'action' => 'view'), null, true);
	}
	
	function users_view(){
		$this->layout = 'users';
		
		// vyhledam si data z objednavky
		$cart = $this->Cart->find('first', array(
			'conditions' => array(
				'id' => $this->Session->read('Cart.id')
			),
			'contain' => array(
				'CartsProduct' => array(
					'Product' => array(
						'TaxClass'
					)
				)
			)
		));
		
		$this->set('cart', $cart);
	}
}
?>