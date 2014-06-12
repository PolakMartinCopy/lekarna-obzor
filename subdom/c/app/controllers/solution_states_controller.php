<?php
class SolutionStatesController extends AppController {
	var $name = 'SolutionStates';
	
	var $index_link = array('controller' => 'solution_states', 'action' => 'index');
	
	var $left_menu_list = array('settings', 'solution_states');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'settings');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		$solution_states = $this->SolutionState->find('all', array(
			'contain' => array()
		));
		
		$this->set('solution_states', $solution_states);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->SolutionState->save($this->data)) {
				$this->Session->setFlash('Stav řešení byl uložen');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Stav řešení se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen stav řešení, který chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$solution_state = $this->SolutionState->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));
		
		if (empty($solution_state)) {
			$this->Session->setFlash('Zvolený stav řešení neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (isset($this->data)) {
			if ($this->SolutionState->save($this->data)) {
				$this->Session->setFlash('Stav řešení byl upraven');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Stav řešení se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		} else {
			$this->data = $solution_state;
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen stav řešení, který chcete smazat');
			$this->redirect($this->index_link);
		}
		
		$solution_state = $this->SolutionState->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));
		
		if (empty($solution_state)) {
			$this->Session->setFlash('Zvolený stav řešení neexistuje');
			$this->redirect($this->index_link);
		}
		
		if ($this->SolutionState->delete($id)) {
			$this->Session->setFlash('Stav řešení byl vymazán');
		} else {
			$this->Session->setFlash('Stav řešení se nepodařilo vymazat, opakujte prosím akci');
		}
		
		$this->redirect($this->index_link);
	}
}
?>