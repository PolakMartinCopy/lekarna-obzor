<?
class Newsletter extends AppModel{
	var $name = 'Newsletter';
	
	var $actsAs = array('Containable');
	
	var $validate = array(
		'name' => array(
			'minLength' => array(
				'rule' => array('minLength', 1),
				'required' => true,
				'message' => 'Vyplňte prosím název newsletteru.'
			),
			'isUnique' => array(
				'rule' => array('isUnique', 'name'),
				'required' => true,
				'message' => 'Newsletter s tímto názvem již existuje! Zvolte prosím jiný název newsletteru.'
			)
		)
	);
	
	var $hasMany = array('NewslettersProduct', 'CustomersNewsletter');
	
	var $hasAndBelongsToMany = array('Product', 'Customer');
	
	/*
	 * @description					Sestavi telo mailu.
	 */
	function create_body($id){
		// nactu si vsechny produkty patrici do newsletteru
		$products = $this->NewslettersProduct->find('all', array(
			'conditions' => array('NewslettersProduct.newsletter_id' => $id),
			'fields' => array('NewslettersProduct.id', 'NewslettersProduct.product_id'),
			'contain' => array()
		));

		$ids = array();
		foreach ( $products as $product ){
			$ids[] = $product['NewslettersProduct']['product_id'];
		}

		$this->Product->recursive = -1;
		$products = $this->Product->find('all', array('conditions' => array('Product.id' => $ids)));
		
		// potrebuju vytahnout ceny pro prihlasene,
		// takze udelam fake prihlaseni
		App::import('Component', 'Session');
		$this->Session = new SessionComponent;
		$this->Session->write('Customer.id', 1);
		
		$mailcontent = '<table>';
		for ( $i = 0; $i < count($products); $i++ ){
			$products[$i]['Product']['discount_price'] = $this->Product->assign_discount_price($products[$i]['Product']['id'], $products[$i]['Product']['price']); 
			$mailcontent .= '<tr>
				<td colspan="2">
					' . $products[$i]['Product']['name'] . '
				</td>
			</tr>
			<tr>
				<td>
					původně: <span style="text-decoration:line-through">' . $products[$i]['Product']['price'] . ' Kč</span>
				</td>
				<td>
					nyní: <strong>' . $products[$i]['Product']['discount_price'] . ' Kč</strong>
				</td>
				</tr>';
		}
		$mailcontent .= '</table>';
		return $mailcontent;
	}

	/*
	 * @description					Vlozi do tabulky vsechny aktualni zakazniky se zadanym cislem newsletteru a nastavi sent na 0
	 */
	function fill_recipients($id){
		$customers = $this->Customer->find('all', array(
			'conditions' => array(),
			'fields' => array('DISTINCT (email)', 'id'),
			'contain' => array()
		));
		
		$inserted = 0;
		foreach ( $customers as $recipient ){
			$recipient['Customer']['email'] = trim($recipient['Customer']['email']);
			if ( valid_email($recipient['Customer']['email']) ){
				$data = array(
					'customer_id' => $recipient['Customer']['id'],
					'newsletter_id' => $id,
					'sent' => 0
				);
				
				unset($this->CustomersNewsletter->id);
				if ( $this->CustomersNewsletter->save($data) ){
					$inserted = $inserted + 1;
				}
			}
		}
		debug($inserted);
		die();
	}
	
	/*
	 * @description					Vyhleda produkty s "$query" v nazvu produktu.
	 */
	function search_products($query){
		// je-li policko pro vyhledavani prazdne, nioc nehledam, vracim prazdne pole,
		// coz znamena, ze nic nebylo nalezeno
		if ( empty($query) ){
			return array();
		}
		
		// vytahnu si produkty, obsahujici hledany vyraz v nazvu
		$this->Product->recursive = -1;
		$products = $this->Product->find('all', array('conditions' => array("Product.name LIKE '%%" . $query . "%%'")));

		// vratim nalezene produkty
		return $products;
	}

	/*
	 * @description					Vyhleda emailove adresy zakazniku.
	 */
	function recipients($id){
		// najde mi prvnich 100 lidi, kterym jeste nebyl odeslan newsletter
		$recipients = $this->CustomersNewsletter->find('all', array(
			'conditions' => array(
				'newsletter_id' => $id,
				'sent' => 0
			),
			'contain' => array(
				'Customer' => array(
					'fields' => array(
						'email', 'id', 'first_name', 'last_name'
					)
				)
			),
			'limit' => 100
		));

		return $recipients;
/*		return array(
			0 => array(
				'Customer' => array(
					'email' => 'vlado.tovarnak@gmail.com',
					'first_name' => 'Vlado',
					'last_name' => 'Tovarnak'
				)
			),
			1 => array(
				'Customer' => array(
					'email' => 'vlado@tovarnak.com',
					'first_name' => 'Vlado',
					'last_name' => 'Tovarnak'
				)
			)
		);*/
	}
	
	/*
	 * @description					Rozesle jednotlivym zakazniku email s newslettry.
	 */
	function send($id){
			include 'class.phpmailer.php';
			$ppm = &new phpmailer;
			$ppm->CharSet = 'windows-1250';
			$ppm->Hostname = 'nutrishop.cz';
			$ppm->Sender = 'newsletter@nutrishop.cz';
			$ppm->From = 'newsletter@nutrishop.cz';
			$ppm->FromName = 'NutriShop CZ';
			$ppm->AddReplyTo('newsletter@nutrishop.cz', 'NutriShop CZ');
			

			$ppm->Body = '<p style="font-size:10px;">nezobrazuje-li se vám obsah tohoto emailu správně, klikněte na následující <a href="http://www.nutrishop.cz/akce-tydne-c6?utm_source=mail_newsletter6_nezobrazuje">odkaz</a></p>
<div style="font-family:Arial">Dobrý den,<br />
<br />
<strong>velice si vážíme Vašeho předchozího zájmu</strong> o nákupy v našem obchodě se sportovní výživou NutriShop.cz<br />
Nyní jsme pro Vás připravili mimořádnou slevovou akci pro měsíc srpen, kterou bychom Vám tímto rádi představili.<br />
<br />
<strong>Výběr z produktů</strong>, které Vám nabízíme za akční ceny si můžete <strong>zobrazit kliknutím</strong> na následující <a href="http://www.nutrishop.cz/akce-tydne-c6?utm_source=mail_newsletter6_kliknete">odkaz</a>.<br />
<br />
Zde je jejich seznam:<br />
 ' . $this->create_body($id) . '<br />
Nákup za akční ceny lze uskutečnit pouze v případě, že se před odesláním objednávky přihlásíte ke svému zákaznickému účtu.<br />
<br />
S přáním příjemně stráveného zbytku léta<br /><br />
zdraví team obchodu NutriShop.cz<br />
<a href="http://www.nutrishop.cz/?utm_source=mail_newsletter6_paticka">www.nutrishop.cz</a>
</div>';

			$ppm->AltBody = 'http://www.nutrishop.cz/akce-tydne-c6?utm_source=mail_newsletter6_altbody';			
			$ppm->IsHTML = true;

			$recipients = $this->recipients($id);
			$poslano = 0;

			foreach ( $recipients as $recipient ){
				$recipient['Customer']['email'] = trim($recipient['Customer']['email']);
				if ( valid_email($recipient['Customer']['email']) ){
					$ppm->ClearAddresses();
					$ppm->Subject = 'akční nabídka pro ' . $recipient['Customer']['first_name'] . ' ' . $recipient['Customer']['last_name'];
					$ppm->AddAddress($recipient['Customer']['email'], $recipient['Customer']['first_name'] . ' ' . $recipient['Customer']['last_name']);

					if ( $ppm->Send() ){
						$data = array(
							'customer_id' => $recipient['Customer']['id'],
							'newsletter_id' => $id,
							'sent' => '1'
						);
						$this->CustomersNewsletter->id = $recipient['CustomersNewsletter']['id'];
						$this->CustomersNewsletter->save($data);
						$poslano++;
					} else {
						debug($recipient);
					}
				}
			}
			die('rozeslano ' . $poslano); 
	}
}
?>