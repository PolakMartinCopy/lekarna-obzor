<?php
class SearchesController extends AppController {

	var $name = 'Searches';

	/**
	 * Vyhledavani produktu v administraci.
	 *
	 */
	function admin_do(){
		if ( isset($this->data) ){
			$this->data['Search']['query'] = trim($this->data['Search']['query']);
			
			App::import('Model', 'Product');
			$this->Product = &new Product;

			$this->Product->unbindModel(
				array(
					'hasAndBelongsToMany' => array('Cart', 'Flag'),
					'hasMany' => array('Subproduct', 'CartsProduct', 'Comment', 'Image'),
					'belongsTo' => array('TaxClass', 'Availability')
				)
			);
			
			// vysledky s celym retezcem
			$products = $this->Product->find('all', array(
				'conditions' => array(
					"Product.name LIKE '%%" . $this->data['Search']['query'] . "%%'"
				)
			));
			
			// vysledky s rozsekanym retezcem
			$split_query = explode(" ", $this->data['Search']['query']);
			$count_split = count($split_query);
			
			if ( $count_split > 1 ){
				$not_ids = array();
				for ( $i = 0; $i < count($products); $i++ ){
					$not_ids[] = $products[$i]['Product']['id'];
				}
				
				$split_conditions = array();
				for ( $i = 0; $i < $count_split; $i++ ){
					$split_conditions[] = "Product.name LIKE '%%" . $split_query[$i] . "%%'";
				}

				// vysledky s rozsekanym retezcem
				$products2 = $this->Product->find('all', array(
					'conditions' => array('AND' => $split_conditions, 'NOT' => array('Product.id' => $not_ids))
				));
				$products = am($products, $products2);
			}
			
			for ( $i = 0; $i < count($products); $i++ ){
				for ( $j = 0; $j < count($products[$i]['CategoriesProduct']); $j++ ){
					$products[$i]['CategoriesProduct'][$j]['path'] = $this->Product->CategoriesProduct->Category->getpath($products[$i]['CategoriesProduct'][$j]['category_id']);
				}
			}

			$this->set('products', $products);
		}
	}
	
	function parsequery(){
		$target = array('controller' => 'searches', 'action' => 'index');
		if ( isset($this->data) && !empty($this->data['Search']['q']) ){
			$target[0] = urlencode($this->data['Search']['q']);
			$target[1] = '0';
		}
		$this->redirect($target, null, true);
	}

	/**
	 * Vyhledavani produktu v obchode.
	 *
	 * @param string $id
	 */
	function do_search(){
		$this->layout = 'content';
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => HP_URI),
			array('anchor' => 'Vyhledávání produktů', 'href' => '/vyhledavani-produktu')
		);
		$this->set('breadcrumbs', $breadcrumbs);
		
		$this->set('title_for_content', 'Vyhledávání produktů');
		$this->set('description_for_content', 'Vyhledávač produktů v obchodě ' . CUST_ROOT);
		$products = array();
		
		if (!empty($_GET) && isset($_GET['q']) && !empty($_GET['q'])) {
			$this->data['Search']['q'] = $_GET['q'];
		}
				
		if (!empty($this->data)) {
			// hledany vyraz musim ocistit
			// od mezer na zacatku a konci celeho vyrazu
			$queries = trim($this->data['Search']['q']);
			
			// od vice mezer za sebou
			while ( eregi("  ", $queries) ){
				$queries = str_replace("  ", " ", $queries);
			}
			
			// zjistim jestli se nejedna o viceslovny nazev produktu
			$queries = explode(" ", $queries);
			
			$or = array();
			foreach ( $queries as $key => $value ){
				$or[] = array(
					'OR' => array(
						"Product.name LIKE '%%" . $value . "%%'",
						"Product.title LIKE '%%" . $value . "%%'",
						"Product.heading LIKE '%%" . $value . "%%'",
						"Product.related_name LIKE '%%" . $value . "%%'",
						"Product.zbozi_name LIKE '%%" . $value . "%%'",
						"Product.short_description LIKE '%%" . $value . "%%'",
						"Product.description  LIKE '%%" . $value . "%%'",
						"Manufacturer.name  LIKE '%%" . $value . "%%'"
					)
				);
			}
			
			$conditions = array(
				'AND' => array(
					$or,
					'Product.active' => true
				)
			);
			
			$fields = array('id', 'name', 'url', 'retail_price_with_dph', 'short_description', 'discount_common', 'discount_member');
			
			App::import('Model', 'Product');
			$this->Product = &new Product;

			// donactu si data o produktech
			$products = $this->Product->find('all', array(
				'contain' => array(
					'Manufacturer' => array(
						'fields' => array('id', 'name'),
					),
					'Image' => array(
						'conditions' => array(
							'is_main' => '1'
						),
						'fields' => array('name')
					),
					'Availability' => array(
						'fields' => array('id', 'name', 'cart_allowed')
					)
				),
				'conditions' => $conditions,
				'fields' => $fields
			));
			
			// musim si projit vsechny produkty, jestli nemaji nejakou slevu
			for ( $i = 0; $i < count($products); $i++ ){
				$products[$i]['Product']['discount_price'] = $this->Product->assign_discount_price($products[$i]);
			}
			usort($products, array('SearchesController', 'sort_by_availability_and_price'));
		}
		$this->set('products', $products);
	}

	function sort_by_availability_and_price($a, $b) {
		if ($a['Availability']['cart_allowed'] && !$b['Availability']['cart_allowed']) {
			return -1;
		} elseif (!$a['Availability']['cart_allowed'] && $b['Availability']['cart_allowed']) {
			return 1;
		} elseif ($a['Availability']['cart_allowed'] == $b['Availability']['cart_allowed']) {
			return $a['Product']['discount_price'] > $b['Product']['discount_price'];
		}
	}
}
?>