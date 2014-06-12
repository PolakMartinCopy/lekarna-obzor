<?php
class OffersController extends AppController {
	var $name = 'Offers';
	
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
			$this->Session->setFlash('Není určena nabídka, kterou chcete zobrazit');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		$documents_conditions = array();
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'documents') {
			$this->Session->delete('Search.DocumentForm1');
			$this->redirect(array('controller' => 'offers', 'action' => 'view', $id));
		}
		
		if ( isset($this->data['DocumentForm1']['Document']['search_form']) && $this->data['DocumentForm1']['Document']['search_form'] == 1 ){
			$this->Session->write('Search.DocumentForm1', $this->data['DocumentForm1']);
			$documents_conditions = $this->Offer->Document->do_form_search($documents_conditions, $this->data['DocumentForm1']);
		} elseif ($this->Session->check('Search.DocumentForm1')) {
			$this->data['DocumentForm1'] = $this->Session->read('Search.DocumentForm1');
			$documents_conditions = $this->Offer->Document->do_form_search($documents_conditions, $this->data['DocumentForm1']);
		}
		
		$offer = $this->Offer->find('first', array(
			'conditions' => array('Offer.id' => $id),
			'contain' => array(
				'BusinessSession',
				'Document' => array(
					'conditions' => $documents_conditions
				)
			)
		));
		
		if (empty($offer)) {
			$this->Session->setFlash('Zvolená nabídka neexistuje');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		if (!$this->Offer->checkUser($this->user, $offer['BusinessSession']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemůžete zobrazit nabídky pro toto obchodní jednání.');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		$this->set('business_session', $offer);
		$this->left_menu_list[] = 'business_session_detailed';
		$this->set('offer', $offer);
	}
	
	function user_add() {
		if (isset($this->params['named']['business_session_id'])) {
			$business_session_id = $this->params['named']['business_session_id'];
		} else {
			$this->Session->setFlash('Není zadáno obchodní jednání, ke kterému chcete zadat nabídku');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		$business_session = $this->Offer->BusinessSession->find('first', array(
			'conditions' => array('BusinessSession.id' => $business_session_id),
			'contain' => array()
		));
		
		if (!$this->Offer->checkUser($this->user, $business_session['BusinessSession']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemůžete přidat nabídku k tomuto obchodnímu jednání.');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		$this->set('business_session_id', $business_session_id);
		$this->set('business_session', $business_session);
		// do leveho menu pridam polozku pro detaily partnera
		$this->left_menu_list[] = 'business_session_detailed';
		
		if (isset($this->data)) {
			if (is_uploaded_file($this->data['Document'][0]['name']['tmp_name'])) {
				$this->data['Document'][0]['name']['name'] = strip_diacritic($this->data['Document'][0]['name']['name'], true);
				$this->data['Document'][0]['name']['name'] = $this->Offer->Document->checkName('files/documents/' . $this->data['Document'][0]['name']['name']);
				if ( move_uploaded_file($this->data['Document'][0]['name']['tmp_name'], $this->data['Document'][0]['name']['name']) ){
					// potrebuju zmenit prava u dokumentu
					chmod($this->data['Document'][0]['name']['name'], 0644);
					$this->data['Document'][0]['name']['name'] = explode("/", $this->data['Document'][0]['name']['name']);
					$this->data['Document'][0]['name'] = $this->data['Document'][0]['name']['name'][count($this->data['Document'][0]['name']['name']) -1];
					
					if ($this->Offer->saveAll($this->data)) {
						$this->Session->setFlash('Nabídka byla uložena');
						$this->redirect(array('controller' => 'business_sessions', 'action' => 'view', $business_session_id));
					} else {
						// smazu dokument, protoze ten je uz vytvoren na disku
						unlink('files/documents/' . $this->data['Document'][0]['name']);
						$this->Session->setFlash('Nabídku se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				} else {
					$this->Session->setFlash('Dokument se nepodařilo přejmenovat z tmp.');
				}
			} else {
				$this->Session->setFlash('Dokument se nepodařilo nahrát.');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určena nabídka, kterou chcete upravit');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		$offer = $this->Offer->find('first', array(
			'conditions' => array('Offer.id' => $id),
			'contain' => array('BusinessSession')
		));
		
		$this->set('business_session', $offer);
		$this->left_menu_list[] = 'business_session_detailed';
		
		if (empty($offer)) {
			$this->Session->setFlash('Zvolená nabídka neexistuje');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		if (!$this->Offer->checkUser($this->user, $offer['BusinessSession']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemůžete upravit nabídku pro toto obchodní jednání.');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			if ($this->Offer->save($this->data)) {
				$this->Session->setFlash('Nabídka byla upravena');
				$this->redirect(array('controller' => 'business_sessions', 'action' => 'view', $offer['Offer']['business_session_id']));
			} else {
				$this->Session->setFlash('Nabídku se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		} else {
			$this->data = $offer;
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určena nabídka, kterou chcete smazat');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		$offer = $this->Offer->find('first', array(
			'conditions' => array('Offer.id' => $id),
			'contain' => array(
				'BusinessSession',
				'Document'
			)
		));
		
		if (empty($offer)) {
			$this->Session->setFlash('Zvolená nabídka neexistuje');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}

		if (!$this->Offer->checkUser($this->user, $offer['BusinessSession']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemůžete smazat nabídku pro toto obchodní jednání.');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		if ($this->Offer->delete($id)) {
			$this->Session->setFlash('Zvolená nabídka byla odstraněna');
		} else {
			$this->Session->setFlash('Zvolenou nabídku se nepodařilo odstranit, opakujte prosím akci');
		}
		$this->redirect(array('controller' => 'business_sessions', 'action' => 'view', $offer['Offer']['business_session_id']));
	}
}