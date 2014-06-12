<?php
class AvailabilitiesController extends AppController {

	var $name = 'Availabilities';

	/**
	 * Edituje dostupnost.
	 *
	 * @param int $id
	 */
	function admin_edit($id){
		if ( !isset($this->data) ){
			$this->Availability->recursive = -1;
			$this->data = $this->Availability->read(null, $id);
			if ( empty($this->data) ){
				$this->Session->setFlash('Neexistující dostupnost!');
				$this->redirect(array('action' => 'index'), null, true);
			}
		} else {
			if ( $this->Availability->save($this->data) ){
				$this->Session->setFlash('Dostupnost byla upravena!');
				$this->redirect(array('action' => 'edit', $this->Availability->id), null, true);
			} else {
				$this->Session->setFlash('Chyba při úpravě dostupnosti!');
			}
		}
	}
	
	/**
	 * Zobrazi seznam dostupnosti.
	 *
	 */
	function admin_index(){
		$this->Availability->recursive = -1;
		$availabilities = $this->Availability->find('all');
		
		$this->set('availabilities', $availabilities);
	}
}
?>