<?
class ProductsController extends AppController {

	var $name = 'Products';
	
	/**
	 * 
	 * Karta produktu
	 * @param int $id - id zobrazovaneho produktu
	 */
	function admin_view($id = null) {
		$this->layout = 'admin';

		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array('ProductDocument', 'Category')
		));
		$this->set('product', $product);

		// vyhledam si seznam vyrobcu
		$manufacturers = $this->Product->Manufacturer->find('list');
		$this->set('manufacturers', $manufacturers);
		
		// vyhledam si seznam dostupnosti
		$availabilities = $this->Product->Availability->find('list');
		$this->set('availabilities', $availabilities);

		// vyhledam si seznam hladin DPH
		$taxClasses = $this->Product->TaxClass->find('list');
		$this->set('taxClasses', $taxClasses);
		
		if ( isset($this->data) && isset($this->data['Product']['action']) && $this->data['Product']['action'] == 'edit'){
			if ( $this->Product->save($this->data) ){
				$this->Session->setFlash('Produkt byl uložen.');
				$this->redirect(array('controller' => 'products', 'action' => 'view', $id), null, true);
			} else {
				$this->Session->setFlash('Ukládání se nezdařilo, zkontrolujte prosím všechna povinná pole.');
			}
		} elseif (isset($this->data['Product'])) {
			$this->data['Product'] = array_merge($product['Product'], $this->data['Product']);
		} else {
			$this->data = $product;
		}
		
		// a mozne options
		$options = $this->Product->Subproduct->AttributesSubproduct->Attribute->Option->find('all', array(
			'contain' => array()
		));
		$this->set('options', $options);
		
		// formular je vyplnen (ne filtrovani)
		if (isset($this->data) && isset($this->data['Product']['action']) && $this->data['Product']['action'] == 'attributes') {
			// musim se podivat, jestli uz tam takovy atributy jsou
			$attributes = array();
			// pro ucely nasledneho mazani nadbytecnych subproduktu si zde iniciuju pole pro zapamatovani suproduktu, ktere odpovidaji
			// datum z formulare
			$subproduct_ids = array();
			foreach ($this->data['Attributes'] as $option_id => $attributes_text) {
				if ($attributes_text != '') {
					$attributes_values = explode("\r\n", $attributes_text);
					foreach ($attributes_values as $value)  {
						$value = trim($value);
						if ($value == '') {
							continue;
						}
						$attribute = array();
						$attribute['Attribute']['value'] = $value;
						$attribute['Attribute']['option_id'] = $option_id;
						$db_attribute = $this->Product->Subproduct->AttributesSubproduct->Attribute->find('first', array(
							'conditions' => $attribute['Attribute'],
							'contain' => array()
						));

						if (empty($db_attribute)) {
							// pokud ne, ulozim a zapamatuju si idcko
							// musim zjistit maximalni sort_order pro dane option_id a nastavit sort_order noveho atributu jako max + 1
							$max = $this->Product->Subproduct->AttributesSubproduct->Attribute->find('first', array(
								'fields' => array('MAX(sort_order) AS MAX'),
								'conditions' => array('option_id' => $attribute['Attribute']['option_id']),
								'contain' => array()
							));
							$attribute['Attribute']['sort_order'] = $max[0]['MAX'] + 1;
							unset($this->Product->Subproduct->AttributesSubproduct->Attribute->id);
							$this->Product->Subproduct->AttributesSubproduct->Attribute->save($attribute);
							$attributes[$option_id][] = $this->Product->Subproduct->AttributesSubproduct->Attribute->id;
						} else {
							// pokud jo, najdu jejich idcko
							$attributes[$option_id][] = $db_attribute['Attribute']['id'];
						}
					}
				}
			}
			$subproducts = $this->Product->Subproduct->find('all', array(
				'conditions' => array('Subproduct.product_id' => $this->data['Product']['id']),
				'contain' => array('AttributesSubproduct')
			));
			// vygeneruju kombinace atributu
			$generated_subproducts = $this->Product->combine($attributes);
			// prochazim vygenerovane subprodukty
			foreach ($generated_subproducts as $generated_subproduct) {
				// musim projit subprodukty produktu a zjistit, jestli uz v db neni subprodukt, ktery chci vkladat
				foreach ($subproducts as $subproduct) {
				// myslim si, ze subprodukt v db je
					$found = true;
					// pokud souhlasi pocet attribute_subproducts u subproduktu z db a vygenerovaneho
					if (sizeof($subproduct['AttributesSubproduct']) == sizeof($generated_subproduct)) {
						// prochazim vztahy mezi atributy a subproduktem z db
						foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) {
							// jestlize neni attributes_subproduct soucasti vygenerovaneho subproduktu
							if (!in_array($attributes_subproduct['attribute_id'], $generated_subproduct)) {
								// nastavim, ze jsem subprodukt nenasel
								$found = false;
								// a attributes_subprodukty dal neprochazim
								break;
							}
						}
						// jestlize jsem subprodukt nasel v db
						if ($found) {
							// zapamatuju si jeho idcko v db
							$subproduct_ids[] = $subproduct['Subproduct']['id'];
							break;
						}
						// pokud se velikost lisi
					} else {
						// nastavim si, ze jsem subprodukt nenasel
						$found = false;
						break;
					}
				}
				// jestlize jsem subprodukt nenasel
				if (!isset($found) || !$found) {
					// musim vytvorit takovej subprodukt a k nemu napojeni na atributy
					$subproduct_save['Subproduct']['product_id'] = $this->data['Product']['id'];
					$subproduct_save['Subproduct']['active'] = true;
					unset($this->Product->Subproduct->id);
					$this->Product->Subproduct->save($subproduct_save);
					$subproduct_id = $this->Product->Subproduct->id;
					$subproduct_ids[] = $subproduct_id;
					foreach ($generated_subproduct as $attribute_id) {
						unset($this->Product->Subproduct->AttributesSubproduct->id);
						$attributes_subproduct_save['AttributesSubproduct']['subproduct_id'] = $subproduct_id;
						$attributes_subproduct_save['AttributesSubproduct']['attribute_id'] = $attribute_id;
						$this->Product->Subproduct->AttributesSubproduct->save($attributes_subproduct_save);
					}
				}
			}
			// musim najit vsechny subprodukty tohoto produktu a ty, co nejsou podle zadanych hodnot platne, musim odstranit
			// tzn musim porovnat saves oproti obsahu databaze a co je navic, tak smazat
			$db_subproduct_ids = $this->Product->Subproduct->find('all', array(
				'conditions' => array('product_id' => $this->data['Product']['id']),
				'contain' => array(),
				'fields' => array('id')
			));

			foreach ($db_subproduct_ids as $db_subproduct_id) {
				if (!in_array($db_subproduct_id['Subproduct']['id'], $subproduct_ids)) {
					$this->Product->Subproduct->delete($db_subproduct_id['Subproduct']['id']);
				}
			}
			$this->Session->setFlash('Úpravy byly provedeny');
			$this->redirect(array('controller' => 'products', 'action' => 'view', $this->data['Product']['id']));
		} else {
			// potrebuju vytvorit vstupni data pro formular
			// tzn pro kazdou option vybrat zvolene values k tomuto produktu - ne jen pro ty options, pro ktere ma produkt atributy, ale
			// uplne pro vsechny
			foreach ($options as $option) {
				// vybiram takovy vazby mezi produktem a atributem, ktery patri k zadanymu produktu
				$attributes_subproducts = $this->Product->Subproduct->AttributesSubproduct->find('all', array(
					'conditions' => array_merge(
						array('Subproduct.product_id' => $id, 'Attribute.option_id' => $option['Option']['id'])
					),
					'contain' => array(
						'Subproduct',
						'Attribute' => array(
							'Option'
						)
					),
					'order' => array('Attribute.option_id ASC', 'Attribute.sort_order ASC'),
					// musim se zbavit "duplicit" - atributes_subproduktu, ktery ukazuji na stejny atributy
					'group' => array('Attribute.id')
				));
				// nadefinuju implicitni hodnoty formularovych poli
				$this->data['Attributes'][$option['Option']['id']] = '';
				foreach ($attributes_subproducts as $attributes_subproduct) {
					$this->data['Attributes'][$option['Option']['id']] .= $attributes_subproduct['Attribute']['value'] . "\n";
				}
				$this->data['Attributes'][$option['Option']['id']] = trim($this->data['Attributes'][$option['Option']['id']]);
			}
		}
		
		// nacist info o obrazcich
		$images = $this->Product->Image->find('all', array(
			'conditions' => array('product_id' => $id),
			'contain' => array()
		));

		// nacist info o kategorii
		$category = $this->Product->CategoriesProduct->find('all', array(
			'conditions' => array('CategoriesProduct.product_id' => $id),
			'contain' => array()
		));

		if (isset($this->data) && isset($this->data['Product']['action']) && $this->data['Product']['action'] == 'image_fields') {
			$this->set('image_fields', $this->data['Product']['image_fields']);
		}

		$this->set(compact('images', 'category'));
	}
	
	function admin_add(){
		$this->layout = 'admin';
		
		if ( !isset($this->params['named']['category_id']) ){
			$categories = $this->Product->Category->generatetreelist();
			$this->set('categories', $categories);
		} else {
			$this->set('category_id', $this->params['named']['category_id']);
		}
		
		// vyhledam si seznam vyrobcu
		$manufacturers = $this->Product->Manufacturer->find('list');
		$this->set('manufacturers', $manufacturers);
		
		// vyhledam si seznam dostupnosti
		$availabilities = $this->Product->Availability->find('list');
		$this->set('availabilities', $availabilities);
		
		// vyhledam si seznam hladin DPH
		$taxClasses = $this->Product->TaxClass->find('list');
		$this->set('taxClasses', $taxClasses);
		
		if ( isset($this->data) ){
			// pokud neni zadana cena s dph, dopocitam ji
			if (!$this->data['Product']['price_tax']) {
				$tax_class = $this->Product->TaxClass->find('first', array(
					'conditions' => array('TaxClass.id' => $this->data['Product']['tax_class_id']),
					'contain'	=> array()
				));	
				$this->data['Product']['price_tax'] = $this->data['Product']['price'] + round($this->data['Product']['price'] * $tax_class['TaxClass']['value'] / 100);
			}
			
			// pokud neni nastavena nakupni cena, nastavi se na 0
			if (!$this->data['Product']['price_buy']) {
				$this->data['Product']['price_buy'] = 0;
			}
//debug($this->data); die();
			if ( $this->Product->save($this->data) ){
				$this->Session->setFlash('Produkt byl uložen.');
				$this->redirect(array('controller' => 'products', 'action' => 'index', 'category_id' => $this->data['Category']['id']), null, true);
			} else {
				$this->Session->setFlash('Ukládání se nezdařilo, zkontrolujte prosím všechna povinná pole.');
			}
		}
	}
	
	function admin_delete($id = null){
		if ( empty($id) ){
			$this->Session->setFlash('Neexistující produkt!');
			$this->redirect(array('controller' => 'products', 'action' => 'index'), null, true);
		}
		
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array('Category')
		));
		
		// asociace s Cart mi dela bordel v mazani, tak ho odpojim
		$this->Product->unbindModel(array('belongsTo' => array('Cart')), false);
		if ( $this->Product->delete($id, false) ){
			$this->Session->setFlash('Produkt byl smazán!');
			$this->redirect(array('controller' => 'products', 'action' => 'index', 'category_id' => $product['Category'][0]['id']), null, true);
		}
	}
	
	function admin_edit($id){
		$this->layout = 'admin';
		
		// vyhledam si seznam vyrobcu
		$manufacturers = $this->Product->Manufacturer->find('list');
		$this->set('manufacturers', $manufacturers);
		
		// vyhledam si seznam dostupnosti
		$availabilities = $this->Product->Availability->find('list');
		$this->set('availabilities', $availabilities);

		// vyhledam si seznam hladin DPH
		$taxClasses = $this->Product->TaxClass->find('list');
		$this->set('taxClasses', $taxClasses);
		
		if ( isset($this->data) ){
			if ( $this->Product->save($this->data) ){
				$this->Session->setFlash('Produkt byl uložen.');
				$this->redirect(array('controller' => 'products', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Ukládání se nezdařilo, zkontrolujte prosím všechna povinná pole.');
			}
		}
		
		$this->data = $this->Product->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));
	}

	function admin_index(){
		$this->layout = 'admin';
		
		$conditions = array();
		
		$contain = array(
			'CategoriesProduct',
			'Category' => array(
				'fields' => array('id', 'name')
			)
		);
		
		// omezeni na vypis produktu dane kategorie
		if ( isset($this->params['named']['category_id'])  && !empty($this->params['named']['category_id']) ){
			$conditions['CategoriesProduct.category_id'] = $this->params['named']['category_id'];
			$this->set('category_id', $this->params['named']['category_id']);
			
			$category = $this->Product->Category->find('first', array(
				'conditions' => array(
					'id' => $this->params['named']['category_id']
				),
				'contain' => array()
			));
			$this->set('category', $category);
		}

		$products = $this->Product->find('all', array(
			'conditions' => $conditions,
			'contain' => $contain
		));
		
		$this->set('products', $products);
	}

	function admin_attributes_list($id = null){
		// nactu si produkt se zadanym idckem
		$this->Product->id = $id;
		$this->Product->contain(array('CategoriesProduct'));
		$product = $this->Product->read();
		
		$this->set('product', $product);
		
		// a mozne options
		$options = $this->Product->Subproduct->AttributesSubproduct->Attribute->Option->find('all');
		$this->set('options', $options);

		// formular je vyplnen (ne filtrovani)
		if (isset($this->data) && !isset($this->data['Option'])) {
			// musim se podivat, jestli uz tam takovy atributy jsou
			$attributes = array();
			// pro ucely nasledneho mazani nadbytecnych subproduktu si zde iniciuju pole pro zapamatovani suproduktu, ktere odpovidaji
			// datum z formulare
			$subproduct_ids = array();
			foreach ($this->data['Attributes'] as $option_id => $attributes_text) {
				if ($attributes_text != '') {
					$attributes_values = explode("\r\n", $attributes_text);
					foreach ($attributes_values as $value)  {
						$value = trim($value);
						if ($value == '') {
							continue;
						}
						$attribute = array();
						$attribute['Attribute']['value'] = $value;
						$attribute['Attribute']['option_id'] = $option_id;
						$db_attribute = $this->Product->Subproduct->AttributesSubproduct->Attribute->find('first', array(
							'conditions' => $attribute['Attribute'],
							'contain' => array()
						));
						if (empty($db_attribute)) {

							// pokud ne, ulozim a zapamatuju si idcko
							// musim zjistit maximalni sort_order pro dane option_id a nastavit sort_order noveho atributu jako max + 1
							$max = $this->Product->Subproduct->AttributesSubproduct->Attribute->find('first', array(
								'fields' => array('MAX(sort_order) AS MAX'),
								'conditions' => array('option_id' => $attribute['Attribute']['option_id']),
								'contain' => array()
							));
							$attribute['Attribute']['sort_order'] = $max[0]['MAX'] + 1;
							unset($this->Product->Subproduct->AttributesSubproduct->Attribute->id);
							$this->Product->Subproduct->AttributesSubproduct->Attribute->save($attribute);
							$attributes[$option_id][] = $this->Product->Subproduct->AttributesSubproduct->Attribute->id;
						} else {
							// pokud jo, najdu jejich idcko
							$attributes[$option_id][] = $db_attribute['Attribute']['id'];
						}
					}
				}
			}
			$subproducts = $this->Product->Subproduct->find('all', array(
				'conditions' => array('Subproduct.product_id' => $this->data['Product']['id']),
				'contain' => array('AttributesSubproduct')
			));
			
			// vygeneruju kombinace atributu
			
			$generated_subproducts = array();
			if (!empty($attributes)) {
				$generated_subproducts = $this->Product->combine($attributes);
			}
			// prochazim vygenerovane subprodukty
			foreach ($generated_subproducts as $generated_subproduct) {
				// musim projit subprodukty produktu a zjistit, jestli uz v db neni subprodukt, ktery chci vkladat
				foreach ($subproducts as $subproduct) {
					// myslim si, ze subprodukt v db je
					$found = true;
					// pokud souhlasi pocet attribute_subproducts u subproduktu z db a vygenerovaneho
					if (sizeof($subproduct['AttributesSubproduct']) == sizeof($generated_subproduct)) {
						// prochazim vztahy mezi atributy a subproduktem z db
						foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) {
							// jestlize neni attributes_subproduct soucasti vygenerovaneho subproduktu
							if (!in_array($attributes_subproduct['attribute_id'], $generated_subproduct)) {
								// nastavim, ze jsem subprodukt nenasel
								$found = false;
								// a attributes_subprodukty dal neprochazim
								break;
							}
						}
						// jestlize jsem subprodukt nasel v db
						if ($found) {
							// zapamatuju si jeho idcko v db
							$subproduct_ids[] = $subproduct['Subproduct']['id'];
							break;
						}
						// pokud se velikost lisi
					} else {
						// nastavim si, ze jsem subprodukt nenasel
						$found = false;
						break;
					}
				}
				// jestlize jsem subprodukt nenasel
				if (!isset($found) || !$found) {
					// musim vytvorit takovej subprodukt a k nemu napojeni na atributy
					$subproduct_save['Subproduct']['product_id'] = $this->data['Product']['id'];
					$subproduct_save['Subproduct']['active'] = true;
					unset($this->Product->Subproduct->id);
					$this->Product->Subproduct->save($subproduct_save);
					$subproduct_id = $this->Product->Subproduct->id;
					$subproduct_ids[] = $subproduct_id;
					foreach ($generated_subproduct as $attribute_id) {
						unset($this->Product->Subproduct->AttributesSubproduct->id);
						$attributes_subproduct_save['AttributesSubproduct']['subproduct_id'] = $subproduct_id;
						$attributes_subproduct_save['AttributesSubproduct']['attribute_id'] = $attribute_id;
						$this->Product->Subproduct->AttributesSubproduct->save($attributes_subproduct_save);
					}
				}
			}
			// musim najit vsechny subprodukty tohoto produktu a ty, co nejsou podle zadanych hodnot platne, musim odstranit
			// tzn musim porovnat saves oproti obsahu databaze a co je navic, tak smazat
			$db_subproduct_ids = $this->Product->Subproduct->find('all', array(
				'conditions' => array('product_id' => $this->data['Product']['id']),
				'contain' => array(),
				'fields' => array('id')
			));
			foreach ($db_subproduct_ids as $db_subproduct_id) {
				if (!in_array($db_subproduct_id['Subproduct']['id'], $subproduct_ids)) {
					$this->Product->Subproduct->delete($db_subproduct_id['Subproduct']['id']);
				}
			}
			$this->Session->setFlash('Úpravy byly provedeny');
			$this->redirect(array('controller' => 'products', 'action' => 'attributes_list', $this->data['Product']['id']));
		} else {
			// potrebuju vytvorit vstupni data pro formular
			// tzn pro kazdou option vybrat zvolene values k tomuto produktu - ne jen pro ty options, pro ktere ma produkt atributy, ale
			// uplne pro vsechny
			foreach ($options as $option) {
				// vybiram takovy vazby mezi produktem a atributem, ktery patri k zadanymu produktu
				$attributes_subproducts = $this->Product->Subproduct->AttributesSubproduct->find('all', array(
					'conditions' => array_merge(
						array('Subproduct.product_id' => $id, 'Attribute.option_id' => $option['Option']['id'])
					),
					'contain' => array(
						'Subproduct',
						'Attribute' => array(
							'Option'
						)
					),
					'order' => array('Attribute.option_id ASC', 'Attribute.sort_order ASC'),
					// musim se zbavit "duplicit" - atributes_subproduktu, ktery ukazuji na stejny atributy
					'group' => array('Attribute.id')
				));
				// nadefinuju implicitni hodnoty formularovych poli
				$this->data['Attributes'][$option['Option']['id']] = '';
				foreach ($attributes_subproducts as $attributes_subproduct) {
					$this->data['Attributes'][$option['Option']['id']] .= $attributes_subproduct['Attribute']['value'] . '
';
				}

				$this->data['Attributes'][$option['Option']['id']] = trim($this->data['Attributes'][$option['Option']['id']]);
			}
		}
	}

	function admin_images_list($id){
		$this->layout = 'admin';
		// nacist info o produktu
		$this->Product->id = $id;
		$this->Product->recursive = -1;
		$product = $this->Product->read(array('id', 'name'));
		// nacist info o obrazcich
		$images = $this->Product->Image->find('all', array(
			'conditions' => array('product_id' => $id)
		));

		// nacist info o kategorii
		$this->Product->CategoriesProduct->recursive = -1;
		$category = $this->Product->CategoriesProduct->findAllByProduct_id($id);

		$this->set('opened_category_id', $category[0]['CategoriesProduct']['category_id']);
		$this->set(compact('product', 'images', 'category'));
	}
	
	function admin_list_products_with_attribute() {
		$this->layout = 'admin';
		$attribute_id = $this->params['named']['attribute_id'];
		$products = $this->Product->Subproduct->find('all', array(
			'conditions' => array('Subproduct.attribute_id' => $attribute_id),
			'contain' => array('Product' => array('Category'), 'Attribute' => array('Option', 'Value'))
		));
		$this->set('products', $products);
	}
	
	function admin_move($id = null) {
		$product_id = $id;
		$category_id = $this->params['named']['category_id'];
		$category_product = $this->Product->find('first', array(
			'conditions' => array('product_id' => $product_id, 'category_id' => $category_id),
			'contain' => array('CategoriesProduct')
		));
		$this->redirect(array('controller' => 'categories_products', 'action' => 'edit', 'id' => $category_product['CategoriesProduct']['id']));
	}
	
	/**
	 * Seznam produktu prilozenych k produktu.
	 *
	 * @param unsigned int $id
	 */
	function admin_documents($id){
		$this->layout = 'admin';
		// do pohledu si poslu id produktu
		$this->set('id', $id);
		
		// natahnu si data o produktu
		$product = $this->Product->find('first', array(
			'conditions' => array(
				'Product.id' => $id
			),
			'contain' => array(
				'ProductDocument'
			)
		));
			
		// data poslu do pohledu
		$this->set('product', $product);
		
		// pokud se jedna o neexistujici produkt, presmeruju
		if ( empty($product) ){
			$this->Session->setFlash('Neexstující produkt.');
			$this->redirect(array('controller' => 'orders', 'admin' => true), null, true);
		}
	}

	function admin_documents_add($id){
		if ( isset($this->data)) {
			$this->data['Document']['name']['name'] = strip_diacritic($this->data['Document']['name']['name']);
			if (!file_exists('files/documents/' . $this->data['Document']['name']['name']) ){
				// muzu nahravat
				if ( move_uploaded_file($this->data['Document']['name']['tmp_name'], 'files/documents/' . $this->data['Document']['name']['name']) ){
					// potrebuju zmenit prava u dokumentu
					chmod('files/documents/' . $this->data['Document']['name']['name'], 0644);
					
					// zalozim zaznam do databaze
					$document_data = array(
						'name' => $this->data['Document']['name']['name'],
						'type' => $this->data['Document']['type'],
						'product_id' => $id
					);
					
					if ( $this->Product->ProductDocument->save($document_data) ){
						$this->Session->setFlash('Dokument byl uložen.');
					}
				} else {
					$this->Session->setFlash('Dokument se nepodařilo nahrát.');
				}
			} else {
				// takovy dokument uz existuje
				$this->Session->setFlash('Dokument tohoto názvu již existuje.');
			}
		}
		$this->redirect(array('controller' => 'products', 'action' => 'view', $id), null, true);
	}

	function admin_documents_edit($id){
		$this->layout = 'admin';
		
		if ( isset($this->data) ){
			$this->Product->ProductDocument->id = $id;
			if ( !$this->Product->ProductDocument->save($this->data, false, array('type')) ){
				$this->Session->setFlash('Chyba při úpravě dokumentu, zkuste to prosím znovu.');
				$this->redirect(array('controller' => 'products', 'action' => 'documents', 'id' => $this->data['ProductDocument']['product_id']), null, true);
			}
			$this->Session->setFlash('Dokument byl upraven.');
			$this->redirect(array('controller' => 'products', 'action' => 'view', $this->data['ProductDocument']['product_id']), null, true);
		}
		$this->data = $this->Product->ProductDocument->read(null, $id);
		$this->set('id', $id);
	}
	
	function admin_documents_delete($id) {
		$document = $this->Product->ProductDocument->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'contain' => array()
		));
		
		
		if ( !empty($document) ){
			$product_id = $document['ProductDocument']['product_id'];
	
			// vymaz z databaze
			if ($this->Product->ProductDocument->del($id) ){
				// podarilo se smazat z db
				// vymaz z disku
				if ( file_exists('files/documents/' . $document['ProductDocument']['name']) ){
					if ( unlink('files/documents/' . $document['ProductDocument']['name']) ){
						// kompletni vymazani se podarilo
						$this->Session->setFlash('Dokument byl vymazán.');
						$this->redirect(array('controller' => 'products', 'action' => 'view', $product_id), null, true);
					}
				}
			}
			$this->Session->setFlash('Dokument se nepodařilo vymazat, zkuste to prosím znovu.');
			$this->redirect(array('controller' => 'products', 'action' => 'view', $product_id), null, true);
		}
		$this->Session->setFlash('Pokoušíte se vymazat neexstující dokument.');
		$this->redirect(array('controller' => 'administrators', 'action' => 'index'), null, true);
	}
	
	function admin_add_subproducts($id) {
		if (isset($this->data)) {

			foreach ($this->data['Product'] as $subproduct_id => $subproduct) {
				$subproduct['Subproduct']['id'] = $subproduct_id;
				$subproduct['Subproduct']['active'] = $subproduct['active'];
				$subproduct['Subproduct']['availability_id'] = $subproduct['availability_id'];
				$subproduct['Subproduct']['price'] = $subproduct['price'];
					
				unset($this->Product->Subproduct->id);
				$this->Product->Subproduct->save($subproduct);
			}
			// zkontroluju nastaveni Product.active
			$product = $this->Product->find('first', array(
				'conditions' => array('Product.id' => $this->data['Product'][$subproduct_id]['product_id']),
				'contain' => array(
					'Subproduct'
				)
			));
			// tady nemam zneaktivneni produktu, proto active nastavim na tvrdo na true
			$product['Product']['active'] = true;
			// kontroluju priznak active u produktu a subproduktu
			$active = false;
			$i = 0;
			while (!$active && $i < sizeof($product['Subproduct'])) {
				$active = $product['Subproduct'][$i]['active'];
				$i++;
			}
			$message = '';
			if ($active && !$product['Product']['active']) {
				$product['Product']['active'] = true;
				unset($this->Product->id);
				if ($this->Product->save($product)) {
					$message = ' Produkt byl aktivován';
				}
			} elseif (!$active && $product['Product']['active']) {
				$product['Product']['active'] = false;
				unset($this->Product->id);
				if ($this->Product->save($product)) {
					$message = ' Produkt byl deaktivován';
				}
			}
		}
		$this->Session->setFlash('Úpravy byly provedeny.');
		$this->redirect(array('controller' => 'products', 'action' => 'view', $id));
	}
		
	function users_index() {
		$this->layout = 'users';
		
		if (!isset($this->data) && $this->Session->check('Order') ) {
			$this->data['Order'] = $this->Session->read('Order');
		}

		// vytahnu si vsechny produkty
		$products = $this->Product->find('all', array(
			'conditions' => array(),
			'contain' => array(
				'CategoriesProduct' => array(
					'Category' => array(
						'fields' => array('id', 'name')
					)
				)
			),
			'order' => array(
				'CategoriesProduct.category_id' => 'asc'
			)
		));
		
		$res = array();
		// pro kazdej produkt
		foreach ($products as $product) {
			// si vytahnu jeho mozny subprodukty, ktery seradim podle Attribute.options_id
			$contain = array(
				'Attribute' => array(
					'fields' => array('id'),
					'Option' => array(
						'fields' => array('id', 'name')
					),
					'Value' => array(
						'fields' => array('id', 'name')
					)
				)
			);
			$subproducts = $this->Product->Subproduct->find('all',
				array(
					'conditions' => array('product_id' => $product['Product']['id']),
					'order' => array('Attribute.option_id ASC'),
					'contain' => $contain
				)
			);
			$attributes = array();
			//pro kazdej subprodukt
			foreach ($subproducts as $subproduct) {
				// si zapamatuju jeho Option a Value
				$key = $subproduct['Attribute']['option_id'];
				$subproduct['Attribute']['Value']['Option'] = $subproduct['Attribute']['Option'];
				$subproduct['Attribute']['Value']['price'] = $subproduct['Subproduct']['price'];
				$attributes[$key][] = array('Value' => $subproduct['Attribute']['Value']);
			}
			
			if (!empty($attributes)) {
				// pokud ma produkt atributy, zapamatuju si vsechny jejich mozny kombinace
				$res[] = array('Product' => $product['Product'], 'values' => $this->Product->combine($attributes));
			} else {
				$res[] = array('Product' => $product['Product']);
			}
		}
		$this->set('products', $res);
	}

	function users_view($id = null){
		// pokud jsou nadefinovana nejaka data,
		// znamena to ze se jedna o pokus o objednani
		if ( isset($this->data) ){
			$result = $this->Product->Cart->add($this->data);
			$this->Session->setFlash($result);
			$this->redirect(array('users' => true, 'controller' => 'products', 'action' => 'view', $id), null, true);
		}
		$this->layout = 'users';

		if ( isset($id) && !empty($id) ){
			$conditions = array(
				'Product.id' => $id
			);
			$contain = array(
				'Manufacturer' => array(
					'fields' => array('id', 'name')
				),
				'Availability' => array(
					'fields' => array('id', 'name')
				),
				'TaxClass' => array(
					'fields' => array('id', 'name', 'value')
				),
				'Image' => array(
					'order' => array(
						'is_main' => 'desc'
					)
				),
				'ProductDocument',
				'Subproduct' => array(
					'conditions' => array('active' => true),
					'AttributesSubproduct' => array(
						'Attribute' => array(
							'Option'
						)
					)
				)
			);
			
			$product = $this->Product->find('first', array(
				'conditions' => $conditions,
				'contain' => $contain
			));
			
			$this->set('product', $product);
		}
	}

	function dph(){
		$products = $this->Product->find('all', array(
			'contain' => array('TaxClass')
		));
		
		for ( $i = 0; $i < count($products); $i++ ){
			$products[$i]['Product']['price_tax'] = $products[$i]['Product']['price'] * ( 1 + ( $products[$i]['TaxClass']['value'] / 100) );
			$products[$i]['Product']['price_tax'] = round($products[$i]['Product']['price_tax'], 2);
			//$products[$i]['Product']['price'] = $products[$i]['Product']['price_wout'];
			//unset($products[$i]['Product']['price_wout']);
			
			$this->Product->id = $products[$i]['Product']['id'];
			$this->Product->save($products[$i]['Product'], false, array('price_tax'));
		}
		debug($products);
		die();
	}
}
?>