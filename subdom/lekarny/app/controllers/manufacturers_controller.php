<?
class ManufacturersController extends AppController {
	var $name = 'Manufacturers';
	var $helpers = array('Html', 'Form', 'Javascript' );
	
	function admin_index() {
		$this->layout = 'admin';
		$this->Manufacturer->recursive = 0;
		$manufacturers = $this->paginate('Manufacturer');
		$this->set(compact('manufacturers'));
	}
	
	function admin_add() {
		$this->layout = 'admin';
		if (!empty($this->data)) {
			$this->Manufacturer->create();

			// hledam jestli v databazi uz neni takova hodnota
			if ( $this->Manufacturer->hasAny(array('name' => $this->data['Manufacturer']['name'])) ){
				$this->Session->setFlash('Hodnota "' . $this->data['Manufacturer']['name'] . '" již v databázi figuruje.');
			} else {
				if ( $this->Manufacturer->save($this->data) ) {
					$this->Session->setFlash('Výrobce byl uložen.');
					$this->redirect(array('action'=>'index'), null, true);
				} else {
					$this->Session->setFlash('Výrobce nemohl být uložen, vyplňte prosím správně všechna pole.');
				}
			}
		}
	}
	
	function admin_edit($id){
		$this->layout = 'admin';
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Neexistující výrobce.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		if (!empty($this->data)) {
			if ($this->Manufacturer->save($this->data)) {
				$this->Session->setFlash('Výrobce byl uložen.');
				$this->redirect(array('action'=>'index'), null, true);
			} else {
				$this->Session->setFlash('Výrobce nemohl být uložen, vyplňte prosím správně všechna pole.');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Manufacturer->read(null, $id);
		}
	}
}

?>