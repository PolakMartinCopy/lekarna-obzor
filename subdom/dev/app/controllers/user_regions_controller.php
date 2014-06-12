<?php
class UserRegionsController extends AppController {
	var $name = 'UserRegions';
	
	var $index_link = array('controller' => 'user_regions', 'action' => 'index');
	
	var $left_menu_list = array('user_regions');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'user_regions');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		$user_id = $this->user['User']['id'];
		
		$conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$conditions = array('UserRegion.user_id' => $user_id);
		}
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'user_regions') {
			$this->Session->delete('Search.UserRegionForm');
			$this->redirect(array('controller' => 'user_regions', 'action' => 'index'));
		}
		
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['UserRegionForm']['UserRegion']['search_form']) && $this->data['UserRegionForm']['UserRegion']['search_form'] == 1 ){
			$this->Session->write('Search.UserRegionForm', $this->data['UserRegionForm']);
			$conditions = $this->UserRegion->do_form_search($conditions, $this->data['UserRegionForm']);
		} elseif ($this->Session->check('Search.UserRegionForm')) {
			$this->data['UserRegionForm'] = $this->Session->read('Search.UserRegionForm');
			$conditions = $this->UserRegion->do_form_search($conditions, $this->data['UserRegionForm']);
		}
		
		$find = array(
			'conditions' => $conditions,
			'contain' => array('User')
		);
		
		$regions = $this->UserRegion->find('all', $find);
		$this->set('regions', $regions);
		
		$this->set('find', $find);
		
		$export_fields = array(
			array('field' => 'UserRegion.id', 'position' => '["UserRegion"]["id"]', 'alias' => 'UserRegion.id'),
			array('field' => 'UserRegion.name', 'position' => '["UserRegion"]["name"]', 'alias' => 'UserRegion.name'),
			array('field' => 'UserRegion.zip', 'position' => '["UserRegion"]["zip"]', 'alias' => 'UserRegion.zip'),
			array('field' => 'CONCAT(User.first_name, " ", User.last_name) AS fullname', 'position' => '[0]["fullname"]', 'alias' => 'User.fullname'),
		);
		$this->set('export_fields', $export_fields);
	}
	
	function user_add() {
		$users = $this->UserRegion->User->find('all', array(
			'fields' => array('User.id', 'CONCAT(User.last_name, " ", User.first_name) as full_name'),
			'order' => array('full_name' => 'asc'),
			'contain' => array()
		));
		
		$autocomplete_users = array();
		foreach ($users as $key => $user) {
			$autocomplete_users[] = array('label' => $user[0]['full_name'], 'value' => $user['User']['id']);
		}

		$this->set('users', json_encode($autocomplete_users));
		
		if (isset($this->data)) {
			if (empty($this->data['UserRegion']['user_id']) && !empty($this->data['UserRegion']['user_id_old'])) {
				$this->data['UserRegion']['user_id'] = $this->data['UserRegion']['user_id_old'];
			}

			if ($this->UserRegion->save($this->data)) {
				$this->Session->setFlash('Oblast byla přidělena zvolenému uživateli');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Oblast se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zvolena oblast, kterou chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$region = $this->UserRegion->find('first', array(
			'conditions' => array('UserRegion.id' => $id),
			'contain' => array(
				'User' => array(
					'fields' => array('User.id', 'CONCAT(User.last_name, " ", User.first_name) as full_name'),
				)
			)
		));
		
		if (empty($region)) {
			$this->Session->setFlash('Zvolená oblast neexistuje');
			$this->redirect($this->index_link);
		}
		
		$users = $this->UserRegion->User->find('all', array(
			'fields' => array('User.id', 'CONCAT(User.last_name, " ", User.first_name) as full_name'),
			'order' => array('full_name' => 'asc'),
			'contain' => array()
		));
		$autocomplete_users = array();
		foreach ($users as $key => $user) {
			$autocomplete_users[] = array('label' => $user[0]['full_name'], 'value' => $user['User']['id']);
		}

		$this->set('users', json_encode($autocomplete_users));
		
		if (isset($this->data)) {
			if (empty($this->data['UserRegion']['user_id']) && !empty($this->data['UserRegion']['user_id_old'])) {
				$this->data['UserRegion']['user_id'] = $this->data['UserRegion']['user_id_old'];
			}
			if ($this->UserRegion->save($this->data)) {
				$this->Session->setFlash('Oblast byla upravena');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Oblast se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		} else {
			$this->data = $region;
			$this->data['UserRegion']['user_name'] = $region[0]['full_name'];
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zvolena oblast, kterou chcete smazat');
			$this->redirect($this->index_link);
		}
		
		$region = $this->UserRegion->find('first', array(
			'conditions' => array('UserRegion.id' => $id),
			'contain' => array()
		));
		
		if (empty($region)) {
			$this->Session->setFlash('Zvolená oblast neexistuje');
			$this->redirect($this->index_link);
		}
		
		if ($this->UserRegion->delete($id)) {
			$this->Session->setFlash('Oblast byla odstraněna');
		} else {
			$this->Session->setFlash('Oblast se nepodailo odstranit, opakujte prosím akci');
		}
		$this->redirect($this->index_link);
	}
}