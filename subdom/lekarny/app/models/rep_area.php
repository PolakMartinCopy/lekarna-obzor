<?
class RepArea extends AppModel {
	var $name = 'RepArea';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Rep');
	
	var $validate = array(
		'start_zip' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Pole Počáteční PSČ musí být neprázdné',
				'last' => true
			),
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Pole Počáteční PSČ musí být číslice (např 60200)'
			),
			'between' => array(
				'rule' => array('between', 5, 5),
				'message' => 'Pole Počáteční PSČ musí být 5 znaků (např 60200)'
			)
		),
		'end_zip' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Pole Koncové PSČ musí být neprázdné',
				'last' => true
			),
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Pole Koncové PSČ musí být číslice (např 60200)'
			),
			'between' => array(
				'rule' => array('between', 5, 5),
				'message' => 'Pole Koncové PSČ musí být 5 znaků (např 60200)'
			)
		),
		'area' => array(
			'rule' => 'notEmpty',
			'message' => 'Pole Oblast musí být neprázdné'
		)
	);
}
?>