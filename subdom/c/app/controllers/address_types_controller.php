<?php
class AddressTypesController extends AppController {
	var $name = 'AddressTypes';
	
	var $index_link = array('controller' => 'address_types', 'action' => 'index');
	
	var $left_menu_list = array('settings', 'address_types');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'settings');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		$address_types = $this->AddressType->find('all', array(
			'contain' => array()
		));
		
		$this->set('address_types', $address_types);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->AddressType->save($this->data)) {
				$this->Session->setFlash('Typ adresy byl uložen');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Typ adresy se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen typ adresy, který chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$address_type = $this->AddressType->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));
		
		if (empty($address_type)) {
			$this->Session->setFlash('Zvolený typ adresy neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (isset($this->data)) {
			if ($this->AddressType->save($this->data)) {
				$this->Session->setFlash('Typ adresy byl upraven');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Typ adresy se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		} else {
			$this->data = $address_type;
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen typ adresy, který chcete smazat');
			$this->redirect($this->index_link);
		}
		
		$address_type = $this->AddressType->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));
		
		if (empty($address_type)) {
			$this->Session->setFlash('Zvolený typ adresy neexistuje');
			$this->redirect($this->index_link);
		}
		
		if ($this->AddressType->delete($id)) {
			$this->Session->setFlash('Typ adresy byl odstraněn');
		} else {
			$this->Session->setFlash('Typ adresy se nepodařilo odstranit, opakujte prosím akci');
		}
		
		$this->redirect($this->index_link);
	}
}
?>