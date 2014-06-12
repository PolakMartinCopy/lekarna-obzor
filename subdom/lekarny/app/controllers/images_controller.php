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
					// zbavim se diakritiky v nazvu obrazku
					$this->data['Image']['image' . $i]['name'] = strip_diacritic($this->data['Image']['image' . $i]['name']);
					// musim si zkontrolovat, abych si neprepsal
					// jiz existujici soubor
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
				if (!$this->Image->hasAny(array('product_id' => $data['product_id']))) {
					$data['is_main'] = 1;
				}
				$this->Image->id = null; // musi se vzdycky zrusit idecko, jinak to updatuje!
				if ( $this->Image->save(array('Image' => $data)) ){
					$message[] = "Obrázek " . $data['name'] . " byl uložen.";
				} else {
					$message[] = "Ukládání obrázku " . $data['name'] . " se nezdařilo!";
				}

				// potrebuju tuto promennou pozdeji
				$product_id = $data['product_id'];
			}
			
			// presmeruju
			$this->Session->setFlash(implode("<br />", $message));
			$this->redirect(array('controller' => 'products', 'action' => 'view', $this->data['Image']['product_id']), null, true);
		}
	}
	
	/**
	 * Editace obrazku.
	 *
	 * @param unknown_type $id
	 */
	function admin_edit($id){
		$this->layout = 'admin';
		if ( isset($this->data) ){
			// vytahnu si puvodni data
			$this->Image->id = $id;
			$this->Image->recursive = -1;
			$image = $this->Image->read();
			
			// porovnam, zda se neco zmenilo
			if ( $image['Image']['name'] != $this->data['Image']['name'] ){
				// zmenilo, ficim dal
				// prejmenovat fyzicky na disku
				if ( file_exists('product-images/' . $image['Image']['name']) ){
					rename('product-images/' . $image['Image']['name'], 'product-images/' . $this->data['Image']['name']);
				}
				// nahled maly
				if ( file_exists('product-images/small/' . $image['Image']['name']) ){
					rename('product-images/small/' . $image['Image']['name'], 'product-images/small/' . $this->data['Image']['name']);
				}
				// nahled stredni
				if ( file_exists('product-images/medium/' . $image['Image']['name']) ){
					rename('product-images/medium/' . $image['Image']['name'], 'product-images/medium/' . $this->data['Image']['name']);
				}

				// prejmenovat v databazi
				$this->Image->save($this->data, false, array('name'));
				
				// presmerovat
				$this->Session->setFlash('Obrázek byl uložen.');
			} else {
				$this->Session->setFlash('Všechna pole zůstala nezměněna, obrázek nebyl uložen.');
			}
			$this->redirect(array('controller' => 'products', 'action' => 'view', $image['Image']['product_id']), null, true);
		}  else {
			$this->Image->id = $id;
			$this->Image->recursive = -1;
			$this->data = $this->Image->read();
		}
	}

	/**
	 * Vlozi obrazek z URL jineho webu.
	 * @author Vlado Tovarnak
	 */
	function admin_add_url(){
		// data jsou odeslana
		if ( isset($this->data) ){
			// pojmenovani obrazku nezustalo prazdne
			if ( !empty($this->data['Image']['new_name']) ){
				// natahnu z url
				if ( $contents = file_get_contents($this->data['Image']['url']) ){
					// zkusim, jestli uz obrazek s takovym jmenem neexistuje
					if ( !file_exists('product-images/' . $this->data['Image']['new_name'] OR 1 == 1 ) ){
						// ulozim obrazek na disk
						$fp = fopen('product-images/' . $this->data['Image']['new_name'], 'w+');
						fwrite($fp, $contents);
						fclose($fp);

						// zkusim, jestli jde zmensit
						if ( $this->Image->isLoadable('product-images/' . $this->data['Image']['new_name']) ){
							// vytvorim nahledove obrazky
							
							$this->Image->makeThumbnails('product-images/' . $this->data['Image']['new_name']);
							// ulozim do databaze
							$new_data['Image'] = array(
								'product_id' => $this->data['Image']['product_id'],
								'name' => $this->data['Image']['new_name'],
								'is_main' => 0
							);
							if (!$this->Image->hasAny(array('product_id' => $new_data['Image']['product_id']))) {
								$new_data['Image']['is_main'] = 1;
							}
							if ( $this->Image->save($new_data, false) ){
								$this->Session->setFlash('Obrázek uložen.');
							} else {
								$this->Session->setFlash('Obrázek neuložen.');
							}
							$this->redirect(array('controller' => 'products', 'action' => 'view', $this->data['Image']['product_id']), null, true);
						} else {
							// hlaska, obrazek se neda nahrat
							// pred hlaskou ho smazu z disku
							unlink('product-images/' . $this->data['Image']['new_name']); 
							$this->Session->setFlash('Obrázek je příliš velký a nelze jej uložit.');
							$this->redirect(array('controller' => 'products', 'action' => 'view', $this->data['Image']['product_id']), null, true);
						}					
					} else {
						$this->Session->setFlash('Obrázek s tímto jménem již existuje, použijte jiné jméno.');
						$this->redirect(array('controller' => 'products', 'action' => 'view', $this->data['Image']['product_id']), null, true);
					}
				}
			} else {
				$this->Session->setFlash('Vyplňte prosím název pro obrázek.');
				$this->redirect(array('controller' => 'products', 'action' => 'view', $this->data['Image']['product_id']), null, true);
			}
		}
		die();
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
			if ($this->Image->del($id)) {
				$this->Session->setFlash('Obrázek byl vymazán.');
				$this->redirect(array('controller' => 'products', 'action'=>'view', $product_id), null, true);
			}
		}
	}

	function admin_set_as_main($id){
		// nactu si obrazek ktery chceme nastavit jako
		// hlavni
		$this->Image->id = $id;
		$this->Image->contain();
		$image = $this->Image->read();

		if ( !empty($image) ){
			// obrazek mam nacteny, tzn. existuje
			// muzu si upravit vsechny jako NEhlavni
			$fields = array('is_main' => "'0'");
			$conditions = array('product_id' => $image['Image']['product_id']);

			
			$this->Image->updateAll($fields, $conditions);
			
			// a upravim si na hlavni ten ktery chci
			$fields = array('is_main' => "'1'");
			$conditions = array('Image.id' => $id);
			
			$this->Image->updateAll($fields, $conditions);

			// mam nastaveno, presmeruju zpatky na seznam obrazku
			$this->Session->setFlash('Obrázek byl nastaven jako hlavní!');
			$this->redirect(array('controller' => 'products', 'action' => 'view', $image['Image']['product_id']), null, true);
		} else {
			$this->Session->setFlash('Špatné ID obrázku!');
			$this->redirect(array('controller' => 'categories', 'action' => 'index'), null, true);
		}
		
	}

	
	function admin_repair(){
		$images = $this->Image->find('all', array(
			'conditions' => array(
				'NOT' => array(
					'name LIKE \'%%jpg%%\''
				)
			),
			'contain' => array(
			)
		));
		
		foreach ( $images as $image ){
			$old_name = $image['Image']['name'];
			$image['Image']['name'] = strip_diacritic($image['Image']['name']) . '.jpg';
			
			if ( rename('product-images/' . $old_name, 'product-images/' . $image['Image']['name']) ){
				if ( rename('product-images/medium/' . $old_name, 'product-images/medium/' . $image['Image']['name']) ){
					if ( rename('product-images/small/' . $old_name, 'product-images/small/' . $image['Image']['name']) ){
						$this->Image->id = $image['Image']['id'];
						if ( !$this->Image->save($image) ){
							debug($image);
						}
					}	
				}	
			}
//			debug($image);
		}
		die();
	}
}
?>