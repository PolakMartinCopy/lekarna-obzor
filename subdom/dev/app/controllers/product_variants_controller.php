<?php 
class ProductVariantsController extends AppController {
	var $name = 'ProductVariants';
	
	var $left_menu_list = array('product_variants');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'product_variants');
		$this->set('left_menu_list', $this->left_menu_list);
		
		$this->Auth->allow('init');
	}
	
	function user_index() {
		// reset filtru
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'product_variants') {
			$this->Session->delete('Search.ProductVariantSearch2');
			$this->redirect(array('controller' => 'product_variants', 'action' => 'index'));
		}
		
		// inicializace vyhledavacich podminek
		$conditions = array('ProductVariant.active' => true);
		
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['ProductVariantSearch2']['ProductVariant']['search_form']) && $this->data['ProductVariantSearch2']['ProductVariant']['search_form'] == 1 ){
			$this->Session->write('Search.ProductVariantSearch2', $this->data['ProductVariantSearch2']);
			$conditions = $this->ProductVariant->do_form_search($conditions, $this->data['ProductVariantSearch2']);
		} elseif ($this->Session->check('Search.ProductVariantSearch2')) {
			$this->data['ProductVariantSearch2'] = $this->Session->read('Search.ProductVariantSearch2');
			$conditions = $this->ProductVariant->do_form_search($conditions, $this->data['ProductVariantSearch2']);
		}
		
		App::import('Model', 'Unit');
		$this->ProductVariant->Unit = new Unit;

		$this->paginate = array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'INNER',
					'conditions' => array('Product.id = ProductVariant.product_id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'LEFT',
					'conditions' => array('Unit.id = Product.unit_id')
				)
			),
			'fields' => array(
				'ProductVariant.id',
				'ProductVariant.lot',
				'ProductVariant.exp',
				'ProductVariant.meavita_price',
				'ProductVariant.meavita_quantity',
				'ProductVariant.meavita_margin',
				'Product.id',
				'Product.vzp_code',
				'Product.group_code',
				'Product.referential_number',
				'Product.name',
				'Product.en_name',
				'Unit.name',
			),
			'order' => array('Product.name' => 'asc'),
			'limit' => 40
		);
		
		$product_variants = $this->paginate();

		$find = $this->paginate;
		// parametry pro xls export
		unset($find['limit']);
		unset($find['fields']);
		
		// pole pro xls export
		$export_fields = $this->ProductVariant->export_fields();
		
		$this->set(compact('product_variants', 'find', 'export_fields'));
	}
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->ProductVariant->saveAll($this->data)) {
				$this->Session->setFlash('Zboží bylo vloženo do číselníku');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Zboží se nepodařilo vložit do číselníku, opravte chyby ve formuláři a opakujte akci');
			}
		}
		
		$units = $this->ProductVariant->Product->Unit->find('list', array(
			'order' => array('Unit.name' => 'asc')
		));
		
		$tax_classes = $this->ProductVariant->Product->TaxClass->find('list', array(
			'order' => array('TaxClass.value' => 'asc')	
		));
		$this->set(compact('units', 'tax_classes'));
	}
	
	function user_ajax_add() {
		$result = array(
			'success' => false,
			'message' => null,	
		);
		
		$product_variant = $_POST['data'];

		if ($this->ProductVariant->saveAll($product_variant)) {
			$result['success'] = true;
			$result['message'] = 'Produkt byl vložen do číselníku';
			$result['productVariantId'] = $this->ProductVariant->id;
		} else {
			$result['message'] = 'Produkt se nepodailo vložit';
		}
		
		echo json_encode($result);
		die();
	}
	
	function user_edit($id = null) {
		if (!isset($id)) {
			$this->Session->setFlash('Není zadán produkt, který chcete upravovat');
			$this->redirect(array('action' => 'index'));
		}
		
		$product_variant = $this->ProductVariant->find('first', array(
			'conditions' => array('ProductVariant.id' => $id),
			'contain' => array('Product')
		));
		
		if (empty($product_variant)) {
			$this->Session->setFlash('Produkt, který chcete upravit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		if (isset($this->data)) {
			if ($this->ProductVariant->saveAll($this->data)) {
				$this->Session->setFlash('Produkt byl upraven');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Produkt se nepodařilo upravit, opravte chyby ve formuláři a opakujte akci');
			}
		} else {
			$this->data = $product_variant;
		}
		
		$units = $this->ProductVariant->Product->Unit->find('list', array(
			'order' => array('Unit.name' => 'asc')
		));
		
		$tax_classes = $this->ProductVariant->Product->TaxClass->find('list', array(
			'order' => array('TaxClass.value' => 'asc')	
		));
		$this->set(compact('units', 'tax_classes'));
	}
	
	function user_delete($id = null) {
		// produkt deaktivuju (soft delete), nemazu!!!
		if (!isset($id)) {
			$this->Session->setFlash('Není zadán produkt, který chcete smazat');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->ProductVariant->hasAny(array('ProductVariant.id' => $id))) {
			$this->Session->setFlash('Produkt, který chcete smazat, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		if ($this->ProductVariant->delete($id)) {
			$this->Session->setFlash('Produkt byl odstraněn');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo odstranit, opakujte prosím akci');
		}
		$this->redirect(array('action' => 'index'));
	}
	
	function user_autocomplete_list() {
		$term = null;
		if ($_GET['term']) {
			$term = $_GET['term'];
		}
		$language_id = null;
		if (isset($this->params['named']['language_id'])) {
			$language_id = $this->params['named']['language_id'];
		}
		
		echo $this->ProductVariant->autocomplete_list($term, $language_id);
		die();
	}
	
	function init() {
		$this->ProductVariant->query('TRUNCATE TABLE product_variants');
		
		$products = $this->ProductVariant->Product->find('all', array(
			'contain' => array(),
			'fields' => array('Product.id', 'Product.price', 'Product.margin')	
		));
		
		foreach ($products as $product) {
			$product_variant = array(
				'ProductVariant' => array(
					'product_id' => $product['Product']['id'],
					'exp' => '',
					'lot' => '',
					'meavita_quantity' => 0,
					'meavita_price' => 0,
					'meavita_margin' => 0,
					'active' => true
				)
			);
			
			$this->ProductVariant->create();
			$this->ProductVariant->save($product_variant);
		}
		die();
	}
}
?>