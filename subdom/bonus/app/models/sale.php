<?php
class Sale extends AppModel {
	var $name = 'Sale';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'User',
		'Customer',
		'RecommendingCustomer' => array(
			'className' => 'Customer'
		)	
	);
	
	var $validate = array(
		'date' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte datum prodeje.'
			)
		),
		'price' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte cenu prodeje.'
			),
			'moreThenZero' => array(
				'rule' => 'moreThenZero',
				'message' => 'Cena prodeje musí být vyšší než 0.'
			)
		),
		'customer_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte zákazníka u prodeje.'
			)
		),
		'user_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Není znám uživatel, který vkládá prodej do systému!',
				'required' => true
			)
		)
	);
	
	var $export_fields = array(
		'Sale.date',
		'Customer.number',
		'Customer.last_name',
		'Customer.first_name',
		'Customer.degree_before',
		'Customer.degree_after',
		'Customer.salutation',
		'Customer.sex',
		'Customer.street',
		'Customer.zip',
		'Customer.city',
		'Customer.birth_certificate_number',
		'RecommendingCustomer.name',
		'Tariff.name',
		'Sale.price',
		'Sale.customer_bonus',
		'Sale.recommending_customer_bonus',
		'Sale.description',
		'Customer.account',
		'User.last_name'
	);
	
	function beforeValidate() {
		// bezne vybiram zakaznika pomoci autocomplete, ktere mi vyplni customer_id. Na vyzadani vsak lze do pole vlozit i customer number a potom musim customer_id zjistit
		if (!empty($this->data['Sale']['customer_name']) && empty($this->data['Sale']['customer_id'])) {
			$customer = $this->Customer->find('first', array(
				'conditions' => array('Customer.number' => $this->data['Sale']['customer_name']),
				'contain' => array(),
				'fields' => array('id')
			));

			// neexistuje zakaznik s vlozenym customer number
			if (!empty($customer)) {
				$this->data['Sale']['customer_id'] = $customer['Customer']['id'];
			}
			
		}
		return true;
	}
	
	// pred vlozenim prodeje dopocitam data potrebna k finalnimu ulozeni
	function beforeSave() {
		if (isset($this->data['Sale']['date'])) {
			// predelam datum z dd.mm.YY na YY-mm-dd
			$this->data['Sale']['date'] = en_date($this->data['Sale']['date']);
			if (!$this->data['Sale']['date']) {
				return false;
			}
		}
		// uprava desetinneho cisla (carka na tecku)
		if (isset($this->data['Sale']['customer_bonus'])) {
			$this->data['Sale']['customer_bonus'] = str_replace(',', '.', $this->data['Sale']['customer_bonus']);
		}
		if (isset($this->data['Sale']['recommending_customer_bonus'])) {
			$this->data['Sale']['recommending_customer_bonus'] = str_replace(',', '.', $this->data['Sale']['recommending_customer_bonus']);
		}

		// najdu zakaznika, ke kteremu pridavam prodej
		$this->Customer->virtualFields = array();
		$customer = $this->Customer->find('first', array(
			'conditions' => array(
				'Customer.id' => $this->data['Sale']['customer_id'],
				'Customer.active' => true
			),
			'contain' => array(
				'Tariff',
				'RecommendingCustomer' => array(
					'conditions' => array('RecommendingCustomer.active' => true),
					'Tariff'
				)	
			),
		));

		// neexistuje zakaznik		
		if (empty($customer)) {
			return false;
		}
		
		// zakaznik neni overen
		if ($customer['Customer']['recommending_customer_id'] && !$customer['Customer']['confirmed']) {
			// jestlize jsem potvrdil / zamitnul doporucujici osobu
			if (isset($this->data['Customer']) && array_key_exists('confirm', $this->data['Customer'])) {
				$customer_save = array(
					'Customer' => array(
						'id' => $customer['Customer']['id'],
						'confirmed' => true
					)
				);
				if (!$this->data['Customer']['confirm']) {

					// smazu DO u zakaznika
					$customer_save['Customer']['recommending_customer_id'] = null;
					unset($customer['RecommendingCustomer']['id']);
				}

				if (!$this->Customer->save($customer_save)) {
					return false;
				}
			} else {
				return false;
			}
			//return array('ERROR' => 'Zakaznik neni overen');
		}

		// pokud nema zakaznik tarif, nemuzu ukladat prodeje
		if (empty($customer['Tariff'])) {
			return false;
		}

		// pokud nemam bonus pro zakaznika stanoven implicitne v korunach
		if (!isset($this->data['Sale']['customer_bonus_amount']) || $this->data['Sale']['customer_bonus_amount'] == null) {
			// a nemam ho ani v procentech
			if (!isset($this->data['Sale']['customer_bonus']) || $this->data['Sale']['customer_bonus'] == null) {
				// dopocitam si zakaznikuv bonus
				$customer_bonus = round($this->data['Sale']['price'] * $customer['Tariff']['owner_amount'] / 100, 2);
				$this->data['Sale']['customer_bonus'] = $customer_bonus;
			// pri vlozeni mam bonus zadan v procentech, takze musim prepocitat na penize
			} elseif (!isset($this->data['Sale']['action']) || $this->data['Sale']['action'] != 'edit') {
				$this->data['Sale']['customer_bonus'] = round($this->data['Sale']['customer_bonus'] * $this->data['Sale']['price'] / 100, 2);
			}
		} else {
			$this->data['Sale']['customer_bonus'] = $this->data['Sale']['customer_bonus_amount'];
		}

		// dopocitam data pro doporucujici osobu
		if (empty($customer['RecommendingCustomer']['id'])) {
			unset($this->data['Sale']['recommending_customer_bonus']);
		} else {
			$recommending_customer = $customer['RecommendingCustomer'];
			// pokud nema doporucujici zakaznik tarif, nemuzu ukladat prodeje
			if (empty($recommending_customer['Tariff'])) {
				return false;
			}
			$this->data['Sale']['recommending_customer_id'] = $recommending_customer['id'];
			// pokud nemam bonus pro doporucujici osobu stanoven implicitne v korunach
			if (!isset($this->data['Sale']['recommending_customer_bonus_amount']) || $this->data['Sale']['recommending_customer_bonus_amount'] == 'null') {
				if (!isset($this->data['Sale']['recommending_customer_bonus']) || $this->data['Sale']['recommending_customer_bonus'] == null) {
					// dopocitam bonus pro doporucujici osobu
					$recommending_customer_bonus = round($this->data['Sale']['price'] * $recommending_customer['Tariff']['recommending_amount'] / 100, 2);
					$this->data['Sale']['recommending_customer_bonus'] = $recommending_customer_bonus;
				} elseif (!isset($this->data['Sale']['action']) || $this->data['Sale']['action'] != 'edit') {
					$this->data['Sale']['recommending_customer_bonus'] = round($this->data['Sale']['recommending_customer_bonus'] * $this->data['Sale']['price'] / 100, 2);
				}
			} else {
				$this->data['Sale']['recommending_customer_bonus'] = $this->data['Sale']['recommending_customer_bonus_amount'];
			}
		}
		
		return true;
	}
	
	// z prave vlozenych dat pripisu bonusy na ucty zakaznika a doporucujici osoby
	function afterSave($created) {
		// pripisu odpovidajici hodnotu na ucet zakaznika, ktery provedl prodej
		$this->Customer->move_account($this->data['Sale']['customer_id'], $this->data['Sale']['customer_bonus']);
		if (isset($this->data['Sale']['recommending_customer_id'])) {
			// pripisu odpovidajici hodnotu na ucet doporucujici osoby
			$this->Customer->move_account($this->data['Sale']['recommending_customer_id'], $this->data['Sale']['recommending_customer_bonus']);
		}
	}
	
	function beforeDelete() {
		$sale = $this->find('first', array(
			'conditions' => array('Sale.id' => $this->id),
			'contain' => array(),
		));
		
		if (empty($sale)) {
			return false;
		}

		// odepisu odpovidajici hodnotu z uctu zakaznika, ktery provedl prodej
		$this->Customer->move_account($sale['Sale']['customer_id'], -$sale['Sale']['customer_bonus']);
		// odepisu odpovidajici hodnotu z uctu doporucujici osoby
		$this->Customer->move_account($sale['Sale']['recommending_customer_id'], -$sale['Sale']['recommending_customer_bonus']);
		
		return true;
	}
}