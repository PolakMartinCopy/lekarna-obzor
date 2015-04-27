<?php
class Category extends AppModel {

	var $name = 'Category';
	
	var $actsAs = array('Tree', 'Menu', 'Containable');
	
	var $validate = array(
		'parent_id' => array(
			'rule' => 'numeric'
		),
		'name' => array(
			'rule' => array('minLength', 1)
		),
	);

	var $hasMany = array(
		'CategoriesProduct' => array(
			'dependent' => true
		),
		'CategoriesMostSoldProduct' => array(
			'dependent' => true
		)
	);
	
	// id kategorii, ktere se nemaji zobrazovat v levem menu
	var $unactive_categories_ids = array();
	
	function countProducts($id){
		// spocita mi kolik aktivnich produktu obsahuje dana kategorie
		$result = $this->CategoriesProduct->find('count', array(
			'conditions' => array(
				'CategoriesProduct.category_id' => $id
			),
			'contain' => array()
		));

		return $result;
	}

	function countActiveProducts($id){
		// spocita mi kolik aktivnich produktu obsahuje dana kategorie
		$result = $this->CategoriesProduct->find('count', array(
			'conditions' => array(
				'CategoriesProduct.category_id' => $id,
				'Product.active' => true
			),
			'contain' => array('Product')
		));

		return $result;
	}
	
	function getSubcategoriesMenuList($start_node = null, $opened_category_id = null){
		if ( !function_exists('skip_node') ){
			function skip_node($start_node, $path){
				$has_it = false;
				if ( isset($start_node) ){
					$new_path = array();
					for (  $i = 0; $i < count($path); $i++ ){
						if ( !$has_it ){
							if ( $path[$i]['Category']['id'] != $start_node ){
								continue;
							} else {
								$new_path[] = $path[$i];
								$has_it = true;
							}
						} else {
							// uz jen kopiruju
							$new_path[] = $path[$i];
						}
					}
					return $new_path;
				}
			}
		}
		
		$this->id = $opened_category_id;
		$fields = array('id', 'name'); // seznam poli, ktera potrebuji z databaze ohledne kategorii
		$path = $this->getPath($this->id, $fields, -1);

		$path = skip_node($start_node, $path);
		
		if ( empty($path) ){
			$path = $this->getPath($start_node, $fields, -1);
		}
		
		$path = skip_node($start_node, $path);
		$path_ids = Set::extract('/Category/id', $path);

		$ids_to_find = array();
		foreach ( $path as $category ){
			$ids_to_find[] = $category['Category']['id'];
		}
		
		$categories = $this->find('threaded', array(
			'conditions' => array(
				"parent_id IN ('" . implode("', '", $ids_to_find) . "')",
//				'id NOT IN (' . implode(',', $this->unactive_categories_ids) . ')'			
			),
			'order' => array("lft" => 'asc'),
			'contain' => array()
		));

		// ke kazde kategorii si zjistim kolik ma
		// v sobe produktu
/*		foreach( $categories as $key => $value ){
			$categories[$key]['Category']['productCount'] = $this->countProducts($categories[$key]['Category']['id']);
		}*/

		return array(
			'categories' => $categories,
			'ids_to_find' => $ids_to_find,
			'opened_category_id' => $opened_category_id,
			'path_ids' => $path_ids
		);
	}
	
	/**
	 * vrati seznam kategorii na prvni urovni a jejich podkategorii pro vypis na hlavni strance shopu
	 */
	function get_homepage_list() {
		// ROOT ma id 5
		$category_id = 5;
		
		$categories = $this->find('all', array(
			'conditions' => array('parent_id' => $category_id),
			'contain' => array(),
			'fields' => array('id', 'name', 'url'),
			'order' => array('lft' => 'asc')
		));
		
		foreach ($categories as &$category) {
			$category['children'] = $this->find('all', array(
				'conditions' => array('parent_id' => $category['Category']['id']),
				'contain' => array(),
				'fields' => array('id', 'name', 'url'),
				'order' => array('lft' => 'asc')
			));
		}
		
		return $categories;
	}
	
	function get_subcategories_ids($category_id) {
		$result = array();

		$result[] = $category_id;
		$subcategories = $this->find('all', array(
			'conditions' => array('parent_id' => $category_id),
			'contain' => array(),
			'fields' => array('id')
		));
		
		if (!empty($subcategories)) {
			foreach ($subcategories as $subcategory) {
				$result = array_merge($result, $this->get_subcategories_ids($subcategory['Category']['id']));
			}
		}
		
		return $result;
	}
	
	// zjisti a ulozi nejprodavanejsi produkty v kategorii
	function set_most_sold($id) {
		// zjistim idcka daneho podstromu kategorii
		$subtree_categories = $this->get_subcategories_ids($id);
		//	najdu 3 nevice prodavane aktivni produkty
		$products = $this->CategoriesProduct->find('all', array(
			'conditions' => array(
				'CategoriesProduct.category_id' => $subtree_categories,
				'NOT' => array('OrderedProduct.product_quantity' => null),
				'Product.active' => true,
				'Availability.cart_allowed' => true
			),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'ordered_products',
					'alias' => 'OrderedProduct',
					'type' => 'LEFT',
					'conditions' => array('OrderedProduct.product_id = CategoriesProduct.product_id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'INNER',
					'conditions' => array('Product.id = CategoriesProduct.product_id')	
				),
				array(
					'table' => 'availabilities',
					'alias' => 'Availability',
					'type' => 'LEFT',
					'conditions' => array('Product.availability_id = Availability.id')
				)
			),
			'fields' => array('CategoriesProduct.product_id', 'CategoriesProduct.quantity'),
			'group' => 'CategoriesProduct.id',
			'limit' => 3,
			'order' => array('CategoriesProduct.quantity' => 'desc')
		));

		// zapamatuju si je v db
		foreach ($products as $product) {
			$save = array(
				'CategoriesMostSoldProduct' => array(
					'category_id' => $id,
					'product_id' => $product['CategoriesProduct']['product_id']
				)
			);
		
			$this->CategoriesMostSoldProduct->create();
			if (!$this->CategoriesMostSoldProduct->save($save)) {
				return false;
			}
		}
		return true;
	}
	
	// vrati nejprodavanejsi produkty dane kategorie
	function get_most_sold($id) {
		// vytahnu si nejprodavanejsi produkty ulozene v db pro danou kategorii
		$products = $this->CategoriesProduct->Category->CategoriesMostSoldProduct->find('all', array(
			'conditions' => array(
				'CategoriesMostSoldProduct.category_id' => $id
			),
			'contain' => array(
				'Product' => array(
					'Image' => array(
						'conditions' => array('Image.is_main' => '1'),
						'fields' => array('Image.id', 'Image.name')
					),
					'fields' => array('Product.id', 'Product.name', 'Product.url')
				)
			)
		));

		// produkty maji byt 3 (vic se jich tam pri generovani neulozi
		// ale muze jich byt min, proto nahodne vyberu zbytek z dane kategorie
		if (count($products) < 3) {
			$product_ids = Set::extract('/Product/id', $products);
			$subtree_ids = $this->get_subcategories_ids($id);
			// vyberu nahodne produkt z kategorie a vlozim ho do pole nejprodavanejsich
			$complement_products = $this->CategoriesProduct->find('all', array(
				'conditions' => array(
					'CategoriesProduct.category_id' => $subtree_ids,
					'NOT' => array('CategoriesProduct.product_id' => $product_ids),
					'Product.active' => true,
					'Availability.cart_allowed' => true
				),
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'products',
						'alias' => 'Product',
						'type' => 'INNER',
						'conditions' => array('Product.id = CategoriesProduct.product_id')
					),
					array(
						'table' => 'availabilities',
						'alias' => 'Availability',
						'type' => 'INNER',
						'conditions' => array('Product.availability_id = Availability.id')
					)
				),
/*				'contain' => array(
					'Product' => array(
						'Image' => array(
							'conditions' => ,
							'fields' => array('Image.id', 'Image.name')
						),
						'fields' => array('Product.id', 'Product.name', 'Product.url')
					)
				), */
				'fields' => array('Product.id', 'Product.name', 'Product.url', 'Availability.*'),
				'limit' => 3-count($products),
				'order' => 'Rand()'
			));

			foreach ($complement_products as &$complement_product) {
				$image = $this->CategoriesProduct->Product->Image->find('first', array(
					'conditions' => array('Image.is_main' => '1', 'Image.product_id' => $complement_product['Product']['id']),
					'contain' => array(),
					'fields' => array('Image.id', 'Image.name')
				));
				$complement_product['Product']['Image'] = array();
				if (!empty($image)) {
					$complement_product['Product']['Image'][0] = $image['Image'];
				}
			}

			$products = array_merge($products, $complement_products);
		}
		return $products;
	}
}
?>