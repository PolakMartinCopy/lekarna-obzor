<?php
class Document extends AppModel {
	var $name = 'Document';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Imposition', 'BusinessPartner', 'Offer');
	
	var $validate = array(
		'title' => array(
			'rule' => 'notEmpty',
			'message' => 'Název dokumentu musí být vyplněn'
		),
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Název souboru musí být vyplněn'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Zvolený název souboru existuje, zadejte prosím jiný'
			)
		)/*,
		'imposition_id' => array(
			'rule' => array('isSetType'),
			'message' => 'Typ souboru musí být vybrán'
		),
		'business_partner_id' => array(
			'rule' => array('isSetType'),
			'message' => 'Typ souboru musí být vybrán'
		),
		'offer_id' => array(
			'rule' => array('isSetType'),
			'message' => 'Typ souboru musí být vybrán'
		)*/
	);
	
	function isSetType() {
		return !(empty($this->data['Document']['imposition_id']) && empty($this->data['Document']['business_partner_id']) && empty($this->data['Document']['offer_id']));
	}
	

	function beforeDelete() {
		$document_id = $this->id;
		$document = $this->find('first', array(
			'conditions' => array('Document.id' => $document_id),
			'contain' => array(),
			'fields' => array('Document.id', 'Document.name')
		));
		
		if (file_exists('files/documents/' . $document['Document']['name'])) {
			return unlink('files/documents/' . $document['Document']['name']);
		}
		
		return false;
	}
	
	function checkName($name_in){
		// predpokladam, ze obrazek s
		// takovym jmenem neexistuje
		$name_out = $name_in;
		
		// pokud existuje, musim zkouset zda neexistuje s _{n}
		// az dokud se najde jmeno s cislem, ktere neexistuje
		if ( file_exists($name_in) ){
			$i = 1;
			$new_fileName = str_replace('.', '_' . $i . '.', $name_in);
			while ( file_exists($new_fileName ) ){
				$i++;
				$new_fileName = str_replace('.', '_' . $i . '.', $name_in);
			}
			$name_out = $new_fileName;
		}
		return $name_out;
	}
	
	function do_form_search($conditions, $data) {
		if (!empty($data['Document']['title'])) {
			$conditions[] = 'Document.title LIKE \'%%' . $data['Document']['title'] . '%%\'';
		}
		if (!empty($data['Document']['created'])) {
			$date = explode('.', $data['Document']['created']);
			$date = $date[2] . '-' . $date[1] . '-' . $date[0];
			$conditions[] = 'Document.created LIKE \'%%' . $date . '%%\'';
		}
		
		$conditions = implode (' AND ', $conditions);
		return $conditions;
	}
}