<?php
class UnitsController extends AppController {
	var $name = 'Units';
	
	var $left_menu_list = array('settings', 'units');
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('active_tab', 'settings');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		$units = $this->Unit->find('all', array(
			'contain' => array()
		));
		
		$this->set('units', $units);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->Unit->save($this->data)) {
				$this->Session->setFlash('Jednotka zboží byla uložena.');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Jednotku zboží se nepodařilo uložit. Opravte chyby ve formuláři a opakujte prosím akci.');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadaná jednotka, kterou chcete upravit');
			$this->redirect(array('action' => 'index'));
		}
		
		$unit = $this->Unit->find('first', array(
			'conditions' => array('Unit.id' => $id),
			'contain' => array()
		));
		
		if (empty($unit)) {
			$this->Session->setFlash('Jednotka, kterou chcete upravit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		if (isset($this->data)) {
			if ($this->Unit->save($this->data)) {
				$this->Session->setFlash('Jednotka zboží byla upravena');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Jednotku zboží se nepodařilo upravit, opravte chyby ve formuláři a opakujte akci');
			}
		} else {
			$this->data = $unit;
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadaná jednotka, kterou chcete smazat');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->Unit->hasAny(array('Unit.id' => $id))) {
			$this->Session->setFlash('Jednotka, kterou chcete upravit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		if ($this->Unit->delete($id)) {
			$this->Session->setFlash('Jednotka zboží byla odstraněna');
		} else {
			$this->Session->setFlash('Jednotku zboží se nepodařilo odstranit, opakujte prosím akci');
		}
		$this->redirect(array('action' => 'index'));
	}
}