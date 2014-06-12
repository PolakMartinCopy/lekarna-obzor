<?php
class TaxClassesController extends AppController {

	var $name = 'TaxClasses';
	var $helpers = array('Html', 'Form', 'Javascript' );


	function admin_index(){
		$this->TaxClass->recursive = 0;
		$taxClasses = $this->paginate('TaxClass');
		$this->set(compact('taxClasses'));
	}

	function admin_edit($id){
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Neexistující daňová třída.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		if (!empty($this->data)) {
			if ($this->TaxClass->save($this->data)) {
				$this->Session->setFlash('Daňová třída byla uložena.');
				$this->redirect(array('action'=>'index'), null, true);
			} else {
				$this->Session->setFlash('Daňová třída nemohla být uložena, vyplňte prosím správně všechna pole.');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->TaxClass->read(null, $id);
		}
	}

	function admin_add() {
		if (!empty($this->data)) {
			// hledam jestli v databazi uz neni takova hodnota
			if ( $this->TaxClass->hasAny(array('name' => $this->data['TaxClass']['name'])) ){
				$this->Session->setFlash('Hodnota "' . $this->data['TaxClass']['name'] . '" již v databázi figuruje.');
			} else {
				if ( $this->TaxClass->save($this->data) ) {
					$this->Session->setFlash('Jednotka byla uložena.');
					$this->redirect(array('action'=>'index'), null, true);
				} else {
					$this->Session->setFlash('Daňová třída nemohla být uložena, vyplňte prosím správně všechna pole.');
				}
			}
		}
	}
	
	function admin_delete($id){
		if ( !$id ) {
			$this->Session->setFlash('Neexistující daňová třída.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		if ( $this->TaxClass->delete($id) ){
			$this->Session->setFlash('Daňová třída byla smazána.');
			$this->redirect(array('action'=>'index'), null, true);
		}
	}
}
?>