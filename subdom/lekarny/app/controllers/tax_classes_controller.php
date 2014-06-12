<?
class TaxClassesController extends AppController {
	var $name = 'TaxClasses';
	var $helpers = array('Html', 'Form', 'Javascript' );
	
	function admin_index(){
		$this->layout = 'admin';
		$this->TaxClass->recursive = 0;
		$taxClasses = $this->paginate('TaxClass');
		$this->set(compact('taxClasses'));
	}
	
	function admin_add() {
		$this->layout = 'admin';
		if (!empty($this->data)) {
			//$this->cleanUpFields();
			$this->TaxClass->create();

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
	
	function admin_edit($id){
		$this->layout = 'admin';
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Neexistující daňová třída.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		if (!empty($this->data)) {
			//$this->cleanUpFields();
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
}
?>