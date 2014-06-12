<?php 
App::import('Model', 'User');
class Rep extends User {
	var $name = 'Rep';
	
	var $useTable = 'users';
	
	var $hasOne = array(
		'RepAttribute' => array(
			'foreignKey' => 'rep_id'
		)
	);
	
	var $name_field = 'CONCAT(Rep.first_name, " ", Rep.last_name)';
	
	var $conditions = array('Rep.user_type_id' => 4);
	
	// nastavim si do kazdeho vyhledavani, ze chci JEN REPY (user_type_id = 4)
	function beforeFind($query_data) {
		$query_data['conditions'] += $this->conditions;
		return $query_data;
	}
	
	function export_fields() {
		return array(
			array('field' => 'Rep.id', 'position' => '["Rep"]["id"]', 'alias' => 'Rep.id'),
			array('field' => 'Rep.first_name', 'position' => '["Rep"]["first_name"]', 'alias' => 'Rep.first_name'),
			array('field' => 'Rep.last_name', 'position' => '["Rep"]["last_name"]', 'alias' => 'Rep.last_name'),
			array('field' => 'Rep.phone', 'position' => '["Rep"]["phone"]', 'alias' => 'Rep.phone'),
			array('field' => 'Rep.email', 'position' => '["Rep"]["email"]', 'alias' => 'Rep.email'),
			array('field' => 'Rep.login', 'position' => '["Rep"]["login"]', 'alias' => 'Rep.login'),
			array('field' => 'Rep.wallet', 'position' => '["Rep"]["wallet"]', 'alias' => 'Rep.wallet')
		);
	}
	
	function delete($id) {
		$rep = array(
			'Rep' => array(
				'id' => $id,
				'active' => false
			)	
		);
		
		return $this->save($rep);
	}
	
	function autocomplete_list($user, $term = null) {
		$conditions = array();

		if ($term) {
			$conditions['Rep.name LIKE'] = '%' . $term . '%';
		}
		
		$this->virtualFields['name'] = $this->name_field;
		$reps = $this->find('all', array(
			'conditions' => $conditions,
			'order' => array('name' => 'asc'),
			'contain' => array(),
			'fields' => array('Rep.name', 'Rep.id')
		));
		unset($this->virtualFields['name']);

		$autocomplete_reps = array();
		foreach ($reps as $rep) {
			$autocomplete_reps[] = array(
					'label' => $rep['Rep']['name'],
					'value' => $rep['Rep']['id']
			);
		}
		return json_encode($autocomplete_reps);
	}
}
?>