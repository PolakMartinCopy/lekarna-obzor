<?php
class CostsController extends AppController {
	var $name = 'Costs';
	
	var $left_menu_list = array('business_sessions');
	
	function beforeFilter(){
		parent::beforeFilter();
		$this->set('active_tab', 'business_sessions');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen náklad, který chcete zobrazit');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		$cost = $this->Cost->find('first', array(
			'conditions' => array('Cost.id' => $id),
			'contain' => array('BusinessSession')
		));
		
		if (empty($cost)) {
			$this->Session->setFlash('Zvolený náklad neexistuje');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		if (!$this->Cost->checkUser($this->user, $cost['BusinessSession']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemůžete zobrazit náklady k tomuto obchodnímu jednání.');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		$this->set('cost', $cost);
	}
	
	function user_add() {
		if (isset($this->params['named']['business_session_id'])) {
			$business_session_id = $this->params['named']['business_session_id'];
		} else {
			$this->Session->setFlash('Není zadáno obchodní jednání, ke kterému chcete zadat náklady');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		$business_session = $this->Cost->BusinessSession->find('first', array(
			'conditions' => array('BusinessSession.id' => $business_session_id),
			'contain' => array()
		));
		if (!$this->Cost->checkUser($this->user, $business_session['BusinessSession']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemůžete přidat náklady k tomuto obchodnímu jednání.');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		$this->set('business_session_id', $business_session_id);
		$this->set('business_session', $business_session);
		// do leveho menu pridam polozku pro detaily partnera
		$this->left_menu_list[] = 'business_session_detailed';
		
		$this->set('monthNames', $this->monthNames);
		
		if (isset($this->data)) {
			// opravim desetinnou carku
			$this->data['Cost']['amount'] = str_replace(',', '.', $this->data['Cost']['amount']);
			if (!preg_match('/\./', $this->data['Cost']['amount'])) {
				$this->data['Cost']['amount'] .= '.00';
			}
			if ($this->Cost->save($this->data)) {
				$this->Session->setFlash('Náklady byly uloženy');
				$this->redirect(array('controller' => 'business_sessions', 'action' => 'view', $business_session_id));
			} else {
				$this->Session->setFlash('Náklady se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen náklad, který chcete upravit');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		$cost = $this->Cost->find('first', array(
			'conditions' => array('Cost.id' => $id),
			'contain' => array('BusinessSession')
		));
		
		if (empty($cost)) {
			$this->Session->setFlash('Zvolený náklad neexistuje');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		if (!$this->Cost->checkUser($this->user, $cost['BusinessSession']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemůžete upravit náklady pro toto obchodní jednání.');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		$this->set('business_session', $cost);
		// do leveho menu pridam polozku pro detaily partnera
		$this->left_menu_list[] = 'business_session_detailed';
		
		$this->set('monthNames', $this->monthNames);
		
		if (isset($this->data)) {
			// opravim desetinnou carku
			$this->data['Cost']['amount'] = str_replace(',', '.', $this->data['Cost']['amount']);
			if (!preg_match('/\./', $this->data['Cost']['amount'])) {
				$this->data['Cost']['amount'] .= '.00';
			}
			if ($this->Cost->save($this->data)) {
				$this->Session->setFlash('Náklady byly upraveny');
				$this->redirect(array('controller' => 'business_sessions', 'action' => 'view', $cost['Cost']['business_session_id']));
			} else {
				$this->Session->setFlash('Náklady se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		} else {
			$this->data = $cost;
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen náklad, který chcete smazat');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		$cost = $this->Cost->find('first', array(
			'conditions' => array('Cost.id' => $id),
			'contain' => array('BusinessPartner')
		));
		
		if (empty($cost)) {
			$this->Session->setFlash('Zvolený náklad neexistuje');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		if (!$this->Cost->checkUser($this->user, $cost['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemůžete odstranit náklady pro toto obchodní jednání.');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		if ($this->Cost->delete($id)) {
			$this->Session->setFlash('Zvolený náklad byl odstraněn');
		} else {
			$this->Session->setFlash('Zvolený náklad se nepodařilo odstranit, opakujte prosím akci');
		}
		$this->redirect(array('controller' => 'business_sessions', 'action' => 'view', $cost['Cost']['business_session_id']));
	}
}