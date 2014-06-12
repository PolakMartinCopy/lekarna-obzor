<?php
class ImpositionStatesController extends AppController {
	var $name = 'ImpositionStates';
	
	var $index_link = array('controller' => 'imposition_states', 'action' => 'index');
	
	var $left_menu_list = array('settings', 'imposition_states');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'settings');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		$imposition_states = $this->ImpositionState->find('all', array(
			'contain' => array()
		));
		
		$this->set('imposition_states', $imposition_states);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->ImpositionState->save($this->data)) {
				$this->Session->setFlash('Stav úkolu byl uložen');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Stav úkolu se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen stav úkolu, který chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$imposition_state = $this->ImpositionState->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));
		
		if (empty($imposition_state)) {
			$this->Session->setFlash('Zvolený stav úkolu neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (isset($this->data)) {
			if ($this->ImpositionState->save($this->data)) {
				$this->Session->setFlash('Stav úkolu byl upraven');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Stav úkolu se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		} else {
			$this->data = $imposition_state;
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen stav úkolu, který chcete smazat');
			$this->redirect($this->index_link);
		}
		
		$imposition_state = $this->ImpositionState->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));
		
		if (empty($imposition_state)) {
			$this->Session->setFlash('Zvolený stav úkolu neexistuje');
			$this->redirect($this->index_link);
		}
		
		if ($this->ImpositionState->delete($id)) {
			$this->Session->setFlash('Stav úkolu byl vymazán');
		} else {
			$this->Session->setFlash('Stav úkolu se nepodařilo vymazat, opakujte prosím akci');
		}
		
		$this->redirect($this->index_link);
	}
}