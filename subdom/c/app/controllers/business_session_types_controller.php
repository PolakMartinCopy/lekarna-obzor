<?php
class BusinessSessionTypesController extends AppController {
	var $name = 'BusinessSessionTypes';
	
	var $index_link = array('controller' => 'business_session_types', 'action' => 'index');
	
	var $left_menu_list = array('settings', 'business_session_types');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'settings');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		$business_session_types = $this->BusinessSessionType->find('all', array(
			'contain' => array()
		));
		
		$this->set('business_session_types', $business_session_types);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->BusinessSessionType->save($this->data)) {
				$this->Session->setFlash('Typ obchodního jednání byl uložen');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Typ obchodního jednání se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen typ obchodního jednání, který chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$business_session_type = $this->BusinessSessionType->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));
		
		if (empty($business_session_type)) {
			$this->Session->setFlash('Zvolený typ obchodního jednání neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (isset($this->data)) {
			if ($this->BusinessSessionType->save($this->data)) {
				$this->Session->setFlash('Typ obchodního jednání byl upraven');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Typ obchodního jednání se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		} else {
			$this->data = $business_session_type;
		}
	}
	
	function user_delete($id = null) {
			if (!$id) {
			$this->Session->setFlash('Není určen typ obchodního jednání, který chcete smazat');
			$this->redirect($this->index_link);
		}
		
		$business_session_type = $this->BusinessSessionType->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));
		
		if (empty($business_session_type)) {
			$this->Session->setFlash('Zvolený typ obchodního jednání neexistuje');
			$this->redirect($this->index_link);
		}
		
		if ($this->BusinessSessionType->delete($id)) {
			$this->Session->setFlash('Typ obchodního jednání byl odstraněn');
		} else {
			$this->Session->setFlash('Typ obchodního jednání se nepodařilo odstranit, opakujte prosím akci');
		}
		
		$this->redirect($this->index_link);
	} 
}