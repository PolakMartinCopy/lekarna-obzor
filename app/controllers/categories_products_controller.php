<?php
class CategoriesProductsController extends AppController {
	var $name = 'CategoriesProducts';
	
	var $helpers = array('Form');
	
	var $paginate = array(
				'limit' => 50,
				'order' => array(
					'CategoriesProduct.product_id' => 'desc'
				),
		);
	
	function admin_add($product_id){
		// zkopiruje produkt do vybrane kategorie
		if ( !isset($this->data) ){
			// nactu si data o produktu
			$product = $this->CategoriesProduct->Product->find('first', array(
				'conditions' => array('Product.id' => $product_id),
				'contain' => array(),
				'fields' => array('id', 'name')
			));
			
			$this->data['CategoriesProduct']['product_id'] = $product_id;
			
			// najdu si kategorie, kam uz produkt patri
			$contained = $this->CategoriesProduct->find('all', array(
				'conditions' => array('product_id' => $product_id),
				'contain' => array('Category')
			));
			
			// nactu si strom kategorií
			$categories = $this->CategoriesProduct->Category->generatetreelist(array('not' => array('id' => array('5'))), '{n}.Category.id', '{n}.Category.name', ' - ');
			$this->set(compact(array('categories', 'contained', 'product')));
		} else {
			$this->CategoriesProduct->create();
			if ($this->CategoriesProduct->save($this->data['CategoriesProduct'])) {
				$this->Session->setFlash('Produkt byl zkopírován.');
				$this->redirect(array('controller' => 'products', 'action' => 'category_actions', $this->data['CategoriesProduct']['product_id'], $this->data['CategoriesProduct']['category_id']), null, true);
			} else {
				$this->Session->setFlash('Produkt nemohl být zkopírován, došlo k chybě.');
			}
		}
	}
		
	// presun produktu mezi kategoriemi
	function admin_edit($id){
		// presune produkt do vybrane kategorie
		if ( !isset($this->data) ){ // jeste nebyl odeslan form

			// nactu si data
			$this->data = $this->CategoriesProduct->read(null, $id);

			// nactu si strom kategorii
			$categories = $this->CategoriesProduct->Category->generatetreelist(array('not' => array('id' => array('1'))), '{n}.Category.id', '{n}.Category.name', ' - ');
			$this->set(compact(array('categories')));
		} else {
			if ($this->CategoriesProduct->save($this->data)) {
				$this->Session->setFlash('Produkt byl přesunut.');
				$this->redirect(array('controller' => 'products', 'action' => 'category_actions', $this->data['CategoriesProduct']['product_id'], $this->data['CategoriesProduct']['category_id']), null, true);
			} else {
				$this->Session->setFlash('Produkt nemohl být přesunut.');
			}
		}
	}
	
	function admin_delete($id, $category_id) {
		$categories_product = $this->CategoriesProduct->find('first', array(
			'conditions' => array('CategoriesProduct.id' => $id),
			'contain' => array('Product', 'Category')
		));
		if ($this->CategoriesProduct->delete($id)) {
			$this->Session->setFlash('Produkt ' . $categories_product['Product']['name'] . ' byl odstraněn z kategorie ' . $categories_product['Category']['name']);
		} else {
			$this->Session->setFlash('Product se z kategorie nepodařilo odstranit, opakujte prosím akci');
		}
		
		$redirect_category_id = $category_id;

		$this->redirect(array('controller' => 'products', 'action' => 'category_actions', $categories_product['CategoriesProduct']['product_id'], $redirect_category_id));
	}
	
	function view($id = null){
		if (!$id) {
			die('neni zvolena kategorie, kterou chcete zobrazit');
		}

		// navolim si layout, ktery se pouzije
		$this->layout = 'content';

		// nastavim si pro menu IDecko kategorie,
		// kterou momentalne prohlizim
		$this->set('opened_category_id', $id);
		// sestavim breadcrumbs
		$path = $this->CategoriesProduct->Category->getPath($id);
		$breadcrumbs = array();
		
		if (!$path) {
			$this->cakeError('error404');
		}
		
		foreach ($path as $item) {
			$breadcrumb = array('anchor' => $item['Category']['breadcrumb'], 'href' => '/' . $item['Category']['url']);
			if ($item['Category']['id'] == 5) {
				$breadcrumb = array('anchor' => 'Domů', 'href' => HP_URI);
			}
			$breadcrumbs[] = $breadcrumb;
		}
		$this->set('breadcrumbs', $breadcrumbs);
		
		// musim nacist produkty ze subkategorii
		$category_ids = array();
		$category_ids = $this->CategoriesProduct->Category->get_subcategories_ids($id);

		if (!empty($category_ids)) {
			$conditions = array('CategoriesProduct.category_id' => $category_ids, 'Product.active' => true);
		}
		
		$order = array();
		// pokud chci produkty seradit podle jmena, muzu to udelat uz v selectu (podle ceny musim radit zvlast pak v kodu)
		if ((isset($_GET['sort_name']) && $_GET['sort_name'] == 'name') || (!isset($_GET['sort_name']) && !isset($_GET['sort_order']))) {
			$sort_direction = 'asc';
			if (isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc') {
				$sort_direction = 'desc';
			}
			$order = array('Product.name' => $sort_direction);
		}

		// nactu si produkty
		$products = $this->CategoriesProduct->Product->find('all', array(
			'conditions' => $conditions,
			'contain' => array(
				'Image' => array(
					'conditions' => array(
							'is_main' => '1'
					),
					'fields' => array('Image.id', 'Image.name', 'Image.is_main')
				),
				'Manufacturer' => array(
					'fields' => array('Manufacturer.id', 'Manufacturer.name')
				),
				'Availability' => array(
					'fields' => array('Availability.id', 'Availability.name', 'Availability.cart_allowed')
				)
			),
			'fields' => array('DISTINCT Product.id', 'Product.name', 'Product.short_description', 'Product.retail_price_with_dph', 'Product.discount_common', 'Product.discount_member', 'Product.url'),
			'joins' => array(
				array(
					'table' => 'categories_products',
					'alias' => 'CategoriesProduct',
					'type' => 'INNER',
					'conditions' => array('Product.id = CategoriesProduct.product_id')
				)
			),
			'order' => $order
		));

		// musim si projit vsechny produkty, jestli nemaji nejakou slevu
		for ( $i = 0; $i < count($products); $i++ ){
			$products[$i]['Product']['discount_price'] = $this->CategoriesProduct->Product->assign_discount_price($products[$i]);
		}

		// pokud chci radit podle ceny (bud nemam vubec zadny parametry pro razeni -> chci radi podle ceny vzestupne, nebo mam zadany v GET, ze chci radit podle ceny)
		if (isset($_GET['sort_name']) && $_GET['sort_name'] == 'price') {
			$sort_direction = '_asc';
			if (isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc') {
				$sort_direction = '_desc';
			}
			$products = $this->CategoriesProduct->Product->sort_by_price($products, $sort_direction);
		}

		$this->set('products', $products);
		
		// nejprodavanejsi produkty v kategorii
		$category_most_sold_products = $this->CategoriesProduct->Category->get_most_sold($id);
		$this->set('category_most_sold_products', $category_most_sold_products);

		// urcim si jak se zobrazi produkty
		$listing_style = 'products_listing_grid';
		$this->set('listing_style', $listing_style);

		// nactu si info o kategorii
		$category = $this->CategoriesProduct->Category->find('first', array(
			'conditions' => array('Category.id' => $id),
			'contain' => array(),
			'fields' => array('Category.id', 'Category.title', 'Category.description', 'Category.heading', 'Category.name', 'Category.content')
		));
		$this->set('title_for_content', $category['Category']['title']);
		$this->set('description_for_content', $category['Category']['description']);
		$this->set('category', $category);
		$page_heading = $category['Category']['heading'];
		if (empty($page_heading)) {
			$page_heading = $category['Category']['name'];
		}
		$this->set('page_heading', $page_heading);
	}
	
	function cancel_filter($id) {
		$this->Session->delete('filter');
		
		$this->CategoriesProduct->Category->contain();
		$category = $this->CategoriesProduct->Category->read(null, $id);
		$url = '/' . $category['Category']['url'];
		if (isset($this->params['named']['ls']) && $this->params['named']['ls'] == 'list') {
			$url .= '/ls:list';
		}
		$this->redirect($url);
	}
	
	function admin_change_dph(){
		$cats = array('1', '52', '15', '30', '23', '6', '24', '25');
		$subcats = array();
		
		foreach ( $cats as $cat => $value ){
			$subs = $this->CategoriesProduct->Category->children($value);
			$subcats = array_merge($subcats, $subs);
		}

		$subcats = Set::extract('/Category/id', $subcats);
		
		// seznam vsech kategorii ktere nebereme v uvahu
		$subcats = array_merge($cats, $subcats);

		$products = $this->CategoriesProduct->find('all', array(
			'conditions' => array(
				'NOT' => array(
					'category_id' => $subcats
				)
			),
			'contain' => array()
		));
		
		$product_ids = Set::extract('/CategoriesProduct/product_id', $products);
		
		$products = $this->CategoriesProduct->Product->find('all', array(
			'conditions' => array(
				'Product.id' => $product_ids
			),
			'contain' => array(),
			'fields' => array('id', 'price', 'tax_class_id', 'name')
		));
		
		foreach ( $products as $product ){
			$dph = 1.19;
			$new_dph = 1.2;
			if ( $product['Product']['tax_class_id'] != 1 ){
				$dph = 1.09;
				$new_dph = 1.1;				
			}
			
			$wout = $product['Product']['price'] / $dph;
			$product['Product']['price'] = round($wout * $new_dph);
			
			if ( $product['Product']['id'] != 121 OR $product['Product']['id'] == 88 OR $product['Product']['id'] == 89 ){
				$this->CategoriesProduct->Product->id = $product['Product']['id']; 
				$this->CategoriesProduct->Product->save($product, false);
			}
			
			debug($product);
			echo '<br>';
		}

		die();
	}
	
	function leave_empty($a) {
		return (!empty($a));
	}
	
	function sort_by_availability_and_price($a, $b) {
		if ($a['Product']['Availability']['cart_allowed'] && !$b['Product']['Availability']['cart_allowed']) {
			return -1;
		} elseif (!$a['Product']['Availability']['cart_allowed'] && $b['Product']['Availability']['cart_allowed']) {
			return 1;
		} elseif ($a['Product']['Availability']['cart_allowed'] == $b['Product']['Availability']['cart_allowed']) {
			return $a['Product']['discount_price'] > $b['Product']['discount_price'];
		}
	}
	
	function admin_generate() {
		$categories_products = $this->CategoriesProduct->find('all', array(
			'conditions' => array('category_id' => array(77, 162)),
			'contain' => array(),
		));
		foreach ($categories_products as $categories_product) {
			unset($categories_product['CategoriesProduct']['id']);
			unset($categories_product['CategoriesProduct']['created']);
			unset($categories_product['CategoriesProduct']['modified']);
			$categories_product['CategoriesProduct']['category_id'] = 163;
			$this->CategoriesProduct->create();
			if (!$this->CategoriesProduct->save($categories_product)) {
				debug($categories_product); die();
			}
		}
		die('hotovo');
	}
}
?>