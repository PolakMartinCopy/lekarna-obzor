<?php
class Anniversary extends AppModel {
	var $name = 'Anniversary';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('AnniversaryType', 'AnniversaryAction', 'ContactPerson');
	
	var $validate = array(
		'date' => array(
			'notEmpty' => array(
				'rule' => 'date',
				'message' => 'Pole Datum musí obsahovat validní údaje o datu',
				'allowEmpty' => false
			)
		),
		'anniversary_type_id' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'allowEmpty' => false,
				'message' => 'Typ výročí musí být vybrán'
			)
		),
		'anniversary_action_id' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'allowEmpty' => false,
				'message' => 'Musí být zvolena akce, která se má provést'
			)
		)
	);
	
	function do_form_search($conditions, $data){
		if ( !empty($data['Anniversary']['anniversary_type_id']) ){
			$conditions[] = 'Anniversary.anniversary_type_id = `' . $data['Anniversary']['anniversary_type_id'] . '`';
		}
		
		if ( !empty($data['Anniversary']['date_from']) ){
			$date_from = explode('.', $data['Anniversary']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions[] = 'STR_TO_DATE( CONCAT_WS( \',\', Month( Anniversary.date ) , Day( Anniversary.date ) , Year( CURDATE( ) ) ) , \'%m,%d,%Y\' ) >= \'' . $date_from . '\'';
		}
		
		if ( !empty($data['Anniversary']['date_to']) ){
			$date_to = explode('.', $data['Anniversary']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions[] = 'STR_TO_DATE( CONCAT_WS( \',\', Month( Anniversary.date ) , Day( Anniversary.date ) , Year( CURDATE( ) ) ) , \'%m,%d,%Y\' ) <= \'' . $date_to . '\'';
		}
		
		if ( !empty($data['Anniversary']['anniversary_action_id']) ){
			$conditions[] = 'Anniversary.anniversary_action_id = `' . $data['Anniversary']['anniversary_action_id'] . '`';
		}
		return $conditions;
	}
	
	function _build_query($conditions, $extra) {
		$data = $extra['data'];
		$conditions[] = 'ContactPerson.active = 1';

		if (!empty($data['Anniversary']['anniversary_type_id'])) {
			$conditions[] = 'Anniversary.anniversary_type_id = ' . $data['Anniversary']['anniversary_type_id'];
		}
		if (!empty($data['Anniversary']['anniversary_action_id'])) {
			$conditions[] = 'Anniversary.anniversary_action_id = ' . $data['Anniversary']['anniversary_action_id'];
		}
	
		$conditions = implode(' AND ', $conditions);

		$date_from = explode('.', $data['Anniversary']['date_from']);
		$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
		
		$date_to = explode('.', $data['Anniversary']['date_to']);
		$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
		
		$year_from = explode('-', $date_from);
		$year_from = $year_from[0];
		
		$year_to = explode('-', $date_to);
		$year_to = $year_to[0];
		
		// vyroci po 'od'
		$from_query = '
		SELECT *, STR_TO_DATE( CONCAT_WS( \',\', Month( Anniversary.date ) , Day( Anniversary.date ) , ' . $year_from . ' ) , \'%m,%d,%Y\' ) AS actual_date
		FROM anniversaries AS Anniversary
		WHERE
			STR_TO_DATE( CONCAT_WS( \',\', Month( Anniversary.date ) , Day( Anniversary.date ) , ' . $year_from . ' ) , \'%m,%d,%Y\' ) >= \'' . $date_from . '\'';

		//$from = $this->query($from_query);
		//debug($from); die();
		
		$next_year = $year_from + 1;

		$inside_query = '';
		while ($next_year <= $year_to) {
			$inside_query .= '
		UNION	
			
		SELECT *, STR_TO_DATE( CONCAT_WS( \',\', Month( Anniversary.date ) , Day( Anniversary.date ) , ' . $next_year . ' ) , \'%m,%d,%Y\' ) AS actual_date
		FROM anniversaries AS Anniversary
		WHERE
			STR_TO_DATE( CONCAT_WS( \',\', Month( Anniversary.date ) , Day( Anniversary.date ) , ' . $next_year . ' ) , \'%m,%d,%Y\' ) >= \'' . $date_from . '\'';
			$next_year++;
		}

		// vyroci pred 'od'
		$after_query = '
		SELECT *, STR_TO_DATE( CONCAT_WS( \',\', Month( Anniversary.date ) , Day( Anniversary.date ) , ' . $next_year . ') , \'%m,%d,%Y\' ) AS actual_date
		FROM anniversaries AS Anniversary
		WHERE
			STR_TO_DATE( CONCAT_WS( \',\', Month( Anniversary.date ) , Day( Anniversary.date ) , ' . $year_from . ') , \'%m,%d,%Y\' ) < \'' . $date_from . '\'';
		
		//$after = $this->query($after_query);
		//debug($after); die();
		
		// kazdy rok opakujici se vyroci
		$every_year_query = '
		SELECT Anniversary.*
		FROM (
		' . $from_query . $inside_query . '
		
		UNION
		
		' . $after_query . '
		
		) AS Anniversary 
			INNER JOIN anniversary_types AS AnniversaryType ON Anniversary.anniversary_type_id = AnniversaryType.id
		WHERE
			Anniversary.actual_date < \'' . $date_to . '\' AND
			AnniversaryType.every_year = 1';
		
		$just_one_query = '
		SELECT Anniversary.*
		FROM (
			SELECT *, Anniversary.date as actual_date
			FROM anniversaries AS Anniversary
		) AS Anniversary
			INNER JOIN anniversary_types AS AnniversaryType ON Anniversary.anniversary_type_id = AnniversaryType.id
		WHERE
			Anniversary.actual_date >= \'' . $date_from . '\' AND
			Anniversary.actual_date < \'' . $date_to . '\' AND
			AnniversaryType.every_year = 0
		';

		$union_query = '
		SELECT *
		FROM (
		' . $every_year_query . '
		
		UNION
		
		' . $just_one_query . '
		
		) AS Anniversary
			INNER JOIN contact_people AS ContactPerson ON Anniversary.contact_person_id = ContactPerson.id
			INNER JOIN business_partners AS BusinessPartner ON ContactPerson.business_partner_id = BusinessPartner.id
			INNER JOIN anniversary_types AS AnniversaryType ON Anniversary.anniversary_type_id = AnniversaryType.id
			INNER JOIN anniversary_actions AS AnniversaryAction ON Anniversary.anniversary_action_id = AnniversaryAction.id
		WHERE ' . $conditions . '
		';

		return $union_query;
	}
	
	function paginate($conditions, $fields, $order, $limit = 20, $page = 1, $recursive, $extra) {
		if ($page == 0) {
			$page = 1;
		}
		if (!empty($order)) {
			foreach ($order as $field => $direction) {
				$order = $field . ' ' . $direction;
			}
		} else {
			$order = 'Anniversary.actual_date ASC';
		}
		
		$union_query = $this->_build_query($conditions, $extra);

		$union_query .= '
		ORDER BY ' . $order . '
		LIMIT ' . ($page-1) * $limit . ', ' . $limit;

		
		$union = $this->query($union_query);
		
		return $union;
	}
	
	function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$union_query = $this->_build_query($conditions, $extra);
		$union_query = preg_replace('/\*/', 'count(*)', $union_query, 1);
		$count = $this->query($union_query);
		
		return $count[0][0]['count(*)'];
	}
}