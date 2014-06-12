<?php
class OrdersController extends AppController {
	var $name = 'Orders';

	var $helpers = array('Form');

	var $paginate = array(
		'limit' => 20,
		'order' => array(
			'Order.created' => 'desc'
		),
	);

	function admin_index(){
		$this->Order->Status->recursive = -1;
		$statuses = $this->Order->Status->find('all');
		foreach ( $statuses as $key => $value ){
			$statuses[$key]['Status']['count'] = $this->Order->find('count', array(
				'conditions' => array('status_id' => $statuses[$key]['Status']['id'])
			));
		}
		$this->set('statuses', $statuses);
		// implicitne si vyhledavam do seznamu "otevrene" statusy
		$conditions = array('Status.closed' => false);
		// kdyz chci omezit vypis na urcity status
		if ( isset( $this->params['named']['status_id'] ) ){
			$conditions = array('status_id' => $this->params['named']['status_id']);
		}
		$orders = $this->paginate('Order', $conditions);
		$this->set('orders', $orders);
	}

	function admin_view($id) {
		// nactu si data o objednavce
		$this->Order->contain(array(
			'OrderedProduct' => array(
				'OrderedProductsAttribute' => array(
					'Attribute' => array(
						'Option'
					)
				),
				'Product' => array(
					'fields' => array('id', 'name', 'url', 'manufacturer_id')
				),
			),
			'Shipping',
			'Customer',
			'Status',
			'Payment',
			'Ordernote' => array(
				'Status' => array(
					'fields' => array('id', 'name')
				),
				'Administrator' => array(
					'fields' => array('id', 'first_name', 'last_name')
				)
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
		$manufacturers = $this->Order->OrderedProduct->Product->Manufacturer->find('list');

		// predam data do view
		$this->set(compact(array('order', 'statuses', 'notes', 'manufacturers')));

	}

	function admin_delete($id){
		$this->Order->delete($id, true);
		$this->Session->setFlash('Objednávka byla smazána!');
		$this->redirect(array('action' => 'index'), null, true);
	}

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
			$this->redirect(array('action' => 'view', $this->Order->id), null, true);
		} else {
			$message = implode("<br />", $valid_requested_fields);
			$this->Session->setFlash('Chyba při změně statusu!<br />' . $message);
			$this->redirect(array('action' => 'view', $this->Order->id), null, true);
		}
	}

	function admin_edit_payment($id = null){
		$this->Order->id = $id;
		$this->Order->save($this->data, false, array('payment_id'));
		$this->Session->setFlash('Způsob platby byl změněn.');
		$this->redirect(array('controller' => 'ordered_products', 'action' => 'edit', $id));
	}
	
	function admin_edit_shipping($id = null){
		$this->Order->id = $id;
		$this->Order->save($this->data, false, array('shipping_id'));
		$this->Session->setFlash('Způsob dopravy byl změněn.');
		$this->redirect(array('controller' => 'ordered_products', 'action' => 'edit', $id));
	}
	
	/**
	 * Kontroluje stavy nedorucenych objednavek podle dopravcu.
	 *
	 */
	function admin_track(){
		$this->Order->recursive = -1;
		
		$orders = $this->Order->find('all',
			array('conditions' => array(
					// nekontroluju 4 - dorucene objednavky
					// 8 - objednavka vracena
					// 5 - storno
					"NOT" => array(
						"status_id" => array('4', '8', '5')
					),
 					"shipping_number != ''",
					//'id' => 3914
				),
				'fields' => array('id', 'shipping_id')
			)
		);

		$bad_orders = array();
		foreach( $orders as $order ){
			// rozlisit zpusob doruceni
			switch ( $order['Order']['shipping_id'] ){
				case "2":
					// ceska posta - dobirka
					$result = $this->Order->track_cpost($order['Order']['id']);
				break;
				case "9":
					// ceska posta - platba predem
					$result = $this->Order->track_cpost($order['Order']['id']);
				break;
				default:
					$result = $order['Order']['id'];
				break;
			}
			
			if ( $result !== true ){
				$bad_orders[] = $result;
			}
		}

		$this->set('bad_orders', $bad_orders);
	}
	
	function address_edit(){
		// navolim si layout, ktery se pouzije
		$this->layout = 'content';
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Rekapitulace objednávky', 'href' => '/rekapitulace-objednavky'),
			array('anchor' => 'Úprava adresy', 'href' => $_SERVER['REQUEST_URI'])
		);
		$this->set('breadcrumbs', $breadcrumbs);

		// nastavim si pro menu zakladni idecko
		$this->set('opened_category_id', 1);

		// nastavim si nadpis stranky
		$this->set('page_heading', 'Úprava adresy');
		
		if ( isset($this->data) ){
			// musi byt validni data
			$this->Order->Customer->Address->set($this->data);
			if ( $this->Order->Customer->Address->validates() ){
				$this->data['Address']['type'] = $this->params['named']['type'];
				switch ( $this->params['named']['type'] ){
					case "d":
						$this->Session->write('Address', $this->data['Address']);
					break;
					case "f":
						$this->Session->write('Address_payment', $this->data['Address']);
					break;
				}
				$this->Session->setFlash('Adresa byla upravena.');
				$this->redirect(array('controller' => 'orders', 'action' => 'recapitulation'), null, true);
			} else {
				$this->Session->setFlash('Některé údaje nejsou správně vyplněny, zkontrolujte prosím formulář.');
			}
		} else {
			// musim rozlisit, kterou adresu edituju
			switch ( $this->params['named']['type'] ){
				case "d":
					$this->data['Address'] = $this->Session->read('Address');
				break;
				case "f":
					$this->data['Address'] = $this->Session->read('Address_payment');
				break;
			}
		}
	}
	
	function add(){
		if ( $this->Session->check('Customer.id') ){
			if ( !isset($this->data) ){
				$this->Order->Customer->Address->recursive = -1;
				$address = $this->Order->Customer->Address->find(array('customer_id' => $this->Session->read('Customer.id'), 'type' => 'd'));
				if ( $this->Order->Customer->Address->save($address) ){
					$address_payment = $this->Order->Customer->Address->find(array('customer_id' => $this->Session->read('Customer.id'), 'type' => 'f'));
					if ( !$this->Order->Customer->Address->save($address_payment) ){
						$this->Session->setFlash('Vložte prosím Vaši fakturační adresu a klikněte znovu na "Zaplatit".');
						$this->redirect(array('controller' => 'customers', 'action' => 'address_edit', 'type' => 'f'));
					}
				} else {
					$this->Session->setFlash('Vložte prosím Vaši doručovací adresu a klikněte znovu na "Zaplatit".');
					$this->redirect(array('controller' => 'customers', 'action' => 'address_edit', 'type' => 'd'));
				}
			} else {
				$address = $this->Order->Customer->Address->find(array('customer_id' => $this->Session->read('Customer.id'), 'type' => 'd'));
				$address_payment = $this->Order->Customer->Address->find(array('customer_id' => $this->Session->read('Customer.id'), 'type' => 'f'));
				$this->Session->write('Address', $address['Address']);
				$this->Session->write('Address_payment', $address_payment['Address']);
				$this->Session->write('Order', $this->data['Order']);
			}
		}

		// potrebuju si vytahnout statistiky o kosiku,
		// abych vedel zda je nejake zbozi v kosi
		
		// pripojim si model
		$this->Order->bindModel(array('hasOne' => array('CartsProduct')));
		// vytahnu si statistiky kosiku
		$cart_stats = $this->Order->CartsProduct->getStats($this->Order->CartsProduct->Cart->get_id());

		// zjistim pocet produktu v kosiku
		if ( $cart_stats['products_count'] == 0 ){
			// v kosiku neni zadne zbozi, dam hlasku a presmeruju na kosik
			$this->Session->setFlash('Nemáte žádné zboží v košíku, v objednávce proto nelze pokračovat.');
			$this->redirect(array('controller' => 'carts_products', 'action' => 'index'), null, true);
		}
		
		// vyzkousim, zda nemuzu preskocit rovnou na rekapitulaci
		if ( $this->Session->check('Customer') &&
			$this->Session->check('Address') &&
			$this->Session->check('Address_payment') &&
			$this->Session->check('Order.shipping_id')
		) {
			$this->redirect(array('controller' => 'orders', 'action' => 'recapitulation'));
		}
		
		// navolim si layout, ktery se pouzije
		$this->layout = 'content';

		// nastavim si pro menu zakladni idecko
		$this->set('opened_category_id', 1);

		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Nová objednávka', 'href' => '/orders/add')
		);
		$this->set('breadcrumbs', $breadcrumbs);
		
		// vytahnu si list pro select shippings
		$shipping_choices = $this->Order->Shipping->find('all', array(
			'contain' => array(),
			'fields' => array('id', 'name', 'price', 'free'),
			'order' => array('name' => 'asc')
		));
		// v selectu chci mit, kolik stoji doprava
		foreach ($shipping_choices as $shipping_choice) {
			$shipping_item = $shipping_choice['Shipping']['name'] . ' - ' . $shipping_choice['Shipping']['price'] . ' Kč';
			if ($cart_stats['total_price'] > $shipping_choice['Shipping']['free']) {
				$shipping_item = $shipping_choice['Shipping']['name'] . ' - zdarma';
			}
			$shipping_choices_list[$shipping_choice['Shipping']['id']] = $shipping_item;
		}
		
		$this->set('shipping_choices', $shipping_choices_list);
		
/*		// vytahnu si list pro select payments
		$payment_choices = $this->Order->Payment->find('list');
		$this->set('payment_choices', $payment_choices);*/

		// formular byl uz odeslan
		if ( isset( $this->data) && !empty($this->data) ){
			if ( empty($this->data['Address']['name']) ){
				$this->data['Address']['name'] = $this->data['Customer']['first_name'] . ' ' . $this->data['Customer']['last_name'];
			}
			
			// validace dat zakaznika
			$this->Order->Customer->set($this->data);
			$valid_customer = $this->Order->Customer->validates();
			
			// validace dat adresy
			$this->Order->Customer->Address->set($this->data);
			$valid_address = $this->Order->Customer->Address->validates();
			
			// jsou-li data validni
			if ( $valid_address && $valid_customer ){
				// v prvnim kroku se vklada pouze dorucovaci adresa
				$this->data['Address']['type'] = 'd';
				
				// poslu si dal data zakaznika, adresy a objednavky
				$this->Session->write('Customer', $this->data['Customer']);
				$this->Session->write('Address', $this->data['Address']);
				$this->Session->write('Order', $this->data['Order']);
				$this->redirect(array('action' => 'recapitulation'), null, true);
			} else {
				$this->Session->setFlash('Pro pokračování v objednávce vyplňte prosím všechna pole.');
			}
		}
	}

	function recapitulation(){
		if (!$this->Session->check('Order.shipping_id')) {
			$this->Session->setFlash('Není zvolena doprava pro Vaši objednávku');
			$this->redirect(array('controller' => 'carts_products', 'action' => 'index'));
		}
		
		$order = $this->Session->read('Order');
		$customer = $this->Session->read('Customer');
		$address = $this->Session->read('Address');

		if (!$this->Session->check('Address_payment')){
			$address_payment = $this->Session->read('Address');
			$address_payment['type'] = 'f';
			$this->Session->write('Address_payment', $address_payment);
		}
		$address_payment = $this->Session->read('Address_payment');

		// data o objednavce
		$this->set('order', $order);
		// data o zakaznikovi
		$this->set('customer', $customer);
		// data o adrese
		$this->set('address', $address);
		// data o adrese fakturacni
		$this->set('address_payment', $address_payment);
		
		// zakladni layout stranky
		$this->layout = 'content';
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Rekapitulace objednávky', 'href' => '/rekapitulace-objednavky')
		);
		$this->set('breadcrumbs', $breadcrumbs);

		// produkty ktere jsou v kosiku
		App::import('Model', 'CartsProduct');
		$this->CartsProduct = new CartsProduct;
		$cart_products = $this->CartsProduct->getProducts();
		if (empty($cart_products)) {
			$this->Session->setFlash('Nemáte žádné zboží v košíku, v objednávce proto nelze pokračovat.');
			$this->redirect(array('controller' => 'carts_products', 'action' => 'index'));
		}
		$this->set('cart_products', $cart_products);

		// potrebuju si projit produkty z kosiku,
		// pokud alespon jeden ma akci s dopravou zdarma,
		// cela objednavka je s dopravou zdarma
		$free_shipping = false; // prepoklad, ze doprava zdarma nebude
		foreach ( $cart_products as $cart_product ){
			// produkt nejaky priznak ma prirazen
			if ( !empty($cart_product['Product']['Flag']) ){
				// projdu vsechny priznaky
				foreach ( $cart_product['Product']['Flag'] as $flag ){
					// priznak pro dopravu zdarma je "1"
					if ( $flag['id'] == 1 && $cart_product['CartsProduct']['quantity'] >= $flag['FlagsProduct']['quantity'] ){
						$free_shipping = true;
					}
				}
			}
		}
		$this->set('free_shipping', $free_shipping);

		// vytahnu si data o zpusobu dopravy
		$shipping = $this->Order->Shipping->get_data($order['shipping_id']);
		// vytahnu si data o zpusobu platby
		$this->set(compact(array('shipping', 'payment')));
	}

	function shipping_edit(){
		// navolim si layout, ktery se pouzije
		$this->layout = 'content';
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Rekapitulace objednávky', 'href' => '/rekapitulace-objednavky'),
			array('anchor' => 'Způsob dopravy a platby', 'href' => '/orders/shipping_edit')
		);
		$this->set('breadcrumbs', $breadcrumbs);

		// nastavim si pro menu zakladni idecko
		$this->set('opened_category_id', 1);

		// pripojim si model
		$this->Order->bindModel(array('hasOne' => array('CartsProduct')));
		// vytahnu si statistiky kosiku
		$cart_stats = $this->Order->CartsProduct->getStats($this->requestAction('/carts/get_id'));
		
		
		// vytahnu si list pro select shippings
		$shipping_choices = $this->Order->Shipping->find('all', array(
			'contain' => array(),
			'fields' => array('id', 'name', 'price', 'free'),
			'order' => array('name' => 'asc')
		));
		// v selectu chci mit, kolik stoji doprava
		foreach ($shipping_choices as $shipping_choice) {
			$shipping_item = $shipping_choice['Shipping']['name'] . ' - ' . $shipping_choice['Shipping']['price'] . ' Kč';
			if ($cart_stats['total_price'] > $shipping_choice['Shipping']['free']) {
				$shipping_item = $shipping_choice['Shipping']['name'] . ' - zdarma';
			}
			$shipping_choices_list[$shipping_choice['Shipping']['id']] = $shipping_item;
		}
		
		$this->set('shipping_choices', $shipping_choices_list);
		
		if ( isset($this->data) ){
			$this->Session->write('Order', $this->data['Order']);
			$this->Session->setFlash('Objednávka byla upravena.');
			$this->redirect(array('controller' => 'orders', 'action' => 'recapitulation')); 
		} else {
			$this->data['Order'] = $this->Session->read('Order');
		}
	}
	
	function finalize(){
		if (!$this->Session->check('Order.shipping_id')) {
			$this->Session->setFlash('Není zvolena doprava pro Vaši objednávku');
			$this->redirect(array('controller' => 'carts_products', 'action' => 'index'));
		}
		
		if (!$this->Session->check('Customer.id')){
			$sess_customer = $this->Session->read('Customer');
			// jedna se o neprihlaseneho a nezaregistrovaneho zakaznika
			// musim vytvorit novy zakaznicky ucet,
			// takze vygeneruju login a heslo
			$customer['Customer'] = $sess_customer;
			$customer['Customer']['login'] = $this->Order->Customer->generateLogin($sess_customer);
			$customer_password = $this->Order->Customer->generatePassword($sess_customer['last_name']);
			$customer['Customer']['password'] = md5($customer_password);
			$customer['Customer']['confirmed'] = 1;
			$customer['Customer']['registration_source'] = 'eshop';
			
			// pridam adresy
			$customer['Address'][] = $this->Session->read('Address');
			$customer['Address'][] = $this->Session->read('Address_payment');

			// ulozim si bokem heslo v nezahashovane podobe,
			// v dalsim pohledu mu ho chci vypsat
			$this->Session->write('cpass', $customer_password);
		
			$c_dataSource = $this->Order->Customer->getDataSource();
			$c_dataSource->begin($this->Order->Customer);
			try {
				$this->Order->Customer->saveAll($customer);
			} catch (Exception $e) {
				$c_dataSource->rollback($this->Order->Customer);
				$this->Session->setFlash('Nepodařilo se uložit data o zákazníkovi, zopakujte prosím dokončení objednávky.');
				$this->redirect(array('controller' => 'orders', 'action' => 'recapitulation'));
			}
			$c_dataSource->commit($this->Order->Customer);
			
			// jedna se o nove zalozeny zakaznicky ucet,
			// takze mu poslu notifikaci, pokud pri registraci
			// uvedl svou emailovou adresu
			$this->Order->Customer->notify_account_created($customer['Customer']);
			$customer['Customer']['id'] = $this->Order->Customer->id;
		} else {
			// zakaznik je zaregistrovany, takze jen nastavim
			// jeho id podle session a updatenu, kdyby nahodou
			// menil nektere ze svych kontaktnich udaju
			$customer = $this->Order->Customer->find('first', array(
				'conditions' => array('Customer.id' => $this->Session->read('Customer.id')),
				'contain' => array(
					'Address' => array(
						'order' => array('Address.type' => 'asc')
					)
				)
			));
		}

		//data pro objednavku
		$order = $this->Order->build($customer);

		$dataSource = $this->Order->getDataSource();
		$dataSource->begin($this->Order);
		try {
			$this->Order->save($order[0]);
			// musim ulozit objednavku a smazat produkty z kosiku
			foreach ($order[1] as $ordered_product) {
				$ordered_product['OrderedProduct']['order_id'] = $this->Order->id;
				$this->Order->OrderedProduct->saveAll($ordered_product);
			}
		} catch (Exception $e) {
			$dataSource->rollback($this->Order);
			$this->Session->setFlash('Uložení objednávky se nepodařilo, zopakujte prosím znovu dokončení objednávky.');
			$this->redirect(array('controller' => 'orders', 'action' => 'recapitulation'));
		}
		$this->Order->cleanCartsProducts();
		$dataSource->commit($this->Order);
		
		$this->Order->notifyCustomer($customer['Customer']);

		$this->Order->notifyAdmin();

		// uklidim promenne
		$this->Session->delete('Order');
		
		// potrebuju na dekovaci strance vedet cislo objednavky
		$this->Session->write('Order.id', $this->Order->id);

		// nastavim hlasku a presmeruju
		$this->Session->setFlash('Vaše objednávka byla úspešně uložena!');
		$this->redirect(array('action' => 'finished'), null, true);
	} // konec funkce
	
	function finished(){
		$id = $this->Session->read('Order.id');
		if (empty($id)){
			$this->redirect(array('controller' => 'carts_products', 'action' => 'index'), null, true);
		}
		
		if (!$this->Session->check('Customer.id')){
			// tenhle zaznam mazu jen kdyz se jedna o neprihlaseneho
			$this->Session->delete('Customer');
		}

		// smazu zaznamy o objednavce ze session
		$pass = $this->Session->read('cpass');
		$this->Session->delete('Order');
		$this->Session->delete('Address');
		$this->Session->delete('Address_payment');
		$this->Session->delete('cpass');
				
		// navolim si layout, ktery se pouzije
		$this->layout = 'content';
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Děkujeme', 'href' => '/orders/finished')
		);
		$this->set('breadcrumbs', $breadcrumbs);

		// nastavim si pro menu zakladni idecko
		$this->set('opened_category_id', 1);
		
		$conditions = array(
			'Order.id' => $id
		);
		
		$contain = array(
			'OrderedProduct' => array(
				'fields' => array(
					'id', 'product_id', 'product_price_with_dph', 'product_quantity'
				),
				'OrderedProductsAttribute',
				'Product' => array(
					'fields' => array(
						'id', 'name', 'tax_class_id'
					),
					'TaxClass' => array(
						'fields' => array(
							'id', 'value'
						)
					)
				)
			),
			'Payment'
		);
		
		$fields = array('id', 'subtotal_with_dph', 'shipping_cost', 'customer_city', 'customer_state', 'customer_email');
		
		$order = $this->Order->find('first', array(
			'conditions' => $conditions,
			'contain' => $contain,
			'fields' => $fields
		));

		$jscript_code = '';
		// celkova dan vsech produktu v objednavce
		$tax_value = 0;
		
 		// heureka overeno zakazniky
		App::import('Vendor', 'HeurekaOvereno', array('file' => 'HeurekaOvereno.php'));
		try {
			$overeno = new HeurekaOvereno('d4cf48ecaecd27fe328c7699c3a8904b');
			$overeno->setEmail($order['Order']['customer_email']);
			foreach ($order['OrderedProduct'] as $op) {
				$overeno->addProductItemId($op['Product']['id']);
				$overeno->addProduct($op['Product']['name']);
			}
			$overeno->addOrderId($order['Order']['id']);
			$overeno->send();
		} catch (Exception $e) {}

		// js kod pro GA
		foreach ( $order['OrderedProduct'] as $op ){
			$sku = $op['Product']['id'];
			$variations = '';
			
			// dan pro konkretni produkt
			$p_tax_value = $op['product_price_with_dph'] - (round($op['product_price_with_dph'] / (1 + ($op['Product']['TaxClass']['value'] / 100)), 0));

			$tax_value = $tax_value + $p_tax_value;
			
			foreach ( $op['OrderedProductsAttribute'] as $opa ){
				$variations[] = $opa['option_name'] . ': ' . $opa['value_name'];
			}
			
			if ( !empty($variations) ){
				$sku .= ' / ' . implode(' - ', $variations);
				$variations = implode(' - ', $variations);
			}
			
			// add item might be called for every item in the shopping cart
			// where your ecommerce engine loops through each item in the cart and
			// prints out _addItem for each
			$jscript_code .= "
				_gaq.push(['_addItem',
					'" . $order['Order']['id'] . "',           // order ID - required
					'" . $sku ."',           // SKU/code - required
					'" . $op['Product']['name'] . "',        // product name
					'" . $variations . "',   // category or variation
					'" . $op['product_price_with_dph'] . "',          // unit price - required
					'" . $op['product_quantity'] . "'               // quantity - required
				]);
			";
		}

		$jscript_code = "
			_gaq.push(['_addTrans',
				'" . $order['Order']['id'] . "',           // order ID - required
				'www." . CUST_ROOT . "',  // affiliation or store name
				'" . $order['Order']['orderfinaltotal'] . "',          // total - required
				'" . $tax_value . "',           // tax
				'" . $order['Order']['shipping_cost'] . "',              // shipping
				'" . $order['Order']['customer_city'] . "',       // city
				'',     // state or province
				'" . $order['Order']['customer_state'] . "'             // country
			]);
		" . "\n\n" . $jscript_code;
		
		$jscript_code .= "\n\n" . "_gaq.push(['_trackTrans']);"; //submits transaction to the Analytics servers

		$contents = file_get_contents('js/ga-add.js');
		
		$jscript_code = str_replace('//GA DATA', $jscript_code, $contents);

		$this->set('jscript_code', $jscript_code);

		// vytahnu info o objednavce
		$order = $this->Order->read(null, $id);
		$order['Customer']['password'] = $pass;
		$this->set('order', $order);
	}
	
	function admin_old_import() {
		$my_orders = $this->Order->find('all', array(
			'contain' => array(),
			'fields' => array('id')
		));
		$my_order_ids = Set::extract('/Order/id', $my_orders);
		
		$conditions = '';
		if (!empty($my_orders)) {
			$conditions = ' WHERE OldOrder.id NOT IN (' . implode(',', $my_order_ids) . ')';
		}
		
		$old_orders = $this->Order->query("
			SELECT *
			FROM old_orders AS OldOrder
			" . $conditions
		);

		foreach ($old_orders as $old_order) {
			// sestavim zakaznika s jeho adresou
			$customer = array(
				'Customer' => array(
					'first_name' => $old_order['OldOrder']['name'],
					'last_name' => $old_order['OldOrder']['surname'],
					'phone' => $old_order['OldOrder']['phone'],
					'email' => $old_order['OldOrder']['email'],
					'company_name' => $old_order['OldOrder']['company'],
					'company_ico' => $old_order['OldOrder']['ico'],
					'company_dic' => $old_order['OldOrder']['dic'],
					'registration_source' => 'eshop',
					'confirmed' => true,
					'newsletter' => true,
					'created' => $old_order['OldOrder']['date'],
				),
				'Address' => array(
					array(
						'name' => $old_order['OldOrder']['name'] . ' ' . $old_order['OldOrder']['surname'],
						'street' => $old_order['OldOrder']['address'],
						'zip' => $old_order['OldOrder']['psc'],
						'city' => $old_order['OldOrder']['city'],
						'state' => 'Česká republika',
						'type' => 'd'
					),
					array(
						'name' => (empty($old_order['OldOrder']['f_name']) ? $old_order['OldOrder']['name'] . ' ' . $old_order['OldOrder']['surname'] : $old_order['OldOrder']['f_name']),
						'street' => (empty($old_order['OldOrder']['f_address']) ? $old_order['OldOrder']['address'] : $old_order['OldOrder']['f_address']),
						'zip' => (empty($old_order['OldOrder']['f_psc']) ? $old_order['OldOrder']['psc'] : $old_order['OldOrder']['f_psc']),
						'city' => (empty($old_order['OldOrder']['f_city']) ? $old_order['OldOrder']['city'] : $old_order['OldOrder']['f_city']),
						'state' => 'Česká republika',
						'type' => 'f'
					)
				)
			);
			// dogeneruju login a heslo
			$customer['Customer']['login'] = $this->Order->Customer->generateLogin($customer['Customer']);
			$customer['Customer']['password'] = $this->Order->Customer->generatePassword($customer['Customer']);
			
			// ulozim zakaznika i s adresou
			if (!$this->Order->Customer->saveAll($customer, array('validate' => false))) {
				debug($old_order);
				debug($customer);
				die('nepodarilo se ulozit zakaznika');
			}
			
			// prevedu jejich info o stavu objednavky do naseho
			switch ($old_order['OldOrder']['status']) {
				case 'new': $status_id = 1; break;
				case 'done': $status_id = 4; break;
				case 'storno': $status_id = 5; break;
				case 'ordr': $status_id = 4; break;
			}
			
			// prevedu jejich info o zpusobu dodani objednavky do naseho
			switch($old_order['OldOrder']['transit']) {
				case 'Soukromý přepravce': $shipping_id = 7; break;
				case 'Naše soukromá doprava': $shipping_id = 6; break;
				case 'Česká pošta': $shipping_id = 2; break;
			}

			// sestavim objednavku
			$order = array(
				'Order' => array(
					'id' => $old_order['OldOrder']['id'],
					'created' => $old_order['OldOrder']['date'],
					'customer_id' => $this->Order->Customer->id,
					'customer_name' => $customer['Address'][1]['name'],
					'customer_ico' => $customer['Customer']['company_ico'],
					'customer_dic' => $customer['Customer']['company_dic'],
					'customer_first_name' => $customer['Customer']['first_name'],
					'customer_last_name' => $customer['Customer']['last_name'],
					'customer_street' => $customer['Address'][1]['street'],
					'customer_city' => $customer['Address'][1]['city'],
					'customer_zip' => $customer['Address'][1]['zip'],
					'customer_state' => $customer['Address'][1]['state'],
					'customer_phone' => $customer['Customer']['phone'],
					'customer_email' => $customer['Customer']['email'],
					'delivery_name' => $customer['Address'][0]['name'],
					'customer_first_name' => $customer['Customer']['first_name'],
					'delivery_last_name' => $customer['Customer']['last_name'],
					'delivery_street' => $customer['Address'][0]['street'],
					'delivery_city' => $customer['Address'][0]['city'],
					'delivery_zip' => $customer['Address'][0]['zip'],
					'delivery_state' => $customer['Address'][0]['state'],
					'status_id' => $status_id,
					'shipping_id' => $shipping_id,
					'payment_id' => NULL,
					'comments' => $old_order['OldOrder']['pozn']
				),
				'OrderedProduct' => array()
			);
			
			// nasctu polozky objednavky
			$old_order_items = $this->Order->query("
				SELECT *
				FROM old_order_items AS OldOrderItem
				WHERE OldOrderItem.order=" . $old_order['OldOrder']['id'] 
			);

			// doplnit celkovou cenu objednavky (s dph) - secist polozky objednavky mimo dopravy
			// inicializace ceny
			$subtotal_with_dph = 0;
			
			foreach ($old_order_items as $old_order_item) {
				// doplnit cenu dopravy objednavky, je jako polozka old objednavky
				if ($old_order_item['OldOrderItem']['code'] == 'POST') {
					$order['Order']['shipping_price'] = $old_order_item['OldOrderItem']['price'];
				} else {
					// podle kodu musim najit produkt, ktery je objednan
					$product = $this->Order->OrderedProduct->Product->find('first', array(
						'conditions' => array('code' => $old_order_item['OldOrderItem']['code']),
						'contain' => array(),
						'fields' => array('id')
					));
					
					$product_id = null;
					if (!empty($product)) {
						$product_id = $product['Product']['id'];
					}
					
					$order['OrderedProduct'][] = array(
						'product_id' => $product_id,
						'product_price_with_dph' => $old_order_item['OldOrderItem']['price'],
						'product_quantity' => $old_order_item['OldOrderItem']['count']
					);
					
					$subtotal_with_dph += $old_order_item['OldOrderItem']['price'];
				}
			}
			
			$order['Order']['subtotal_with_dph'] = $subtotal_with_dph;
			
			if (!$this->Order->saveAll($order)) {
				debug($order);
				debug($old_order);
				debug($old_order_items);
				die('nepodailo se ulozit objednavku');
			}
		}
		die('hotovo');
	}
	
	function viewPdf($id){
		$order = $this->Order->find('first', array(
			'conditions' => array(
				'Order.id' => $id
			),
			'contain' => array(
				'Customer',
				'OrderedProduct' => array(
					'Product'
				),
				'Shipping'
			)
		));
	
		$this->set('order', $order);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
} // konec tridy
?>