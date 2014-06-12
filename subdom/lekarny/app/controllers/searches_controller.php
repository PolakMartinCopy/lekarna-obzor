<?php
class SearchesController extends AppController {
	var $name = 'Searches';
	
	function do_search(){
		$this->layout = 'users';

		$products = array();
				
		if ( !empty($this->data) ){
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
						"Product.description  LIKE '%%" . $value . "%%'",
						"Manufacturer.name  LIKE '%%" . $value . "%%'"
					)
				);
			}
			
			$conditions = array(
				'AND' => array(
					$or
				)
			);
			
			$fields = array('id', 'name', 'price', 'description');
			
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
					)
				),
				'conditions' => $conditions,
				'fields' => $fields
			));
			
			if ( empty($products) ){
				//$this->redirect(array('controller' => 'searches', 'action' => 'index', 'id' => $this->data['Search']['q']), null, true);
			}
		}
		$this->set('products', $products);
	}
}
?>