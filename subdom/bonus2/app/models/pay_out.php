<?php
class PayOut extends AppModel {
	var $name = 'PayOut';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Customer', 'User');
	
	var $validate = array(
		'date' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte datum výplaty bonusu.'
			)
		),
		'amount' => array(
			'notEmpty' => array(				
				'rule' => 'notEmpty',
				'message' => 'Zadejte výši výplaty bonusu.',
				'last' => true
			),
			'lessThenAccount' => array(
				'rule' => 'lessThenAccount',
				'message' => 'Pro výplatu není dostatek prostředků na účtu zákazníka.',
				'last' => true
			),
			'moreThenZero' => array(
				'rule' => 'moreThenZero',
				'message' => 'Výplata musí být vyšší než 0'
			)
		),
		'customer_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte zákazníka u výplaty.'
			)
		),
		'user_id' => array(
			'rule' => 'notEmpty',
			'message' => 'Není znám uživatel, který provádí výplatu bonusu!',
			'required' => true
		)
	);
	
	var $export_fields = array(
		'PayOut.date',
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
		'PayOut.amount',
		'Customer.account',
		'User.last_name'
	);
	
	var $document_file = 'pay_out.pdf';
	
	// fce pro validaci, nenecha vlozit vyplatu vetsi nez je castka na uctu zakaznika
	// pri editaci vyplaty musi pocitat s tim, ze se puvodni vyplata smaze a tim se penize opet "prictou na ucet", takze pokud jsem mel napr na ucte 100, a opravuju vyplatu s hodnotou 50,
	// muzu vyplatit az 150
	function lessThenAccount() {
		if (isset($this->data['PayOut']['customer_id']) && !empty($this->data['PayOut']['customer_id'])) {
			// musim zkontrolovat, jestli neni vyplacena castka vyssi nez stav na uctu, v tom pripade nemuzu vyplatu provest
			$customer = $this->Customer->find('first', array(
				'conditions' => array('Customer.id' => $this->data['PayOut']['customer_id']),
				'contain' => array(),
				'fields' => array('Customer.id', 'Customer.account')
			));
			// pokud edituju vyplatu, musim pred validaci pricist puvodni penize "zpet" na ucet zakaznika
			if (isset($this->data['PayOut']['id'])) {
				$pay_out = $this->find('first', array(
					'conditions' => array('PayOut.id' => $this->data['PayOut']['id']),
					'contain' => array(),
					'fields' => array('PayOut.id', 'PayOut.amount')
				));
				
				$customer['Customer']['account'] += $pay_out['PayOut']['amount'];
			}
			if ($customer['Customer']['account'] < $this->data['PayOut']['amount']) {
				return false;
			}
		}
		return true;		
	}
	
	function beforeValidate() {
		// bezne vybiram zakaznika pomoci autocomplete, ktere mi vyplni customer_id. Na vyzadani vsak lze do pole vlozit i customer number a potom musim customer_id zjistit
		if (!empty($this->data['PayOut']['customer_name']) && empty($this->data['PayOut']['customer_id'])) {
			$customer = $this->Customer->find('first', array(
					'conditions' => array('Customer.number' => $this->data['PayOut']['customer_name']),
					'contain' => array(),
					'fields' => array('id')
			));
	
			// neexistuje zakaznik s vlozenym customer number
			if (!empty($customer)) {
				$this->data['PayOut']['customer_id'] = $customer['Customer']['id'];
			}
				
		}
		return true;
	}
	
	// pred vlozenim zaznamu
	function beforeSave() {
		if (isset($this->data['PayOut']['date'])) {
			// predelam datum z dd.mm.YY na YY-mm-dd
			$this->data['PayOut']['date'] = en_date($this->data['PayOut']['date']);
			if (!$this->data['PayOut']['date']) {
				return false;
			}
		}

		return true;
	}
	
	// po vlozeni - odectu vyplacenou castku z uctu
	function afterSave() {
		$this->Customer->move_account($this->data['PayOut']['customer_id'], -$this->data['PayOut']['amount']);
	}
	
	function beforeDelete() {
		$pay_out = $this->find('first', array(
			'conditions' => array('PayOut.id' => $this->id),
			'contain' => array(),
		));
	
		if (empty($pay_out)) {
			return false;
		}
	
		// pripisu odpovidajici hodnotu na ucet zakaznika, kteremu byla odstranena vyplata
		$this->Customer->move_account($pay_out['PayOut']['customer_id'], $pay_out['PayOut']['amount']);
	
		return true;
	}
	
	function create_pdf($pay_out) {
		App::import('Vendor', 'FPDF', array('file' => 'fpdf16/fpdf.php'));
		$pdf = &new FPDF;
		
		$pdf->AddPage();
		$pdf->AddFont('muj_arial', null, 'ae6f4530740019d1b33da0fdc98ffa84_arial.php');
		$pdf->AddFont('muj_arial_bold', null, 'ce6d1ba706659703a56f89323b8eff6d_arialbd.php');
		$pdf->SetDisplayMode('real');
		
		$pdf->SetFont('muj_arial_bold', null, 16);
				
		// prvni ramecek
		$pdf->SetLineWidth(0.3);
		$pdf->Line(20, 10, 180, 10);
		$pdf->Line(20, 10, 20, 130);
		$pdf->Line(180, 10, 180, 130);
		$pdf->Line(20, 130, 180, 130);
		// obsah prvniho ramecku
		$pdf->Cell(180, 30, '', 0, 1);
		
		$pdf->Image('images/lekarna-obzor-big.jpg', 35, 18, 131, 12, 'JPG');
		$pdf->Cell(180, 8, utf82cp1250('Dárkový poukaz ve výši ' . round($pay_out['PayOut']['amount']) . ',- Kč'), 0, 1, 'C');
		$pdf->Cell(180, 5, '', 0, 1);
		$pdf->SetFont('muj_arial', null, 11);
		$pdf->Cell(180, 5, utf82cp1250('Při předložení tohoto dárkového poukazu k nákupu zboží v Lékárně Obzor nad'), 0, 1, 'C');
		$pdf->Cell(180, 5, utf82cp1250('500,- Kč obdrží jeho majitel od společnosti Pharmacorp CZ s.r.o v hotovosti'), 0, 1, 'C');
		$pdf->Cell(180, 5, utf82cp1250('částku ' . round($pay_out['PayOut']['amount']) . ',- Kč.'), 0, 1, 'C');
		$pdf->Cell(180, 20, '', 0, 1);
		$pdf->SetFont('muj_arial_bold', null, 11);
		$pdf->Cell(180, 5, utf82cp1250('Pharmacorp CZ s.r.o., Brno, Fillova 260/1, PSČ 638 00'), 0, 1, 'C');
		$pdf->SetFont('muj_arial', null, 11);
		$pdf->Cell(180, 5, utf82cp1250('IČO: 29372828 DIČ: CZ29372828'), 0, 1, 'C');
		$pdf->Cell(180, 5, utf82cp1250('spol. je vedená u KS v Brně sp. značka C 76275'), 0, 1, 'C');
		$pdf->Cell(180, 5, '', 0, 1, 'C');
		$pdf->Cell(180, 5, utf82cp1250('tel.: 00420 533 101 360, e-mail: lekarnaobzor@pharmacorp.cz'), 0, 1, 'C');
		$pdf->SetFont('muj_arial_bold', null, 11);
		$pdf->Cell(180, 5, utf82cp1250('www.lekarna-obzor.cz'), 0, 1, 'C');
		
		// druhy ramecek
		$pdf->SetLineWidth(0.3);
		$pdf->Line(20, 150, 180, 150);
		$pdf->Line(20, 150, 20, 280);
		$pdf->Line(180, 150, 180, 280);
		$pdf->Line(20, 280, 180, 280);
		// obsah druheho ramecku

		$pdf->Cell(180, 40, '', 0, 1);
		$pdf->SetFont('muj_arial_bold', null, 14);
		$pdf->Cell(180, 8, utf82cp1250('Potvrzení o proplacení hotovosti za dárkový poukaz'), 0, 1, 'C');
		$pdf->Cell(180, 5, '', 0, 1);
		$pdf->SetLeftMargin(30);
		$pdf->SetFont('muj_arial_bold', null, 11);
		$pdf->Cell(180, 5, utf82cp1250('Příjemce: ' . $pay_out['Customer']['first_name'] . ' ' . $pay_out['Customer']['last_name']), 0, 1);
		$pdf->SetFont('muj_arial', null, 11);
		$pdf->Cell(180, 5, utf82cp1250('Adresa: ' . $pay_out['Customer']['street'] . ', ' . $pay_out['Customer']['zip'] . ' ' . $pay_out['Customer']['city']), 0, 1);
		$pdf->Cell(180, 5, utf82cp1250('RČ: ' . $pay_out['Customer']['birth_certificate_number']), 0, 1);
		$pdf->Cell(180, 5, '', 0, 1);
		$pdf->SetFont('muj_arial_bold', null, 11);
		$pdf->Cell(180, 5, utf82cp1250('Vyplatil: Pharmacorp CZ s.r.o.'), 0, 1);
		$pdf->SetFont('muj_arial', null, 11);
		$pdf->Cell(180, 5, utf82cp1250('Adresa: Brno, Fillova 260/1, PSČ 638 00'), 0, 1);
		$pdf->Cell(180, 5, utf82cp1250('IČO: 29372828'), 0, 1);
		$pdf->Cell(180, 5, utf82cp1250('DIČ: CZ29372828'), 0, 1);
		$pdf->Cell(180, 5, '', 0, 1);
		$pdf->SetFont('muj_arial_bold', null, 11);
		$pdf->Cell(180, 5, utf82cp1250('Vyplaceno: ' . round($pay_out['PayOut']['amount']) . ',- Kč'), 0, 1);
		$pdf->Cell(180, 5, utf82cp1250('Účel platby: platba za dárkový poukaz'), 0, 1);
		$pdf->Cell(180, 5, utf82cp1250('Dne: ' . cz_date($pay_out['PayOut']['date'])), 0, 1);
		$pdf->Cell(180, 25, '', 0, 1);
		
		$pdf->Cell(80, 5, utf82cp1250('........................................'), 0);
		$pdf->Cell(100, 5, utf82cp1250('........................................'), 0, 1);
		$pdf->Cell(80, 5, utf82cp1250('Vydal: ' . $pay_out['User']['first_name'] . ' ' . $pay_out['User']['last_name']), 0);
		$pdf->Cell(100, 5, utf82cp1250('Přijal: ' . $pay_out['Customer']['first_name'] . ' ' . $pay_out['Customer']['last_name']), 0, 1);

		$pdf->Output();
		//return $pdf->Output($this->document_file, 'F');
	}
}