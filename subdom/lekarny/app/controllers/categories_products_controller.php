<?
class CategoriesProductsController extends AppController {
	var $name = 'CategoriesProducts';
	
	function admin_edit($id) {
		$this->CategoriesProduct->id = $id;
		$this->CategoriesProduct->contain();
		$category_product = $this->CategoriesProduct->read();
		$this->set('category_product', $category_product);
		
		$categories = $this->CategoriesProduct->Category->generateTreeList(null, null, null, '-');
		$this->set('categories', $categories);
		if (isset($this->data)) {
			if ($this->CategoriesProduct->save($this->data)) {
				$this->Session->setFlash('Produkt byl přesunut.');
				$this->redirect(array('controller' => 'products', 'action' => 'view', $category_product['CategoriesProduct']['product_id']));
			} else {
				$this->Session->setFlash('Produkt se nepodařilo přesunout');
			}
			
		}
	}
	
	function users_view($id = null) {
		$this->layout = 'users';
		$category = $this->CategoriesProduct->Category->find('first', array(
			'conditions' => array(
				'id' => $id
			)
		));
		$this->set('category', $category);
		
		$childs = $this->CategoriesProduct->Category->children($id);
		if ( !empty($childs) ){
			$categories = Set::extract('/Category/id', $childs);
		}
		
		$categories[] = $id;
		
		$products = $this->CategoriesProduct->find('all', array(
			'conditions' => array(
				'category_id' => $categories
			),
			'contain' => array(
				'Product' => array(
					'Image' => array(
						'conditions' => array(
							'is_main' => '1'
						),
						'limit' => 1
					)
				)
			)
		));
		
		$this->set('products', $products);
	}
}
?>