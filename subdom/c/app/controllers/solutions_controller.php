<?php 
class SolutionsController extends AppController {
	var $name = 'Solutions';
	
	var $left_menu_list = array('impositions');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'impositions');
		
		$this->Auth->allow('repair');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_solve($id = null) {
		$result = array('success' => false, 'message' => null);

		if (!$id) {
			$result['message'] = 'Není uveden úkol, který chcete označit jako vyřešený';
		} else {
			$solution = array(
				'Solution' => array(
					'id' => $id,
					'solution_state_id' => 3
				)
			);
			if ($this->Solution->save($solution)) {
				$result['message'] = 'Úkol byl označen jako vyřešený';
				$result['success'] = true;
				// zjistim si retezec pro oznaceni vyreseneho ukolu (id = 3)
				$state_name = $this->Solution->SolutionState->find('first', array(
					'conditions' => array('id' => 3),
					'contain' => array(),
					'fields' => array('name')
				));
				$result['state_name'] = $state_name['SolutionState']['name'];
			} else {
				$result['message'] = 'Úkol se nepodařilo označit jako vyřešený, opakujte prosím akci';
			}
		}
		
		echo json_encode($result);
		die();
	}
	
	function user_delete($id = null) {
		$back_link = array('controller' => 'impositions', 'action' => 'index');
		if (isset($this->params['named']['back_link'])) {
			$back_link = unserialize(base64_decode($this->params['named']['back_link']));
		}
		
		if (!$id) {
			$this->Session->setFlash('Není uveden úkol, který chcete smazat');
			$this->redirect($back_link);
		}
		
		$solution = $this->Solution->find('first', array(
			'conditions' => array('Solution.id' => $id),
			'contain' => array(
				'Imposition' => array(
					'Document',
					'Solution'
				)
			)
		));
		
		$dataSource = $this->Solution->getDataSource();
		$dataSource->begin($this->Solution);
		try {
			$this->Solution->delete($id);
			if (count($solution['Imposition']['Solution']) == 1) {
				foreach ($solution['Imposition']['Document'] as $document) {
					if (file_exists('files/documents/' . $document['name'])) {
						unlink('files/documents/' . $document['name']);
					}
					$this->Solution->Imposition->Document->delete($document['id']);
				}
				$this->Solution->Imposition->delete($solution['Imposition']['id']);
			}
		} catch (Exception $e) {
			$dataSource->rollback($this->Solution);
			$this->Session->setFlash('Odstranění požadavku se nezdařilo, opakujte prosím akic');
			$this->redirect($back_link);
		}
		$dataSource->commit($this->Solution);
		$this->Session->setFlash('Požadavek byl odstraněn');
		$this->redirect($back_link);
	}
	
	function user_edit($id = null) {
		$back_link = array('controller' => 'impositions', 'action' => 'index');
		if (isset($this->params['named']['back_link'])) {
			$back_link = unserialize(base64_decode($this->params['named']['back_link']));
		} elseif (isset($this->data['Solution']['back_link'])) {
			$back_link = unserialize(base64_decode($this->data['Solution']['back_link']));
		}

		if (!$id) {
			$this->Session->setFlash('Není uveden požadavek, který chcete upravit');
			$this->redirect($back_link);
		}
		
		$solution = $this->Solution->find('first', array(
			'conditions' => array('Solution.id' => $id),
			'contain' => array()
		));
		
		if (empty($solution)) {
			$this->Session->setFlash('Požadavek neexistuje');
			$this->redirect($back_link);
		}
		
		$this->set('solution', $solution);
		$this->set('back_link', $back_link);
		
		$imposition = $this->Solution->Imposition->find('first', array(
			'conditions' => array('Imposition.id' => $solution['Solution']['imposition_id']),
			'contain' => array(),
			'fields' => array('id')
		));
		$this->set('imposition', $imposition);
		$this->left_menu_list[] = 'imposition_detailed';
		
		$solution_states = $this->Solution->SolutionState->find('list');
		$this->set('solution_states', $solution_states);
		
		$this->set('monthNames', $this->monthNames);
		
		if (isset($this->data)) {
			$this->data['Solution']['accomplishment_date'] = cal2db_date($this->data['Solution']['accomplishment_date']);
			if ($this->Solution->save($this->data)) {
				$this->Session->setFlash('Požadavek byl upraven');
				$this->redirect($back_link);
			} else {
				$this->Session->setFlash('Požadavek se nepodařilo upravit, opakujte prosím akci');
			}
		} else {
			$this->data = $solution;
			$this->data['Solution']['accomplishment_date'] = db2cal_date($this->data['Solution']['accomplishment_date']);
		}
	}
	
	function repair() {
		$solutions = $this->Solution->find('all', array(
			'conditions' => array(
				'created' => '2012-07-03 06:32:45'
			),
			'contain' => array()
		));
		
		foreach ($solutions as $solution) {
			$this->Solution->delete($solution['Solution']['id']);
		}
		
		die('here');
	}
}
?>