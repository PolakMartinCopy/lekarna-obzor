<?php
class ImagesController extends AppController {

	var $name = 'Images';
	var $helpers = array('Html', 'Form' );

	function admin_add() {
		if (!empty($this->data) ) {
			// musim si prestrukturovat data
			// proto to co budu ukladat do databaze
			// si budu vkladat do nove promenne
			$new_data = array();

			// prochazim vice souboru a nechci pri jedne chybe
			// prerusit zpracovani, ulozim si chybovky do jedne
			// promenne, na konci si nastavim setFlash
			$message = array();

			// projdu si vsechny formularove prvky pro
			// obrazky postupne
			for ( $i = 0; $i < $this->data['Image']['image_fields']; $i++ ) {
				if ( is_uploaded_file($this->data['Image']['image' . $i]['tmp_name']) ){
					// musim si zkontrolovat, abych si neprepsal
					// jiz existujici soubor
					$this->data['Image']['image' . $i]['name'] = strip_diacritic($this->data['Image']['image' . $i]['name'], false);
					$this->data['Image']['image' . $i]['name'] = $this->Image->checkName('product-images/' . $this->data['Image']['image' . $i]['name']);

					if ( move_uploaded_file($this->data['Image']['image' . $i]['tmp_name'], $this->data['Image']['image' . $i]['name']) ){

						// potrebuju zmenit prava u obrazku
						chmod($this->data['Image']['image' . $i]['name'], 0644);
						// nez ulozim do databaze, potrebuju si vytvorit nahledy
						// nejdriv musim ale otestovat obrazek, jestli se bude dat
						// nacist pomoci imagecreatefrom...
						// vyrezervovana pamet pro operace je 8MegaByte to je 8388608 bytes
						if ( !$this->Image->isLoadable($this->data['Image']['image' . $i]['name']) ){
		
							// pred presmerovani musim vymazat obrazek z disku,
							// aby mi tam zbytecne nezustaval
							unlink($this->data['Image']['image' . $i]['name']);
		
							// presmeruju a vypisu hlasku
							$message[] = 'Obrázek <strong>' . $this->data['Image']['image' . $i]['name'] . '</strong> má příliš velké rozměry, zmenšete jej a zkuste nahrát znovu.';
						} else {
							$this->Image->makeThumbnails($this->data['Image']['image' . $i]['name']);
							$this->data['Image']['image' . $i]['name'] = explode("/", $this->data['Image']['image' . $i]['name']);
							$this->data['Image']['image' . $i]['name'] = $this->data['Image']['image' . $i]['name'][count($this->data['Image']['image' . $i]['name']) -1];
							$new_data[] = array(
											'product_id' => $this->data['Image']['product_id'],
											'name' => $this->data['Image']['image' . $i]['name'],
											'is_main' => 0
										);
						} // isLoadable
					} else {
						$message[] = 'Obrázek <strong>' . $this->data['Image']['image' . $i]['name'] . '</strong> nemohl být uložen, zkuste to znovu prosím.';
					} // move_uploaded_file
				} // is_uplodaed_file
			} // for cyklus

			// prosel jsem vsechny obrazky
			// musim zpracovat vysledek
			foreach ( $new_data as $data ){
				$this->Image->id = null; // musi se vzdycky zrusit idecko, jinak to updatuje!
				if ( $this->Image->save(array('Image' => $data)) ){
					$message[] = "Obrázek " . $data['name'] . " byl uložen.";
				} else {
					$message[] = "Ukládání obrázku " . $data['name'] . " se nezdařilo!";
				}

				// potrebuju tuto promennou pozdeji
				$product_id = $data['product_id'];
			}
			
			// potrebuju vedet zda je nastaveny nejaky obrazek
			// jako hlavni
			$this->Image->id = null;
			$conditions = array('product_id' => $product_id);
			$fields = array('id', 'is_main');
			$order = array('is_main' => 'desc');
			$images = $this->Image->find('all', array(

				'conditions' => $conditions,

				'fields' => $fields,

				'order' => $order,

				'recursive' => 1

			));
			
			if ( $images[0]['Image']['is_main'] != '1' ){
				$fields = array('is_main' => "'1'");
				$conditions = array('id' => $images[0]['Image']['id']);
				$this->Image->updateAll($fields, $conditions);
			}

			// presmeruju
			$this->Session->setFlash(implode("<br />", $message));
			$this->redirect(array('controller' => 'products', 'action' => 'images_list', $this->data['Image']['product_id']), null, true);
		}
	}

	
	function admin_add_dir_image(){
		$file = $this->params['pass']['name'];
		$product_id = $this->params['pass']['product_id'];
		$file_destination = 'upload/images' . $file;
		$file_out = $this->Image->checkName('product-images/' . $this->params['pass']['name']);
		if ( file_exists('upload/images/' . $file) ){
			if ( !$this->Image->isLoadable('upload/images/' . $file) ){
				$this->Session->setFlash('Obrázek <strong>' . $file . '</strong> má příliš velké rozměry, zmenšete jej a zkuste nahrát znovu.');
				$this->redirect(array('controller' => 'dirimages', 'action' => 'list', $product_id), null, true);
			} else {
				$this->Image->makeThumbnails('upload/images/' . $file, $file_out);
				if ( rename('upload/images/' . $file, $file_out) ){
					$file_out = explode('/', $file_out);
					$file_out = $file_out[count($file_out) - 1];
					$data = array(
						'name' => $file_out,
						'product_id' => $product_id,
						'is_main' => 0
					);
					if ( $this->Image->save($data) ){
						$this->Session->setFlash('Obrázek <strong>' . $file . '</strong> byl přiřazen k produktu.');
						$this->redirect(array('controller' => 'dirimages', 'action' => 'list', $product_id), null, true);
					} else {
						$this->Session->setFlash('Obrázek <strong>' . $file . '</strong> se nepodařilo vložit do databáze.');
						$this->redirect(array('controller' => 'dirimages', 'action' => 'list', $product_id), null, true);
					}
				}
			}
		} else {
			$this->Session->setFlash('Obrázek <strong>' . $file . '</strong> neexistuje!');
			$this->redirect(array('controller' => 'dirimages', 'action' => 'list', $product_id), null, true);
		}
	}


	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Image');
			$this->redirect(array('action'=>'index'), null, true);
		} else {
			// nactu si info o obrazku, potrebuju ID produktu kvuli
			// navratovemu URL
			$this->Image->id = $id;
			$this->Image->read(array('name', 'product_id'));
			$image_name = $this->Image->data['Image']['name'];
			$product_id = $this->Image->data['Image']['product_id'];

			// musim vymazat vsechny obrazky z disku
			if ( file_exists('product-images/' . $image_name) ){
				unlink('product-images/' . $image_name);
			}
			if ( file_exists('product-images/small/' . $image_name) ){
				unlink('product-images/small/' . $image_name);
			}
			if ( file_exists('product-images/medium/' . $image_name) ){
				unlink('product-images/medium/' . $image_name);
			}

			// vymazu zaznam z databaze a presmeruju
			if ($this->Image->delete($id)) {
				$this->Session->setFlash('Obrázek byl vymazán.');
				$this->redirect(array('controller' => 'products', 'action'=>'images_list', $product_id), null, true);
			}
		}
	}

	function admin_set_as_main($id){
		// nactu si obrazek ktery chceme nastavit jako
		// hlavni
		$this->Image->id = $id;
		$this->Image->recursive = -1;
		$image = $this->Image->read();

		if ( !empty($image) ){
			// obrazek mam nacteny, tzn. existuje
			// muzu si upravit vsechny jako NEhlavni
			$fields = array('is_main' => "'0'");
			$conditions = array('product_id' => $image['Image']['product_id']);
			$this->Image->updateAll($fields, $conditions);
			
			// a upravim si na hlavni ten ktery chci
			$fields = array('is_main' => "'1'");
			$conditions = array('id' => $id);
			$this->Image->updateAll($fields, $conditions);

			// mam nastaveno, presmeruju zpatky na seznam obrazku
			$this->Session->setFlash('Obrázek byl nastaven jako hlavní!');
			$this->redirect(array('controller' => 'products', 'action' => 'images_list', $image['Image']['product_id']), null, true);
		} else {
			$this->Session->setFlash('Špatné ID obrázku!');
			$this->redirect(array('controller' => 'categories', 'action' => 'index'), null, true);
		}
		
	}
	
	function repair_small() {
		$image_folder = 'product-images/';
		$images_dir = opendir($image_folder);
		
		while($image_name = readdir($images_dir)) {
			if ($image_name != '.' && $image_name != '..') {
				$image_name = $image_folder . $image_name;
				$this->Image->makeThumbnails($image_name);
			}
		}
		die();
	}
}
?>