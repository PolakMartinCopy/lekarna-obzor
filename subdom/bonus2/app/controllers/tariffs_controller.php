<?php
class TariffsController extends AppController {
	var $name = 'Tariffs';
	
	function beforeRender() {
		parent::beforeRender();
		$this->set('active_tab', 'users');
	}
	
	function user_add() {
		$user = $this->Auth->user();
		
		if (!$user['User']['is_admin']) {
			$this->Session->setFlash('Nemáte oprávnění prohlížet tento obsah.');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			if ($this->Tariff->save($this->data)) {
				$this->Session->setFlash('Bonusový tarif byl vytvořen.');
			} else {
				$this->Session->setFlash('Bonusový tarif se nepodařilo vytvořit, opravte chyby ve formuláři a opakujte prosím akci.');
				// predam si vysledky validace do sesny, abych k nim mel pristup i po redirectu
				if ($this->Session->check('validationErrors.Tariff')) {
					$this->Session->delete('validationErrors.Tariff');
				}
				$this->Session->write('validationErrors.Tariff', $this->Tariff->validationErrors);
				if ($this->Session->check('data.Tariff')) {
					$this->Session->delete('data.Tariff');
				}
				$this->Session->write('data.Tariff', $this->data['Tariff']);
			}
		}
		$this->redirect(array('controller' => 'users', 'action' => 'index'));
	}
	
	function user_edit($id = null) {
		$user = $this->Auth->user();
		
		if (!$user['User']['is_admin']) {
			$this->Session->setFlash('Nemáte oprávnění prohlížet tento obsah.');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}
		
		if (!$id) {
			$this->Session->setFlash('Není zadán tarif, který chcete upravovat.');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			if ($this->Tariff->save($this->data)) {
				$this->Session->setFlash('Tarif byl upraven.');
				$this->redirect(array('controller' => 'users', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Tarif se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci.');
			}
		} else {
			$tariff = $this->Tariff->find('first', array(
				'conditions' => array('Tariff.id' => $id),
				'contain' => array()
			));
			
			if (empty($tariff)) {
				$this->Session->setFlash('Tarif, který chcete upravovat, neexistuje.');
				$this->redirect(array('controller' => 'users', 'action' => 'index'));
			}
			
			$this->data = $tariff;
		}
	}
}