<?php
class ShippingsController extends AppController {

	var $name = 'Shippings';
	
	function admin_index(){
		$shippings = $this->Shipping->find('all', array(
			'conditions' => array(),
			'contain' => array(),
			'order' => array(
				'id' => 'asc'
			)
		));
		$this->set('shippings', $shippings);
	}
	
	function admin_edit($id = null){
		if ( empty($id) ){
			$this->Session->setFlash('Není definováno ID způsobu dopravy.');
			$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, true);
		}
		
		if ( !isset($this->data) ){
			$this->data = $this->Shipping->find('first', array(
				'conditions' => array(
					'id' => $id
				),
				'contain' => array()
			));
			
			if ( empty($this->data) ){
				$this->Session->setFlash('Neexistující způsob dopravy!');
				$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, true);
			}
		} else {
			if ( $this->Shipping->save($this->data) ){
				$this->Session->setFlash('Způsob dopravy byl upraven!');
				$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Způsob dopravy se nepodařilo uložit, zkuste to prosím znovu!');
			}
		}
	}

	function admin_add(){
		if ( isset($this->data) ){
			if ( $this->Shipping->save($this->data) ){
				$this->Session->setFlash('Způsob dopravy byl uložen!');
				$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Chyba při ukládání způsobu dopravy, zkontrolujte prosím všechna pole!');
			}
		}
	}
	
	function admin_delete($id){
		if ( !isset($id) ){
			$this->Session->setFlash('Neexistující způsob dopravy!');
			$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, true);
		} else {
			if ($this->Shipping->Order->hasAny(array('shipping_id' => $id))) {
				$this->Session->setFlash('Tento způsob dopravy je přiřazen k některým existujícím objednávkám, nelze jej proto vymazat!');
			} else {
				$this->Shipping->delete($id);
				$this->Session->setFlash('Způsob dopravy byl vymazán!');
			}
			$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, true);
		}
	}
	
}
?>