<?php
class OptionsController extends AppController {

	var $name = 'Options';
	var $helpers = array('Html', 'Form', 'Javascript' );

	function admin_index() {
		$this->Option->recursive = 0;
		$this->set('options', $this->paginate('Option'));
	}

	function admin_add() {
		if (!empty($this->data)) {
			if ( $this->Option->hasAny(array('name' => $this->data['Option']['name'])) ){
				$this->Session->setFlash('Hodnota "' . $this->data['Option']['name'] . '" již v databázi figuruje.');
			} else {
				$this->Option->create();
				if ($this->Option->save($this->data)) {
					$this->Session->setFlash('Název byl uložen.');
					$this->redirect(array('action'=>'index'), null, true);
				} else {
					$this->Session->setFlash('Název nemohl být uložen, vyplňte prosím správně všechna pole.');
				}
			}
		}
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Invalid Option');
			$this->redirect(array('action'=>'index'), null, true);
		}
		if (!empty($this->data)) {
			if ($this->Option->save($this->data)) {
				$this->Session->setFlash('Název byl upraven.');
				$this->redirect(array('action'=>'index'), null, true);
			} else {
				$this->Session->setFlash('Název nemohl být uložen, vyplňte prosím správně všechna pole.');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Option->read(null, $id);
		}
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující název.');
			$this->redirect(array('action'=>'index'), null, true);
		}

		if ($this->Option->delete($id)) {
			$this->Session->setFlash('Název byl vymazán.');
			$this->redirect(array('action'=>'index'), null, true);
		}
	}

	function list_opt(){
		$options = $this->Option->generateList(null, array('name' => 'asc'), null, '{n}.Option.id', '{n}.Option.name');
		return $options;
	}
}
?>