<?php
class BusinessSessionStatesController extends AppController {
	var $name = 'BusinessSessionStates';
	
	var $index_link = array('controller' => 'business_session_states', 'action' => 'index');
	
	var $left_menu_list = array('settings', 'business_session_states');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'settings');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		$business_session_states = $this->BusinessSessionState->find('all', array(
			'contain' => array()
		));
		
		$this->set('business_session_states', $business_session_states);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->BusinessSessionState->save($this->data)) {
				$this->Session->setFlash('Stav obchodního jednání byl uložen');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Stav obchodního jednání se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen stav obchodního jednání, který chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$business_session_state = $this->BusinessSessionState->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));
		
		if (empty($business_session_state)) {
			$this->Session->setFlash('Zvolený stav obchodního jednání neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (isset($this->data)) {
			if ($this->BusinessSessionState->save($this->data)) {
				$this->Session->setFlash('Stav obchodního jednání byl upraven');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Stav obchodního jednání se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		} else {
			$this->data = $business_session_state;
		}
	}
	
	function user_delete($id = null) {
			if (!$id) {
			$this->Session->setFlash('Není určen stav obchodního jednání, který chcete smazat');
			$this->redirect($this->index_link);
		}
		
		$business_session_state = $this->BusinessSessionState->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));
		
		if (empty($business_session_state)) {
			$this->Session->setFlash('Zvolený stav obchodního jednání neexistuje');
			$this->redirect($this->index_link);
		}
		
		if ($this->BusinessSessionState->delete($id)) {
			$this->Session->setFlash('Stav obchodního jednání byl odstraněn');
		} else {
			$this->Session->setFlash('Stav obchodního jednání se nepodařilo odstranit, opakujte prosím akci');
		}
		
		$this->redirect($this->index_link);
	} 
}