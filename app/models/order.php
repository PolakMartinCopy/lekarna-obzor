<?php
class Order extends AppModel {
	var $name = 'Order';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array(
		'OrderedProduct' => array(
			'dependent' => true
		),
		'Ordernote' => array(
			'dependent' => true,
			'order' => array('Ordernote.created' => 'desc')
		)
	);
	var $belongsTo = array('Customer', 'Shipping', 'Payment', 'Status');


	function afterFind($results){
		if ( isset( $results[0]['Order']) && isset($results[0]['Order']['subtotal_with_dph']) && isset($results[0]['Order']['shipping_cost'])  ){
			$count = count($results);
			for ( $i = 0; $i < $count; $i++ ){
				$results[$i]['Order']['orderfinaltotal'] = $results[$i]['Order']['subtotal_with_dph'] + $results[$i]['Order']['shipping_cost']; 
			}
				
		} else {
			if ( isset($results['subtotal_with_dph']) && isset($results['shipping_cost']) ){
				$results['orderfinaltotal'] = $results['subtotal_with_dph'] + $results['shipping_cost'];
			}
		}

		return $results;
	}

	function reCount($id = null){

		// predpokladam, ze postovne bude za 0 Kc

		$order['Order']['shipping_cost'] = 0;



		// nactu si produkty z objednavky a data o ni

		$contain = array(

			'OrderedProduct' => array(

				'fields' => array('OrderedProduct.id', 'product_id', 'product_quantity', 'product_price_with_dph'),

				'Product' => array(

					'fields' => array('Product.id', 'name'),

					'FlagsProduct'

				)

			)

		);

		

		$conditions = array('Order.id' => $id);

		

		$fields = array('Order.id', 'customer_ico', 'customer_dic', 'subtotal_with_dph', 'shipping_cost', 'shipping_id');

		

		$products = $this->find('first', array(

			'conditions' => $conditions,

			'contain' => $contain,

			'fields' => $fields

		));

	

		

		// nathnu si detaily o postovnem,

		// na ktere chceme menit

		$this->Shipping->recursive = -1;

		$shipping = $this->Shipping->read(null, $products['Order']['shipping_id']);

		

		// pokud je postovne pro normalni zakazniky

		// a pro firemni zakazniky zdarma, nemusim kontrolovat cenu postovneho

		if ( $shipping['Shipping']['price'] != '0' ){

			// po nacteni zkontroluju celkovou cenu objednavky v zavislosti

			// na tom, zda se jedna o koncaka, nebo o firmu

			if ( ( empty($products['Order']['customer_ico']) && $products['Order']['subtotal_with_dph'] <= $shipping['Shipping']['free'] )){

				// predpoklad, ze se za postovne platit bude

				// zavisi to na tom, jestli tolik co koncak,

				// nebo tolik co zakaznik s icem

				

				// prepoklad ze je to koncovy zakaznik

//				$order['Order']['shipping_cost'] = $shipping['Shipping']['ico_price'];

				if ( empty($products['Order']['customer_ico']) ){

					// je to koncak, dam tam cenu pro koncaky

					$order['Order']['shipping_cost'] = $shipping['Shipping']['price'];

				}



				// musim zjistit, jestli neni v objednavce produkt,

				// ktery ma flag s dopravou zdarma

				foreach ( $products['OrderedProduct'] as $op ){

					if ( !empty($op['Product']['FlagsProduct']) ){

						foreach ( $op['Product']['FlagsProduct'] as $pf ){

							// pokud se jedna o flag s dopravou zdarma a pocet produktu

							// splnuje podminku pro dopravu zdarma

							if ( $pf['flag_id'] == 1 && $pf['quantity'] <= $op['product_quantity'] ){

								// indukuje dopravu zdarma

								$order['Order']['shipping_cost'] = 0;

							}

						}

					}

				}

			}

		}

		

		$order_total = 0;

		$free_shipping = false;

		foreach ( $products['OrderedProduct'] as $product ){

			$order_total = $order_total + $product['product_price_with_dph'] * $product['product_quantity'];

		}



		$order['Order']['subtotal_with_dph'] = $order_total;

		$this->id = $id;

		$this->save($order, false, array('subtotal_with_dph', 'shipping_cost'));

	}

	/**
	 * Zjisti stav objednavky odeslane pres ceskou postu.
	 * 
	 * @param unsigned int $id - Cislo objednavky.
	 */
	function track_cpost($id = null){
		App::import('Helper', 'Session');
		$this->Session = new SessionHelper;
		
		// nactu si objednavku, protoze potrebuju vedet
		// cislo baliku v kterem byla objednavka expedovana
		$this->recursive = -1;
		$order = $this->read(null, $id);
		
		$this->Shipping->id = $order['Order']['shipping_id'];
		$this->Shipping->recursive = -1;
		$shipping = $this->Shipping->read();
		
		$tracker_url = $shipping['Shipping']['tracker_prefix'] . trim($order['Order']['shipping_number']) . $shipping['Shipping']['tracker_postfix'];
		// nactu si obsah trackovaci stranky
		$contents = @file_get_contents($tracker_url);
		if ( $contents !== false ){
			$contents = eregi_replace("\r\n", "", $contents);
			$contents = eregi_replace("\t", "", $contents);
			
			// z obsahu vyseknu usek, ktery zminuje jednotlive stavy objednavky
			$pattern = '|<table class="datatable2"> <tr> <th>Datum</th>.*</tr>(.*)</table>|U';
			preg_match_all($pattern, $contents, $table_contents);

			if (!isset($table_contents[1][0])) {
				$pattern = '/<div class="infobox">(.*)<\/div>/';
				if (preg_match($pattern, $contents, $messages)) {
					$message = strip_tags($messages[1]);
					return $id;
				}
				return $id;
				die('nesedi pattern pri zjisteni dorucenosti baliku u CP - tabulka');
			}
			
			// stavy si rozhodim do jednotlivych prvku pole
			$pattern = '|<tr>(.*)</tr>|U';
			preg_match_all($pattern, $table_contents[1][0], $rows);
			if (isset($rows[1])) {
				return $id;
				die('nesedi pattern pri zjisteni dorucenosti baliku u CP - radek tabulky');
			}

			// priznak, zda jsem narazil na status ktery meni objednavku
			// na dorucenou, ulozenou na poste apod.
			$found = false;
			
			foreach ($rows[1] as $os){
				if ( eregi('Dodání zásilky.', $os) ){
					// mam dorucenou objednavku, dal neprochazim
					$found = true;

					// pokud byla dorucena, najdu si datum doruceni
					$date = '';
					
					$pattern = '|([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4})|';
					preg_match_all($pattern, $os, $date);
					if (!isset($date[1][0])) {
						return $id;
						die('nesedi pattern pri zjisteni dorucenosti baliku u CP - datum');
					}
					$date = date('d.m.Y', strtotime($date[1][0]));
					// musim zmenit objednavku na doruceno a zapsat poznamku o tom, kdy byla dorucena
					$this->id = $id;
					$this->save(array('status_id' => '4'), false, array('status_id', 'modified'));
					
					// zapisu poznamku o tom, kdy byla dorucena
					$note = array('order_id' => $id,
						'status_id' => '4',
						'administrator_id' => $this->Session->read('Administrator.id'),
						'note' => 'Zásilka byla automaticky identifikována jako doručená zákazníkovi. Datum doručení: ' . $date
					);
					unset($this->Ordernote->id);
					$this->Ordernote->save($note);
				}
			}
			
			// doruceno nemam, hledam, jestli se zasilka nevratila zpet odesilateli
			if ( !$found ){
				foreach ($rows[1] as $os){
					if ( eregi('Item was returned to sender', $os) ){
						$found = true;
						
						// pokud byla vracena, najdu si datum vraceni
						$date = '';
						
						$pattern = '|([0-9]{2}\.[0-9]{2}\.[0-9]{4})|';
						preg_match_all($pattern, $os, $date);
						if (!isset($date[1][0])) {
							return $id;
							die('nesedi pattern pri zjisteni dorucenosti baliku u CP - datum');
						}
						$date = date('d.m.Y', strtotime($date[1][0]));
						
						// musim zmenit objednavku na vraceno a zapsat poznamku o tom, kdy byla vracena
						$this->id = $id;
						$this->save(array('status_id' => '4'), false, array('status_id', 'modified'));
						
						// zapisu poznamku o tom, kdy byla vracena
						$note = array('order_id' => $id,
							'status_id' => '8',
							'administrator_id' => $this->Session->read('Administrator.id'),
							'note' => 'Zásilka byla automaticky identifikována jako vrácená zpět. Datum návratu: ' . $date
						);
						unset($this->Ordernote->id);
						$this->Ordernote->save($note);
					}
				}
			}

			// stav doruceno ani vraceno nemam, hledam ulozeni na poste
			if ( !$found ){
				foreach ($rows[1] as $os){
					// objednavka je ulozena na poste a ceka na vyzvednuti
					// zaroven ale kontroluju, jestli uz clovek nebyl upozornen,
					// tzn ze objednavka uz ma status cislo 9
					if ( eregi('After unsuccessful attempt of delivery', $os) && $order['Order']['status_id'] != 9 ){
						// pokud byla ulozena, najdu si datum ulozeni
						$date = '';
						
						$pattern = '|([0-9]{2}\.[0-9]{2}\.[0-9]{4})|';
						preg_match_all($pattern, $os, $date);
						if (!isset($date[1][0])) {
							return $id;
							die('nesedi pattern pri zjisteni dorucenosti baliku u CP - datum');
						}
						$date = date('d.m.Y', strtotime($date[1][0]));
						
						// musim zmenit objednavku na ulozeno a zapsat poznamku o tom, kdy byla ulozena
						$this->id = $id;
						$this->save(array('status_id' => '9'), false, array('status_id', 'modified'));
						
						// zapisu poznamku o tom, kdy byla ulozena
						$note = array('order_id' => $id,
							'status_id' => '9',
							'administrator_id' => $this->Session->read('Administrator.id'),
							'note' => 'Zásilka byla automaticky identifikována jako uložená na poště. Zákazníkovi byl odeslan email o uložení. Datum uložení: ' . $date
						);
						
						if ( !$this->Status->change_notification($id, 9) ){
							$note['note'] = 'Zásilka byla automaticky identifikována jako uložená na poště. ZÁKAZNÍKOVI NEBYL ODESLÁN MAIL! Datum uložení: ' . $date; 
							return false;
						}
						
						unset($this->Ordernote->id);
						$this->Ordernote->save($note);
					}
					
				}
			}
			return true;
		}
		return $id;
	}

	function build($customer) {
		App::import('model', 'CakeSession');
		$this->Session = &new CakeSession;
		// ze sesny vytahnu data o objednavce a doplnim potrebna data
		$order['Order'] = $this->Session->read('Order');
		
		$order['Order']['customer_first_name'] = $customer['Customer']['first_name'];
		$order['Order']['customer_last_name'] = $customer['Customer']['last_name'];
		$order['Order']['customer_phone'] = $customer['Customer']['phone'];
		$order['Order']['customer_email'] = $customer['Customer']['email'];
		
		if ( isset($customer['Customer']['company_name']) ){
			$order['Order']['customer_company_name'] = $customer['Customer']['company_name'];
		}
		
		if ( isset($customer['Customer']['company_ico']) ){
			$order['Order']['customer_ico'] = $customer['Customer']['company_ico'];
		}

		if ( isset($customer['Customer']['company_dic']) ){
			$order['Order']['customer_dic'] = $customer['Customer']['company_dic'];
		}
		// doplnim data o fakturacni adrese		
		$order['Order']['customer_name'] = $customer['Address'][1]['name'];
		$order['Order']['customer_street'] = $customer['Address'][1]['street'] . ' ' . $customer['Address'][1]['street_no'];
		$order['Order']['customer_city'] = $customer['Address'][1]['city'];
		$order['Order']['customer_zip'] = $customer['Address'][1]['zip'];
		$order['Order']['customer_state'] = $customer['Address'][1]['state'];
		// doplnim data o dorucovaci adrese
		$order['Order']['delivery_name'] = $customer['Address'][0]['name'];		
		$order['Order']['delivery_street'] = $customer['Address'][0]['street'] . ' ' . $customer['Address'][0]['street_no'];
		$order['Order']['delivery_city'] = $customer['Address'][0]['city'];
		$order['Order']['delivery_zip'] = $customer['Address'][0]['zip'];
		$order['Order']['delivery_state'] = $customer['Address'][0]['state'];
		
		$order['Order']['customer_id'] = $customer['Customer']['id'];
		$order['Order']['status_id'] = 1;
		
		// data pro produkty objednavky
		App::import('Model', 'CartsProduct');
		$this->CartsProduct = &new CartsProduct;
		$cart_products = $this->CartsProduct->getProducts();
		$cart_id = $this->CartsProduct->Cart->get_id();
		$mail_products = array();
		$order_total_with_dph = 0;
		$order_total_wout_dph = 0;
		$free_shipping = false;

		$cp_count = 0;
		$ordered_products = array();
		foreach ( $cart_products as $cart_product ){
			// projdu vsechny priznaky
			foreach ( $cart_product['Product']['Flag'] as $flags_product ){
				// priznak pro dopravu zdarma je "1"
				if ( $flags_product['FlagsProduct']['flag_id'] == 1 && $cart_product['CartsProduct']['quantity'] >= $flags_product['FlagsProduct']['quantity'] ){
					$free_shipping = true;
				}
			}
			// data pro produkt
			$ordered_products[$cp_count]['OrderedProduct']['product_id'] = $cart_product['CartsProduct']['product_id'];
			$ordered_products[$cp_count]['OrderedProduct']['subproduct_id'] = $cart_product['CartsProduct']['subproduct_id'];
			$ordered_products[$cp_count]['OrderedProduct']['product_price_with_dph'] = $cart_product['CartsProduct']['price_with_dph'];
			$ordered_products[$cp_count]['OrderedProduct']['product_price_wout_dph'] = $cart_product['CartsProduct']['price_wout_dph'];
			$ordered_products[$cp_count]['OrderedProduct']['product_quantity'] = $cart_product['CartsProduct']['quantity'];
			
			$order_total_with_dph = $order_total_with_dph + ($cart_product['CartsProduct']['quantity'] * $cart_product['CartsProduct']['price_with_dph']);
			$order_total_wout_dph = $order_total_wout_dph + ($cart_product['CartsProduct']['quantity'] * $cart_product['CartsProduct']['price_wout_dph']);
			// pamatuju si atributy objednaneho produktu
			$ordered_products[$cp_count]['OrderedProductsAttribute'] = array();
			if ( !empty($cart_product['CartsProduct']['subproduct_id']) ){
				$as_count = 0;
				foreach ( $cart_product['Subproduct']['AttributesSubproduct'] as $attributes_subproduct ){
					// vlozeni dat do atributu
					$ordered_products[$cp_count]['OrderedProductsAttribute'][$as_count]['attribute_id'] = $attributes_subproduct['attribute_id'];
					
					$as_count++;
				}
			}
			$cp_count++;
		}
		
		// dopocitavam si cenu dopravneho pro objednavku
		// predpokladam nulovou cenu
		$order['Order']['shipping_cost'] = 0;
		if ( !$free_shipping ){
			// objednavka neobsahuje produkt s dopravou zdarma,
			// cenu dopravy si proto dopocitam v zavislosti na
			// cene objednaneho zbozi
			$order['Order']['shipping_cost'] = $this->Shipping->get_cost($order['Order']['shipping_id'], $order_total_with_dph);
		}

		// cena produktu v kosiku, bez dopravneho
		$order['Order']['subtotal_with_dph'] = $order_total_with_dph;
		$order['Order']['subtotal_wout_dph'] = $order_total_wout_dph;

		return array($order, $ordered_products);
	}
	
	function cleanCartsProducts () {
		App::import('model', 'CartsProduct');
		$this->CartsProduct = &new CartsProduct;
		$cart_id = $this->CartsProduct->Cart->get_id();
		
		$carts_products = $this->CartsProduct->find('all', array(
			'conditions' => array('cart_id' => $cart_id),
			'contain' => array(),
			'fields' => array('id')
		));
		
		foreach ($carts_products as $carts_product) {
			$this->CartsProduct->delete($carts_product['CartsProduct']['id']);
		}
	}
	
	function notifyCustomer($customer) {
		App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
		if ( isset($customer['email']) && !empty($customer['email']) ){
			$customer_mail = 'Vážená(ý) ' . $customer['first_name'] . ' ' . $customer['last_name'] . "\n\n";
			$customer_mail .= 'Tento email je potvrzením objednávky v online obchodě http://www.' . CUST_ROOT . '/ v němž jste si právě objednal(a). ';
			$customer_mail .= 'Na mail prosím nereagujte, je automaticky vygenerován. Již brzy Vás budeme kontaktovat, o stavu Vaší objednávky, mailem, nebo telefonicky.' . "\n\n";
			$customer_mail .= 'DETAILY OBJEDNÁVKY:' . "\n";
			$customer_mail .= '----------------------------------' . "\n";
			$customer_mail .= 'číslo objednávky: ' . $this->id . "\n";
			$customer_mail .= 'datum vytvoření: ' . date('d') . '.' . date('m') . '.' . date('Y') . "\n\n";
			$customer_mail .= 'OBJEDNANÉ ZBOŽÍ:' . "\n";
			$customer_mail .= '----------------------------------' . "\n";

			$ordered_products = $this->OrderedProduct->find('all', array(
				'conditions' => array('OrderedProduct.order_id' => $this->id),
				'contain' => array(
					'Product' => array(
						'fields' => array('id', 'name')
					),
					'OrderedProductsAttribute' => array(
						'Attribute' => array(
							'Option' => array(
								'fields' => array('id', 'name')
							),
							'fields' => array('id', 'value')
						),
						'fields' => array('id')
					)
				)
			));

			foreach ( $ordered_products as $ordered_product ){
				$customer_mail .= $ordered_product['OrderedProduct']['product_quantity'] . ' x ' . $ordered_product['Product']['name'];

				$attributes = array();
				if ( !empty($ordered_product['OrderedProductsAttribute']) ){
					foreach ( $ordered_product['OrderedProductsAttribute'] as $attribute ){
						$attributes[] = $attribute['Attribute']['Option']['name'] . ': ' . $attribute['Attribute']['value'];
					}
					$attributes = implode(', ', $attributes);
					$customer_mail .= ' (' . $attributes . ')';
				}
				$customer_mail .= ' = ' . ($ordered_product['OrderedProduct']['product_quantity'] * $ordered_product['OrderedProduct']['product_price_with_dph']) . 'Kč' . "\n";
			}
			
			$order = $this->find('first', array(
				'conditions' => array('Order.id' => $this->id),
				'contain' => array()
			));
			
			$order = $order['Order'];

			$customer_mail .= '----------------------------------' . "\n";
			$customer_mail .= 'Mezisoučet: ' . $order['subtotal_with_dph'] . 'Kč' . "\n";
			$customer_mail .= 'Poštovné: ' . $order['shipping_cost'] . ' Kč' . "\n";
			$customer_mail .= 'Objednávka celkem: ' . ($order['subtotal_with_dph'] + $order['shipping_cost']) . ' Kč' . "\n\n";
	
			$customer_mail .= 'DODACÍ ADRESA:' . "\n";
			$customer_mail .= '----------------------------------' . "\n";
			$customer_mail .= $order['delivery_name'] . "\n";
			$customer_mail .= $order['delivery_street'] . "\n";
			$customer_mail .= $order['delivery_zip'] . ' ' . $order['delivery_city'] . "\n";
			$customer_mail .= $order['delivery_state'] . "\n\n";
	
			$customer_mail .= 'FAKTURAČNÍ ADRESA:' . "\n";
			$customer_mail .= '----------------------------------' . "\n";
			$customer_mail .= $order['customer_name'] . "\n";
			$customer_mail .= $order['customer_street'] . "\n";
			$customer_mail .= $order['customer_zip'] . ' ' . $order['customer_city'] . "\n";
			$customer_mail .= $order['customer_state'] . "\n\n";
	
			$mail_c = new phpmailer();
			// uvodni nastaveni
			$mail_c->CharSet = 'utf-8';
			$mail_c->Hostname = CUST_ROOT;
			$mail_c->Sender = CUST_MAIL;
	
			// nastavim adresu, od koho se poslal email
			$mail_c->From     = CUST_MAIL;
			$mail_c->FromName = "Automatické potvrzení";
	
			$mail_c->AddReplyTo(CUST_MAIL, CUST_NAME);
	
			$mail_c->AddAddress($customer['email'], $customer['first_name'] . ' ' . $customer['last_name']);
			
			$mail_c->Subject = 'POTVRZENÍ OBJEDNÁVKY (č. ' . $this->id . ')';
			$mail_c->Body = $customer_mail;
			$mail_c->Send();
		}
	}
	
	function notifyAdmin() {
		// notifikacni email prodejci
		// vytvorim tridu pro mailer
		App::import('Vendor', 'phpmailer', array('phpmailer/class.phpmailer.php'));
		$mail = new phpmailer();

		// uvodni nastaveni
		$mail->CharSet = 'utf-8';
		$mail->Hostname = CUST_ROOT;
		$mail->Sender = CUST_MAIL;

		// nastavim adresu, od koho se poslal email
		$mail->From     = CUST_MAIL;
		$mail->FromName = "Automatické potvrzení";

		$mail->AddReplyTo(CUST_MAIL, CUST_ROOT);

		$mail->AddAddress(CUST_MAIL, CUST_NAME);
		$mail->AddBCC('vlado@tovarnak.com');
		
		$mail->Subject = 'E-SHOP OBJEDNÁVKA (č. ' . $this->id . ')';
		$mail->Body = 'Právě byla přijata nová objednávka pod číslem ' . $this->id . '.' . "\n";
		$mail->Body .= 'Pro její zobrazení se přihlašte v administraci obchodu: http://www.' . CUST_ROOT . '/admin/' . "\n";
		$mail->Send();
	}
} // konec tridy
?>