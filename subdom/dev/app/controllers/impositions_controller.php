<?php
class ImpositionsController extends AppController {
	var $name = 'Impositions';
	
	var $index_link = array('controller' => 'impositions', 'action' => 'index');
	
	var $left_menu_list = array('impositions');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'impositions');
		$this->Auth->allowedActions = array('generate', 'notify');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function assigned_impositions() {
		$user_id = $this->user['User']['id'];
		
		$impositions_conditions = array('Imposition.user_id' => $user_id);
		
		$this->paginate['Imposition'] = array(
			'conditions' => $impositions_conditions,
			'contain' => array(
				'ImpositionState',
				'BusinessPartner',
				'User'
			),
			'order' => array('Imposition.accomplishment_date' => 'desc'),
			'limit' => 5
		);
		$impositions = $this->paginate('Imposition');

		return $impositions;
	}
	
	function user_index() {
		$date = date('Y-m-d');
		// chci vybrat ukoly pro nasledujicich 6 dni
		$date_to = date('Y-m-d', strtotime('+6 days'));

		$this->set('date', $date);
		$this->set('imposition_states', $this->Imposition->ImpositionState->find('list'));
		
		$user_id = $this->user['User']['id'];
		
		$page = 1;
		if (isset($this->passedArgs['page'])) {
			$page = $this->passedArgs['page'];
		}
		
		$users = $this->Imposition->User->find('all', array(
			'contain' => array(),
			'fields' => array('id', 'first_name', 'last_name')
		));
		
		$users = Set::combine($users, '{n}.User.id', array('{0} {1}', '{n}.User.last_name', '{n}.User.first_name'));
		$this->set('users', $users);
		$this->set('impositions_users', $users);
		
		$solution_states = $this->Imposition->Solution->SolutionState->find('list', array(
			'contain' => array()
		));
		$solution_states = array(0 => 'Všechny') + $solution_states;
		$this->set('solution_states', $solution_states);
		
		// ukoly, ktere jsem ja zadal
		$solutions_conditions = array('Imposition.user_id' => $user_id);
		// ukoly, ktere mne byly zadany
		$solutions_to_solve_conditions = array('ImpositionsUser.user_id' => $user_id);
		// vsechny ukoly
		$all_solutions_conditions = array();
	
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'impositions') {
			$this->Session->delete('Search.ImpositionForm');
			$this->redirect(array('controller' => 'impositions', 'action' => 'index'));
		}
		
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['ImpositionForm']['Imposition']['search_form']) && $this->data['ImpositionForm']['Imposition']['search_form'] == 1 ){
			$this->Session->write('Search.ImpositionForm', $this->data['ImpositionForm']);
			$conditions = $this->Imposition->do_form_search($this->data['ImpositionForm']);
		} elseif ($this->Session->check('Search.ImpositionForm')) {
			$this->data['ImpositionForm'] = $this->Session->read('Search.ImpositionForm');
			$conditions = $this->Imposition->do_form_search($this->data['ImpositionForm']);
		} else {
			$conditions = array(
				'Solution.accomplishment_date <=' => $date_to,
				'Solution.solution_state_id' => 2
			);
		}

		$solutions_conditions = array_merge($conditions, $solutions_conditions);
		$solutions_to_solve_conditions = array_merge($solutions_to_solve_conditions, $conditions);
		$all_solutions_conditions = array_merge($conditions, $all_solutions_conditions);

		$export_fields = array(
			array('field' => 'Imposition.id', 'position' => '["Imposition"]["id"]', 'alias' => 'Imposition.id'),
			array('field' => 'Solution.accomplishment_date', 'position' => '["Solution"]["accomplishment_date"]', 'alias' => 'Solution.accomplishment_date'),
			array('field' => 'SolutionState.name', 'position' => '["SolutionState"]["name"]', 'alias' => 'SolutionState.name'),
			array('field' => 'Imposition.description', 'position' => '["Imposition"]["description"]', 'alias' => 'Imposition.description'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'CONCAT(User.first_name, " ", User.last_name) AS fullname', 'position' => '[0]["fullname"]', 'alias' => 'User.fullname'),
		);
		$this->set('export_fields', $export_fields);

		// ukoly, ktere jsem ja zadal (pro dany den a dalsi vyhledavaci kriteria)
		// chci je seradit, nejdrive neresene (muzu v pohledu zvyraznit), pak uz vyresene a podle data
		
		$joins = array(
			array(
				'table' => 'impositions',
				'alias' => 'Imposition',
				'type' => 'INNER',
				'conditions' => array('Solution.imposition_id = Imposition.id')
			),
			array(
				'table' => 'business_partners',
				'alias' => 'BusinessPartner',
				'type' => 'INNER',
				'conditions' => array('Imposition.business_partner_id = BusinessPartner.id')
			),
			array(
				'table' => 'users',
				'alias' => 'User',
				'type' => 'INNER',
				'conditions' => array('Imposition.user_id = User.id')
			),
			array(
				'table' => 'impositions_users',
				'alias' => 'ImpositionsUser',
				'type' => 'INNER',
				'conditions' => array('ImpositionsUser.imposition_id = Imposition.id')
			),
			array(
				'table' => 'solution_states',
				'alias' => 'SolutionState',
				'type' => 'INNER',
				'conditions' => array('Solution.solution_state_id = SolutionState.id')
			),
			array(
				'table' => 'recursive_impositions',
				'alias' => 'RecursiveImposition',
				'type' => 'LEFT',
				'conditions' => array('RecursiveImposition.imposition_id = Imposition.id')
			)
		);
		
		$fields = array(
			'DISTINCT Imposition.id, Solution.accomplishment_date',
			'Imposition.id', 'Imposition.description', 'Imposition.title',
			'Solution.id', 'Solution.accomplishment_date', 'Solution.solution_state_id',
			'BusinessPartner.id', 'BusinessPartner.name',
			'User.id', 'User.first_name', 'User.last_name',
			'SolutionState.name',
			'RecursiveImposition.id',
		);
		
		$order = array(
			'Solution.solution_state_id' => 'ASC',
			'Solution.accomplishment_date' => 'ASC'
		);
		
		$assigned_solutions_find = array(
			'conditions' => $solutions_conditions,
			'contain' => array(),
			'joins' => $joins,
			'fields' => $fields,
			'order' => $order
		);

		$assigned_solutions = $this->Imposition->Solution->find('all', $assigned_solutions_find);

		foreach ($assigned_solutions as $index => $assigned_solution) {
			$assigned_solutions[$index]['impositions_users'] = $this->Imposition->get_impositions_users($assigned_solution['Imposition']['id']);
		}

		$this->set('assigned_solutions', $assigned_solutions);
		$this->set('assigned_solutions_find', $assigned_solutions_find);
		
		// ukoly, ktere mam vyresit
		// chci je seradit, nejdrive neresene (muzu v pohledu zvyraznit), pak uz vyresene a podle data
		$solutions_to_solve_find = array(
			'conditions' => $solutions_to_solve_conditions,
			'contain' => array(),
			'joins' => $joins,
			'fields' => $fields,
			'order' => $order
		);
		
		$solutions_to_solve = $this->Imposition->Solution->find('all', $solutions_to_solve_find);

		foreach ($solutions_to_solve as $index => $solution_to_solve) {
			$solutions_to_solve[$index]['impositions_users'] = $this->Imposition->get_impositions_users($solution_to_solve['Imposition']['id']);
		}
		
		$this->set('solutions_to_solve', $solutions_to_solve);
		$this->set('solutions_to_solve_find', $solutions_to_solve_find);
		
		if ($this->user['User']['user_type_id'] == 1) {
			// vsechny ukoly pro tento den
			// chci je seradit, nejdrive neresene (muzu v pohledu zvyraznit), pak uz vyresene a podle data

			$all_solutions_find = array(
				'conditions' => $all_solutions_conditions,
				'contain' => array(),
				'joins' => $joins,
				'fields' => $fields,
				'order' => $order
			);
			
			$all_solutions = $this->Imposition->Solution->find('all', $all_solutions_find);
			
			foreach ($all_solutions as $index => $all_solution) {
				$all_solutions[$index]['impositions_users'] = $this->Imposition->get_impositions_users($all_solution['Imposition']['id']);
			}
			
			$this->set('all_solutions', $all_solutions);
			$this->set('all_solutions_find', $all_solutions_find);
		}
		
	}
	
	function user_view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zvolen úkol, který chcete zobrazit');
			$this->redirect($this->index_link);
		}
		
		$documents_conditions = array();
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'documents') {
			$this->Session->delete('Search.DocumentForm');
			$this->redirect(array('controller' => 'impositions', 'action' => 'view', $id));
		}
		
		if ( isset($this->data['DocumentForm']['Document']['search_form']) && $this->data['DocumentForm']['Document']['search_form'] == 1 ){
			$this->Session->write('Search.DocumentForm', $this->data['DocumentForm']);
			$documents_conditions = $this->Imposition->Document->do_form_search($documents_conditions, $this->data['DocumentForm']);
		} elseif ($this->Session->check('Search.DocumentForm')) {
			$this->data['DocumentForm'] = $this->Session->read('Search.DocumentForm');
			$documents_conditions = $this->Imposition->Document->do_form_search($documents_conditions, $this->data['DocumentForm']);
		}
		
		$imposition = $this->Imposition->find('first', array(
			'conditions' => array('Imposition.id' => $id),
			'contain' => array(
				'BusinessPartner',
				'User',
				'ImpositionsUser' => array(
					'User'
				),
				'ImpositionState',
				'Document' => array(
					'conditions' => $documents_conditions
				),
				'RecursiveImposition' => array(
					'ImpositionPeriod'
				),
				'Solution' => array(
					'SolutionState',
					'order' => array(
						'Solution.solution_state_id' => 'asc',
						'Solution.accomplishment_date' => 'desc'
					)
				)
			)
		));
		
		if (empty($imposition)) {
			$this->Session->setFlash('Požadovaný úkol neexistuje');
			$this->redirect($this->index_link);
		}
		
		$imposition['Imposition']['description'] = str_replace("\n", '<br/>', $imposition['Imposition']['description']);
		
		$impositions_user = $this->Imposition->ImpositionsUser->find('first', array(
			'conditions' => array(
				'ImpositionsUser.imposition_id' => $id,
				'ImpositionsUser.user_id' => $this->user['User']['id']
			),
			'contain' => array()
		));

		// detail se zobrazi tomu, kdo ukol zadal a tomu, kdo je oznaceny jako resitel
		if (!$this->Imposition->checkUser($this->user, $imposition['Imposition']['user_id']) && empty($impositions_user)) {
			$this->Session->setFlash('Neoprávněný přístup. Nemáte právo zobrazit tento úkol.');
			$this->redirect($this->index_link);
		}
		
		$this->set('imposition', $imposition);
		$this->left_menu_list[] = 'imposition_detailed';
	}
	
	function user_add() {
		$user_id = $this->user['User']['id'];
		$this->set('user_id', $user_id);
		
		$users = $this->Imposition->ImpositionsUser->User->find('all', array(
			'fields' => array('User.id', 'CONCAT(User.last_name, " ", User.first_name) as full_name'),
			'order' => array('full_name' => 'asc'),
			'contain' => array()
		));
		$users = Set::combine($users, '{n}.User.id', '{n}.0.full_name');
		$this->set('users', $users);
		
/* 		$conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$conditions = array('BusinessPartner.user_id' => $user_id);
		}
		
		$business_partners = $this->Imposition->BusinessPartner->find('all', array(
			'conditions' => $conditions,
			'order' => array('name' => 'asc'),
			'contain' => array(
				'Address' => array(
					'conditions' => array('Address.address_type_id' => 1)
				)
			)
		));
		
		$autocomplete_business_partners = array();
		foreach ($business_partners as $business_partner) {
			$autocomplete_business_partners[] = array(
				'label' => $business_partner['BusinessPartner']['name'] . ', ' . $business_partner['Address'][0]['street'] . ' ' . $business_partner['Address'][0]['number'] . ', ' . $business_partner['Address'][0]['city'] . ', ' . $business_partner['Address'][0]['zip'],
				'value' => $business_partner['BusinessPartner']['id']
			);
		} */
		$autocomplete_business_partners = $this->Imposition->BusinessPartner->autocomplete_list($this->user);
		$this->set('business_partners', $autocomplete_business_partners);
		
		$period_options = $this->Imposition->RecursiveImposition->ImpositionPeriod->find('list');
		$this->set('period_options', $period_options);
		
		if (isset($this->data)) {
			$data_backup = $this->data;
			if (empty($this->data['Imposition']['business_partner_id']) && !empty($this->data['Imposition']['business_partner_id_old'])) {
				$this->data['Imposition']['business_partner_id'] = $this->data['Imposition']['business_partner_id_old'];
			}
			$impositions_users = $this->data['ImpositionsUser'];

			$tmp = array();
			if (is_array($this->data['ImpositionsUser']['user_id'])) {
				foreach ($this->data['ImpositionsUser']['user_id'] as $impositions_user) {
					$tmp[] = array('user_id' => $impositions_user);
				}
			}

			if (empty($tmp)) {
				$this->data = $data_backup;
				$this->Session->setFlash('Úkol nemá zadaného řešitele, vyberte prosím ze seznamu a znovu odešlete úkol');
			} else {
				$this->data['ImpositionsUser'] = $tmp;
				
				// pokud je zaskrtnute, ze chci rekurzivni ukol, pracuju dal s od, do, period
				// jinak beru termin dokonceni a vytvarim hned pozadavek na reseni
				if (!$this->data['Imposition']['recursive']) {
					unset($this->data['RecursiveImposition']);
					$this->data['Solution']['accomplishment_date'] = cal2db_date($this->data['Solution']['accomplishment_date']);
					$solution = $this->data['Solution'];
					$solution['solution_state_id'] = 2;
					unset($this->data['Solution']);
					$this->data['Solution'][0] = $solution;
				} else {
					// vygeneruju podle zadanych hodnot solutions
					$this->data['RecursiveImposition']['from'] = cal2db_date($this->data['RecursiveImposition']['from']);
					$this->data['RecursiveImposition']['to'] = cal2db_date($this->data['RecursiveImposition']['to']);
				
					$from = mktime(0, 0, 0, $this->data['RecursiveImposition']['from']['month'],$this->data['RecursiveImposition']['from']['day'], $this->data['RecursiveImposition']['from']['year']);
					// pri nekonecne rekurzi se pozadavky generuji max rok dopredu
					$to = strtotime('+1 year', $from);
					if ($this->data['RecursiveImposition']['to_check']) {
						$to = mktime(0, 0, 0, $this->data['RecursiveImposition']['to']['month'],$this->data['RecursiveImposition']['to']['day'], $this->data['RecursiveImposition']['to']['year']);
					} else {
						$this->data['RecursiveImposition']['to'] = null;
					}
	
					$period = $this->Imposition->RecursiveImposition->ImpositionPeriod->find('first', array(
						'conditions' => array('ImpositionPeriod.id' => $this->data['RecursiveImposition']['imposition_period_id']),
						'contain' => array()
					));
					$period = $period['ImpositionPeriod']['interval'];
					unset($this->data['Solution']);
	
					// pozadavky se generuji maximalne rok dopredu
					$from_plus_year = strtotime('+1 year', $from);
					if ($to > $from_plus_year) {
						$to = $from_plus_year;
					}
					while ($from <= $to) {
						$this->data['Solution'][] = array(
							'solution_state_id' => 2,
							'accomplishment_date' => date('Y-m-d', $from)
						);
						$from = strtotime($period, $from);
					}
					if (!$this->data['RecursiveImposition']['to']) {
						$this->data['RecursiveImposition']['to'] = NULL;
					}
				}
	
				if ($this->Imposition->saveAll($this->data)) {
					$this->Session->setFlash('Úkol byl uložen');
					$this->redirect(array('controller' => 'impositions', 'action' => 'view', $this->Imposition->id));
				} else {
					$this->data = $data_backup;
					$this->Session->setFlash('Úkol se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
				}
			}
		} else {
			// nastavim si implicitne formularova pole s daty
			$this->data['Solution']['accomplishment_date'] = date('d.m.Y');
			$this->data['RecursiveImposition']['from'] = date('d.m.Y');
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zvolen úkol, který chcete upravit');
			$this->redirect($this->index_link);
		}
		
		$imposition = $this->Imposition->find('first', array(
			'conditions' => array('Imposition.id' => $id),
			'contain' => array(
				'ImpositionsUser' => array(
					'User'
				),
				'BusinessPartner' => array(
					'Address' => array(
						'conditions' => array('Address.address_type_id' => 1)
					)
				),
				'RecursiveImposition',
				'Solution' => array(
					'limit' => 1
				)
			)
		));
		
		if (empty($imposition)) {
			$this->Session->setFlash('Požadovaný úkol neexistuje');
			$this->redirect(array('controller' => 'impositions', 'action' => 'index'));
		}
		
		//upravit ukol muze jen ten, kdo jej zalozil
		if (!$this->Imposition->checkUser($this->user, $imposition['Imposition']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemáte právo upravit tento úkol.');
			$this->redirect(array('controller' => 'impositions', 'action' => 'index'));
		}
		
		$back_link = array('controller' => 'impositions', 'action' => 'view', $id);
		if (isset($this->params['named']['back_link'])) {
			$back_link = unserialize(base64_decode($this->params['named']['back_link']));
		}
		$this->set('back_link', $back_link);
		
		$period_options = $this->Imposition->RecursiveImposition->ImpositionPeriod->find('list');
		$this->set('period_options', $period_options);
		
		$users = $this->Imposition->ImpositionsUser->User->find('all', array(
			'fields' => array('User.id', 'CONCAT(User.last_name, " ", User.first_name) as full_name'),
			'order' => array('full_name' => 'asc'),
			'contain' => array()
		));
		$users = Set::combine($users, '{n}.User.id', '{n}.0.full_name');
		$this->set('users', $users);
		
/* 		$conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$conditions = array('BusinessPartner.user_id' => $user_id);
		}
		
		$business_partners = $this->Imposition->BusinessPartner->find('all', array(
			'conditions' => $conditions,
			'order' => array('name' => 'asc'),
			'contain' => array(
				'Address' => array(
					'conditions' => array('Address.address_type_id' => 1)
				)
			)
		));
		$autocomplete_business_partners = array();
		foreach ($business_partners as $business_partner) {
			$autocomplete_business_partners[] = array(
				'label' => $business_partner['BusinessPartner']['name'] . ', ' . $business_partner['Address'][0]['street'] . ' ' . $business_partner['Address'][0]['number'] . ', ' . $business_partner['Address'][0]['city'] . ', ' . $business_partner['Address'][0]['zip'],
				'value' => $business_partner['BusinessPartner']['id']
			);
		} */
		$autocomplete_business_partners = $this->Imposition->BusinessPartner->autocomplete_list($this->user);
		$this->set('business_partners', $autocomplete_business_partners);
		
		$imposition_states = $this->Imposition->ImpositionState->find('list');
		$this->set('imposition_states', $imposition_states);
		
		$this->set('imposition', $imposition);
		$this->left_menu_list[] = 'imposition_detailed';
		
		if (isset($this->data)) {
			$data_backup = $this->data;
			
			// prevedu data do db podoby
			$this->data['Solution']['accomplishment_date'] = cal2db_date($this->data['Solution']['accomplishment_date']);
			$this->data['RecursiveImposition']['from'] = cal2db_date($this->data['RecursiveImposition']['from']);
			$this->data['RecursiveImposition']['to'] = cal2db_date($this->data['RecursiveImposition']['to']);
 
			if (empty($this->data['Imposition']['business_partner_name'])) {
				$this->data['Imposition']['busines_partner_id'] = '';
				$this->data['Imposition']['business_partner_id_old'] = '';
			}
			if (empty($this->data['Imposition']['business_partner_id']) && !empty($this->data['Imposition']['business_partner_id_old'])) {
				$this->data['Imposition']['business_partner_id'] = $this->data['Imposition']['business_partner_id_old'];
			}
			
			$this->data['Imposition']['notified'] = false;
			
			$impositions_users = $this->data['ImpositionsUser'];
			
			$tmp = array();
			if (is_array($this->data['ImpositionsUser']['user_id'])) {
				foreach ($this->data['ImpositionsUser']['user_id'] as $impositions_user) {
					$tmp[] = array(
						'user_id' => $impositions_user,
						'imposition_id' => $id
					);
				}
			}
			if (empty($tmp)) {
				$this->data = $data_backup;
				$this->Session->setFlash('Úkol nemá zadaného řešitele, vyberte prosím ze seznamu a znovu odešlete úkol');
			} else {
				$this->data['ImpositionsUser'] = $tmp;

				$this->Imposition->begin();
				$caught = false;
				try {
					if (!$this->Imposition->save($this->data)) {
						throw new Exception('Úkol se nepodařilo uložit');
					}
					// synchronizace resitelu z formu do DB
					$in_db_ids = array();
					if (empty($this->data['ImpositionsUser'])) {
						$to_del = $this->Imposition->ImpositionsUser->find('all', array(
							'conditions' => array('ImpositionsUser.imposition_id' => $id),
							'contain' => array(),
							'fields' => array('id')
						));
					} else {
						// ulozim do db ty z multiple selectu, ktery tam nejsou
						foreach ($this->data['ImpositionsUser'] as $save) {
							$db_impositions_user = $this->Imposition->ImpositionsUser->find('first', array(
								'conditions' => $save,
								'contain' => array()
							));
		
							if (empty($db_impositions_user)) {
								$this->Imposition->ImpositionsUser->create();
								$this->Imposition->ImpositionsUser->save($save);
								$in_db_ids[] = $this->Imposition->ImpositionsUser->id;
							} else {
								$in_db_ids[] = $db_impositions_user['ImpositionsUser']['id'];
							}
						}
						// smazu z db ty, ktery nejsou v multiple selectu
						$to_del = $this->Imposition->ImpositionsUser->find('all', array(
							'conditions' => array(
								'ImpositionsUser.imposition_id' => $id,
								'ImpositionsUser.id NOT IN (' . implode(',', $in_db_ids) . ')'
							),
							'contain' => array(),
							'fields' => array('id')
						));
					}
					foreach ($to_del as $item) {
						$this->Imposition->ImpositionsUser->delete($item['ImpositionsUser']['id']);
					}

					// podle informaci o rekurzi musim vygenerovat nove pozadavky na reseni a nadbytecne smazat
					// pokud je zaskrtnute, ze chci rekurzivni ukol, pracuju dal s od, do, period
					// jinak beru termin dokonceni a vytvarim hned pozadavek na reseni
					if (!$this->data['Imposition']['recursive']) {
						$solution['accomplishment_date'] = $this->data['Solution']['accomplishment_date']['year'] . '-' . $this->data['Solution']['accomplishment_date']['month'] . '-' . $this->data['Solution']['accomplishment_date']['day'];
						$solution['imposition_id'] = $this->data['Imposition']['id'];

						// hledam, jestli pro dany den a dany ukol uz v db nemam reseni
						$db_solution = $this->Imposition->Solution->find('first', array(
							'conditions' => $solution,
							'contain' => array(),
							'fields' => array('id')
						));
	
						$not_to_del_solution_id = '';
						// pokud ho tam mam
						if ($db_solution) {
							// oznacim si ho, ze ho nechci smazat
							$not_to_del_solution_id = $db_solution['Solution']['id'];
						} else {
							// pokud ho tam nemam, tak takove vytvorim
							$this->Imposition->Solution->create();
							$solution['solution_state_id'] = 2;
							if (!$this->Imposition->Solution->save($solution)) {
								throw new Exception('Nepodařilo se uložit nový požadavek na řešení při zrušení rekurzivity úkolu');
							}
							// a zapamatuju si ho, abych si ho pak nesmazal
							$not_to_del_solution_id = $this->Imposition->Solution->id;
						}
						if (!$this->Imposition->Solution->deleteAll(array(
							'Solution.id !=' => $not_to_del_solution_id,
							'Solution.imposition_id' => $this->data['Imposition']['id'],
							'Solution.solution_state_id' => 2
						))) {
							throw new Exception('Nepodařilo se odstranit nadbytečné požadavky na řešení při zrušení rekurzivity úkolu');
						}
	
						if ($imposition['RecursiveImposition']['id']) {
							if (!$this->Imposition->RecursiveImposition->delete($imposition['RecursiveImposition']['id'])) {
								throw new Exception('Nepodařilo se odstranit info o rekurzi při zrušení rekurzivity úkolu');
							}
						}
					} else {
						// vygeneruju podle zadanych hodnot solutions
						$from = mktime(0, 0, 0, $this->data['RecursiveImposition']['from']['month'], $this->data['RecursiveImposition']['from']['day'], $this->data['RecursiveImposition']['from']['year']);
						$to = strtotime('+1 year', $from);
						$today = date('Y-m-d');
						if ($this->data['RecursiveImposition']['to_check']) {
							$to = mktime(0, 0, 0, $this->data['RecursiveImposition']['to']['month'], $this->data['RecursiveImposition']['to']['day'], $this->data['RecursiveImposition']['to']['year']);
						} else {
							$this->data['RecursiveImposition']['to'] = null;
						}

						$period = $this->Imposition->RecursiveImposition->ImpositionPeriod->find('first', array(
							'conditions' => array('ImpositionPeriod.id' => $this->data['RecursiveImposition']['imposition_period_id']),
							'contain' => array()
						));
						$period = $period['ImpositionPeriod']['interval'];
						unset($this->data['Solution']);
		
						// pozadavky se generuji maximalne rok dopredu
						$from_plus_year = strtotime('+1 year', $from);
						if ($to > $from_plus_year) {
							$to = $from_plus_year;
						}
						$not_to_del_solution_ids = array();
						while ($from <= $to) {
							$solution = array(
								'accomplishment_date' => date('Y-m-d', $from),
								'imposition_id' => $this->data['Imposition']['id']
							);

							$db_solution = $this->Imposition->Solution->find('first', array(
								'conditions' => $solution,
								'contain' => array(),
								'fields' => array('id')
							));
								
							if ($db_solution) {
								$not_to_del_solution_ids[] = $db_solution['Solution']['id'];
							} else {
								// nevytvarim pozadavky na reseni do minulosti
								if ($solution['accomplishment_date'] >= $today) {
									$this->Imposition->Solution->create();
									$solution['solution_state_id'] = 2;
									if (!$this->Imposition->Solution->save($solution)) {
										throw new Exception('Požadavek na řešení se nepodařilo uložit');
									}
									$not_to_del_solution_ids[] = $this->Imposition->Solution->id;
								}
							}
							$from = strtotime($period, $from);
						}
	
						if (!$this->Imposition->Solution->deleteAll(array(
							'Solution.id NOT IN (' . implode(',', $not_to_del_solution_ids) . ')',
							'Solution.imposition_id' => $this->data['Imposition']['id'],
							'Solution.solution_state_id' => 2
						))) {
							throw new Exception('Nepodařilo se odstranit nadbytečné požadavky na řešení, opakujte prosím akci');
						}
	
						$this->data['RecursiveImposition']['imposition_id'] = $this->data['Imposition']['id'];
						if (!$this->Imposition->RecursiveImposition->save($this->data)) {
							throw new Exception('Nepodařilo se uložit info o rekurzi úkolu');
						}
					}
				} catch (Exception $e) {
					$this->Imposition->rollback();
					$this->data = $data_backup;
					$this->Session->setFlash($e);
					$caught = true;
				}
				if (!$caught) {
					$this->Imposition->commit();
					$this->Session->setFlash('Úkol byl uložen');
					$this->redirect($back_link);
				}
			}
		} else {
			$this->data = $imposition;
			$this->data['Imposition']['business_partner_name'] = $imposition['BusinessPartner']['name'] . ', ' . $imposition['BusinessPartner']['Address'][0]['street'] . ' ' . $imposition['BusinessPartner']['Address'][0]['number'] . ', ' . $imposition['BusinessPartner']['Address'][0]['city'] . ', ' . $imposition['BusinessPartner']['Address'][0]['zip'];
			unset($this->data['ImpositionsUser']);
			foreach ($imposition['ImpositionsUser'] as $user) {
				$this->data['ImpositionsUser']['user_id'][] = $user['User']['id'];
			}
			if ($this->data['RecursiveImposition']['id']) {
				$this->data['RecursiveImposition']['from'] = db2cal_date($this->data['RecursiveImposition']['from']);
				$this->data['RecursiveImposition']['to'] = db2cal_date($this->data['RecursiveImposition']['to']);
				$this->data['Imposition']['recursive'] = true;
				unset($this->data['Solution']);
			} else {
				$this->data['Solution']['id'] = $imposition['Solution'][0]['id'];
				$this->data['Solution']['accomplishment_date'] = db2cal_date($imposition['Solution'][0]['accomplishment_date']);
			}
		}
	}
	
	/**
	 * 
	 * Smaze cely ukol + nevyresene pozadavky (pokud ma ukol vyresene pozadavky, tak se sam nemaze) 
	 * @param int $id - id mazaneho ukolu
	 */
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán úkol, který chcete smazat');
			$this->redirect(array('controller' => 'impositions', 'action' => 'index'));
		}
		
		$imposition = $this->Imposition->find('first', array(
			'conditions' => array('Imposition.id' => $id),
			'contain' => array('Solution', 'Document')
		));
		
		if (empty($imposition)) {
			$this->Session->setFlash('Úkol neexistuje');
			$this->redirect(array('controller' => 'impositions', 'action' => 'index'));
		}

		// projdu pozadavky a budu odmazavat ty, ktere nejsou vyreseny
		// pokud zjistim, ze jsem smazal vsechny pozadavky daneho ukolu, smazu nakonec i ukol samotny
		// s ukolem musim pripadne smazat i dokumenty (z db i z disku)
		$dataSource = $this->Imposition->getDataSource();
		$dataSource->begin($this->Imposition);
		try {
			$has_solved = false;
			foreach ($imposition['Solution'] as $solution) {
				if ($solution['solution_state_id'] == 3) {
					$has_solved = true;
				} else {
					$this->Imposition->Solution->delete($solution['id']);
				}
			}

			if (!$has_solved) {
				foreach ($imposition['Document'] as $document) {
					if (file_exists('files/documents/' . $document['name'])) {
						unlink('files/documents/' . $document['name']);
					}
					$this->Imposition->Document->delete($document['id']);
				}
				$this->Imposition->delete($imposition['Imposition']['id']);
			}
		} catch (Exception $e) {
			$dataSource->rollback($this->Imposition);
			$this->Session->setFlash('Odstranění úkolu se nezdařilo, opakujte prosím akic');
			$this->redirect(array('controller' => 'impositions', 'action' => 'index'));
		}
		$dataSource->commit($this->Imposition);
		$this->Session->setFlash('Úkol byl odstraněn');
		$this->redirect(array('controller' => 'impositions', 'action' => 'index'));
	}
	
	/**
	 * 
	 * Vyhleda neuzavrene ukoly, ktere jsou 3 a min dnu pred terminem ukonceni
	 * a posle jejich zadavatelum email
	 */
	function user_ending() {
		$today = date('Y-m-d');
		$end_day = date('Y-m-d', strtotime('+3 days'));

		$impositions = $this->Imposition->find('all', array(
			'conditions' => array(
				'Imposition.imposition_state_id' => array(1, 2),
				'Imposition.accomplishment_date >=' => $today,
				'Imposition.accomplishment_date <=' => $end_day,
			),
			'contain' => array(
				'User',
				'ImpositionsUser' => array(
					'User'
				)
			)
		));
		
		foreach ($impositions as $imposition) {
			$this->Imposition->notifyEnding($imposition);
		}
		
		$this->Session->setFlash('Upozornění na neuzavřené úkoly, u kterých se blíží termín dokončení, byla odeslána.');
		$this->redirect(array('controller' => 'impositions', 'action' => 'index'));
	}
	
	/**
	 * 
	 * Skript pro volani CRONem
	 * vyhleda ukoly, kterym maji byt vygenerovany nove pozadavky na reseni (za rok)
	 * a vygeneruje je
	 */
	function generate() {
		// zjistim si datum za rok a jeden den, pro ktere budu hledat ukoly, ktere maji mit zadany pozadavky
		$next_year = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y') + 1);
		// vytahnu si vsechny rekurzivni ukoly, ktere maji bud nekonecnou rekurzi, nebo konec rekurze pozdeji, nez za rok
		$impositions = $this->Imposition->find('all', array(
			'conditions' => array(
				'OR' => array(
					'RecursiveImposition.to >' => date('Y-m-d', $next_year),
					'AND' => array(
						'RecursiveImposition.to IS NULL',
						'RecursiveImposition.id IS NOT NULL'
					)
				)
			),
			'contain' => array(
				'RecursiveImposition' => array(
					'ImpositionPeriod'
				),
				'Solution' => array(
					'order' => array('Solution.accomplishment_date' => 'DESC'),
					'limit' => 1
				)
			)
		));

		// projdu zjistene ukoly a musim rozhodnout, jestli maji mit za rok vytvoreny pozadavek na rekurzi
		foreach ($impositions as $imposition) {
			// mam pro dany den vygenerovat pozadavek na reseni?
			// z posledniho pozadavky udelam from - pocatek rekurze
			$from = explode('-', $imposition['Solution'][0]['accomplishment_date']);
			// vygeneruju podle zadanych hodnot solutions
			$from = mktime(0, 0, 0, $from[1], $from[2], $from[0]);
			// za konec oznacim zjistene datum "za rok"
			$to = $next_year;

			$period = $imposition['RecursiveImposition']['ImpositionPeriod']['interval'];

			$from = strtotime($period, $from);
			$solution = array();
			while ($from <= $to) {
				// zjistim mozne pozadavky, ktere by mely byt vygenerovany od posledniho zadaneho pozadavku do data "za rok" s danou periodou
				$solution['Solution'] = array(
					'solution_state_id' => 2,
					'accomplishment_date' => date('Y-m-d', $from),
					'imposition_id' => $imposition['Imposition']['id']
				);
				$from = strtotime($period, $from);

				// kdyby se nahodou pustil skript vickrat za den, budu kontrolovat, jestli uz pro dany ukol a den neni v db pozadavek
				// pokud ano, tak uz ho znovu neukladam
				$db_solution = $this->Imposition->Solution->find('first', array(
					'conditions' => array(
						'Solution.imposition_id' => $imposition['Imposition']['id'],
						'Solution.accomplishment_date' => $solution['Solution']['accomplishment_date']
					),
					'contain' => array()
				));
				
				if (empty($db_solution)) {
					$this->Imposition->Solution->create();
					if (!$this->Imposition->Solution->save($solution)) {
						echo 'Požadavek pro úkol ' . $imposition['Imposition']['id'] . ' a datum ' . $solution['Solution']['accomplishment_date'] . ' se nepodařilo uložit';
					}
				}
			}
		}
		die('hotovo');
	}
	
	function notify() {
		// pro kazdeho uzivatele vytahnu ukoly, ktere nemaji potvrzenou notifikaci (notified = false)
		$users = $this->Imposition->User->find('all', array(
			'contain' => array()
		));
		foreach ($users as $index => $user) {
			$users[$index]['impositions'] = $this->Imposition->ImpositionsUser->find('all', array(
				'conditions' => array(
					'ImpositionsUser.user_id' => $user['User']['id'],
					'Imposition.notified' => false
				),
				'contain' => array(
					'Imposition' => array(
						'BusinessPartner' => array(
							'fields' => array('id', 'name')
						),
						'User' => array(
							'fields' => array('first_name', 'last_name')
						)
					)
				)
			));
		}

		// sestavim emailove upozorneni na tyto ukoly a odeslu kazdemu uzivateli
		foreach ($users as $user) {
			if (!empty($user['impositions'])) {
				if ($this->Imposition->notifyNew($user)) {
					foreach ($user['impositions'] as $imposition) {
						$imposition['Imposition']['notified'] = true;
						$this->Imposition->save($imposition);
					}
				}
			}
		}
		echo "Notifikace byly odeslany\n";
		die('konec');
	}
	
	function user_notify() {
		$back_link = '/user/impositions';
		if (isset($this->params['named']['back_link'])) {
			$back_link = '/' . base64_decode($this->params['named']['back_link']);
		}
		// pro kazdeho uzivatele vytahnu ukoly, ktere nemaji potvrzenou notifikaci (notified = false)
		$users = $this->Imposition->User->find('all', array(
			'contain' => array()
		));
		foreach ($users as $index => $user) {
			$users[$index]['impositions'] = $this->Imposition->ImpositionsUser->find('all', array(
				'conditions' => array(
					'ImpositionsUser.user_id' => $user['User']['id'],
					'Imposition.notified' => false
				),
				'contain' => array(
					'Imposition' => array(
						'BusinessPartner' => array(
							'fields' => array('id', 'name')
						),
						'User' => array(
							'fields' => array('first_name', 'last_name')
						)
					)
				)
			));
		}

		// sestavim emailove upozorneni na tyto ukoly a odeslu kazdemu uzivateli
		foreach ($users as $user) {
			if (!empty($user['impositions'])) {
				if ($this->Imposition->notifyNew($user)) {
					foreach ($user['impositions'] as $imposition) {
						$imposition['Imposition']['notified'] = true;
						$this->Imposition->save($imposition);
					}
				}
			}
		}
		$this->Session->setFlash('Notifikace byly odeslány');
		$this->redirect($back_link);
	}
}