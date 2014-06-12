<?php
class AddressesController extends AppController {
	var $name = 'Addresses';
	
	var $left_menu_list = array('business_partners', 'business_partner_detailed');
	
	function beforeFilter(){
		parent::beforeFilter();
		$this->set('active_tab', 'business_partners');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}

	function user_add() {
		if (isset($this->params['named']['business_partner_id'])) {
			$business_partner_id = $this->params['named']['business_partner_id'];
		} else {
			$this->Session->setFlash('Není zadán obchodní partner, ke kterému chcete adresu přidat');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		$business_partner = $this->Address->BusinessPartner->find('first', array(
			'conditions' => array('BusinessPartner.id' => $business_partner_id),
			'contain' => array()
		));
		
		if (empty($business_partner)) {
			$this->Session->setFlash('Neexistující obchodní partner');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'index'));
		}
		
		if (!$this->Address->checkUser($this->user, $business_partner['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. K tomuto obchodnímu partnerovi nemůžete přidávat adresy.');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'index'));
		} 
		
		if (isset($this->params['named']['address_type_id'])) {
			$address_type_id = $this->params['named']['address_type_id'];
		} else {
			$this->Session->setFlash('Není zadán typ adresy, kterou chcete vložit');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner_id));
		}
		
		$just_one_address = $this->Address->find('first', array(
			'conditions' => array(
				'Address.business_partner_id' => $business_partner_id,
				'Address.address_type_id' => $address_type_id,
				'AddressType.just_one' => true
			),
			'contain' => array('AddressType')
		));
		
		if (!empty($just_one_address)) {
			$this->Session->setFlash('Tato adresa může být pro obchodního partnera zadaná pouze jednou. Upravte prosím stávající adresu.');
			$this->redirect(array('controller' => 'addresses', 'action' => 'edit', $just_one_address['Address']['id']));
		}
		
		$this->set('business_partner_id', $business_partner_id);
		$this->set('address_type_id', $address_type_id);
		
		// do leveho menu pridam polozku pro detaily partnera
		list($seat_address, $delivery_address, $invoice_address) = $this->Address->get_addresses($business_partner_id);
		$this->set(compact('business_partner', 'seat_address', 'delivery_address', 'invoice_address'));
		
		if (isset($this->data)) {
			if ($this->Address->save($this->data)) {
				$this->Session->setFlash('Adresa byla uložena');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner_id));
			} else {
				$this->Session->setFlash('Adresu se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určena adresa, kterou chcete upravovat');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'index'));
		}
		
		$address = $this->Address->find('first', array(
			'conditions' => array('Address.id' => $id),
			'contain' => array('BusinessPartner')
		));
		
		if (empty($address)) {
			$this->Session->setFlash('Zvolená adresa neexistuje');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'index'));
		}
		
		if (!$this->Address->checkUser($this->user, $address['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. K tomuto obchodnímu partnerovi nemůžete upravovat adresy.');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'index'));
		} 
		
		$business_partner_id = $address['Address']['business_partner_id'];
		$this->set('business_partner_id', $business_partner_id);
		
		// do leveho menu pridam polozku pro detaily partnera
		$business_partner = $this->Address->BusinessPartner->find('first', array(
			'conditions' => array('BusinessPartner.id' => $business_partner_id),
			'contain' => array()
		));
		list($seat_address, $delivery_address, $invoice_address) = $this->Address->get_addresses($business_partner_id);
		$this->set(compact('business_partner', 'seat_address', 'delivery_address', 'invoice_address'));
		
		if (isset($this->data)) {
			if ($this->Address->save($this->data)) {
				$this->Session->setFlash('Adresa byla upravena');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner_id));
			} else {
				$this->Session->setFlash('Adresu se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		} else {
			$this->data = $address;
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určena adresa, kterou chcete smazat');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'index'));
		}
		
		$address = $this->Address->find('first', array(
			'conditions' => array('Address.id' => $id),
			'contain' => array('BusinessPartner')
		));
		
		if (empty($address)) {
			$this->Session->setFlash('Zvolená adresa neexistuje');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'index'));
		}
		
		if (!$this->Address->checkUser($this->user, $address['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Tomuto obchodnímu partnerovi nemůžete mazat adresy.');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'index'));
		} 
		
		if ($this->Address->delete($id)) {
			$this->Session->setFlash('Adresa byla odstraněna');
		} else {
			$this->Session->setFlash('Adresu se nepodařilo odstranit, opakujte prosím akci');
		}
		$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $address['Address']['business_partner_id']));
	}

}