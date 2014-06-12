<?
class RepAreasController extends AppController {
	var $name = 'RepAreas';
	
	function admin_index() {
		$this->layout = 'admin';
		
		$rep_id = $this->params['named']['rep_id'];
		
		$this->RepArea->Rep->id = $rep_id;
		$this->RepArea->Rep->contain();
		$rep = $this->RepArea->Rep->read();
		
		$this->set('rep', $rep);
		
		$rep_areas = $this->RepArea->find('all', array(
			'conditions' => array('rep_id' => $rep_id),
			'contain' => array('Rep')
		));
		
		$this->set('rep_areas', $rep_areas);
		
		$reps = $this->RepArea->Rep->find('list', array(
			'conditions' => array(
				'Rep.id NOT IN (' . $rep_id . ',1,4)' 
			),
			'fields' => array('Rep.id', 'Rep.last_name'),
			'order' => array('Rep.last_name' => 'asc')
		));

		$this->set('reps', $reps);
	}
	
	function admin_add() {
		$this->layout = 'admin';
		
		$rep_id = $this->params['named']['rep_id'];
		
		$this->set('rep_id', $rep_id);
		
		if ( isset($this->data) ) {
			if ( $this->RepArea->save($this->data) ) {
				$this->Session->setFlash('Oblast byla vytvořena');
				$this->redirect(array('controller' => 'rep_areas', 'action' => 'index', 'rep_id' => $rep_id));
			} else {
				$this->Session->setFlash('Oblast se nepodařilo vytvořit, opravte chyby a opakujte akci');
			}
		}
	}
	
	function admin_edit($id) {
		$this->layout = 'admin';
		
		$this->set('id', $id);
		
		$this->RepArea->id = $id;
		$this->RepArea->contain();
		$rep_area = $this->RepArea->read();
		$this->set('rep_id', $rep_area['RepArea']['rep_id']);
		
		if ( isset($this->data) ) {
			if ( $this->RepArea->save($this->data) ) {
				$this->Session->setFlash('Oblast byla upravena');
				$this->redirect(array('controller' => 'rep_areas', 'action' => 'index', 'rep_id' => $rep_area['RepArea']['rep_id']));
			} else {
				$this->Session->setFlash('Oblast se nepodařilo upravit, opravte chyby a opakujte akci');
			}
		} else {
			$this->data = $rep_area;
			$this->set('rep_id', $this->data['RepArea']['rep_id']);
		}
	}
	
	function admin_move($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zvolena oblast, kterou chcete přesunout.');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		$area = $this->RepArea->find('first', array(
			'conditions' => array('RepArea.id' => $id),
			'contain' => array()
		));
		
		if (empty($area)) {
			$this->Session->setFlash('Oblast neexistuje');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			if ($this->RepArea->save($this->data)) {
				$this->Session->setFlash('Oblast byla přesunuta zvolenému repovi');
			} else {
				$this->Session->setFlash('Oblast se nepodařilo přesunout');
			}
		}
		$this->redirect(array('controller' => 'rep_areas', 'action' => 'index', 'rep_id' => $area['RepArea']['rep_id']));
	}
	
	function admin_delete($id) {
		$this->RepArea->id = $id;
		$this->RepArea->contain();
		$rep_area = $this->RepArea->read();
		
		if ($this->RepArea->del($id)) {
			$this->Session->setFlash('Oblast byla odstraněna');
		} else {
			$this->Session->setFlash('Oblast se nepodařilo odstranit, opakujte prosím akci');
		}
		$this->redirect(array('controller' => 'rep_areas', 'action' => 'index', 'rep_id' => $rep_area['RepArea']['rep_id']));
	}

	function rep_index() {
		$this->layout = 'rep';
		
		$rep_id = $this->Session->read('Rep.id');
		$rep_areas = $this->RepArea->find('all', array(
			'conditions' => array('rep_id' => $rep_id),
			'contain' => array()
		));
		$this->set('rep_areas', $rep_areas);
	}
}
?>