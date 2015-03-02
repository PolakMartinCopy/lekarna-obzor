<?php
class ProductsController extends AppController {

	var $name = 'Products';
	var $helpers = array('Html', 'Form', 'Javascript');

	var $paginate = array(
		'limit' => 10,
	);
	
	function index($category_id = null) {
		$this->Product->recursive = 0;
		$this->set('products', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán produkt, který chcete zobrazit.');
			$this->cakeError('error404');
		}
		// pro pripad, ze nema uzivatel povoleny javascript musim natahat validacni hlasky z modelu comment
		$this->loadModel('Comment');
		// vyhledam si info o produktu
		$product = $this->Product->find('first', array(
			'conditions' => array(
				'Product.id' => $id,
				'Product.active' => true
			),
			'contain' => array(
				'CategoriesProduct' => array(
					'Category' => array(
						'fields' => array('id', 'name', 'url')
					)
				),
				'Image' => array(
					'order' => array(
						'is_main' => 'desc'
					),
					'fields' => array('id', 'name')
				),
				'Manufacturer' => array(
					'fields' => array('id', 'name')
				),
				'Availability' => array(
					'fields' => array('id', 'name', 'color', 'cart_allowed')
				),
				'TaxClass' => array(
					'fields' => array('id', 'value')
				),
				'Flag' => array(
					'fields' => array('id', 'name')
				),
				'Subproduct' => array(
					'fields' => array('id'),
					'AttributesSubproduct' => array(
						'fields' => array('id'),
						'Attribute' => array(
							'fields' => array('id', 'value'),
							'Option' => array(
								'fields' => array('id', 'name')
							)
						)
					)
				),
				'Comment' => array(
					'Administrator' => array(
						'fields' => array('first_name', 'last_name')
					),
					'conditions' => array('Comment.confirmed' => 1),
					'order' => array('Comment.created' => 'ASC')
				),
				'ProductType' => array(
					'fields' => array('id', 'name')
				)
			),
			'fields' => array(
				'Product.id',
				'Product.title',
				'Product.description',
				'Product.name',
				'Product.breadcrumb',
				'Product.heading',
				'Product.url',
				'Product.retail_price_with_dph',
				'Product.discount_common',
				'Product.discount_member',
				'Product.short_description',
				'Product.note',
				'Product.product_type_id',
				'Product.code',
				'Product.ean',
				'Product.sukl',
				'Product.group'
			)
		));

		// kontrola, zda ctu produkt, ktery je aktivni
		if ( empty($product)) {
			$this->Session->setFlash('Neexistující produkt.');
			$this->cakeError('error404');
		}
//debug($product);
		// osetruju pokus o vlozeni do kosiku
		if ( isset($this->data['Product']) ){
			// vkladam vyberem z vypisu vsech moznosti
			if (isset($this->data['Subproduct']['id'])) {
				$subproduct = $this->Product->Subproduct->find('first', array(
					'conditions' => array('Subproduct.id' => $this->data['Subproduct']['id']),
					'contain' => array(
						'AttributesSubproduct' => array(
							'Attribute'
						)
					)
				));
				$new_data['CartsProduct']['quantity'] = $this->data['Subproduct']['quantity'];
				$new_data['CartsProduct']['product_id'] = $this->data['Product']['id'];
				$new_data['CartsProduct']['subproduct_id'] = $this->data['Subproduct']['id'];
			} elseif (isset($this->data['Subproduct']['quantity'])) {
				// vkladam do kosiku produkt bez variant
				$new_data['CartsProduct']['product_id'] = $this->data['Product']['id'];
				$new_data['CartsProduct']['quantity'] = $this->data['Subproduct']['quantity'];
			} else {
				// vkladam do kosiku produkt z vypisu produktu v kategorii
				$new_data['CartsProduct']['product_id'] = $this->data['Product']['id'];
				$new_data['CartsProduct']['quantity'] = $this->data['Product']['quantity'];
			}
			
			$this->data = $new_data;
			
			$result = $this->Product->requestAction('carts_products/add', $this->data);
			// vlozim do kosiku
			if ( $result ){
				$this->Session->setFlash('Produkt byl uložen do nákupního košíku. Obsah Vašeho košíku si můžete zobrazit <a href="/kosik">zde</a>.');
				$product = $this->Product->read(array('Product.url'), $this->data['CartsProduct']['product_id']);
				$this->redirect('/' . $product['Product']['url'], null, true);
			} else {
				$this->Session->setFlash('Uložení produktu do košíku se nezdařilo. Zkuste to prosím znovu.');
			}
		} elseif (isset($this->data['RequestForm'])) {
			// dotaz z poptavkoveho formu na detailu produktu
			$this->data['RequestForm']['ip'] = $_SERVER['REMOTE_ADDR'];
			$this->data['RequestForm']['user_gent'] = $this->Session->read('Config.userAgent');

			if ($this->Product->RequestForm->checkSpam($this->data)) {
				if ($this->Product->RequestForm->save($this->data)) {
					if ($this->Product->RequestForm->notify($this->data, $product)) {
						$this->Session->setFlash('Zadané údaje byly odeslány ke zpracování');
						$this->redirect('/' . $product['Product']['url']);
					} else {
						$this->Session->setFlash('Odeslání dotazu se nezdařilo.');
					}
				} else {
					$this->Session->setFlash('Uložení dotazu se nezdařilo, formulář je chybně vyplněn. Opravte prosím chyby a odešlete ofrmulář znovu.');
				}
			} else {
				$this->Session->setFlash('Uložení dotazu se nezdařilo. Příliš mnoho pokusů o odeslání za poslední hodinu.');
			}
		}

		// navolim si layout stranky
		$this->layout = 'product_detail';

		// vyhledam si jestli neni produkt zlevnen
		$product['Product']['discount_price'] = $this->Product->assign_discount_price($product);
		$this->set('product', $product);
		
		// dohledam souvisejici produkty
		$related_products = $this->Product->RelatedProduct->find('all', array(
			'conditions' => array('RelatedProduct.product_id' => $id),
			'contain' => array(),
			'fields' => array('id', 'related_product_id')
		));
		
		$similar_product_ids = Set::extract('/RelatedProduct/related_product_id', $related_products);
		
		$similar_products = $this->Product->find('all', array(
			'conditions' => array(
				'Product.id' => $similar_product_ids,
				'Product.active' => true
			),
			'contain' => array(
				'Manufacturer' => array(
					'fields' => array('id', 'name')
				),
				'Image' => array(
					'conditions' => array('Image.is_main' => true),
					'fields' => array('id', 'name')
				)
			),
			'fields' => array(
				'Product.id',
				'Product.url',
				'Product.name',
				'Product.related_name',
				'Product.short_description',
				'Product.retail_price_with_dph',
				'Product.discount_common',
				'Product.discount_member'
			)
		));
		
		foreach ($similar_products as $key => $similar_product) {
			$similar_products[$key]['Product']['discount_price'] = $this->Product->assign_discount_price($similar_product);
			$similar_products[$key]['Product']['cart_allowed'] = true;
		}
		
		$similar_products = $this->Product->sort_by_price($similar_products, '_asc');
		
		$this->set('similar_products', $similar_products);
		
		// nastavim si titulek stranky
		$page_heading = $product['Product']['heading'];
		if (empty($page_heading)) {
			$page_heading = $product['Product']['name'];
		}
		$this->set('page_heading', $page_heading);
		
		$this->set('title_for_content', $product['Product']['title']);
		$this->set('description_for_content', $product['Product']['short_description']);
		// z infa o produktu si vytahnu ID otevrene kategorie
		$opened_category_id = $product['CategoriesProduct'][0]['category_id'];
		$this->set('opened_category_id', $opened_category_id);
		
		// sestavim breadcrumbs
		$path = $this->Product->CategoriesProduct->Category->getPath($opened_category_id);
		$breadcrumbs = array();
		foreach ($path as $item) {
			$breadcrumb = array('anchor' => $item['Category']['name'], 'href' => '/' . $item['Category']['url']);
			if ($item['Category']['id'] == 5) {
				$breadcrumb = array('anchor' => 'Domů', 'href' => HP_URI);
			}
			$breadcrumbs[] = $breadcrumb;
		}
		$breadcrumbs[] = array('anchor' => $product['Product']['breadcrumb'], 'href' => '/' . $product['Product']['url']);
		$this->set('breadcrumbs', $breadcrumbs);
		
		// potrebuju si spocitat, kolik dotazu (kometaru bylo k produktu pridano)
		$comments_count = $this->Product->Comment->find('count', array(
			'conditions' => array(
				'Comment.product_id' => $id,
				'Comment.confirmed' => '1'
			)
		));
		$this->set('comments_count', $comments_count);
		
		// pokud je uzivatel prihlasen, predvyplnim poptavkovy form
		if ($this->Session->check('Customer')) {
			$this->data['RequestForm']['name'] = $this->Session->read('Customer.first_name') . ' ' . $this->Session->read('Customer.last_name');
			$this->data['RequestForm']['email'] = $this->Session->read('Customer.email');
			$this->data['RequestForm']['phone'] = $this->Session->read('Customer.phone');
		}
		
		// zapnu fancybox
		//$this->set('fancybox', true);
	}

	function admin_attributes_list($id = null){
		// nactu si produkt se zadanym idckem
		$this->Product->id = $id;
		$this->Product->contain(array('CategoriesProduct'));
		$product = $this->Product->read();
		
		$this->set('product', $product);
		$this->set('opened_category_id', $product['CategoriesProduct'][0]['category_id']);
		
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
					$this->data['Attributes'][$option['Option']['id']] .= $attributes_subproduct['Attribute']['value'] . "\n";
				}

				$this->data['Attributes'][$option['Option']['id']] = trim($this->data['Attributes'][$option['Option']['id']]);
			}
		}
	}

	function admin_images_list($id){
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
		$category = $this->Product->CategoriesProduct->find('all', array(
			'conditions' => array('product_id' => $id)
		));

		$this->set('opened_category_id', $category[0]['CategoriesProduct']['category_id']);
		$this->set(compact('product', 'images', 'category'));
	}

	function admin_add($id = null) {
		if (!empty($this->data)) {
			$this->Product->create();

			// uprava pole s cenou, aby se mohlo vkladat take s desetinnou carkou
			$this->data['Product']['retail_price_with_dph'] = str_replace(',', '.', $this->data['Product']['retail_price_with_dph']);
			$this->data['Product']['retail_price_with_dph'] = floatval($this->data['Product']['retail_price_with_dph']);
			if (!empty($this->data['Product']['discount_common'])) {
				$this->data['Product']['discount_common'] = floatval(str_replace(',', '.', $this->data['Product']['discount_common']));
			}
			if (!empty($this->data['Product']['discount_member'])) {
				$this->data['Product']['discount_member'] = floatval(str_replace(',', '.', $this->data['Product']['discount_member']));
			}
			if (!empty($this->data['Product']['zbozi_cpc'])) {
				$this->data['Product']['zbozi_cpc'] = floatval(str_replace(',', '.', $this->data['Product']['zbozi_cpc']));
			}
			if (!empty($this->data['Product']['heureka_cpc'])) {
				$this->data['Product']['heureka_cpc'] = floatval(str_replace(',', '.', $this->data['Product']['heureka_cpc']));
			}
			
			// udelam si kontrolu, jestli je vyplneny titulek a url
			if ( empty($this->data['Product']['title']) ){
				$this->data['Product']['title'] = $this->data['Product']['name'];
			}
			
			// zkontroluju, jestli jsou vyplnene heading, breadcrumb, zbozi a related name
			if (empty($this->data['Product']['heading'])) {
				$this->data['Product']['heading'] = $this->data['Product']['name'];
			}
			if (empty($this->data['Product']['breadcrumb'])) {
				$this->data['Product']['breadcrumb'] = $this->data['Product']['name'];
			}
			if (empty($this->data['Product']['related_name'])) {
				$this->data['Product']['related_name'] = $this->data['Product']['name'];
			}
			if (empty($this->data['Product']['zbozi_name'])) {
				$this->data['Product']['zbozi_name'] = $this->data['Product']['name'];
			}
			if (empty($this->data['Product']['heureka_name'])) {
				$this->data['Product']['heureka_name'] = $this->data['Product']['name'];
			}
			
			// ukladam produkt
			if ($this->Product->save($this->data)) {
				// kontrolu jestli je vyplnene pole pro url musim udelat az po save,
				// abych vedel IDecko produktu
				if ( empty($this->data['Product']['url']) ){
					$this->data['Product']['url'] = strip_diacritic($this->data['Product']['name']) . '-p' . $this->Product->id;
					// a znovu savenu ( UPDATE )
					$this->Product->save($this->data);
				}

				$this->data['CategoriesProduct']['product_id'] = $this->Product->getLastInsertID();
				if ( $this->Product->CategoriesProduct->save($this->data['CategoriesProduct']) ){
					$this->Session->setFlash('Produkt byl uložen.');
					$this->redirect(array('controller' => 'categories', 'action' => 'list_products', $this->data['CategoriesProduct']['category_id']), null, true);
				} else {
					$this->Session->setFlash('Produkt nemohl být uložen.');
				}
			} else {
				$this->set('opened_category_id', $this->data['CategoriesProduct']['category_id']);
				$this->set('category_id', $this->data['CategoriesProduct']['category_id']);
				$this->Session->setFlash('Produkt nemohl být uložen.');
			}
		}

		$this->set('opened_category_id', $id);
		$this->set('category_id', $id);

		$manufacturers = $this->Product->Manufacturer->find('list', array('order' => array('Manufacturer.name' => 'asc')));
		$taxClasses = $this->Product->TaxClass->find('list');
		$availabilities = $this->Product->Availability->find('list');
		$product_types = $this->Product->ProductType->find('list');
		$tinyMce = true;
		$this->set(compact('category', 'manufacturers', 'taxClasses', 'tinyMce', 'availabilities', 'product_types'));
	}

	function admin_edit($id = null, $opened_category_id = null) {
		
		if ( !$id && empty($this->data) ) {
			$this->Session->setFlash('Neexistující produkt.');
			$this->redirect(array('action'=>'index'), null, true);
		}

		if (!empty($this->data)) {
			// uprava pole s cenou, aby se mohlo vkladat take s desetinnou carkkou
			$this->data['Product']['retail_price_with_dph'] = str_replace(',', '.', $this->data['Product']['retail_price_with_dph']);
			$this->data['Product']['retail_price_with_dph'] = floatval($this->data['Product']['retail_price_with_dph']);

			if (!empty($this->data['Product']['discount_common'])) {
				$this->data['Product']['discount_common'] = floatval(str_replace(',', '.', $this->data['Product']['discount_common']));
			}
			if (!empty($this->data['Product']['discount_member'])) {
				$this->data['Product']['discount_member'] = floatval(str_replace(',', '.', $this->data['Product']['discount_member']));
			}
			if (!empty($this->data['Product']['zbozi_cpc'])) {
				$this->data['Product']['zbozi_cpc'] = floatval(str_replace(',', '.', $this->data['Product']['zbozi_cpc']));
			}
			if (!empty($this->data['Product']['heureka_cpc'])) {
				$this->data['Product']['heureka_cpc'] = floatval(str_replace(',', '.', $this->data['Product']['heureka_cpc']));
			}
			
			
			// udelam si kontrolu, jestli je vyplneny titulek a url
			if ( empty($this->data['Product']['title']) ){
				$this->data['Product']['title'] = $this->data['Product']['name'];
			}
			if ( empty($this->data['Product']['url']) ){
				$this->data['Product']['url'] = strip_diacritic($this->data['Product']['name']) . '-p' . $id;
			}
			
			// zkontroluju, jestli jsou vyplnene heading, breadcrumb, zbozi a related name
			if (empty($this->data['Product']['heading'])) {
				$this->data['Product']['heading'] = $this->data['Product']['name'];
			}
			if (empty($this->data['Product']['breadcrumb'])) {
				$this->data['Product']['breadcrumb'] = $this->data['Product']['name'];
			}
			if (empty($this->data['Product']['related_name'])) {
				$this->data['Product']['related_name'] = $this->data['Product']['name'];
			}
			if (empty($this->data['Product']['zbozi_name'])) {
				$this->data['Product']['zbozi_name'] = $this->data['Product']['name'];
			}
			if (empty($this->data['Product']['heureka_name'])) {
				$this->data['Product']['heureka_name'] = $this->data['Product']['name'];
			}

			if ($this->Product->save($this->data)) {
				$this->Session->setFlash('Produkt byl uložen.');
				$this->redirect(array('controller' => 'categories', 'action' => 'list_products', $opened_category_id), null, true);
			} else {
				$this->Session->setFlash('Produkt nemohl být uložen, některá pole zůstala nevyplněna.');
			}
			$this->set('opened_category_id', $this->data['Category']['id']);
		} else {
			$this->Product->unbindModel(array(
				'hasMany' => array('Subproduct', 'Image', 'CartsProduct', 'Comment'),
				'hasAndBelongsToMany' => array('Cart', 'Flag'),
				'belongsTo' => array('Availability')
			));
			$this->data = $this->Product->read(null, $id);

			// zkontroluju, jestli jsou vyplnene heading, breadcrumb, zbozi a related name
			if (empty($this->data['Product']['heading'])) {
				$this->data['Product']['heading'] = $this->data['Product']['name'];
			}
			if (empty($this->data['Product']['breadcrumb'])) {
				$this->data['Product']['breadcrumb'] = $this->data['Product']['name'];
			}
			if (empty($this->data['Product']['related_name'])) {
				$this->data['Product']['related_name'] = $this->data['Product']['name'];
			}
			if (empty($this->data['Product']['zbozi_name'])) {
				$this->data['Product']['zbozi_name'] = $this->data['Product']['name'];
			}
			if (empty($this->data['Product']['heureka_name'])) {
				$this->data['Product']['heureka_name'] = $this->data['Product']['name'];
			}
			
			if (empty($opened_category_id)) {
				$opened_category_id = $this->data['CategoriesProduct'][0]['category_id'];
			}
			
			$fields = array('product_type_id', 'code', 'ean', 'sukl', 'group');
			foreach ($fields as $field) {
				if ($this->data['Product'][$field]) {
					$this->data['Product']['show_' . $field] = true;
				}
			}
			$this->set('opened_category_id', $opened_category_id);
		}
		$manufacturers = $this->Product->Manufacturer->find('list', array('order' => array('Manufacturer.name' => 'asc')));
		$taxClasses = $this->Product->TaxClass->find('list');
		$availabilities = $this->Product->Availability->find('list');
		$product_types = $this->Product->ProductType->find('list');
		$tinyMce = true;
		$this->set(compact('manufacturers','taxClasses', 'tinyMce', 'availabilities', 'product_types'));
	}

	/*
	* @description				Vymaze produkt.
	*/
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující produkt.');
			$this->redirect(array('action'=>'index'), null, true);
		}

		// nactu si info o produktu, ktery budu mazat
		$this->Product->contain('CategoriesProduct');
		$product = $this->Product->read(null, $id);

		$product['Product']['active'] = false;
		if ($this->Product->save($product)) {
			// musim se podivat, jestli nemam produkt v nejprodavanejsich a odsud ho vymazat
			$this->Product->CategoriesMostSoldProduct->deleteAll(array('product_id' => $id));
			$this->Session->setFlash('Produkt byl vymazán');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo vymazat, opakujte prosím akci');
		}
		$this->redirect(array('controller' => 'categories', 'action' => 'list_products', $product['CategoriesProduct'][0]['category_id']), null, true);
	}
	
	function admin_related($id){
		// do pohledu si poslu id produktu
		$this->set('id', $id);
		
		// natahnu si data o produktu
		$product = $this->Product->find('first', array(
			'conditions' => array(
				'Product.id' => $id
			)
		));
		$this->set('opened_category_id', $product['CategoriesProduct'][0]['category_id']);
		// data poslu do pohledu
		$this->set('product', $product);
		
		// pokud se jedna o neexistujici produkt, presmeruju
		if ( empty($product) ){
			$this->Session->setFlash('Neexstující produkt.');
			$this->redirect(array('controller' => 'orders', 'admin' => true), null, true);
		}
		
		// predpoklad, ze zadne souvisejici produkty nema
		$related_products = array();
		
		// zacnu vyhledavat souvisejici
		$related_ids = $this->Product->RelatedProduct->get_list($id);
		if ( !empty($related_ids) ){
			$related_products = $this->Product->find('all', array(
				'conditions' => array(
					"Product.id IN ('" . implode("', '", $related_ids) . "')"
				),
				'contain' => array(
					'Image' => array(
						'conditions' => array(
							'is_main' => '1'
						)
					)
				)
			));
		}
		$this->set('related_products', $related_products);
		
		// zjistim zda nechceme vyhledat nejake produkty
		if ( isset($this->data['Product']['query']) && !empty($this->data['Product']['query']) ){
			$query_products = $this->Product->find('all', array(
				'conditions' => array(
					"Product.name LIKE '%%" . $this->data['Product']['query'] . "%%'",
					// musim vynechat samotny produkt
					// a produkty uz pridane
					"Product.id NOT IN ('" . $id . "', '" . implode("', '", $related_ids) . "')",
					'Product.active' => true
				),
				'contain' => array(
					'Image' => array(
						'conditions' => array(
							'is_main' => '1'
						)
					)
				)
			));
			$this->set('query_products', $query_products);
		}
	}

	/**
	 * Obsluhuje pridani souvisejiciho produktu.
	 *
	 * @param unsigned int $id
	 */
	function admin_related_add($id){
		// souvisejicich produktu muze byt nejvyse 5
		$related_count = $this->Product->RelatedProduct->find('count', array(
			'conditions' => array(
				'RelatedProduct.product_id' => $id
			),
			'contain' => array()
		));
		
		if ($related_count >= 5) {
			$this->Session->setFlash('Vazbu nelze přidat, souvisejících produktů může být nejvýše 5.');
			$this->redirect(array('controller' => 'products', 'action' => 'related', $id), null, true);
		}
		
		// zjistim, zda uz neexistuje vazba
		$product = $this->Product->RelatedProduct->find('all', array(
			'conditions' => array(
				'RelatedProduct.product_id' => $id,
				'RelatedProduct.related_product_id' => $this->data['Product']['related_product_id']
			)
		));
		
		if ( empty($product) ){
			// vazba neexistuje, vytvorim ji
			$related_data = array(
				'product_id' => $id,
				'related_product_id' => $this->data['Product']['related_product_id']
			);
			unset($this->Product->RelatedProduct->id);
			if ( !$this->Product->RelatedProduct->save($related_data) ){
				$this->Session->setFlash('Ukládání souvisejícího produktu se nezdařilo, zkuste to znovu prosím.');
				$this->redirect(array('controller' => 'products', 'action' => 'related', $id), null, true);
			}
		}
		
		// testuju, jestli nechceme pridat i souvislost z druhe strany
		if ( isset($this->data['Product']['both']) ){
			// souvisejicich produktu muze byt nejvyse 5
			$related_count = $this->Product->RelatedProduct->find('count', array(
				'conditions' => array(
					'RelatedProduct.product_id' => $this->data['Product']['related_product_id']
				),
				'contain' => array()
			));
			
			if ($related_count >= 5) {
				$this->Session->setFlash('Vazbu z druhé strany nelze přidat, souvisejících produktů může být nejvýše 5.');
				$this->redirect(array('controller' => 'products', 'action' => 'related', $id), null, true);
			}
			
			// zjistim, zda uz neexistuje vazba
			$product = $this->Product->RelatedProduct->find('all', array(
				'conditions' => array(
					'RelatedProduct.product_id' => $this->data['Product']['related_product_id'],
					'RelatedProduct.related_product_id' => $id
				)
			));
			
			if ( empty($product) ){
				// vazba neexistuje, vytvorim ji
				$related_data = array(
					'product_id' => $this->data['Product']['related_product_id'],
					'related_product_id' => $id
				);
				unset($this->Product->RelatedProduct->id);
				if ( !$this->Product->RelatedProduct->save($related_data) ){
					$this->Session->setFlash('Ukládání souvisejícího produktu se nezdařilo, zkuste to znovu prosím.');
					$this->redirect(array('controller' => 'products', 'action' => 'related', $id), null, true);
				}
			}
		}
		
		// ukladani souvisejicich produktu je dokoncene
		$this->Session->setFlash('Související produkt byl uložen.');
		$this->redirect(array('controller' => 'products', 'action' => 'related', $id), null, true);
	}

	/**
	 * Obsluhuje zruseni souvisejiciho produktu.
	 *
	 * @param unsigned int $id
	 */
	function admin_related_delete($id){
		// najdu si id zaznamu
		$related = $this->Product->RelatedProduct->find('first', array(
			'conditions' => array(
				'product_id' => $id,
				'related_product_id' => $this->data['Product']['related_product_id']
			)
		));

		if ( !empty($related) ){
			if ( !$this->Product->RelatedProduct->delete($related['RelatedProduct']['id'])){
				$this->Session->setFlash('Souvislost s produktem nemohla být vymazána, zkuste to znovu prosím.');
				$this->redirect(array('controller' => 'products', 'action' => 'related', $id), null, true);
			}
		}
		
		$this->Session->setFlash('Souvislost s produktem byla vymazána.');
		$this->redirect(array('controller' => 'products', 'action' => 'related', $id), null, true);
	}
	
	/**
	 * Aktivuje produkt smazany pomoci admin_delete (nastavi active zpet na true)
	 */
	function admin_activate($id) {
		if (!$id) {
			$this->Session->setFlash('Neexistující produkt.');
			$this->redirect(array('action'=>'index'), null, true);
		}

		// nactu si info o produktu, ktery budu mazat
		$this->Product->contain('CategoriesProduct');
		$product = $this->Product->read(null, $id);

		$product['Product']['active'] = true;
		if ($this->Product->save($product)) {
			$this->Session->setFlash('Produkt byl aktivován');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo aktivovat, opakujte prosím akci');
		}
		$this->redirect(array('controller' => 'categories', 'action' => 'list_products', $product['CategoriesProduct'][0]['category_id']), null, true);
	}
	
	function admin_delete_from_db($id) {
		if (!$id) {
			$this->Session->setFlash('Neexistující produkt.');
			$this->redirect(array('action'=>'index'), null, true);
		}

		// nactu si info o produktu, ktery budu mazat
		$this->Product->contain('CategoriesProduct', 'Subproduct');
		$product = $this->Product->read(null, $id);
		
		// musim vymazat vsechny subprodukty a obrazky
		foreach ($product['Subproduct'] as $subproduct) {
			$this->Product->Subproduct->AttributesSubproduct->deleteAll(array('subproduct_id' => $subproduct['id']));
			$this->Product->Subproduct->delete($subproduct['id']);
		}
		$this->Product->Image->deleteAllImages($id);

		if ($this->Product->delete($id)) {
			$this->Session->setFlash('Produkt byl vymazán z databáze.');
			$this->redirect(array('controller' => 'categories', 'action' => 'list_products', $product['CategoriesProduct'][0]['category_id']), null, true);
		}
	}
	
	function admin_category_actions($product_id, $category_id) {
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $product_id),
			'contain' => array(
				'CategoriesProduct' => array(
					'fields' => array('id'),
					'Category' => array(
						'fields' => array('name')
					)
				)
			),
			'fields' => array('id', 'name')
		));
		
		$theCategoriesProduct = $this->Product->CategoriesProduct->find('first', array(
			'conditions' => array(
				'CategoriesProduct.product_id' => $product_id,
				'CategoriesProduct.category_id' => $category_id
			),
			'contain' => array(),
			'fields' => array('id')
		));
		
		$categories = $this->Product->CategoriesProduct->Category->generatetreelist(array('not' => array('id' => array('5'))), '{n}.Category.id', '{n}.Category.name', ' - ');

		$this->set('product', $product);
		$this->set('opened_category_id', $category_id);
		$this->set('theCategoriesProduct', $theCategoriesProduct);
		$this->set('categories', $categories);
	}
	
	/**
	 * 
	 * Zduplikuje produkt do zvolene kategorie i s obrazky a subprodukty
	 * @param int $id - product_id
	 */
	function admin_copy($id) {
		if ( !isset($this->data) ){
			$this->Product->recursive = -1;
			$this->data = $this->Product->read(null, $id);

			$categories = $this->Product->CategoriesProduct->Category->generatetreelist(array('not' => array('id' => array('5'))), '{n}.Category.id', '{n}.Category.name', ' - ');
			$this->set(compact(array('categories')));
		} else {
			// nactu si data produktu
			$conditions = array(
				'Product.id' => $id
			);
			$contain = array(
				'Subproduct' => array(
					'AttributesSubproduct'
				),
				'Image'
			);
			
			$product = $this->Product->find('first', array(
				'conditions' => $conditions,
				'contain' => $contain
			));

			// predpoklad ze se vse provede spravne
			$this->Session->setFlash('Produkt byl zduplikován.');
			
			// zalozim produkt
			unset($this->Product->id);
			unset($product['Product']['id']);
			if ( $this->Product->save($product) ){
				// mam ulozeny produkt, musim zmenit URL produktu podle noveho ID
				$new_url = strip_diacritic($product['Product']['name']) . '-p' . $this->Product->id;
				
				// upravim URL pro duplikovany produkt
				if ( $this->Product->save(array('url' => $new_url), false) ){
					$result = $this->Product->copy_images($this->Product->id, $product['Image']);
					if ( $result !== true ){
						$this->Session->setFlash($result);
					} else {
						// zaradim produkt do nove kategorie
						$p2c_data = array(
							'category_id' => $this->data['Product']['category_id'],
							'product_id' => $this->Product->id
						);
						if ( !$this->Product->CategoriesProduct->save($p2c_data) ){
							$this->Session->setFlash('Nepodařilo se zařadit produkt do nové kategorie.');
						} else {
							// zkopiruju si subprodukty
							if ( !empty($product['Subproduct']) ){
								foreach( $product['Subproduct'] as $sp ){
									$sp_data = array(
										'product_id' => $this->Product->id,
										'price_with_dph' => $sp['price_with_dph'],
										'active' => $sp['active'],
										'availability_id' => $sp['availability_id']
									);
									unset($this->Product->Subproduct->id);
									if ( !$this->Product->Subproduct->save($sp_data) ){
										$this->Session->setFlash('Nepodařilo se duplikovat subproduct ID = ' . $sp['id'] );
									} else {
										// musim nakopirovat i vztahy mezi subprodukty a atributy
										foreach ($sp['AttributesSubproduct'] as $att_sp) {
											$att_sp_data = array(
												'attribute_id' => $att_sp['attribute_id'],
												'subproduct_id' => $this->Product->Subproduct->id
											);
											unset($this->Product->Subproduct->AttributesSubproduct->id);
											if (!$this->Product->Subproduct->AttributesSubproduct->save($att_sp_data)) {
												$this->Session->setFlash('Nepodařilo se duplikovat vztah mezi atributem a subproduktem ID = ' . $att_sp['id']);
											}
										}
									}
								}
							}
							
						}
					}
				} else {
					$this->Session->setFlash('Chyba při úpravě nového URL produktu.');
				}
			} else {
				$this->Session->setFlash('Chyba při zakládání produktu.');
			}
			
			$this->redirect(array('controller' => 'products', 'action' => 'category_actions', $id, $this->data['Product']['this_category_id']), null, true);
		}
	}
	/**

	 * Obsluhuje administraci subproduktu

	 *

	 * @param int $id - product_id

	 */

	function admin_add_subproducts($id) {
		if (isset($this->data)) {

			foreach ($this->data['Product'] as $subproduct_id => $subproduct) {
				$subproduct['Subproduct']['id'] = $subproduct_id;
				$subproduct['Subproduct']['active'] = $subproduct['active'];
				$subproduct['Subproduct']['availability_id'] = $subproduct['availability_id'];
				$subproduct['Subproduct']['pieces'] = $subproduct['pieces'];
				$subproduct['Subproduct']['price_with_dph'] = $subproduct['price_with_dph'];
					
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
		$this->redirect(array('controller' => 'products', 'action' => 'attributes_list', $id));
	}
	
	function admin_recomended() {
		$this->set('autocomplete', true);
		
		$recomended = $this->Product->find('all', array(
			'conditions' => array('Product.recomended' => true),
			'contain' => array(
				'Availability' => array(
					'fields' => array('cart_allowed')
				)
			),
			'fields' => array('Product.id', 'Product.name', 'Product.active', 'Product.retail_price_with_dph', 'Product.url')
		));
		
		$this->set('recomended', $recomended);
		
		$active_products = $this->Product->find('all', array(
			'conditions' => array(
				'Product.active' => true,
				'Product.recomended' => false
			),
			'contain' => array(),
			'fields' => array('id', 'name')
		));
		
		$autocomplete_active_products = array();
		foreach ($active_products as $active_product) {
			$autocomplete_active_products[] = array(
				'label' => iconv('windows-1250', 'utf-8', $active_product['Product']['name']),
				'value' => $active_product['Product']['id']
			);
		}
	
		if (!function_exists('json_encode')) {
			App::import('Vendor', 'Services_JSON', array('file' => 'JSON.php'));
			$json = &new Services_JSON();
			$return = $json->encode($autocomplete_active_products);
		} else {
			$return = json_encode($autocomplete_active_products);
		}
		$this->set('active_products', $return);
	}
	
	function admin_recomended_add() {
		if (isset($this->data)) {
			// doporucenych produktu muze byt nejvic 9, pokud je jich 9, nemuzu pridat novy
			$recomended_count = $this->Product->find('count', array(
				'conditions' => array('Product.recomended' => true)
			));
			
			if ($recomended_count < 9) {
				$this->data['Product']['recomended'] = true;
				if ($this->Product->save($this->data, false)) {
					$this->Session->setFlash('Produkt ' . $this->data['Product']['name'] . ' byl označen jako doporučený');
				} else {
					$this->Session->setFlash('Produkt ' . $this->data['Product']['name'] . ' se nepodařilo označit jako doporučený, opakujte prosím akci');
				}
			} else {
				$this->Session->setFlash('Doporučených produktů může být nejvíce 9, před vložením dalšího prosím některý ze stávajících odstraňte.');
			}
		}
		$this->redirect(array('controller' => 'products', 'action' => 'recomended'));
	}
	
	/**
	 * 
	 * Odstrani produkt ze seznamu doporucenych produktu
	 * @param int $id - id odstranovaneho produktu
	 */
	function admin_recomended_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán produkt, který chcete odstranit ze seznamu doporučených');
			$this->redirect(array('controller' => 'products', 'action' => 'recomended'));
		}
		
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(),
			'fields' => array('id', 'name')
		));
		
		if (empty($product)) {
			$this->Session->setFlash('Požadovaný produkt neexistuje');
			$this->redirect(array('controller' => 'products', 'action' => 'recomended'));
		}
		
		$product['Product']['recomended'] = false;
		if ($this->Product->save($product, false)) {
			$this->Session->setFlash('Produkt ' . $product['Product']['name'] . ' byl odstraněn ze seznamu doporučených');
			$this->redirect(array('controller' => 'products', 'action' => 'recomended'));
		}
	}

	/**
	 * 
	 * Sprava nejprodavanejsich produktu na hp
	 */
	function admin_most_sold() {
		$this->set('autocomplete', true);
		
		$most_sold = $this->Product->find('all', array(
			'conditions' => array('Product.most_sold' => true),
			'contain' => array(
				'Availability' => array(
					'fields' => array('cart_allowed')
				)
			),
			'fields' => array('Product.id', 'Product.name', 'Product.active', 'Product.retail_price_with_dph', 'Product.url')
		));
		
		$this->set('most_sold', $most_sold);
		
		$active_products = $this->Product->find('all', array(
			'conditions' => array(
				'Product.active' => true,
				'Product.most_sold' => false
			),
			'contain' => array(),
			'fields' => array('id', 'name')
		));
		
		$autocomplete_active_products = array();
		foreach ($active_products as $active_product) {
			$autocomplete_active_products[] = array(
				'label' => $active_product['Product']['name'],
				'value' => $active_product['Product']['id']
			);
		}
		
		if (!function_exists('json_encode')) {
			App::import('Vendor', 'Services_JSON', array('file' => 'JSON.php'));
			$json = &new Services_JSON();
			$return = $json->encode($autocomplete_active_products);
		} else {
			$return = json_encode($autocomplete_active_products);
		}
	
		$this->set('active_products', $return);
	}
	
	function admin_most_sold_add() {
		if (isset($this->data)) {
			// nejprodavanejsich produktu muze byt nejvic 3, pokud je jich 3, nemuzu pridat novy
			$most_sold_count = $this->Product->find('count', array(
				'conditions' => array('Product.most_sold' => true)
			));
			
			if ($most_sold_count < 3) {
				$this->data['Product']['most_sold'] = true;
				if ($this->Product->save($this->data, false)) {
					$this->Session->setFlash('Produkt ' . $this->data['Product']['name'] . ' byl označen jako nejprodávanější');
				} else {
					$this->Session->setFlash('Produkt ' . $this->data['Product']['name'] . ' se nepodařilo označit jako nejprodávanější, opakujte prosím akci');
				}
			} else {
				$this->Session->setFlash('nejprodávanějších produktů může být nejvíce 9, před vložením dalšího prosím některý ze stávajících odstraňte.');
			}
		}
		$this->redirect(array('controller' => 'products', 'action' => 'most_sold'));
	}
	
	/**
	 * 
	 * Odstrani produkt ze seznamu nejprodavanejsich produktu
	 * @param int $id - id odstranovaneho produktu
	 */
	function admin_most_sold_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán produkt, který chcete odstranit ze seznamu nejprodávanějších');
			$this->redirect(array('controller' => 'products', 'action' => 'most_sold'));
		}
		
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(),
			'fields' => array('id', 'name')
		));
		
		if (empty($product)) {
			$this->Session->setFlash('Požadovaný produkt neexistuje');
			$this->redirect(array('controller' => 'products', 'action' => 'most_sold'));
		}
		
		$product['Product']['most_sold'] = false;
		if ($this->Product->save($product, false)) {
			$this->Session->setFlash('Produkt ' . $product['Product']['name'] . ' byl odstraněn ze seznamu nejprodávanějších');
			$this->redirect(array('controller' => 'products', 'action' => 'most_sold'));
		}
	} 	

	/**
	 * 
	 * Sprava nejnovejsich produktu na hp
	 */
	function admin_newest() {
		$this->set('autocomplete', true);
		
		$newest = $this->Product->find('all', array(
			'conditions' => array('Product.newest' => true),
			'contain' => array(
				'Availability' => array(
					'fields' => array('cart_allowed')
				)
			),
			'fields' => array('Product.id', 'Product.name', 'Product.active', 'Product.retail_price_with_dph', 'Product.url')
		));
		
		$this->set('newest', $newest);
		
		$active_products = $this->Product->find('all', array(
			'conditions' => array(
				'Product.active' => true,
				'Product.newest' => false
			),
			'contain' => array(),
			'fields' => array('id', 'name')
		));
		
		$autocomplete_active_products = array();
		foreach ($active_products as $active_product) {
			$autocomplete_active_products[] = array(
				'label' => $active_product['Product']['name'],
				'value' => $active_product['Product']['id']
			);
		}
		
		if (!function_exists('json_encode')) {
			App::import('Vendor', 'Services_JSON', array('file' => 'JSON.php'));
			$json = &new Services_JSON();
			$return = $json->encode($autocomplete_active_products);
		} else {
			$return = json_encode($autocomplete_active_products);
		}
		
		$this->set('active_products', $return);
	}
	
	function admin_newest_add() {
		if (isset($this->data)) {
			// nejnovějších produktu muze byt nejvic 3, pokud je jich 3, nemuzu pridat novy
			$newest_count = $this->Product->find('count', array(
				'conditions' => array('Product.newest' => true)
			));
			
			if ($newest_count < 3) {
				$this->data['Product']['newest'] = true;
				if ($this->Product->save($this->data, false)) {
					$this->Session->setFlash('Produkt ' . $this->data['Product']['name'] . ' byl označen jako nejnovější');
				} else {
					$this->Session->setFlash('Produkt ' . $this->data['Product']['name'] . ' se nepodařilo označit jako nejnovější, opakujte prosím akci');
				}
			} else {
				$this->Session->setFlash('nejnovějších produktů může být nejvíce 9, před vložením dalšího prosím některý ze stávajících odstraňte.');
			}
		}
		$this->redirect(array('controller' => 'products', 'action' => 'newest'));
	}
	
	/**
	 * 
	 * Odstrani produkt ze seznamu doporucenych produktu
	 * @param int $id - id odstranovaneho produktu
	 */
	function admin_newest_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán produkt, který chcete odstranit ze seznamu nejnovějších');
			$this->redirect(array('controller' => 'products', 'action' => 'newest'));
		}
		
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(),
			'fields' => array('id', 'name')
		));
		
		if (empty($product)) {
			$this->Session->setFlash('Požadovaný produkt neexistuje');
			$this->redirect(array('controller' => 'products', 'action' => 'newest'));
		}
		
		$product['Product']['newest'] = false;
		if ($this->Product->save($product, false)) {
			$this->Session->setFlash('Produkt ' . $product['Product']['name'] . ' byl odstraněn ze seznamu nejnovějších');
			$this->redirect(array('controller' => 'products', 'action' => 'newest'));
		}
	} 
	
	function admin_wout_ean() {
		$products = $this->Product->find('all', array(
			'conditions' => array(
				'OR' => array(
					array('Product.ean' => null),
					array('Product.ean' => '')
				),
				'Product.active' => true
			),
			'contain' => array(
				'CategoriesProduct' => array(
					'fields' => array('CategoriesProduct.category_id'),
					'limit' => 1
				)
			),
			'fields' => array('Product.id', 'Product.name')
		));

		$this->set('products', $products);
	}

	/*
	 * @description				Vytahne z databaze podle rucne definovaneho seznamu nejprodavanejsi produkty.
	 */
	function homepage_list($id = null){
		return $this->Product->homepage_list($id);
	}

	function suggested(){
		$suggested_ids = array(121, 38, 22, 41, 32);
		$this->Product->recursive = 1;
		$products = $this->Product->find('all', array(

			'conditions' => array('id' => $suggested_ids)

		));
		return $products;
	}

	function page_newest_products(){
		// navolim si layout, ktery se pouzije
		$this->layout = 'front_end';
		
		// vyhledam nejnovejsich 10 produktu
		$products = $this->Product->find('all', array('limit' => 10, 'order' => array('Product.created' => 'desc')));
		$this->set('products', $products);
		
		// urcim si jak se zobrazi produkty
		// bud jako mrizka(tabulka) s obrazky
		// nebo jako jednoduchy seznam
		$listing_style = 'products_listing_grid';
		if (
			( isset($this->params['named']['ls']) && $this->params['named']['ls'] == 'list' )
			|| ( isset($this->params['url']['ls']) && $this->params['url']['ls'] == 'list' )
		 ){
			$listing_style = 'products_listing_list';
		}
		$this->set('listing_style', $listing_style);
		
	}
	
	function newest(){
		return $this->Product->newest();
	}
	
	function generateSeo(){
		$this->Product->recursive = -1;
		$products = $this->Product->find('all');
		
		foreach ( $products as $product ){
			$this->Product->id = $product['Product']['id'];
			$product['Product']['title'] = $product['Product']['name'];
			$product['Product']['url'] = strip_diacritic($product['Product']['name']) . '-p' . $product['Product']['id'];
			$this->Product->save($product);
		}
		
		debug($products);
		die();
	}

	function view_comments($id = null) {
		if ( isset($id) ) {
			// nactu si info o produktu
			$product = $this->Product->find('first', array(
				'conditions' => array('Product.id' => $id),
				'contain' => array('CategoriesProduct')
			));
			
			$opened_category_id = $product['CategoriesProduct'][0]['category_id'];
			$this->set('opened_category_id', $opened_category_id);
			
			$breadcrumbs = $this->Product->CategoriesProduct->Category->getPath($opened_category_id);
			if (!empty($breadcrumbs)) {
				$breadcrumbs[0]['Category']['name'] = 'Domů';
			}
			unset($breadcrumbs[1]);
			$this->set('breadcrumbs', $breadcrumbs);

			$comments = $this->Product->Comment->find('all', array(
				'conditions' => array(
					'Comment.product_id' => $id,
					'Comment.confirmed' => 1
				),
				'order' => array('Comment.created' => 'ASC')
			));
			
			// layout pro stranku
			$this->layout = 'product_detail';
			
			// sestavim breadcrumbs
			$path = $this->Product->CategoriesProduct->Category->getPath($opened_category_id);
			$breadcrumbs = array();
			foreach ($path as $item) {
				$breadcrumb = array('anchor' => $item['Category']['name'], 'href' => '/' . $item['Category']['url']);
				if ($item['Category']['id'] == 5) {
					$breadcrumb = array('anchor' => 'Domů', 'href' => HP_URI);
				}
				$breadcrumbs[] = $breadcrumb;
			}
			$breadcrumbs[] = array('anchor' => $product['Product']['breadcrumb'], 'href' => '/' . $product['Product']['url']);
			$breadcrumbs[] = array('anchor' => 'Komentáře', 'href' => '/products/view_comments/' . $product['Product']['id']);
			$this->set('breadcrumbs', $breadcrumbs);
			
			// titulek a popis stranky
			$this->set('title_for_content', 'Dotazy, komentáře a diskuse k ' . $product['Product']['name']);
			$this->set('description_for_content', $product['Product']['name'] . ' diskuse, dotazy a komentáře.');
						
			// posunu detaily o produktu
			$this->set('product', $product);
			
			// nalezene komentare
			$this->set('comments', $comments);
			
			// zakazu indexovani pokud produkt nema zadny komentar
			if ( empty( $comments ) ){
				$this->set('meta', array('robots' => '<meta name="robots" content="noindex, nofollow" />'));
			}
		} else {
			$this->redirect("/", null, true);
		}
	}
} // konec tridy
?>
