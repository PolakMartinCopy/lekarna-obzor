<?php
class Product extends AppModel {
	var $name = 'Product';
	
	var $actsAs = array('Containable');
	
	var $validate = array(
		'name' => array(
			'rule' => array('minLength', 1),
			'message' => 'Název produktu musí být vyplněn!'
		),
		'short_description' => array(
			'rule' => array('minLength', 1),
			'message' => 'Krátký popis produktu musí být vyplněn!'
		),
		'retail_price_with_dph' => array(
			'rule' => array('minLength', 1),
			'message' => 'Cena produktu musí být vyplněna!'
		),
	);

	var $hasAndBelongsToMany = array(
		'Cart' => array('className' => 'Cart'),
		'Flag' => array('className' => 'Flag')
	);

	var $hasMany = array(
		'Subproduct' => array(
			'className' => 'Subproduct'
		),
		'Image' =>array(
			'className' => 'Image'
		),
		'CartsProduct' => array(
			'className' => 'CartsProduct'
		),
		'Comment' => array(
			'className' => 'Comment'
		),
		'CategoriesProduct' => array(
			'dependent' => true
		),
		'RelatedProduct' => array(
			'dependent' => true
		),
		'RequestForm' => array(
			'dependent' => true
		),
		'CategoriesMostSoldProduct' => array(
			'dependent' => true
		),
		'NewslettersProduct' => array(
			'dependent' => true
		)
	);

	var $belongsTo = array(
			'Manufacturer' => array('className' => 'Manufacturer',
								'foreignKey' => 'manufacturer_id',
								'conditions' => '',
								'fields' => '',
								'order' => '',
								'counterCache' => ''),
			'TaxClass' => array('className' => 'TaxClass',
								'foreignKey' => 'tax_class_id',
								'conditions' => '',
								'fields' => '',
								'order' => '',
								'counterCache' => ''),
			'Availability',
			'ProductType'
	);
	
	function beforeSave() {
		// sprava volby zobrazeni skupiny, ean, sukl atd...
		// pokud neni zaskrtnuty checkbox potvrzujici, ze chci data zobrazit v detailu, odpovidajici data odnastavim
		$fields = array('product_type_id', 'code', 'ean', 'sukl', 'group');
		foreach ($fields as $field) {
			if (isset($this->data['Product']['show_' . $field]) && !$this->data['Product']['show_' . $field] && isset($this->data['Product'][$field])) {
				$this->data['Product'][$field] = null;
			}
			
			if ($this->data['Product'][$field] == '') {
				$this->data['Product'][$field] = null;
			}
		}

		return true;
	}

	function assign_discount_price($product){
		App::import('Helper', 'Session');
		$this->Session = new SessionHelper;

		$discount_price = $product['Product']['retail_price_with_dph'];
		
		// vychozi sleva je obecna sleva
		if ($product['Product']['discount_common'] > 0 && $product['Product']['discount_common'] < $discount_price) {
			$discount_price = $product['Product']['discount_common'];
		}
		
		// jestlize je uzivatel prihlaseny
		if ($this->Session->check('Customer')) {
			// podivam se, jestli je zadana sleva pro prihlasene a je mensi, nez obecna sleva
			if (isset($product['Product']['discount_member']) && $product['Product']['discount_member'] > 0 && $product['Product']['discount_member'] < $discount_price) {
				// kdyz jo, tak ji dam jako vyslednou slevu
				$discount_price = $product['Product']['discount_member'];
			}
		}
		
		return $discount_price;
	}
	
	function copy_images($new_product_id, $images){
		if ( !empty($images) ){
			foreach ( $images as $image ){
				// zkopiruju fyzicky na disku
				if (file_exists('product-images/' . $image['name'])){
					// zjistim si jmeno obrazku a musim ho prejmenovat
					$image_name = explode('.', $image['name']);
					$i = 1;
					while ( file_exists('product-images/' . $image_name[0] . '_' . $i . '.jpg') ){
						$i = $i + 1;
					}
					
					// vim jake muzu dat nove jmeno obrazku
					// obstaram na disku kopii obrazku
					if ( !copy('product-images/' . $image['name'], 'product-images/' . $image_name[0] . '_' . $i . '.jpg') ){
						return 'Nepodařilo se zkopírovat obrázek ' . $image['name'] . ' do ' . $image_name[0] . '_' . $i . '.jpg';
					}
					
					if ( !copy('product-images/small/' . $image['name'], 'product-images/small/' . $image_name[0] . '_' . $i . '.jpg') ){
						return 'Nepodařilo se zkopírovat SMALL obrázek ' . $image['name'] . ' do ' . $image_name[0] . '_' . $i . '.jpg';
					}
					
					if ( !copy('product-images/medium/' . $image['name'], 'product-images/medium/' . $image_name[0] . '_' . $i . '.jpg') ){
						return 'Nepodařilo se zkopírovat MEDIUM obrázek ' . $image['name'] . ' do ' . $image_name[0] . '_' . $i . '.jpg';
					}
				} else {
					return 'Nepodařilo se nalézt obrázek ' . $image['name'] . ' na disku.';
				}

				// vyresetuju si ID obrazku
				unset($this->Image->id);

				$new_image_data = array(
					'name' => $image_name[0] . '_' . $i . '.jpg',
					'product_id' => $new_product_id,
					'is_main' => $image['is_main']
				);
				
				if ( !$this->Image->save($new_image_data) ){
					return 'Nepodařilo se uložit nový obrázek ' . $new_image_data['name'] . ' do databáze.';
				}
			}
		}
		return true;
	}

	function get_subproducts($id){
		$options = $this->Subproduct->AttributesSubproduct->Attribute->Option->find('list');
		
		// projdu si existujici atributy a k nim si priradim subprodukty
		$subs = array();
		$hasAttributes = false;
		$this->Subproduct->unbindModel(array('belongsTo' => array('Product')));
		$subproducts = $this->Subproduct->find('list', array(
			'conditions' => array('product_id' => $id),
			'contain' => array()
		));
		//
		foreach ( $options as $option => $value ){
			
			$attributes = $this->Subproduct->AttributesSubproduct->find('all',
				array(
					'conditions' => array(
						'AttributesSubproduct.subproduct_id' => $subproducts,
						'Attribute.option_id' => $option
					),
					'contain' => array(
						'Attribute'
					),
					'order' => array('Attribute.sort_order' => 'asc'),
					'fields' => array('DISTINCT Attribute.id', 'Attribute.value')
				)
			);

			if ( $this->Subproduct->AttributesSubproduct->getNumRows() > 0 ){
				$hasAttributes = true;
			}
			$subs[$option] = array('Option' => array('name' => $options[$option], 'id' => $option));
			foreach ( $attributes as $attribute ){
				$subs[$option]['Value'][] = array(
					'id' => $attribute['Attribute']['id'],
					'name' => $options[$option],
					'value' => $attribute['Attribute']['value'],
				);
			}
		}
		if ( !$hasAttributes ){
			$subs = null;
		}
		
		return $subs;
	}
	
	function checkSubproductChoices($data){
		// zkontrolujeme, jestli pri vkladani do kosiku
		// jsou zvolene vsechny atributy produktu,
		// ktere se zvolit daji

		// nacitam si vsechny options ktere produkt muze mit
		$options = $this->optionsList($data['Product']['id']);

		// pokud nejake muze mit, musim zkontrolovat zda jsou zadany
		if ( !empty($options) ){
			foreach ( $options as $option ){
				$index = $option['Attribute']['option_id'];
				if ( !isset($data['Product']['Option'][$index]) || empty($data['Product']['Option'][$index])  ){
					return false;
				}
			}
		}
		return true;
	}

	function optionsList($id){
		return $this->query('SELECT DISTINCT (Attribute.option_id) FROM attributes Attribute, subproducts s WHERE s.product_id = ' . $id . ' AND s.attribute_id = Attribute.id');
	}

			/**
	 * z atributu produktu tvori vsechny jejich mozne kombinace 
	 *
	 * @param pole atributu $array
	 * @return pole vsech moznych kombinaci vstupnich atributu
	 */
	function combine($array) {
		$res = array();
		if (!empty($array)) {
			$first = current($array);
			array_shift($array);
			$tail = $array;
			if (empty($tail)) {
				foreach ($first as $item) {
					$res[] = array($item);
				}
			} else {
				foreach ($first as $item) {
					foreach ($this->combine($tail) as $j) {
						$res[] = array_merge(array($item), $j);
					}
				}
			}
		}
		return $res;
	}
	
	function newest() {
		$products = $this->find('all', array(
			'order' => array('created' => 'desc'),
			'limit' => 10,
			'fields' => array('id', 'created'),
			'recursive' => -1
		));
		$index = rand(0, 9);

		$newest_product = $this->find('first', array(
			'conditions' => array('Product.id' =>  $products[$index]['Product']['id']),
			'contain' => array('Image')
		));
		
		// vyhledam si cenu, zda neni zlevnen
		$newest_product['Product']['discount_price'] = $this->assign_discount_price($newest_product);
		return array('newest_product' => $newest_product);
	}
	
	function get_list($id = null){
		// option nese informaci o tom, ktery seznam chceme publikovat
		switch ( $id ){
			case "most_sold":
				// seznam nejprodavanejsich produktu
				$most_sold = $this->find('all', array(
					'conditions' => array('Product.most_sold' => true, 'active' => true),
					'contain' => array(),
					'fields' => array('id')
				));
				$list = Set::extract('/Product/id', $most_sold);
			break;
			case "suggested":
				// seznam doporucenych produktu
				$recomended = $this->find('all', array(
					'conditions' => array('Product.recomended' => true, 'active' => true),
					'contain' => array(),
					'fields' => array('id')
				));
				$list = Set::extract('/Product/id', $recomended);
			break;
			case "newest":
				// seznam doporucenych produktu
				$newest = $this->find('all', array(
					'conditions' => array('Product.newest' => true, 'active' => true),
					'contain' => array(),
					'fields' => array('id')
				));
				$list = Set::extract('/Product/id', $newest);
			break;
			
		}

		// seznam vlozim do podminek
		$conditions = array(
			'Product.id' => $list,
		);
		
		// vyhledam si produkty
		$products = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(
				'Image' => array(
					'conditions' => array('is_main' => '1')
				)
			),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.url',
//				'Product.description',
//				'Product.retail_price_with_dph',
//				'Product.discount_common',
//				'Product.discount_member'
			)
		));
		
		// priradim k nim zlevnene ceny, pokud nejake jsou
//		$count = count($products);
//		for ( $i = 0; $i < $count; $i++ ){
//			$products[$i]['Product']['discount_price'] = $this->assign_discount_price($products[$i]);
//		}
		return $products;
	}
	
	function sort_by_price($products, $direction){
		function sort_by_final_price_desc($a, $b){
			$a_final_price = $a['Product']['retail_price_with_dph'];
			if ( !empty($a['Product']['discount_price']) ){
				$a_final_price = $a['Product']['discount_price'];
			}
			
			$b_final_price = $b['Product']['retail_price_with_dph'];
			if ( !empty($b['Product']['discount_price']) ){
				$b_final_price = $b['Product']['discount_price'];
			}
			
			return $a_final_price < $b_final_price;
		}

		function sort_by_final_price_asc($a, $b){
			$a_final_price = $a['Product']['retail_price_with_dph'];
			if ( !empty($a['Product']['discount_price']) ){
				$a_final_price = $a['Product']['discount_price'];
			}
			
			$b_final_price = $b['Product']['retail_price_with_dph'];
			if ( !empty($b['Product']['discount_price']) ){
				$b_final_price = $b['Product']['discount_price'];
			}
			
			return $b_final_price < $a_final_price;
		}

		usort($products, 'sort_by_final_price' . $direction);
		return $products;
	}
	
	function sort_products($products, $subject, $direction) {
		if ($subject == 'price') {
			$products = $this->sort_by_price($products, '_' . $direction);
		}
		
		return $products;
	}
	
	function isRecommendedLetterPossible($id) {
		App::import('Model', 'Shipping');
		$this->Shipping = &new Shipping;
		// mame tam zpusoby dopravy doporucenym psanim, ktere se maji zobrazit ale pouze v pripade, ze:
		//		- v kosiku (objednavce) jsou pouze produkty z definovanych kategorii
		//		- v kosiku (objednavce) je mene nez maximalni definovany pocet produktu
		// zpusoby dopravy doporucenym psanim jsou definovany jako properties objektu Order
		// kategorie definovane pro zpusob dopravy doporucenym psanim
		$recommended_letter_category_ids = $this->Shipping->recommended_letter_category_ids;
	
		$product_categories = $this->CategoriesProduct->find('all', array(
			'conditions' => array('CategoriesProduct.product_id' => $id),
			'contain' => array()
		));
	
		$product_categories_ids = Set::extract('/CategoriesProduct/category_id', $product_categories);
		// pokud je prazdny prunik pole s definovanymi kategoriemi a pole s kategoriemi produktu, produkt neni v
		// nektere z definovanych kategorii a nemuzu ho poslat doporucenym psanim
		$category_ids_intersect = array_intersect($recommended_letter_category_ids, $product_categories_ids);
		if (empty($category_ids_intersect)) {
			return false;
		}
		return true;
	}
	
}
?>