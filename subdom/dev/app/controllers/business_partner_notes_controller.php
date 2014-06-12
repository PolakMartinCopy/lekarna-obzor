<?php 
class BusinessPartnerNotesController extends AppController {
	var $name = 'BusinessPartnerNotes';
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->BusinessPartnerNote->save($this->data)) {
				$this->Session->setFlash('Poznámka byla uložena');
			} else {
				$this->Session->setFlash('Poznámku se nepodařilo uložit');
			}
			$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $this->data['BusinessPartnerNote']['business_partner_id'], 'tab' => 13));
		}
	}
	
	function user_edit($id = null) {
		$note = $this->BusinessPartnerNote->find('first', array(
			'conditions' => array('BusinessPartnerNote.id' => $id),
			'contain' => array()	
		));
		if (isset($this->data)) {
			if ($this->BusinessPartnerNote->save($this->data)) {
				$this->Session->setFlash('Poznámka byla uložena');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $note['BusinessPartnerNote']['business_partner_id'], 'tab' => 13));
			} else {
				$this->Session->setFlash('Poznámku se nepodařilo uložit');
			}
		} else {
			$this->data = $note;
		}
		$this->set('note', $note);
	}
	
	function user_delete($id = null) {
		$note = $this->BusinessPartnerNote->find('first', array(
			'conditions' => array('BusinessPartnerNote.id' => $id),
			'contain' => array(),
			'fields' => array('BusinessPartnerNote.business_partner_id')
		));
		if ($this->BusinessPartnerNote->delete($id)) {
			$this->Session->setFlash('Poznámka byla odstraněna');
		} else {
			$this->Session->setFlash('Poznámku se nepodařilo odstranit');
		}
		$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $note['BusinessPartnerNote']['business_partner_id'], 'tab' => 13));
	}
}
?>