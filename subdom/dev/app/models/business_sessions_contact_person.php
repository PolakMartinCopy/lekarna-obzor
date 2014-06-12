<?php
class BusinessSessionsContactPerson extends AppModel {
	var $name = 'BusinessSessionsContactPerson';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('BusinessSession', 'ContactPerson');
	
	var $validate = array(
		'business_session_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false,
			'message' => 'Není zvoleno obchodního jednání'
		),
		'contact_person_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false,
			'message' => 'Není zvolena kontaktní osoba'
		)
	);
}