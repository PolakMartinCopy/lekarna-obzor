<?php
class AnniversaryTypesController extends AppController {
	var $name = 'AnniversaryTypes';
	
	var $index_link = array('controller' => 'anniversary_types', 'action' => 'index');
	
	var $left_menu_list = array('settings', 'anniversary_types');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'settings');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		$anniversary_types = $this->AnniversaryType->find('all', array(
			'contain' => array()
		));
		
		$this->set('anniversary_types', $anniversary_types);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->AnniversaryType->save($this->data)) {
				$this->Session->setFlash('Typ výročí byl uložen');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Typ výročí se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen typ výročí, který chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$anniversary_type = $this->AnniversaryType->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));
		
		if (empty($anniversary_type)) {
			$this->Session->setFlash('Zvolený typ výročí neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (isset($this->data)) {
			if ($this->AnniversaryType->save($this->data)) {
				$this->Session->setFlash('Typ výročí byl upraven');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Typ výročí se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		} else {
			$this->data = $anniversary_type;
		}
	}
	
	function user_delete($id = null) {
			if (!$id) {
			$this->Session->setFlash('Není určen typ výročí, který chcete smazat');
			$this->redirect($this->index_link);
		}
		
		$anniversary_type = $this->AnniversaryType->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));
		
		if (empty($anniversary_type)) {
			$this->Session->setFlash('Zvolený typ výročí neexistuje');
			$this->redirect($this->index_link);
		}
		
		if ($this->AnniversaryType->delete($id)) {
			$this->Session->setFlash('Typ výročí byl odstraněn');
		} else {
			$this->Session->setFlash('Typ výročí se nepodařilo odstranit, opakujte prosím akci');
		}
		
		$this->redirect($this->index_link);
	} 
}