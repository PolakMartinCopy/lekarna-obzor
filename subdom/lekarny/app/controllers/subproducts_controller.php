<?php
class SubproductsController extends AppController {

	var $name = 'Subproducts';
	var $helpers = array('Html', 'Form', 'Javascript' );

	function index() {
		$this->Subproduct->recursive = 0;
		$this->set('subproducts', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid Subproduct.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		$this->set('subproduct', $this->Subproduct->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$product_id = $this->data['Subproduct']['product_id'];
			unset($this->data['Subproduct']['product_id']);

			foreach ( $this->data['Subproduct'] as $key => $subproduct ){
				$this->Subproduct->create();
				if ( !isset($this->data['Subproduct'][$key]['attribute_id']) ){
					unset($this->data['Subproduct'][$key]);
					continue;
				}
				$sort_order = $this->Subproduct->countSortOrder($this->data['Subproduct'][$key]['attribute_id'], $this->data['Subproduct'][$key]['product_id']) + 1;
				$this->data['Subproduct'][$key]['sort_order'] = $sort_order;
				if (!$this->data['Subproduct'][$key]['price']) {
					$this->data['Subproduct'][$key]['price'] = 0;
				}
				$this->Subproduct->save($this->data['Subproduct'][$key]);
			}

			$this->redirect(array('controller' => 'products', 'action'=>'view', $product_id, $this->Subproduct->optionFilter($this->params['pass'])), null, true);
		}
		$products = $this->Subproduct->Product->find('list');
		$attributes = $this->Subproduct->Attribute->find('list');
		$this->set(compact('products', 'attributes'));
	}

	function admin_edit($id = null) {
		$this->layout = 'admin';
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Neexistující atribut.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		if (!empty($this->data)) {
			if ($this->Subproduct->save($this->data)) {
				$this->Session->setFlash('Atribut byl uložen.');
				$this->redirect(array('controller' => 'products', 'action' => 'view', 'id' => $this->data['Subproduct']['product_id'], $this->Subproduct->optionFilter($this->params['pass'])	), null, true);
			} else {
				$this->Session->setFlash('Atribut nemohl být uložen.');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Subproduct->read(null, $id);
		}
		$products = $this->Subproduct->Product->find('list');
		$attributes = $this->Subproduct->Attribute->find('list');
		$this->set(compact('products','attributes'));
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující atribut.');
			$this->redirect(array('action'=>'index'), null, true);
		}else{
			$subproduct = $this->Subproduct->read('product_id');
			if ($this->Subproduct->del($id)) {
				$this->Session->setFlash('Atribut byl smazán.');
				$this->redirect(array('controller' => 'products', 'action'=>'view', $subproduct['Subproduct']['product_id'], $this->Subproduct->optionFilter($this->params['pass'])	), null, true);
			}
		}
	}

	function admin_moveup($id){
		// musim si nacist info o atributu
		// nastavim hloubku
		$this->Subproduct->recursive = 0;

		// nastavim idecko hledaneho subproduktu
		$this->Subproduct->id = $id;

		// musim odpojit Product a Attribute
		$this->Subproduct->unbindModel(
			array(
				'belongsTo' => array('Product')
			)
		);

		// nactu si Subproduct
		$subproduct = $this->Subproduct->read();
		
		// spocitam si toplevel pro dany atribut
		$top_level = $subproduct['Attribute']['option_id'] * 10 + 1;
		
		// pokud je nejvyse, rovnou presmeruju a vypisu hlasku
		if ( $subproduct['Subproduct']['sort_order'] == $top_level ){
			$this->Session->setFlash('Atribut je na nejvyšší možné pozici.');
			$this->redirect(array('controller' => 'products', 'action' => 'view', $subproduct['Subproduct']['product_id'], $this->Subproduct->optionFilter($this->params['pass'])), null, true);
		}

		// najdu si prvek pred ktery posouvam
		$conditions = array(
			'product_id' => $subproduct['Subproduct']['product_id'],
			'sort_order' => $subproduct['Subproduct']['sort_order'] - 1
		);
		$predchozi = $this->Subproduct->find($conditions, array('id'));

		// musim udelat posun nahoru a dolu u dvou sousednich prvku
		$this->Subproduct->updateAll(array('sort_order' => $subproduct['Subproduct']['sort_order'] - 1), array('Subproduct.id' => $id) );
		$this->Subproduct->updateAll(array('sort_order' => $subproduct['Subproduct']['sort_order']), array('Subproduct.id' => $predchozi['Subproduct']['id']) );

		$this->Session->setFlash('Atribut byl posunut.');
		$this->redirect(array('controller' => 'products', 'action' => 'view', 'id' => $subproduct['Subproduct']['product_id'], $this->Subproduct->optionFilter($this->params['pass'])), null, true);
	}

	function admin_movedown($id){
		// musim si nacist info o atributu
		// nastavim hloubku
		$this->Subproduct->recursive = 0;

		// nastavim idecko hledaneho subproduktu
		$this->Subproduct->id = $id;

		// musim odpojit Product
		$this->Subproduct->unbindModel(
			array(
				'belongsTo' => array('Product')
			)
		);

		// nactu si Subproduct
		$subproduct = $this->Subproduct->read();
		
		// spocitam si bottom level pro dany atribut
		// vytahnu si max level pro dany atribut
		$bottom_level_left = $subproduct['Attribute']['option_id'] * 10;
		$bottom_level_right = ($subproduct['Attribute']['option_id'] + 1) * 10;
		$conditions = array(
			'product_id' => $subproduct['Subproduct']['product_id'],
			'sort_order' => '> ' . $bottom_level_left,
			'sort_order' => '< ' . $bottom_level_right
		);
		$bottom_level = $this->Subproduct->find($conditions, 'MAX(sort_order) as bottom_level');
		$bottom_level = $bottom_level[0]['bottom_level'];
		
		// pokud je nejnize, rovnou presmeruju a vypisu hlasku
		if ( $subproduct['Subproduct']['sort_order'] == $bottom_level ){
			$this->Session->setFlash('Atribut je na nejnižší možné pozici.');
			$this->redirect(array('controller' => 'products', 'action' => 'view', $subproduct['Subproduct']['product_id'], $this->Subproduct->optionFilter($this->params['pass'])	), null, true);
		}

		// najdu si prvek za ktery posouvam
		$conditions = array(
			'product_id' => $subproduct['Subproduct']['product_id'],
			'sort_order' => $subproduct['Subproduct']['sort_order'] + 1
		);
		$nasledujici = $this->Subproduct->find($conditions, array('id'));

		// musim udelat posun nahoru a dolu u dvou sousednich prvku
		$this->Subproduct->updateAll(array('sort_order' => $subproduct['Subproduct']['sort_order'] + 1), array('Subproduct.id' => $id) );
		$this->Subproduct->updateAll(array('sort_order' => $subproduct['Subproduct']['sort_order']), array('Subproduct.id' => $nasledujici['Subproduct']['id']) );

		$this->Session->setFlash('Atribut byl posunut.');
		$this->redirect(array('controller' => 'products', 'action' => 'view', $subproduct['Subproduct']['product_id'], $this->Subproduct->optionFilter($this->params['pass'])	), null, true);
	}
	
	function admin_control($product_id) {
		// nachystam si podminku pro vyber subproduktu
		$subproduct_conditions = array('Subproduct.product_id' => $product_id);
		$attribute_id_conditions = array();
		// pokud jsou nastavena spravna data (s ['Option'])
		if (isset($this->data['Option'])) {
			// vytahnu si z nich ty atributy, podle kterych chci filtrovat
			$attribute_ids = array();
			foreach ($this->data['Option'] as $attribute) {
				if (!empty($attribute['id'])) {
					$attribute_ids[] = $attribute['id'];
				}
			}
			// pokud jsou pole filtru prazda (v zadnem selectu nemam nic vybraneho), tak vybiram vsechny subprodukty, jinak jen podle zvolenych
			// atributu
			if (!empty($attribute_ids)) {
				$attribute_id_conditions = array('AttributesSubproduct.attribute_id' => $attribute_ids);
			} else {
				$this->redirect(array('controller' => 'products', 'action' => 'view', $product_id));
			}
		}	
		
		// nactu produkt, jeho subprodukty a attributes_subproducts podle nastavenych podminek
		$subproducts = $this->Subproduct->find('all', array(
			'conditions' => $subproduct_conditions,
			'contain' => array(
				'AttributesSubproduct' => array(
					'conditions' => $attribute_id_conditions,
				)
			)
		));
		
		if (isset($attribute_ids)) {
			// vyberu jen ty subprodukty, ktere splnuji vsechny podminky z filtru
			$res = array();
			foreach ($subproducts as $index => $subproduct) {
				// vyberu jen ty, ktere splnuji vsechny podminky (maji vsechny atributy zvolene ve filtru)
				if (sizeof($subproduct['AttributesSubproduct']) == sizeof($attribute_ids)) {
					$res[$index] = $subproduct;
				}
			}
			$subproducts = $res;
		}
		
		// donactu data o vybranych subproduktech
		if (sizeof($subproducts) == 1) {
			$subproduct = current($subproducts);
			$subproduct_ids = array($subproduct['Subproduct']['id']);
		} else {
			// preindexuju pole, aby zacinalo od 1
			$tmp = $subproducts;
			$subproducts = array();
			foreach ($tmp as $t) {
				$subproducts[] = $t;
			}
			$subproduct_ids = Set::extract('/Subproduct/id', $subproducts);
		}
		$subproducts = $this->Subproduct->find('all', array(
			'conditions' => array('Subproduct.id' => $subproduct_ids),
			'contain' => array(
				'AttributesSubproduct' => array(
					'Attribute' => array(
						'Option'
					)
				)
			)
		));
		
		
		// nastavim pole s atributy subproduktu
		foreach ($subproducts as $subproduct) {
			$data['Product'][$subproduct['Subproduct']['id']]['availability_id'] = $subproduct['Subproduct']['availability_id'];
			$data['Product'][$subproduct['Subproduct']['id']]['pieces'] = $subproduct['Subproduct']['pieces'];
			$data['Product'][$subproduct['Subproduct']['id']]['price'] = $subproduct['Subproduct']['price'];
			$data['Product'][$subproduct['Subproduct']['id']]['active'] = $subproduct['Subproduct']['active'];
		}
		
		// pro ucely filtru suproduktu podle atributu si musim vytahnout atributy, ktere se vztahuji k danemu produktu
		// takze najdu subprodukty produktu
		$filter_subproducts = $this->Subproduct->find('all', array(
			'conditions' => array('Subproduct.product_id' => $product_id),
			'contain' => array()
		));
		
		// najdu vztahy mezi subprodukty a atributy
		$filter_attributes_subproducts = $this->Subproduct->AttributesSubproduct->find('all', array(
			'conditions' => array('AttributesSubproduct.subproduct_id' => Set::extract('/Subproduct/id', $filter_subproducts)),
			'contain' => array(),
			'fields' => array('DISTINCT AttributesSubproduct.attribute_id')
		));

		// vytahnu si options a atributy se vztahem k danemu produktu
		$filter_options = $this->Subproduct->AttributesSubproduct->Attribute->Option->find('all', array(
			'contain' => array(
				'Attribute' => array(
					'conditions' => array(
						'Attribute.id' => Set::extract('/AttributesSubproduct/attribute_id', $filter_attributes_subproducts)
					),
					'order' => array('Attribute.sort_order' => 'ASC')	
				)
			)
		));

		// preusporadam pole options, abych mohl snadno udelat selecty
		foreach ($filter_options as $index => $option) {
			$filter_options[$index]['Attribute'] = Set::combine($option['Attribute'], '{n}.id', '{n}.value');
		}

		// hodnoty z tabulky availabilities pro select
		$availabilities = $this->Subproduct->Availability->find('list');

		return compact('filter_options', 'data', 'availabilities', 'subproducts');
	}
	
	function get_subproduct($product_id, $product_attributes) {
		$atts = unserialize(base64_decode($product_attributes));
		$attributes = array();
		foreach ($atts as $att) {
			$attributes[] = $this->Subproduct->AttributesSubproduct->Attribute->find('first', array(
				'conditions' => array(
					'option_id' => $att['Option']['id'],
					'value' => $att['Value']['name']
				),
				'contain' => array()
			));
		}
		
		// musim najit subprodukt daneho produktu s danymi atributy
		$subproducts = $this->Subproduct->find('all', array(
			'conditions' => array('Subproduct.product_id' => $product_id),
			'contain' => array('AttributesSubproduct')
		));
			
		$attributes_ids = Set::extract('/Attribute/id', $attributes);
		sort($attributes_ids);
			
		foreach ($subproducts as $subproduct) {
			$sp_attributes_ids = Set::extract('/attribute_id', $subproduct['AttributesSubproduct']);
			sort($sp_attributes_ids);
			if ($attributes_ids == $sp_attributes_ids) {
				return $subproduct;
			}
		}
		return false;
	}
	
	function to_names(){
		$attributes = $this->params['attributes'];
		$attributes = unserialize($attributes);
		$product_attributes = null;
		if ( !empty($attributes) ){
			$subproducts_ids = array();
			
			foreach( $attributes as $option => $subproduct_id ){
				$subproducts_ids[] = $subproduct_id;
			}
	
			$this->Subproduct->unbindModel(
				array(
					'belongsTo' => array('Product')
				)
			);
			$this->Subproduct->recursive = 2;
			$attributes = $this->Subproduct->findAll(array('OR' => array('Subproduct.id' => $subproducts_ids) ));
	
			$product_attributes = array();
			foreach ( $attributes as $attribute ){
				$product_attributes[$attribute['Attribute']['Option']['name']] = $attribute['Attribute']['Value']['name'];
			}
		} // empty
		return $product_attributes;
	}
} // konec definice tridy
?>