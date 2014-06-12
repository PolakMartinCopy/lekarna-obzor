<?php 
class ImpositionPeriodsController extends AppController {
	var $name = 'ImpositionPeriods';
	
	var $index_link = array('controller' => 'imposition_periods', 'action' => 'index');
	
	var $left_menu_list = array('settings', 'imposition_periods');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'settings');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		$periods = $this->ImpositionPeriod->find('all', array(
			'contain' => array()
		));
		$this->set('periods', $periods);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->ImpositionPeriod->save($this->data)) {
				$this->Session->setFlash('Perioda úkolů byla uložena');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Periodu úkolů se nepodařilo uložit, opakujte prosím akci');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadaná perioda, kterou chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$period = $this->ImpositionPeriod->find('first', array(
			'conditions' => array('ImpositionPeriod.id' => $id),
			'contain' => array()
		));
		
		if (empty($period)) {
			$this->Session->setFlash('Perioda úkolů neexistuje');
			$this->redirect($this->index_link);
		}
		
		$this->set('period', $period);
		
		if (isset($this->data)) {
			if ($this->ImpositionPeriod->save($this->data)) {
				$this->Session->setFlash('Perioda úkolů byla upravena');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Periodu úkolů se nepodařilo upravit, opakujte prosím akci');
			}
		} else {
			$this->data = $period;
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadaná perioda, kterou chcete smazat');
			$this->redirect($this->index_link);
		}
		
		$period = $this->ImpositionPeriod->find('first', array(
			'conditions' => array('ImpositionPeriod.id' => $id),
			'contain' => array('RecursiveImposition')
		));
		
		if (!$this->ImpositionPeriod->hasAny(array('ImpositionPeriod.id' => $id))) {
			$this->Session->setFlash('Perioda úkolů neexistuje');
			$this->redirect($this->index_link);
		}
		
		if ($this->ImpositionPeriod->RecursiveImposition->hasAny(array('RecursiveImposition.imposition_period_id' => $id))) {
			$this->Session->setFlash('Perioda je přiřazena nějakým úkolům a proto ji nelze odstranit');
			$this->redirect($this->index_link);
		}
		
		if ($this->ImpositionPeriod->delete($id)) {
			$this->Session->setFlash('Perioda úkolů byla odstraněna');
		} else {
			$this->Session->setFlash('Periodu úkolů se nepodařilo odstranit, opakujte prosím akci');
		}
		$this->redirect($this->index_link);
	}
}
?>