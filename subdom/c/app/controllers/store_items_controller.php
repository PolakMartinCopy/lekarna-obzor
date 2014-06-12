<?php
class StoreItemsController extends AppController {
	var $name = 'StoreItems';
	
	var $left_menu_list = array('store_items');
	
	function beforeRender() {
		parent::beforeRender();
		$this->set('active_tab', 'store_items');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.StoreItemForm');
			$this->redirect(array('controller' => 'store_items', 'action' => 'index'));
		}
		
		$conditions = array('Address.address_type_id' => 1);
		if ($this->user['User']['user_type_id'] == 3) {
			$conditions = array('BusinessPartner.user_id' => $this->user['User']['id']);
		}
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['StoreItem']['search_form']) && $this->data['StoreItem']['search_form'] == 1){
			$this->Session->write('Search.StoreItemForm', $this->data);
			$conditions = $this->StoreItem->do_form_search($conditions, $this->data);
		} elseif ($this->Session->check('Search.StoreItemForm')) {
			$this->data = $this->Session->read('Search.StoreItemForm');
			$conditions = $this->StoreItem->do_form_search($conditions, $this->data);
		}

		App::import('Model', 'Address');
		$this->StoreItem->Address = new Address;
		App::import('Model', 'Unit');
		$this->StoreItem->Unit = new Unit;

		
		// pomoci strankovani (abych je mohl jednoduse radit) vyberu VSECHNY polozky skladu odberatele
		$this->paginate['StoreItem'] = $find = array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = StoreItem.business_partner_id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = Address.business_partner_id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'left',
					'conditions' => array('StoreItem.product_id = Product.id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'left',
					'conditions' => array('Product.unit_id = Unit.id')
					)
			),
			'fields' => array(
				'StoreItem.id',
				'StoreItem.quantity',
				'StoreItem.item_total_price',
				
				'BusinessPartner.id',
				'BusinessPartner.name',
				
				'Address.city',
				'Address.region',
						
				'Product.id',
				'Product.name',
				'Product.vzp_code',
				'Product.group_code',
				'Product.price',
							
				'Unit.shortcut'
			),
			'order' => array('StoreItem.modified' => 'desc'),
			'limit' => 30
		);
		
		$stores = $this->paginate();

		$export_fields = array(
			array('field' => 'StoreItem.id', 'position' => '["StoreItem"]["id"]', 'alias' => 'StoreItem.id'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'Address.city', 'position' => '["Address"]["city"]', 'alias' => 'Address.city'),
			array('field' => 'Address.region', 'position' => '["Address"]["region"]', 'alias' => 'Address.region'),
			array('field' => 'Product.id', 'position' => '["Product"]["id"]', 'alias' => 'Product.id'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.name', 'position' => '["Product"]["name"]', 'alias' => 'Product.name'),
			array('field' => 'StoreItem.quantity', 'position' => '["StoreItem"]["quantity"]', 'alias' => 'StoreItem.quantity'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'Product.price', 'position' => '["Product"]["price"]', 'alias' => 'Product.price'),
			array('field' => 'StoreItem.item_total_price', 'position' => '["StoreItem"]["item_total_price"]', 'alias' => 'StoreItem.item_total_price'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
		);
		$this->set(compact('find', 'stores', 'export_fields'));		
	}
	
	function user_pdf_export() {
		$business_partner_id = $this->data['PDF']['business_partner_id'];
		$business_partner = $this->StoreItem->BusinessPartner->find('first', array(
			'conditions' => array('BusinessPartner.id' => $business_partner_id),
			'contain' => array(
				'Address' => array(
					'conditions' => array('Address.address_type_id' => 1),
					'fields' => array('Address.id', 'Address.street', 'Address.number', 'Address.o_number', 'Address.city', 'Address.zip')
				)
			),
			'fields' => array('BusinessPartner.id', 'BusinessPartner.name', 'BusinessPartner.ico')
		));
		$user = $this->Auth->user();

		// vyhledam data podle zadanych kriterii
		$store_items = $this->StoreItem->find('all', array(
    		'conditions' => array(
    			'StoreItem.business_partner_id' => $business_partner['BusinessPartner']['id'],
    			'StoreItem.quantity >' => 0
    		),
			'contain' => array(),
			'joins' => array(
            	array(
                    'table' => 'products',
                    'alias' => 'Product',
                    'type' => 'left',
                    'conditions' => array('StoreItem.product_id = Product.id')
                ),
            	array(
                    'table' => 'units',
                    'alias' => 'Unit',
                    'type' => 'left',
                    'conditions' => array('Product.unit_id = Unit.id')
                )
	        ),
			'order' => array('Product.vzp_code' => 'asc'),
			'fields' => array(
	            'StoreItem.id',
	            'Product.id',
	            'Product.vzp_code',
	            'Product.name',
	            'StoreItem.quantity',
	            'Unit.shortcut',
	            'Product.price',
	            'StoreItem.item_total_price',
	            'Product.group_code'
        	)
		));
		
		$this->set(compact('business_partner', 'user', 'store_items'));
		
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
}