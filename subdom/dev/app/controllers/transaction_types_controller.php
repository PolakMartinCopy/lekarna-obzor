<?php
class TransactionTypesController extends AppController {
	var $name = 'TransactionTypes';
	
	var $left_menu_list = array('settings', 'transaction_types');
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('active_tab', 'settings');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		$transaction_types = $this->TransactionType->find('all', array(
			'contain' => array(),
		));

		$this->set('transaction_types', $transaction_types);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->TransactionType->save($this->data)) {
				$this->Session->setFlash('Typ transakce byl uložen.');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Typ transakce se nepodařilo uložit. Opravte chyby ve formuláři a opakujte prosím akci.');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán typ transakce, který chcete upravit');
			$this->redirect(array('action' => 'index'));
		}
		
		$transaction_type = $this->TransactionType->find('first', array(
			'conditions' => array('TransactionType.id' => $id),
			'contain' => array()
		));
		
		if (empty($transaction_type)) {
			$this->Session->setFlash('Typ transakce, který chcete upravit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		if (isset($this->data)) {
			if ($this->TransactionType->save($this->data)) {
				$this->Session->setFlash('Typ transakce byl upraven');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Typ transakce se nepodařilo upravit, opravte chyby ve formuláři a opakujte akci');
			}
		} else {
			$this->data = $transaction_type;
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán typ transakce, který chcete smazat');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->TransactionType->hasAny(array('TransactionType.id' => $id))) {
			$this->Session->setFlash('Typ transakce, který chcete upravit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		if ($this->TransactionType->delete($id)) {
			$this->Session->setFlash('Typ transakce byl odstraněn');
		} else {
			$this->Session->setFlash('Typ transakce se nepodařilo odstranit, opakujte prosím akci');
		}
		$this->redirect(array('action' => 'index'));
	}
}