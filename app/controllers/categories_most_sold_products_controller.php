<?php 
class CategoriesMostSoldProductsController extends AppController {
	var $name = 'CategoriesMostSoldProducts';
	
	function admin_generate() {
		// vyprazdnim tabulku		
		if (!$this->CategoriesMostSoldProduct->truncate()) {
			die('nepodarilo se vyprazdnit tabulky categories_most_sold_products');
		}
		
		// nactu seznam kategorii
		$categories = $this->CategoriesMostSoldProduct->Category->find('all', array(
			'conditions' => array(
				'NOT' => array('Category.id' => $this->CategoriesMostSoldProduct->Category->unactive_categories_ids)
			),
			'contain' => array(),
			'fields' => array('id', 'name')	
		));
		
		// nastavim virtualni pole pro sumu prodanych produktu
		$this->CategoriesMostSoldProduct->Category->CategoriesProduct->virtualFields = array('quantity' => 'SUM(OrderedProduct.product_quantity)');
		
		// pro kazdou kategorii:
		foreach ($categories as $category) {
			if (!$this->CategoriesMostSoldProduct->Category->set_most_sold($category['Category']['id'])) {
				die('ulozeni nejprodavanejsich produktu v kategorii ' . $category['Category']['name'] . ' se nezdarilo');
			}
		}
		
		$this->Session->setFlash('Nejprodávanější produkty kategorií byly vygenerovány.');
		$back_link = array('controller' => 'orders', 'action' => 'index');
		if (isset($this->params['named']['back_link'])) {
			$back_link = base64_decode($this->params['named']['back_link']);
		}
		$this->redirect($back_link);
	}
}
?>