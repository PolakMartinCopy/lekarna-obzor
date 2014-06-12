<?php
class CategoriesController extends AppController {

	var $name = 'Categories';
	var $helpers = array('Html', 'Form', 'Javascript' );

	function admin_index() {
		$this->redirect('/admin/categories/view/5', null, true);
	}

	function admin_view($id = null) {
		$this->Category->id = $id;
		$this->set('category', $this->Category->read()); // nacitani dat o kategorii
		$fields = array("id", "name"); // seznam poli, ktera potrebuji z databaze ohledne kategorii
		$this->set('path_categories', $this->Category->getPath($id, $fields, -1));
		// $path_categories obsahuje cestu do kategorie, kterou prave prohlizim
		$fields = array("id", "name"); // seznam poli, ktera potrebuji z databaze ohledne kategorii
		$this->set('childs', $this->Category->children($id, true, $fields, null, null, 1, -1));
		$this->set('opened_category_id', $id);
	}

	function getCategoriesMenuList($opened_category_id = 5) {
		$this->Category->unbindModel( array('hasAndBelongsToMany' => array('Product')), false);
		$this->Category->id = $opened_category_id;
		$fields = array('id'); // seznam poli, ktera potrebuji z databaze ohledne kategorii
		$path = $this->Category->getPath($this->Category->id, $fields, -1);
		
		$ids_to_find = array();
		foreach ($path as $category){
			$ids_to_find[] = $category['Category']['id'];
		}

		$categories = $this->Category->find('all', array(

			'conditions' => array("parent_id IN ('" . implode("', '", $ids_to_find) . "')"),

			'order' => array("lft" => 'asc')

		));

		// ke kazde kategorii si zjistim kolik ma
		// v sobe produktu
		foreach( $categories as $key => $value ){
			$categories[$key]['Category']['productCount'] = $this->Category->countProducts($categories[$key]['Category']['id']);
			$categories[$key]['Category']['activeProductCount'] = $this->Category->countActiveProducts($categories[$key]['Category']['id']);
		}

		return array(
			'categories' => $categories, 'ids_to_find' => $ids_to_find, 'opened_category_id' => $opened_category_id
		);
	}
	
	function getSubcategoriesMenuList($start_node = null, $opened_category_id = null){
		return $this->Category->getSubcategoriesMenuList($start_node, $opened_category_id);
	}

	function admin_add($id = null) {
		$this->set('tinyMce', true);
		$this->set('tinyMceElement', 'CategoryContent');
		
		if ( !empty($this->data) ){
			$this->Category->create();

			// musim si zkontrolovat, jestli neni prazdny titulek
			// a popisek a url, pokud jsou, musim si je vygenerovat
			if ( empty($this->data['Category']['title']) ){
				$this->data['Category']['title'] = $this->data['Category']['name'];
			}
			if ( empty($this->data['Category']['description']) ){
				$this->data['Category']['description'] = "Kategorii " . $this->data['Category']['name'] . " naleznete v nabídce online lékárny " . CUST_ROOT;
			}
			// pripadne doplnim nadpis a breadcrumb podle nazvu
			if (empty($this->data['Category']['heading'])) {
				$this->data['Category']['heading'] = $this->data['Category']['name'];
			}
			if (empty($this->data['Category']['breadcrumb'])) {
				$this->data['Category']['breadcrumb'] = $this->data['Category']['heading'];
			}

			if ( $this->Category->save($this->data['Category']) ){

				// url musim kontrolovat az po ulozeni, protoze
				// neznam ID kategorie, nejdriv ji ulozim a pak checknu
				if ( empty($this->data['Category']['url']) ){
					$this->data['Category']['url'] = strip_diacritic($this->data['Category']['name'] . '-c'. $this->Category->id);
					// zmenim url a znovu ulozim ( UPDATE )
					$this->Category->save($this->data['Category']);
				}

				$this->Session->setFlash('Kategorie byla vložena!');
				$this->redirect(array('action' => 'view', $this->data['Category']['parent_id']));
			} else {
				$this->Session->setFlash('Kategorie nebyla vložena!');
			}
			$this->set('parent_id', $this->data['Category']['parent_id']);
			$this->set('opened_category_id', $this->data['Category']['parent_id']);
		}
		else{
			$this->set('opened_category_id', $id);
			$this->set('parent_id', $id);
		}
	}

	function admin_edit($id = null) {
		$this->set('opened_category_id', $id);
		
		$this->set('tinyMce', true);
		$this->set('tinyMceElement', 'CategoryContent');
		
		if (!empty($this->data)) {
			// musim si zkontrolovat, jestli neni prazdny titulek
			// a popisek a url, pokud jsou, musim si je vygenerovat
			if ( empty($this->data['Category']['title']) ){
				$this->data['Category']['title'] = $this->data['Category']['name'];
			}
			if ( empty($this->data['Category']['description']) ){
				$this->data['Category']['description'] = "Kategorii " . $this->data['Category']['name'] . " naleznete v nabídce online obchodu s příslušenstvím pro bazény a chemií " . CUST_ROOT;
			}
			if ( empty($this->data['Category']['url']) ){
				$this->data['Category']['url'] = strip_diacritic($this->data['Category']['name'] . '-c'. $id);
			}
			// pripadne doplnim nadpis a breadcrumb
			if (empty($this->data['Category']['heading'])) {
				$this->data['Category']['heading'] = $this->data['Category']['name'];
			}
			if (empty($this->data['Category']['breadcrumb'])) {
				$this->data['Category']['breadrumb'] = $this->data['Category']['name'];
			}

			if ($this->Category->save($this->data)) {
				$this->Session->setFlash('Kategorie byla uložena.');
				$this->redirect(array('action'=>'view', $id), null, true);
			} else {
				$this->Session->setFlash('Ukládání kategorie se nezdařilo!');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Category->read(null, $id);
			if (empty($this->data['Category']['heading'])) {
				$this->data['Category']['heading'] = $this->data['Category']['name'];
			}
			if (empty($this->data['Category']['breadcrumb'])) {
				$this->data['Category']['breadcrumb'] = $this->data['Category']['name'];
			}
		}
		$products = $this->Category->CategoriesProduct->Product->find('list');
		$this->set(compact('products'));
	}

	function admin_delete($id = null) {
		if ( !$id || $id == 5 ) {
			$this->Session->setFlash('Neexistující kategorie.');
			$this->redirect(array('action'=>'index'), null, true);
		}

		// zjistim si pocet deti a jestli jsou v kategorii nejake produkty
		$children = $this->Category->childcount($id);
		$productCount = $this->Category->countProductsActive($id);

		if ( $children != 0 ){
			// jestlize obsahuje podkategorie, nedovolim mazat a vypisu hlasku
			$this->Session->setFlash('Kategorii nelze vymazat, protože není prázdná, obsahuje jiné podkategorie!');
			$this->redirect(array('action' => 'view', $id), null, true);
		} elseif ( $productCount != 0 ){
			// obsahuje produkty, nedovolim mazat
			$this->Session->setFlash('Kategorii nelze vymazat, protože není prázdná, obsahuje produkty!');
			$this->redirect(array('action' => 'view', $id), null, true);
		}

		$this->Category->id = $id;
		$category = $this->Category->read('parent_id');
		if ($this->Category->delete($id)) {
			$this->Session->setFlash('Kategorie byla vymazána.');
			$this->redirect(array('action'=>'view', $category['Category']['parent_id']), null, true);
		}
	}

	function getCategoriesList($active_id = 1){
		$this->set('list', $this->find('all'));
	}

	function admin_list_products($category_id) {
		$this->paginate['CategoriesProduct'] = array(
			'contain' => 'Product',
			'order' => array('Product.active' => 'desc')
		);

		$data = $this->paginate('CategoriesProduct', array('category_id' => $category_id) );
		$this->set(
			'products', $data
		);
		$this->set('opened_category_id', $category_id);
	}

	function admin_moveup($id){
		// otestuju si, jestli je nastavene id
		// a je ruzne od 5
		if ( isset($id) && $id != 5 ){
			if ( $this->Category->moveup($id) ){
				$this->Session->setFlash('Kategorie byla posunuta nahoru.');
				$this->redirect(array('action'=>'view', $id));
			} else {
				$this->Session->setFlash('Kategorii nelze posunout, je na nejvyssi možné pozici.');
				$this->redirect(array('action'=>'view', $id));
			}
		} else {
			// presmeruju na zakladni stranku
			$this->Session->setFlash('Kategorie neexistuje, nebo se snažíte posunout zakladni kategorii..');
			$this->redirect(array('action'=>'view', 5));
		}
	}

	function admin_movedown($id){
		// otestuju si, jestli je nastavene id
		// a je ruzne od 5
		if ( isset($id) && $id != 5 ){
			if ( $this->Category->movedown($id) ){
				$this->Session->setFlash('Kategorie byla posunuta dolů.');
				$this->redirect(array('action'=>'view', $id));
			} else {
				$this->Session->setFlash('Kategorii nelze posunout, je na nejnižší možné pozici.');
				$this->redirect(array('action'=>'view', $id));
			}
		} else {
			// presmeruju na zakladni stranku
			$this->Session->setFlash('Kategorie neexistuje, nebo se snažíte posunout zakladni kategorii..');
			$this->redirect(array('action'=>'view', 5));
		}
	}

	function admin_movenode($id){
		if ( !isset($this->data) ){ // formular jeste nebyl odeslan
			// nactu si data o kategorii, s kterou chci pracovat
			$this->Category->recursive = -1;
			$this->data = $this->Category->read(null, $id);

			// nahraju si strukturovany seznam kategorii,
			// vynecham hlavni kategorii a kategorii, kterou chci p5esunout
			$categories = $this->Category->generatetreelist(array('not' => array('id' => array('5', $id))), '{n}.Category.id', '{n}.Category.name', ' - ');
			$this->set(compact(array('categories')));
			$this->set('opened_category_id', $id);
		} else {
			$this->Category->id = $id;
			$data = array(
				'parent_id' => $this->data['Category']['target_id']
			);
			$this->Category->save($data, false, array('parent_id'));

			$this->Session->setFlash('Kategorie byla přesunuta do nového uzlu.');
			$this->redirect(array('action' => 'view', $id), null, true);
		}
	}
} // konec definice tridy
?>