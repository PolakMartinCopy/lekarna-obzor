<?php
class CustomersController extends AppController {
	var $name = 'Customers';

	var $helpers = array('Form', 'Html');

	function beforeFilter(){
		parent::beforeFilter();
		// testuju zda je uzivatel prihlasen
		// pokud neni, presmeruju na stranku
		// s prihlasovacim formularem
		
		$allowed_actions = array(
			'add',
			'login',
			'password',
			'subscribe_newsletter'
		);
		
		if ( !$this->Session->check('Customer') && !in_array($this->params['action'], $allowed_actions) && !eregi("admin_", $this->params['action'])  ){
			$this->Session->setFlash('Pro zobrazení této stránky se musíte přihlásit.');
			$this->redirect(array('action' => 'login'), null, true);
		}
	}

	function admin_delete($id = null){
		if ( empty($id) ){
			$this->Session->setFlash('Není definováno ID zákazníka, kterého chcete smazat!');
			$this->redirect(array('controller' => 'orders', 'admin' => true), null, true);
		} else {
			// nactu si data k zakaznikovi a smazu je
			$customer = $this->Customer->find('first', array(
				'conditions' => array(
					'id' => $id
				),
				'contain' => array(
					'Address',
					'Order'
				)
			));
			if ( empty($customer) ){
				$this->Session->setFlash('Není definováno ID zákazníka, kterého chcete smazat!');
				$this->redirect(array('controller' => 'orders', 'admin' => true), null, true);
			} else {
				// zakaznik existuje, muzu ho smazat (mazu jenom zaznam o zakaznikovi + jeho adresy, nic vic)
				// nejdrive vsechny adresy
				$address_delete_result = $this->Customer->Address->deleteAll(array(
					'customer_id' => $customer['Customer']['id']
				), false, false);
				
				if ( !$address_delete_result ){
					$this->Session->setFlash('Nastala chyba pi mazání zákazníkových adres, zkuste to znovu prosím!');
					$this->redirect(array('controller' => 'customers', 'action' => 'view', $customer['Customer']['id']), null, true);
				}
				
				// pak smazu zakaznika
				if ( !$this->Customer->delete($customer['Customer']['id']) ){
					$this->Session->setFlash('Nepodailo se smazat záznam o zákazníkovi, zkuste to prosím znovu!');
					$this->redirect(array('controller' => 'customers', 'action' => 'view', $customer['Customer']['id']), null, true);
				}
				
				$this->Session->setFlash('Zákazník byl úspěšně odstraněn z databáze!');
				$this->redirect(array('controller' => 'orders', 'admin' => true), null, true);
			}
		}
	}
	
	/**
	 * Seznam zákazníků registrovaných v obchodě.
	 *
	 * @param string $id - zacatecni pismeno jmena
	 */
	function admin_list($id = null){
		if ( !isset($id) ){
			$id = 'a';
		}
		
		$customers = $this->Customer->find('all', array(
			'conditions' => array("Customer.last_name LIKE '" . $id . "%%' OR Customer.last_name LIKE '" . strtoupper($id) . "%%' "),
			'recursive' => -1,
			'order' => 'Customer.last_name'
		));
		
		$count = count($customers);
		for ( $i = 0; $i < $count; $i++ ){
			// vytazeni objednavek zakaznika
			$customers[$i]['Customer']['orders'] = $this->Customer->Order->find('all', array(
				'conditions' => array('customer_id' => $customers[$i]['Customer']['id']),
				'fields' => array('id', 'subtotal_with_dph', 'shipping_cost'),
				'recursive' => -1
			));
			
			// vytazeni adres zakaznika
			$customers[$i]['Customer']['addresses'] = $this->Customer->Address->find('all', array(
				'conditions' => array('customer_id' => $customers[$i]['Customer']['id']),
				'fields' => array('id', 'name'),
				'recursive' => -1
			));
		}
		
		$this->set('customers', $customers);
		$this->set('id', $id);
		$this->set('alphabet', array(0 => 'a', 'á', 'b', 'c', 'č', 'd', 'ď', 'e', 'é', 'f', 'g', 'h', 'i', 'í', 'j', 'k', 'l', 'm', 'n', 'ň', 'o', 'ó', 'p', 'q', 'r', 'ř', 's', 'š', 't', 'ť', 'u', 'ú', 'v', 'w', 'x', 'y', 'z', 'ž'));
	}

	function admin_view($id = null){
		$customer = $this->Customer->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'contain' => array(
				'Address',
				'Order'
			)
		));
		$this->set('customer', $customer);
	}

	
	function admin_export() {
		$customers = $this->Customer->find('all', array(
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'LEFT',
					'conditions' => array('Address.customer_id = Customer.id', 'Address.type = "d"')
				)
			),
			'fields' => array('Customer.first_name', 'Customer.last_name', 'Customer.email', 'Customer.phone', 'Customer.created', 'CONCAT(Address.street," ", Address.street_no, ", ", Address.zip, " ", Address.city) AS address'),
		));

		$this->layout = 'csv_file';
		$this->set('file_name', 'zakaznici.csv');
		$this->set('customers', $customers);
	}
	
	
	
	/*
	 * @description			Registrace noveho uzivatele do systemu.
	 */
	function add(){
		// kontrola, jestli se nesnazi o registraci, i kdyz je prihlaseny
		if ( $this->Session->check('Customer.id') ){
			$this->Session->setFlash('Jste již přihlášen(a) ke svému účtu, chcete-li zaregistrovat nový účet, nejprve se odhlašte.');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'), null, true);
		}
		
		// nastavim si meta udaje
		$this->set('title_for_content', 'Registrace nového uživatele');
		$this->set('description_for_content', 'Zaregistrujte se a získáte přehled o výhodnějších cenách pro registrované uživatele.');
		
		// nastavim layout
		$this->layout = 'content';
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => HP_URI),
			array('anchor' => 'Registrace', 'href' => $_SERVER['REQUEST_URI'])
		);
		$this->set('breadcrumbs', $breadcrumbs);
		
		// formular byl vyplnen
		if ( isset($this->data) ){
			// pro zakaznika si preddefinuju login a heslo
			$this->data['Customer']['login'] = $this->Customer->generateLogin($this->data['Customer']);

			// kvuli notifikacnimu mailu potrebuju vedet nekryptovane heslo, ulozim si
			// ho proto bokem
			$password_not_md5 = $this->Customer->generatePassword($this->data['Customer']);
			// do databaze musi jit heslo kryptovane
			$this->data['Customer']['password'] = md5($password_not_md5); 

			$this->data['Customer']['confirmed'] = 1;
			$this->data['Customer']['registration_source'] = 'eshop - registrace';
			
			$this->Session->write('cpass', $password_not_md5);
			
			$address = $this->data['Address'][0];
			// pokud pri registraci zakaznik zadal adresu
			if (!empty($address['street']) || !empty($address['street_no']) || !empty($address['zip']) || !empty($address['city'])) {
				// preskladam data, abych mohl ulozit a validovat saveAllem
				$this->data['Address'][0]['name'] = $this->data['Address'][1]['name'] = $this->data['Customer']['first_name'] . ' ' . $this->data['Customer']['last_name'];
				$this->data['Address'][1]['street'] = $address['street'];
				$this->data['Address'][1]['street_no'] = $address['street_no'];
				$this->data['Address'][1]['zip'] = $address['zip'];
				$this->data['Address'][1]['city'] = $address['city'];
				$this->data['Address'][1]['state'] = $address['state'];
				$this->data['Address'][0]['type'] = 'f';
				$this->data['Address'][1]['type'] = 'd';
			} else {
				unset($this->data['Address']);
			}

			// ukladam zakaznika
			if ( $this->Customer->saveAll($this->data) ){
				// ulozeni probehlo v poradku
				// musim se podivat, jestli nebyl predtim zakaznik prihlasen k odberu newsletteru a pokud ano, zaznam odstranit, aby se mu pak neposilali dvakrat
				App::import('Model', 'Subscriber');
				$this->Subscriber = new Subscriber;
				$subscriber = $this->Subscriber->find('first', array(
					'conditions' => array('email' => $this->data['Customer']['email']),
					'contain' => array(),
					'fields' => array('id')
				));
				if (!empty($subscriber)) {
					$this->Subscriber->delete($subscriber['Subscriber']['id']);
				}
				// naimportuju mailer class a odeslu zakaznikovi mail, ze jeho ucet byl vytvoren, do dat o zakaznikovi si
				// musim ale vratit nekryptovane heslo
				$this->data['Customer']['password'] = $password_not_md5;

				$this->Customer->notify_account_created($this->data['Customer']); 
				
				// vsechno je ulozene, presmeruju na prihlasovaci stranku a vypisu hlasku o registraci
				$this->Session->setFlash('Váš účet na ' . CUST_ROOT . ' byl nyní vytvořen a přihlašovací údaje byly odeslány na email <strong>' . $this->data['Customer']['email'] . '</strong>.');
				$this->redirect(array('controller' => 'customers', 'action' => 'login', 'reg-success'), null, true);
			} else {
				$this->Session->setFlash('Registrace selhala, opravte prosím chyby ve formuláři a odešlete jej znovu.');
			}
		}
	}
	
	function add_address($id){
		// nastavim layout
		$this->layout = 'content';
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => HP_URI),
			array('anchor' => 'Vložení nové adresy', 'href' => $_SERVER['REQUEST_URI'])
		);
		$this->set('breadcrumbs', $breadcrumbs);

		if ( isset($this->data) ){
			if ( $this->Customer->Address->save($this->data) ){
				$this->Session->setFlash('Adresa byla uložena!');
				$this->redirect(array('controller' => 'customers', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Adresa nebyla uložena, zkontrolujte prosím všechna pole!');
			}
		}
	}

	function address_edit(){
		// nastavim nadpis
		$this->set('page_heading', ($this->params['named']['type'] == 'd' ? 'Doručovací' : 'Fakturační' ) . ' adresa');

		// nastavim layout
		$this->layout = 'content';
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => HP_URI),
			array('anchor' => 'Zákaznický panel', 'href' => '/customers/index'),
			array('anchor' => ($this->params['named']['type'] == 'd' ? 'Doručovací' : 'Fakturační' ) . ' adresa', 'href' => $_SERVER['REQUEST_URI'])
		);
		$this->set('breadcrumbs', $breadcrumbs);
				
		$address = $this->Customer->Address->find(array('Address.customer_id' => $this->Session->read('Customer.id'), 'Address.type' => $this->params['named']['type']));
		if ( empty($this->data) ){
			$this->data = $address;
			if ( empty($address) ){
				$this->data['Address']['type'] = $this->params['named']['type'];
			}
		} else {
			// znamena to, ze clovek ma uz adresu daneho typu v db,
			// jen ji chce upravit
			if ( !empty($address) ){
				$this->data['Address']['id'] = $address['Address']['id'];
			}
			
			$this->data['Address']['customer_id'] = $this->Session->read('Customer.id');
			
			if ( $this->Customer->Address->save($this->data) ){
				$this->Session->setFlash('Adresa byla uložena.');
				$this->redirect(array('controller' => 'customers', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Adresa nebyla vyplněna správně, zkontrolujte prosím formulář.');
			}
		}
	}
	
	function edit(){
		// nastavim layout
		$this->layout = 'content';
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => HP_URI),
			array('anchor' => 'Zákaznický panel', 'href' => '/customers/index'),
			array('anchor' => 'Editace zákazníka', 'href' => $_SERVER['REQUEST_URI'])	
		);
		$this->set('breadcrumbs', $breadcrumbs);

		if ( isset($this->data) ){
			// data uz jsou nactena a odeslana,
			// musim to zpracovat
			switch ( $this->data['Customer']['edit'] ){
				case "info":
					// upravuju info o zakaznikovi
					unset($this->data['Customer']['edit']);

					// vytahnu si idecko zakaznika podle session
					$this->Customer->id = $this->Session->read('Customer.id');

					// ulozim si ho
					if ( $this->Customer->save($this->data) ){
						$this->Session->setFlash('Údaje byly změněny!');
					} else {
						$this->Session->setFlash('Chyba při ukládání údajů, zkuste prosím znovu!');
					}
				break;
				case "pass":
					// upravuju zakaznicke heslo
					unset($this->data['Customer']['edit']);
					
					// nejdriv zmenu autorizujeme podle id a puvodniho hesla
					$customer = $this->Customer->find(array('id' => $this->Session->read('Customer.id'), 'password' => md5($this->data['Customer']['old_password'])));
					if ( empty( $customer ) ){
						// nepodarilo se mi nacist podle podminek zakznika
						$this->Session->setFlash('Vyplnil(a) jste špatně původní heslo! Zkuste to prosím znovu.');
						$this->redirect(array('controller' => 'customers', 'action' => 'edit'), null, true);
					} else {
						if ( $this->data['Customer']['new_password'] == $this->data['Customer']['new_password_rep']){
							if ( strlen($this->data['Customer']['new_password']) < 8 ){
								$this->Session->setFlash('Nové heslo musí mít minimálně osm znaků! Zkuste to prosím znovu.');
								$this->redirect(array('controller' => 'customers', 'action' => 'edit'), null, true);
							} else {
								$customer = array('password' => md5($this->data['Customer']['new_password']));
								$this->Customer->id = $this->Session->read('Customer.id');
								if ( $this->Customer->save($customer, false) ){
									// pouzivam save BEZ validace dat syntaxe:
									// save(data, validovat?, povolena pole)
									$this->Session->setFlash('Heslo bylo změněno!');
									$this->redirect(array('controller' => 'customers', 'action' => 'edit'), null, true);
								} else {
									die();
									$this->Session->setFlash('Změna hesla se nezdařila! Zkuste to prosím znovu.');
									$this->redirect(array('controller' => 'customers', 'action' => 'edit'), null, true);
								}
							}
						} else {
							$this->Session->setFlash('Pole pro nové heslo a zopakování hesla jsou rozdílná! Zkuste to prosím znovu.');
							$this->redirect(array('controller' => 'customers', 'action' => 'edit'), null, true);
						}
					}
				break;
				default:
					$this->Session->setFlash('Neexistující zákazník!');
					$this->redirect(array('controller' => 'customers', 'action' => 'index'), null, true);
				break;
			}
		} else {
			// vytahnu data o zakaznikovi
			$this->Customer->recursive = 0;
			$this->data = $this->Customer->find(array('id' => $this->Session->read('Customer.id')));

			// pokud se mi nepodari vytahnout zakaznika, zakaznik neexistuje
			if ( empty($this->data) ){
				$this->Session->setFlash('Neexistující zákazník!');
				$this->redirect(array('controller' => 'customers', 'action' => 'index'), null, true);
			}
			
			// else nedavam, data se posouvaji do view
		}
	}

	function edit_address($id){
		// nastavim nadpis
		$this->set('page_heading', 'Editace adresy');

		// nastavim layout
		$this->layout = 'content';

		if ( !isset($this->data) ){
			$this->Customer->Address->recursive = -1;
			$this->data = $this->Customer->Address->find(array('Address.id' => $id,'Address.customer_id' => $this->Session->read('Customer.id')));
			if ( empty($this->data) ){
				$this->Session->setFlash('Neexistující adresa!');
				$this->redirect(array('controller' => 'customers', 'action' => 'index'), null, true);
			}
			// else je obslouzeny ve view
		} else {
			$this->Customer->Address->recursive = -1;
			$address = $this->Customer->Address->find(array('Address.id' => $id,'Address.customer_id' => $this->Session->read('Customer.id')));
			if ( empty( $address ) ){
				$this->Session->setFlash('Neexistující adresa!');
				$this->redirect(array('controller' => 'customers', 'action' => 'index'), null, true);
			} else {
				$this->Customer->Address->id = $address['Address']['id'];
				if ( $this->Customer->Address->save($this->data) ){
					$this->Session->setFlash('Adresa byla uložena!');
					$this->redirect(array('controller' => 'customers', 'action' => 'edit_address', $id), null, true);
				} else {
					$this->Session->setFlash('Adresu se nepodařilo uložit!');
				}
			}
		}
	}
	
	function index(){
		// nastavim layout
		$this->layout = 'content';
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => HP_URI),
			array('anchor' => 'Zákaznický panel', 'href' => $_SERVER['REQUEST_URI'])
		);
		$this->set('breadcrumbs', $breadcrumbs);
		
		$this->Customer->recursive = 2;
		$this->Customer->Order->unbindModel(
			array(
				'belongsTo' => array('Payment', 'Shipping', 'Customer'),
			)
		);
		$customer = $this->Customer->read(null, $this->Session->read('Customer.id'));
		$this->set('customer', $customer);
	}
	
	/*
	 * @desription				Prihlasovani zakaznika.
	 */
	function login() {
		/*
		 * @description				Validuje heslo zahashovane v osCommerce.
		 */
		function valid_osc_password($plain, $password){
			$split = explode(':', $password);
			if ( count($split) != 2 ){
				return false;
			}
			
			if ( md5($split[1] . $plain ) == $split[0] ) {
				return true;
			}
			
			return false;
		}

		// nastavim nadpis
		$this->set('page_heading', 'Přihlášení zákazníka');

		// nastavim layout
		$this->layout = 'content';
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => HP_URI),
			array('anchor' => 'Přihlášení', 'href' => $_SERVER['REQUEST_URI'])
		);
		$this->set('breadcrumbs', $breadcrumbs);

		if ( isset($this->data) ){
			// zakladni nastaveni pro presmerovani
			$backtrace_url = array(
				'controller' => 'customers',
				'action' => 'index'
			);
			// pokud je nadefinovano url, kam se ma presmerovat,
			// prepisu zakladni nastaveni
			if ( isset( $this->data['Customer']['backtrace_url'] ) ){
				$backtrace_url = $this->data['Customer']['backtrace_url'];
			}
			
			$conditions = array('login' => $this->data['Customer']['login']);
			
			$customer = $this->Customer->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
			));

			if ( empty($customer) ){
				$this->Session->setFlash('Neplatný login!');
			} else {
				if ( empty($this->data['Customer']['password'])
					 OR ( $this->data['Customer']['password'] != $customer['Customer']['password']
					 AND md5($this->data['Customer']['password']) != $customer['Customer']['password']
					 AND !valid_osc_password($this->data['Customer']['password'], $customer['Customer']['password'] )
					 )
				){
					$this->Session->setFlash('Neplatné heslo!');
				} else {
					// ulozim si info o zakaznikovi do session
					$this->Session->write('Customer', $customer['Customer']);
					
					// ze session odstranim data o objednavce,
					// pokud se snazil zakaznik pred prihlasenim neco
					// vyplnovat v objednavce, delalo by mi to bordel
					$this->Session->delete('Order');
					
					// presmeruju
					$this->Session->setFlash('Jste přihlášen(a) jako ' . $customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name']);
					$this->redirect($backtrace_url, null, true);
				}
			}
		}
	}

	function logout(){
		$this->Session->delete('Customer');
		$this->Session->setFlash('Jste úspěšně odhlášen(a)!');
		$this->redirect(array('controller' => 'customers', 'action' => 'login'), null, true);
	}
	
	function orders_list(){
		// nastavim layout
		$this->layout = 'content';
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
				array('anchor' => 'Domů', 'href' => HP_URI),
				array('anchor' => 'Zákaznický panel', 'href' => '/customers'),
				array('anchor' => 'Objednávky', 'href' => '/customers/orders_list'),
		);
		$this->set('breadcrumbs', $breadcrumbs);

		$orders = $this->Customer->Order->find('all', array(
			'conditions' => array('customer_id' => $this->Session->read('Customer.id')),
			'order' => array('Order.created' => 'desc')
		));
		$this->set('orders', $orders);
	}
	

	function order_detail($id){
		$this->Customer->Order->recursive = 2;
		$this->Customer->Order->unbindModel(array('belongsTo' => array('Customer')));
		$this->Customer->Order->Shipping->unbindModel(array('hasMany' => array('Order')));
		$this->Customer->Order->Payment->unbindModel(array('hasMany' => array('Order')));
		$this->Customer->Order->Status->unbindModel(array('hasMany' => array('Order')));
		$this->Customer->Order->OrderedProduct->unbindModel(array('belongsTo' => array('Order')));
		
		$order = $this->Customer->Order->find(array('Order.id' => $id, 'Order.customer_id' => $this->Session->read('Customer.id') ));
		if ( empty($order) ){
			$this->Session->setFlash('Neexistující objednávka!');
			$this->redirect(array('controller' => 'customers', 'action' => 'orders_list'));
		} else {
			// nastavim layout
			$this->layout = 'content';
			
			// sestavim breadcrumbs
			$breadcrumbs = array(
				array('anchor' => 'Domů', 'href' => HP_URI),
				array('anchor' => 'Zákaznický panel', 'href' => '/customers'),
				array('anchor' => 'Objednávky', 'href' => '/customers/orders_list'),
				array('anchor' => 'Detaily objednávky číslo ' . $order['Order']['id'], 'href' => $_SERVER['REQUEST_URI'])	
			);
			$this->set('breadcrumbs', $breadcrumbs);
			
			$this->set('order', $order);
		}
	}

	function password(){
		// nastavim layout
		$this->layout = 'content';
		
		// nastavim nadpis
		$this->set('page_heading', 'Vyžádání hesla');
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => HP_URI),
			array('anchor' => 'Obnova hesla', 'href' => $_SERVER['REQUEST_URI'])
		);
		$this->set('breadcrumbs', $breadcrumbs);
		
		if ( isset($this->data) ){
			$this->Customer->recursive = -1;
			$customer = $this->Customer->find(array('email' => $this->data['Customer']['email']));
			if ( empty($customer) ){
				$this->Session->setFlash('Účet s takovou emailovou adresou neexistuje.');
			} else {
				$this->Customer->changePassword($customer);
				$this->Session->setFlash('Email o změně hesla byl odeslán.');
				$this->redirect(array('controller' => 'customers', 'action' => 'login')); 
			}
		}
	}
}
?>
