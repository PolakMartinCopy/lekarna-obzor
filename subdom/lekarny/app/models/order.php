<?
class Order extends AppModel{
	var $name = 'Order';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Company', 'Payment', 'Shipping', 'Status');
	
	var $hasMany = array(
		'OrderedProduct' => array(
			'dependent' => true
		),
		'Ordernote' => array(
			'dependent' => true
		)
	);

	function notify_admins_new_order($order_id){
		App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
		$mail = &new phpmailer;
		
		// uvodni nastaveni maileru
		$mail->CharSet = 'UTF-8';
		$mail->Hostname = 'lekarny.lekarna-obzor.cz';
		$mail->Sender = 'no-reply@lekarna-obzor.cz';
		
		// nastavim adresu, od koho se poslal email
		$mail->From     = "no-reply@lekarna-obzor.cz";
		$mail->FromName = "Automatické potvrzení";
		$mail->AddReplyTo("no-reply@lekarna-obzor.cz","Pharmacorp CZ s.r.o.");
		
		// nastavim kam se posila email
		$mail->AddAddress('lekarny@lekarna-obzor.cz');
//		$mail->AddBCC('vlado.tovarnak@gmail.com', 'Vladimír Tovarňák');

		$mail->Subject = 'objednavkovy system - nova objednavka s číslem ' . $order_id;

		$mail->Body = "Právě byla uložena nová objednávka do objednávkového systému společnosti Pharmacorp CZ s.r.o. pod číslem " . $order_id . "
Pro přihlášení do systému pužijte následující URL:
http://lekarny.lekarna-obzor.cz/admin/administrators/login

--
Pharmacorp CZ s.r.o.
http://www.lekarna-obzor.cz/
";

		return $mail->Send();
	}

	function reCount($id = null){
		// predpokladam, ze postovne bude za 0 Kc
		$order['Order']['shipping_cost'] = 0;

		// nactu si produkty z objednavky a data o ni
		$contain = array(
			'OrderedProduct' => array(
				'fields' => array('id', 'product_id', 'product_quantity', 'product_price'),
				'Product' => array(
					'fields' => array('id', 'name'),
				)
			)
		);
		
		$conditions = array('id' => $id);
		
		$fields = array('id', 'company_name', 'company_ico', 'company_dic', 'subtotal', 'shipping_cost', 'shipping_id');
		
		$products = $this->find('first', array(
			'conditions' => $conditions,
			'contain' => $contain,
			'fields' => $fields
		));
	
		
		// nathnu si detaily o postovnem,
		$this->Shipping->recursive = -1;
		$shipping = $this->Shipping->read(null, $products['Order']['shipping_id']);
		
		$order_total = 0;
		foreach ( $products['OrderedProduct'] as $product ){
			$order_total = $order_total + $product['product_price'] * $product['product_quantity'];
		}
		
		// predpoklad, ze postovne bude zdarma
		$order['Order']['shipping_cost'] = 0;
		// pokud je postovne pro zakazniky zdarma nemusim nic kontrolovat
		if ( $shipping['Shipping']['price'] != '0' ){
			// jinak si porovnam cenu objednavky s "cenou" postovneho zdarma
			// pokud je celkova cena objednavky mala, musim uctovat postovne
			if ( $shipping['Shipping']['free'] > $order_total ){
				$order['Order']['shipping_cost'] = $shipping['Shipping']['price'];
			}
		}
		
		$order['Order']['subtotal'] = $order_total;
		$this->id = $id;
		$this->save($order, false, array('subtotal', 'shipping_cost'));
	}
}
?>