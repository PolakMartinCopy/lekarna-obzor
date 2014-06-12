<?php
class ManufacturersController extends AppController {

	var $name = 'Manufacturers';
	var $helpers = array('Html', 'Form', 'Javascript' );


	function admin_index(){
		$this->Manufacturer->recursive = 0;
		$manufacturers = $this->paginate('Manufacturer');
		$this->set(compact('manufacturers'));
	}

	function admin_edit($id){
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

	function admin_add() {
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
	
	function admin_delete($id){
		if ( !$id ) {
			$this->Session->setFlash('Neexistující výrobce.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		if ( $this->Manufacturer->delete($id) ){
			$this->Session->setFlash('Výrobce byl smazán.');
			$this->redirect(array('action'=>'index'), null, true);
		}
	}

	/*
	 * Vygeneruje seznam vsech vyrobcu v databazi.
	 * @author Vlado Tovarnak
	 * @param null
	 * @return array
	 */
	function listing(){
		return $this->Manufacturer->listing();
	}

	function redirect_to_new(){
		// rozseknu URL podle otazniku
		$id = explode('?', $_SERVER['REQUEST_URI']);
		
		// pouziju druhou cast, abych si vytahl
		// vsechny parametry
		$id = explode('&', $id[1]);
		
		// zacnu prochazet parametry, hledam ID vyrobce
		foreach ( $id as $nid ){
			// rozseknu parametr a hodnotu od sebe
			$hid = explode('=', $nid);

			// pokud se jedna o hledany parametr
			if ( $hid[0] == 'manufacturers_id' ){
				// vytahnu si z parametru IDecko vyrobce
				$cid = $hid[1];
				
				$this->Manufacturer->recursive = -1;
				$manufacturer = $this->Manufacturer->read(null, $cid);
				
				if ( !empty($manufacturer) ){
					header ('HTTP/1.1 301 Moved Permanently');
			  		header ('Location: /'. strip_diacritic($manufacturer['Manufacturer']['name']) . '-v' . $manufacturer['Manufacturer']['id']);
					die();
				}
			}
		}
		$this->cakeError('error404');
		
	}
	
	function show(){
		if ( isset($this->data['Manufacturer']['id']) ){
			$this->Manufacturer->recursive = -1;
			$manufacturer = $this->Manufacturer->read(null, $this->data['Manufacturer']['id']);

			// oseknu si diakritiku z jmena vyrobce
			$name = strip_diacritic($manufacturer['Manufacturer']['name']);

			$url = $name . "-v" . $manufacturer['Manufacturer']['id'];

			$this->redirect('/' . $url, null, true);
		} else {
			$this->Session->setFlash('Nebyl vybrán vyrobce kterého chcete zobrazit.');
			$this->redirect('/', null, true);
		}
	}
}
?>