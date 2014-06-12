<?php
class BusinessSessionsUser extends AppModel {
	var $name = 'BusinessSessionsUser';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('User', 'BusinessSession');
	
	var $validate = array(
/*		'business_session_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false,
			'message' => 'Není zvoleno obchodního jednání'
		),
		'user_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false,
			'message' => 'Není zvolen uživatel přizvaný na obchodní jednání'
		)*/
	);
}