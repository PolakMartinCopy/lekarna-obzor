<?
class ShippingsController extends AppController{
	var $name = 'Shippings';
	
	function admin_index() {
		$this->layout = 'admin';
		$shippings = $this->Shipping->find('all', array(
			'contain' => array()
		));
		$this->set('shippings', $shippings);
	}
	
	function admin_add() {
		$this->layout = 'admin';
		if (isset($this->data)) {
			if ($this->Shipping->save($this->data)) {
				$this->Session->setFlash('Způsob dopravy vytvořen.');
				$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Způsob dopravy se nepodařilo vytvořit, opakujte akci');
			}
		}
	}
	
	function admin_edit($id) {
		$this->layout = 'admin';
		$this->set('id', $id);
		if (isset($this->data)) {
			if ($this->Shipping->save($this->data)) {
				$this->Session->setFlash('Způsob dopravy byl upraven.');
				$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Způsob dopravy se nepodařilo upravit, opakujte akci.');
			}
		} else {
			$this->Shipping->id = $id;
			$this->Shipping->contain();
			$this->data = $this->Shipping->read();
		}
	}
	
	function admin_delete($id) {
		if ($this->Shipping->del($id)) {
			$this->Session->setFlash('Způsob dopravy byl odstraněn.');
		} else {
			$this->Session->setFlash('Způsob dopravy se nepodařilo odstranit, opakujte akci.');
		}
		$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, false);
	}
}
?>