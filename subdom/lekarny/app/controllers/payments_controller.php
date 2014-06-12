<?
class PaymentsController extends AppController{
	var $name = 'Payments';
	
	function admin_index() {
		$this->layout = 'admin';
		$payments = $this->Payment->find('all', array(
			'contain' => array()
		));
		$this->set('payments', $payments);
	}
	
	function admin_add() {
		$this->layout = 'admin';
		if (isset($this->data)) {
			if ($this->Payment->save($this->data)) {
				$this->Session->setFlash('Nový způsob platby vytvořen.');
				$this->redirect(array('controller' => 'payments', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Způsob platby se nepodařilo vytvořit, opakujte akci.');
			}
		}
	}
	
	function admin_edit($id) {
		$this->layout = 'admin';
		$this->set('id', $id);
		if (isset($this->data)) {
			if ($this->Payment->save($this->data)) {
				$this->Session->setFlash('Způsob platby byl upraven.');
				$this->redirect(array('controller' => 'payments', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Způsob platby se nepodařilo upravit, opakujte akci.');
			}
		} else {
			$this->Payment->id = $id;
			$this->Payment->contain();
			$this->data = $this->Payment->read();
		}
	}
	
	function admin_delete($id) {
		if ($this->Payment->del($id)) {
			$this->Session->setFlash('Způsob platby byl odstraněn.');
		} else {
			$this->Session->setFlash('Způsob platby se nepodařilo odstranit, opakujte akci.');
		}
		$this->redirect(array('controller' => 'payments', 'action' => 'index'), null, true);
	}
}
?>