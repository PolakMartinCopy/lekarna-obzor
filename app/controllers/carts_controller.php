<?php
class CartsController extends AppController {
	var $name = 'Carts';

	var $helpers = array('Html', 'Form', 'Javascript');

	function get_id() {
		return $this->Cart->get_id();
	}
	
	// vysypani kosiku
	function dump() {
		$cart_id = $this->Cart->get_id();
		$success = $this->Cart->CartsProduct->deleteAll(array('cart_id' => $cart_id));
		if ($success) {
			$this->Session->setFlash('Produkty z košíku byly odstraněny.');
		} else {
			$this->Session->setFlash('Produkty z košíku se nepodařilo odstranit.');
		}
		$this->redirect(array('controller' => 'carts_products', 'action' => 'index'));
	}
	
	// vysypani kosiku ajaxem
	function ajax_dump() {
		$result = array(
			'success' => false,
			'message' => ''	
		);
		
		$cart_id = $this->Cart->get_id();
		$success = $this->Cart->CartsProduct->deleteAll(array('cart_id' => $cart_id));
		if ($success) {
			$result['success'] = true;
			$result['message'] = 'Produkty z košíku byly odstraněny.';
		} else {
			$result['message'] = 'Produkty z košíku se nepodařilo odstranit.';
		}
		
		echo json_encode($result);
		die();
	}
}
?>