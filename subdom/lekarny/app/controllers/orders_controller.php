<?
class OrdersController extends AppController{
	var $name = 'Orders';

	function admin_edit(){
		// kontrola, zda jsou pro dany status vyzadovana nejake pole
		$valid_requested_fields = array();
		$requested_fields = $this->Order->Status->has_requested($this->data['Order']['status_id']);
		if ( !empty($requested_fields) ){
			// nejaka pole jsou vyzadovana, takze si to musim zkontrolovat
			$this->Order->recursive = -1;
			$order = $this->Order->read(null, $this->data['Order']['id']);
			
			foreach ( $requested_fields as $key => $value ){
				if ( empty($order['Order'][$key]) && empty($this->data['Order'][$key])  ){
					$valid_requested_fields[] = $value;
				}
			}
		}
		
		if ( empty($valid_requested_fields) ){

			// ukladani poznamky o zmene stavu
			// vytvorim si data pro poznamku o zmene objednavky
			$this->data['Ordernote']['administrator_id'] = $this->Session->read('Administrator.id');
			$this->data['Ordernote']['status_id'] = $this->data['Order']['status_id'];
			$this->data['Ordernote']['order_id'] = $this->data['Order']['id'];
	
			// osetrim, zda dochazi ke zmene cisla baliku,
			// pokud ne, unsetnu si cislo baliku
			if ( empty($this->data['Order']['shipping_number']) ){
				unset($this->data['Order']['shipping_number']);
			} else {
				$this->data['Ordernote']['note'] .= "\n" . 'přidáno čílo balíku: ' . $this->data['Order']['shipping_number'];
			}
			
			// osetrim, zda dochazi ke zmene variabilniho symbolu,
			// pokud ne, unsetnu si variablni symbol
			if ( empty($this->data['Order']['variable_symbol']) ){
				unset($this->data['Order']['variable_symbol']);
			} else {
				$this->data['Ordernote']['note'] .= "\n" . 'přidán variabilní symbol: ' . $this->data['Order']['variable_symbol'];
			}
			
			$this->Order->Ordernote->save($this->data);
				
			
			// zalozim si idecko, abych updatoval
			$this->Order->id = $this->data['Order']['id'];
			unset($this->data['Order']['id']);
				
			// zmena stavu			
			// ulozim bez validace
			$this->Order->save($this->data, false);

			// odeslat na mail notifikaci zakaznikovi
			$mail_result = $this->Order->Status->change_notification($this->Order->id, $this->data['Order']['status_id']);

			
			$this->Session->setFlash('Objednávka byla změněna!');
			$this->redirect(array('action' => 'view', 'id' => $this->Order->id), null, true);
		} else {
			$message = implode("<br />", $valid_requested_fields);
			$this->Session->setFlash('Chyba při změně statusu!<br />' . $message);
			$this->redirect(array('action' => 'view', 'id' => $this->Order->id), null, true);
		}
	}

	function admin_edit_customer_info($id){
		$this->layout = 'admin';
		
		if ( isset($this->data) ){
			if ( $this->Order->save($this->data) ){
				$this->Session->setFlash('Údaje o zákazníkovi byly opraveny.');
				$this->redirect(array('controller' => 'orders', 'action' => 'view', 'id' => $id), null, true);
			} else {
				$this->Session->setFlash('Chyba při ukládání dat zákazníka, zkontrolujte prosím formulář.');
			}
		} else {
			$this->Order->contain(array(
			));
			$this->data = $this->Order->read(null, $id);
		}
		$this->set('id', $id);
	}
	
	function admin_edit_payment($id = null){
		$this->Order->id = $id;
		$this->Order->save($this->data, false, array('payment_id'));
		$this->Session->setFlash('Způsob platby byl změněn.');
		$this->redirect(array('controller' => 'ordered_products', 'action' => 'edit', 'id' => $id));
	}
	
	function admin_edit_shipping($id = null){
		$this->Order->id = $id;
		$this->Order->save($this->data, false, array('shipping_id'));
		$this->Session->setFlash('Způsob dopravy byl změněn.');
		$this->redirect(array('controller' => 'ordered_products', 'action' => 'edit', 'id' => $id));
	}
	
	function admin_index(){
		$this->layout = 'admin';
		$statuses = $this->Order->Status->find('all', array(
			'contain' => array()
		));

		foreach ( $statuses as $key => $value ){
			$statuses[$key]['Status']['count'] = $this->Order->find('count', array(
				'conditions' => array('status_id' => $statuses[$key]['Status']['id'])
			));
		}
		$this->set('statuses', $statuses);

		// implicitne si vyhledavam do seznamu "otevrene" statusy
		$conditions = array('Status.closed' => '0');
		
		// kdyz chci omezit vypis na urcity status
		if ( isset( $this->params['named']['status_id'] ) ){
			$conditions = array('status_id' => $this->params['named']['status_id']);
		}
		
		$this->paginate['contain'] = array(
			'Status'
		);
		$this->paginate['order'] = array(
			'created' => 'desc'
		);

		$orders = $this->paginate('Order', $conditions);
		$this->set('orders', $orders);
	}
	
	function admin_view($id){
		$this->layout = 'admin';

		// nactu si data o objednavce
		$this->Order->contain(array(
			'OrderedProduct' => array(
				'Product',
				'OrderedProductsAttribute' => array(
					'Attribute' => array(
						'Option'
					)
				)
			),
			'Shipping',
			'Status',
			'Payment',
			'Ordernote' => array(
				'order' => array(
					'created' => 'desc'
				),
				'Status',
				'Administrator'
			)
		));
		$order = $this->Order->read(null, $id);

		// pokud je zadano spatne id, nic se nenacte,
		// osetrim presmerovanim
		if ( empty( $order ) ){
			$this->Session->setFlash('Neexistující objednávka!');
			$this->redirect(array('action' => 'index'), null, true);
		}

		// potrebuju vytahnout mozne statusy
		$statuses = $this->Order->Status->find('list');
		
		// predam data do view
		$this->set(compact(array('order', 'statuses', 'notes')));
	}
	
	function admin_delete($id) {
		if ($this->Order->del($id)) {
			$this->Session->setFlash('Objednávka byla odstraněna');
		} else {
			$this->Session->setFlash('Objednávku se nepodařilo odstranit, opakujte akci');
		}
		$this->redirect(array('controller' => 'orders', 'action' => 'index'));
	}
	
	function count($id){
		$count = $this->Order->find('count', array(
			'conditions' => array(
				'company_id' => $id
			)
		));
		return $count;
	}

	function users_duplicate($id = null){
		$this->layout = 'users';
		
		// osetrim volani metody bez IDecka
		if ( empty($id) ){
			$this->Session->setFlash('Neexistující objednávka.');
			$this->redirect(array('users' => true, 'controller' => 'orders', 'action' => 'index'), null, true);
		}
		
		// musim si objednavku nacist, zaroven osetrim pristup
		// k cizi objednavce pres company_id
		$order = $this->Order->find('first', array(
			'condtions' => array(
				'id' => $id,
				'company_id' => $this->Session->read('Company.id')
			),
			'fields' => array('id'),
			'contain' => array(
				'OrderedProduct' => array(
					'OrderedProductsAttribute'
				)
			)
		));
		
		if ( empty($order) ){
			$this->Session->setFlash('Neexistující objednávka.');
			$this->redirect(array('users' => true, 'controller' => 'orders', 'action' => 'index'), null, true);
		}
		
		// nachystam si data pro pridani do kosiku
		$data['CartsProduct'] = array();
		foreach ( $order['OrderedProduct'] as $op ){
			$pa = array();
			
			foreach ( $op['OrderedProductsAttribute'] as $opa ){
				$attribute = $this->Order->OrderedProduct->OrderedProductsAttribute->Attribute->find('first', array(
					'conditions' => array('Attribute.id' => $opa['attribute_id']),
					'contain' => array('Option')
				));
				$pa[] = array(
					'Option' => array(
						'id' => $attribute['Option']['id'],
						'name' => $attribute['Option']['name']
					),
					'Value' => array(
						'name' => $attribute['Attribute']['value']
					)
				);
			}
			
			$subproduct = $this->requestAction('/subproducts/get_subproduct/' . $op['product_id']. '/' . base64_encode(serialize($pa)));
			
			$data['CartsProduct'][] = array(
				'product_id' => $op['product_id'],
				'quantity' => $op['product_quantity'],
				'product_attributes' => serialize($pa),
				'subproduct_id' => $subproduct['Subproduct']['id']
			);
		}
		
		
		// data jsou nachystana, musim si zalozit novy kosik
		App::import('Model', 'Cart');
		$this->Cart = &new Cart;
		
		// vytvorim novy kosik (objednavku)
		$cart_id = $this->Cart->_create($this->Session->read('Company.id'));
		$cart = $this->Cart->find('first', array(
			'conditions' => array(
				'id' => $cart_id
			),
			'contain' => array()
		));
		
		$this->Session->write('Cart', $cart['Cart']);

		// pridam si vsechny produkty z objednavky do kosiku
		$this->Cart->add($data);
		
		// presmeruju na rekapitulaci a dam hlasku
		$this->Session->setFlash('Objednávka byla zkopírována a je nyní uložena jako rozpracovaná.');
		$this->redirect(array('users' => true, 'controller' => 'carts', 'action' => 'view'), null, true);
	}
	
	function users_finalize(){
		if ( !isset($this->data) ){
			$this->Session->setFlash('Přeskočil(a) jste rekapitulaci objednávky.');
			$this->redirect(array('users' => true, 'controller' => 'orders', 'action' => 'recap'), null, true);
		}
		
		// nactu si kosik a zkontroluju, jestli je neco v kosiku,
		// pokud v kosiku nic neni, musim presmerovat na vypis objednavky

		// nactu si objednane zbozi, pres importovany model kosiku
		App::import('Model', 'Cart');
		$this->Cart = &new Cart;
		
		$cart = $this->Cart->find('first', array(
			'conditions' => array(
				'id' => $this->Session->read('Cart.id')
			),
			'contain' => array(
				'CartsProduct' => array(
					'Product' => array(
						'TaxClass'
					),
				)
			)
		));
		
		if ( empty($cart['CartsProduct']) ){
			$this->Session->setFlash('Objednávka neobsahuje žádné položky, nemůže být proto odeslána.');
			$this->redirect(array('users' => true, 'controller' => 'carts', 'action' => 'view'), null, true);
		}
		
		// kosik obsahuje zbozi, muzu si zacit chystat vlozeni do databaze
		// natahnout si data o spolecnosti
		$company = $this->Order->Company->find('first', array(
			'conditions' => array(
				'Company.id' => $this->Session->read('Company.id')
			),
			'contain' => array()
		));		
		
		// zacnu sestavovat data
		$order_data['Order'] = array(
			'company_id' => $company['Company']['id'],
			'company_name' => $company['Company']['name'],
			'company_ico' => $company['Company']['ico'],
			'company_dic' => $company['Company']['dic'],
			'person_first_name' => $company['Company']['person_first_name'],
			'person_last_name' => $company['Company']['person_last_name'],
			'person_phone' => $company['Company']['person_phone'],
			'person_email' => $company['Company']['person_email'],
			'payment_name' => $company['Company']['payment_name'],
			'payment_street' => $company['Company']['payment_street'] . ' ' . $company['Company']['payment_street_number'],
			'payment_postal_code' => $company['Company']['payment_postal_code'],
			'payment_city' => $company['Company']['payment_city'],
			'delivery_name' => $company['Company']['delivery_name'],
			'delivery_street' => $company['Company']['delivery_street'] . ' ' . $company['Company']['delivery_street_number'],
			'delivery_postal_code' => $company['Company']['delivery_postal_code'],
			'delivery_city' => $company['Company']['delivery_city'],
			'status_id' => '1', // automaticky jako neprevzata
		);
		
		$this->data['Order'] = array_merge($this->data['Order'], $order_data['Order']);

		// muzu zalozit objednavku
		if ( !$this->Order->save($this->data['Order']) ){
			$this->Session->setFlash('Objednávku se nepodařilo uložit, zkuste to prosím znovu.');
			$this->redirect(array('users' => true, 'controller' => 'orders', 'action' => 'recap'), null, true);
		}

		// nacist si co je v kosiku a vsechno prepocitat
		// prochazim vsechny produkty v objednavce
		$order_total_price = 0; // inicializace celkove ceny objednavky
		$order_total_price_tax = 0; //inicializace celkove ceny objednavky s DPH
		foreach ( $cart['CartsProduct'] as $cp ){
			// sestavim si data pro produkt
			// potrebuju cenu s dani
			// zjistim rozdil ceny produktu a ceny produktu v kosiku ==> prirustkovou cenu
			// k prirustkove cene pripocitam dan a sectu cenu produktu s dani s prirustkovou cenou s dani
			$subproducts_price = $cp['price'] - $cp['Product']['price'];
			$subproducts_price_tax = $subproducts_price + ($subproducts_price * $cp['Product']['TaxClass']['value']) / 100;
			$cp['price_tax'] = $cp['Product']['price_tax'] + $subproducts_price_tax;
			$ordered_product_data = array(
				'order_id' => $this->Order->id,
				'product_id' => $cp['product_id'],
				//'product_price' => $cp['Product']['price'],
				// cenou neni cena produktu, ale cena produktu v kosiku
				'product_price' => $cp['price'],
				//'product_price_tax' => $cp['Product']['price_tax'],
				'product_price_tax' => $cp['price_tax'],
				'product_quantity' => $cp['quantity']
			);
			$order_total_price = $order_total_price + $cp['price'] * $cp['quantity'];
			$order_total_price_tax = $order_total_price_tax + $cp['price_tax'] * $cp['quantity'];
			
			// ukladam data o objednanem produktu
			// musim si zresetovat IDecko aby se me to neupdatovalo
			unset($this->Order->OrderedProduct->id);
			if ( !$this->Order->OrderedProduct->save($ordered_product_data) ){
				$this->Session->setFlash('Objednávané produkty se nepodařilo uložit, zkuste to prosím znovu.');
				$this->redirect(array('users' => true, 'controller' => 'orders', 'action' => 'recap'), null, true);
			}
			$ordered_product_id = $this->Order->OrderedProduct->getLastInsertID();
			
			// projdu si, zda ma produkt varianty a ulozim je
			$product_attributes = unserialize($cp['product_attributes']);

			if ( !empty($product_attributes) ){
				foreach ( $product_attributes as $pa ){
					$attribute = $this->Order->OrderedProduct->OrderedProductsAttribute->Attribute->find('first', array(
						'conditions' => array(
							'option_id' => $pa['Option']['id'],
							'value' => $pa['Value']['name']
						)
					));
					$ordered_product_attribute_data = array(
						'option_id' => $pa['Option']['id'],
						'option_name' => $pa['Option']['name'],
						'value_name' => $pa['Value']['name'],
						'attribute_id' => $attribute['Attribute']['id'],
						'ordered_product_id' => $ordered_product_id
					);
					
					// musim si zresetovat IDecko aby se me to neupdatovalo
					unset($this->Order->OrderedProduct->OrderedProductsAttribute->id);
					if ( !$this->Order->OrderedProduct->OrderedProductsAttribute->save($ordered_product_attribute_data) ){
						$this->Session->setFlash('Atributy objednávaných produktů se nepodařilo uložit, zkuste to prosím znovu.');
						$this->redirect(array('users' => true, 'controller' => 'orders', 'action' => 'recap'), null, true);
					}
				}
			}
		}
		
		// musim upravit cenu za dopravu a celkovou cenu objednavky
		$shipping = $this->Order->Shipping->find('first', array(
			'conditions' => array('Shipping.id' => $this->data['Order']['shipping_id']),
			'contain' => array()
		));
		
		$order_update_data = array(
			'subtotal' => $order_total_price,
			'subtotal_tax' => $order_total_price_tax,
			'shipping_cost' => 0
		);
		
		if ( $shipping['Shipping']['price'] > 0 && $order_total_price < $shipping['Shipping']['free'] ){
			$order_update_data['shipping_cost'] = $shipping['Shipping']['price'];
		}
		
		if ( !$this->Order->save($order_update_data) ){
			$this->Session->setFlash('Objednávka nebyla správně upravena, zkuste to prosím znovu.');
			$this->redirect(array('users' => true, 'controller' => 'orders', 'action' => 'recap'), null, true);
		}

		// notifikovat uzivatele o spravnem ulozeni objednavky do db
		$this->Order->Company->notify_new_order($this->Order->id, $this->Session->read('Company.id'));

		// notifikovat administratory o spravnem ulozeni objednavky do db
		$this->Order->notify_admins_new_order($this->Order->id);
		
		// musim zrusit kosik v db a v session
		// nejdrive vymazu vsechny produkty v danem kosiku
		$this->Cart->delete($cart['Cart']['id'], true);
		
		// nasledne smazu kosik z db a potom ze session
		$this->Session->delete('Cart');

		// nastavim flash a presmeruju
		$this->Session->setFlash('Vaše objednávka byla uložena.');
		$this->redirect(array('users' => true, 'controller' => 'orders', 'action' => 'index'), null, true);
	}

	function users_index(){
		$this->layout = 'users';
		
		$orders = $this->Order->find('all', array(
			'conditions' => array(
				'Order.company_id' => $this->Session->read('Company.id')
			),
			'contain' => array('Status'),
			'order' => array(
				'Order.created' => 'desc'
			)
		));
		$this->set('orders', $orders);
	}
	
	function users_view($id = null){
		$this->layout = 'users';
		
		$order = $this->Order->find('first', array(
			'conditions' => array(
				'Order.company_id' => $this->Session->read('Company.id'),
				'Order.id' => $id
			),
			'fields' => array('id', 'shipping_cost', 'subtotal', 'subtotal_tax'),
			'contain' => array(
				'OrderedProduct' => array(
					'fields' => array('id', 'product_price', 'product_price_tax', 'product_quantity'),
					'Product' => array(
						'fields' => array('id', 'name')
					),
					'OrderedProductsAttribute' => array(
						'Attribute' => array(
							'fields' => array('value'),
							'Option' => array(
								'fields' => array('name')
							)
						)
					)
				),
				'Shipping' => array(
					'fields' => array('id', 'name')
				),
				'Payment' => array(
					'fields' => array('id', 'name')
				)
			)
		));
		if ( empty($order) ){
			$this->Session->setFlash('Neexistující objednávka.');
			$this->redirect(array('users' => true, 'controller' => 'orders', 'action' => 'index'), null, true);
		}
		
		$this->set('order', $order);
	}
	
	function users_recap(){
		$this->layout = 'users';

		// vytahnu si data o spolecnosti
		$company = $this->Order->Company->find('first', array(
			'conditions' => array(
				'Company.id' => $this->Session->read('Company.id')
			),
			'contain' => array()
		));
		$this->set('company', $company);
		
		// nactu si objednane zbozi, pres importovany model kosiku
		App::import('Model', 'Cart');
		$this->Cart = &new Cart;
		
		$cart = $this->Cart->find('first', array(
			'conditions' => array(
				'id' => $this->Session->read('Cart.id')
			),
			'contain' => array(
				'CartsProduct' => array(
					'Product' => array(
						'TaxClass'
					)
				)
			)
		));
		$this->set('cart', $cart);

		// potrebuju znat zpusoby dodani
		$shippings = $this->Order->Shipping->find('list');
		$this->set('shippings', $shippings);
		
		// potrebuju znat zpusoby platby
		$payments = $this->Order->Payment->find('list');
		$this->set('payments', $payments);
	}

	function rep_index() {
		$this->layout = 'rep';
		
		/// musim si vyjmenovat vsechny jmenne atributy, jinak nefunguje prefix 'rep' dohromady se jmennymi atributy
		Router::connectNamed(array('page', 'direction', 'sort', 'status_id', 'company_id'));
		
		// hlidam, jestli jdu ze seznamu zakazniku a jake objednavky chci vlastne vypisovat
		// aktualni mesic
		if (isset($this->params['named']['month'])) {
			$actual_month = array(
				'start_date' => array(
					'day' => 1,
					'month' => date('m'),
					'year' => date('Y')
				),
				'end_date' => array(
					'day' => date('t'),
					'month' => date('m'),
					'year' => date('Y')
				)
			);
			$this->Session->write('Rep.order_dates', $actual_month);
			unset($this->passedArgs['month']);
			// nebo uplne vsechny objednavky
		} elseif (isset($this->params['named']['all'])) {
			$all = array(
				'start_date' => array(
					'day' => 1,
					'month' => 1,
					'year' => 2009
				),
				'end_date' => array(
					'day' => date('t'),
					'month' => date('m'),
					'year' => date('Y')
				)
			);
			$this->Session->write('Rep.order_dates', $all);
			unset($this->passedArgs['all']);
		}
		
		// nactu si prihlaseneho repa
		$rep_id = $this->Session->read('Rep.id');
		
		App::import('Model', 'Rep');
		$this->Rep = &new Rep;
		
		$this->Rep->id = $rep_id;
		$rep = $this->Rep->read();

		// pokud ma rep prideleny oblasti 
		if ( !empty($rep['RepArea']) ) {
			if (isset($this->data)) {
				// musim si zapamatovat zvolene hodnoty do session, aby s nima mohl dal pracovat
				$this->Session->write('Rep.order_dates', $this->data['Order']);
			}

			$order_dates = $this->Session->read('Rep.order_dates');
			// podminka pro vyber objednavek v zadanem casovem obdobi
			$date_condition = array();
			if (!empty($order_dates)) {
				$date_condition = array(
					'AND' => array(
						'created >=' => $order_dates['start_date']['year'] . '-' . $order_dates['start_date']['month'] . '-' . $order_dates['start_date']['day'],
						'created <=' => $order_dates['end_date']['year'] . '-' . $order_dates['end_date']['month'] . '-' . $order_dates['end_date']['day']
					)
				);
				$this->data['Order']['start_date'] = $order_dates['start_date'];
				$this->data['Order']['end_date'] = $order_dates['end_date'];
			}
			
			// podminka pro vyber objednavek pouze od zvolene spolecnost
			$company_condition = array();
			if (isset($this->params['named']['company_id'])) {
				$company_condition = array('company_id' => $this->params['named']['company_id']);
				$this->Order->Company->id = $this->params['named']['company_id'];
				$this->Order->Company->contain();
				$company = $this->Order->Company->read();
				$this->set('company_name', $company['Company']['name']);
			}
			
			// podminka pro vyber objednavek pouze z repovych oblasti
			$areas_conditions = array();
			foreach ($rep['RepArea'] as $area) {
				$area_condition = array(
					'AND' => array(
						'delivery_postal_code >=' => $area['start_zip'],
						'delivery_postal_code <=' => $area['end_zip'],
					)
				);
				
				$areas_conditions['OR'][] = $area_condition;
			}
			
			// spojim si pripravene podminky do vysledne
			$rep_conditions = array(
				'AND' => $date_condition + $company_condition + $areas_conditions
			);
		
			$statuses = $this->Order->Status->find('all', array(
				'contain' => array()
			));

			// pocet vsech objednavek odpovidajicim podminkam
			$orders_count = $this->Order->find('count', array(
				'conditions' => $rep_conditions,
				'contain' => array()
			));
			$this->set('orders_count', $orders_count);
			
			// pocty objednavek v danem stavu
			foreach ( $statuses as $key => $value ){
				$statuses[$key]['Status']['count'] = $this->Order->find('count', array(
					'conditions' => array_merge(array('status_id' => $statuses[$key]['Status']['id'], $rep_conditions)),
					'contain' => array()
				));
			}

			$this->set('statuses', $statuses);

			// implicitne si vyhledavam do seznamu "otevrene" statusy
			//$conditions = array('Status.closed' => '0');
			$conditions = array();
		
			// kdyz chci omezit vypis na urcity status
			if ( isset( $this->params['named']['status_id'] ) ){
				$conditions = array('status_id' => $this->params['named']['status_id']);
			}

			$this->paginate['contain'] = array(
				'Status'
			);
			$this->paginate['order'] = array(
				'created' => 'desc'
			);

			$conditions = array_merge($conditions, $rep_conditions);
		
			$orders = $this->paginate('Order', $conditions);

			$this->set('orders', $orders);
		} else {
			$this->set('no_areas', true);
		}
	}
	
	function rep_view($id) {
		$this->layout = 'rep';
		
		$rep_id = $this->Session->read('Rep.id');
		App::import('Model', 'Rep');
		$this->Rep = &new Rep;
		
		$this->Rep->id = $rep_id;
		$rep = $this->Rep->read();
		
		if (!empty($rep['RepArea'])) {
			// podminka pro vyber objednavek pouze z repovych oblasti
			$areas_conditions = array();
			foreach ($rep['RepArea'] as $area) {
				$area_condition = array(
					'AND' => array(
						'delivery_postal_code >=' => $area['start_zip'],
						'delivery_postal_code <=' => $area['end_zip'],
					)
				);
				
				$areas_conditions['OR'][] = $area_condition;
			}
		}

		// nactu si data o objednavce
		$contain = array(
			'OrderedProduct' => array(
				'Product',
				'OrderedProductsAttribute'
			),
			'Shipping',
			'Status',
			'Payment',
			'Ordernote' => array(
				'order' => array(
					'created' => 'desc'
				),
				'Status',
				'Administrator'
			)
		);
		
		$order = $this->Order->find('first', array(
			'conditions' => $areas_conditions + array('Order.id' => $id),
			'contain' => $contain
		));

		// pokud je zadano spatne id, nic se nenacte,
		// osetrim presmerovanim
		if ( empty( $order ) ){
			$this->Session->setFlash('Neexistující objednávka!');
			$this->redirect(array('rep' => true, 'controller' => 'orders', 'action' => 'index'), null, true);
		}
		
		// predam data do view
		$this->set(compact(array('order', 'notes')));
	}
}
?>