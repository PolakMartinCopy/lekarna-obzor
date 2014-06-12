<?php
class CategoriesController extends AppController {

	var $name = 'Categories';
	
	var $layout = 'admin';
	
	function count_products($id){
		return $this->Category->count_products($id);
	}

	function getCategoriesMenuList($opened_category_id = 0){
		$this->Category->id = $opened_category_id;
		$conditions = array();
		$ids_to_find = array();		

		if ( $opened_category_id != 0 ){
			$fields = array('id'); // seznam poli, ktera potrebuji z databaze ohledne kategorii
			$path = $this->Category->getPath($this->Category->id, $fields, -1);
			foreach ( $path as $category ){
				$ids_to_find[] = $category['Category']['id'];
			}
			$conditions = "parent_id IN ('" . implode("', '", $ids_to_find) . "')";
		}

		$categories = $this->Category->find('all', array(
			'conditions' => $conditions,
			'order' => array('lft' => 'asc'),
			'contain' => array()
		));

		return array(
			'Categories' => $categories, 'ids_to_find' => $ids_to_find, 'opened_category_id' => $opened_category_id
		);
	}
	
	function admin_index($id = null) {
		if ( empty($id) ){
			$id = 0;
		}
		
		$conditions = array(
			'parent_id' => $id
		);
		
		$contain = array();

		if ( empty($id) ){
		}

		$categories = $this->Category->find('all', array(
			'conditions' => $conditions,
			'contain' => $contain
		));

		$reordered_categories = array();
		
		$count = count($categories);
		for ( $i = 0; $i < $count; $i++ ){
			$children = $this->Category->children($categories[$i]['Category']['id']);
			array_push($reordered_categories, $categories[$i]);
			
			for ( $j = 0; $j < count($children); $j++ ){
				array_push($reordered_categories, $children[$j]);
			}
		}

		$this->set('categories', $reordered_categories);
		
		$category = $this->Category->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'contain' => array()
		));
		$this->set('category', $category);
	}
	
	function admin_add(){
		$parent_id = 0;
		if ( isset($this->params['named']['parent_id']) ){
			$parent_id = $this->params['named']['parent_id'];
		}
		
		if ( !empty($this->data) ){
			if ( $this->Category->save($this->data['Category']) ){
				$this->Session->setFlash('Kategorie byla vložena!');
				$this->redirect(array('action' => 'index', 'id' => $this->data['Category']['parent_id']));
			} else {
				$this->Session->setFlash('Kategorie nebyla vložena!');
			}
			$this->set('parent_id', $this->data['Category']['parent_id']);
		}

		$this->set('parent_id', $parent_id);
	}
	
	function admin_delete($id = null){
		if ( empty($id) ){
			$this->Session->setFlash('Neexistující kategorie!');
			$this->redirect(array('controller' => 'categories', 'action' => 'index'), null, true);
		}
		
		// musim zjistit, jestli kategorie obsahuje nejake produkty
		// a jestli neobsahuje nejake podkategorie
		
		// kontrola prazdnosti kategorie
		$children = $this->Category->children($id);
		if ( !empty($children) ){
			$this->Session->setFlash('Kategorie obsahuje podkategorie, nelze ji proto smazat!');
			$this->redirect(array('controller' => 'categories', 'action' => 'index'), null, true);
		}

		if ( $this->Category->CategoriesProduct->hasAny(array('category_id' => $id)) ){
			$this->Session->setFlash('Kategorie obsahuje produkty, nelze ji proto smazat!');
			$this->redirect(array('controller' => 'categories', 'action' => 'index'), null, true);
		}

		// kategorie je prazdna, muzu ji smazat
		if ( $this->Category->del($id) ){
			$this->Session->setFlash('Kategorie byla smazána!');
			$this->redirect(array('controller' => 'categories', 'action' => 'index'), null, true);
		}
	}
	
	function admin_edit($id = null){
		if ( empty($id) ){
			$this->Session->setFlash('Neexistující kategorie.');
			$this->redirect(array('controller' => 'categories', 'action' => 'index'), null, true);
		}
		
		if ( isset($this->data) ){
			if ( $this->Category->save($this->data) ){
				$this->Session->setFlash('Kategorie byla upravena!');
				$this->redirect(array('controller' => 'categories', 'action' => 'index', $id), null, true);
			}
		}
		
		$this->Category->contain();
		$this->data = $this->Category->find('first', array('conditions' => array('id' => $id)));
	}

	function admin_move($id = null) {
		if ( empty($id) ){
			$this->Session->setFlash('Neexistující kategorie.');
			$this->redirect(array('controller' => 'categories', 'action' => 'index'), null, true);
		}
		
		$this->Category->contain();
		$this->Category->id = $id;
		$category = $this->Category->read();
		$this->set('category', $category);
		
		if (isset($this->data)) {
			if ($this->Category->save($this->data)) {
				$this->Session->setFlash('Kategorie byla přesunuta');
				$this->redirect(array('controller' => 'categories', 'action' => 'index', 'id' => $category['Category']['parent_id']));
			} else {
				$this->Session->setFlash('Kategorii se nepodařilo přesunout');
			}
		} else {
			$categories = $this->Category->generateTreeList(null, null, null, '-');
			$categories = array_map(array('CategoriesController', 'add_space'), $categories);
			$categories = array(0 => 'Katalog') + $categories;
			unset($categories[$category['Category']['id']]);
			$this->set('categories', $categories);
		}
	}
	
	function add_space($item) {
		return '-' . $item;
	}
} // konec definice tridy
?>