<?php
App::import('Vendor','xtcpdf');

$tcpdf = new XTCPDF();
$textfont = 'dejavusans'; // looks better, finer, and more condensed than 'dejavusans'

$tcpdf->SetAuthor("c.lekarna-obzor.cz");
$tcpdf->SetAutoPageBreak( false );
$tcpdf->setHeaderFont(array($textfont,'',40));
$tcpdf->xheadercolor = array(150,0,0);
$tcpdf->xheadertext = 'Lekarna-Obzor.cz';
$tcpdf->xfootertext = 'Copyright © %d Lekarna-Obzor.cz. All rights reserved.';

// add a page (required with recent versions of tcpdf)
$tcpdf->AddPage();

$tcpdf->SetFillColor(255,255,255);

$tcpdf->SetFont($textfont, 'B', 14);
$tcpdf->Cell(190, 0, 'Dodací list', 0, 0, 'L', false);

// mezera
$tcpdf->Cell(190, 15, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'B',11);
$tcpdf->Cell(100, 0, 'Dodavatel', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, 'Odběratel', 0, 1, 'L', false);

// mezera
$tcpdf->Cell(190, 3, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell(100, 0, 'PharmaCorp CZ s.r.o.', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $delivery_note['BusinessPartner']['name'], 0, 1, 'L', false);

$street_info = '';
$city_info = '';
if (!empty($delivery_note['Address'])) {
	$street_info = $delivery_note['Address']['street'] . ' ' . $delivery_note['Address']['number'];
	if (!empty($delivery_note['Address']['o_number'])) {
		$street_info .= '/' . $delivery_note['Address']['o_number'];
	}
	$city_info = $delivery_note['Address']['zip'] . ' ' . $delivery_note['Address']['city'];
}
$ico_info = '';
if (!empty($delivery_note['BusinessPartner']['ico'])) {
	$ico_info = 'IČO: ' . $delivery_note['BusinessPartner']['ico'];
}

$tcpdf->Cell(100, 0, 'Fillova 260/1', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $street_info, 0, 1, 'L', false);

$tcpdf->Cell(100, 0, '63800 Brno', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $city_info, 0, 1, 'L', false);

$tcpdf->Cell(190, 15, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'BU',11);
$tcpdf->Cell(190, 0, 'Datum vystavení: ' . czech_date($delivery_note['DeliveryNote']['date']), 0, 1, 'R', false);

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'B',11);
$tcpdf->Cell(50, 0, 'Kód VZP', 0, 0, 'L', false);
$tcpdf->Cell(100, 0, 'Název zboží', 0, 0, 'L', false);
$tcpdf->Cell(40, 0, 'Množství MJ', 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);

foreach ($delivery_note['ProductsTransaction'] as $products_transaction) {
	$tcpdf->Cell(50, 0, $products_transaction['Product']['vzp_code'], 0, 0, 'L', false);
	$tcpdf->Cell(100, 0, $products_transaction['Product']['name'], 0, 0, 'L', false);
	$quantity_info = $products_transaction['quantity'] . ' ' . $products_transaction['Product']['Unit']['shortcut'];
	$tcpdf->Cell(40, 0, $quantity_info, 0, 1, 'L', false);
}

$tcpdf->Cell(190, 10, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'BU',11);
$tcpdf->Cell(190, 0, 'Aktuální stav zboží (včetně tohoto dodacího listu)', 0, 1, 'L', false);
$tcpdf->SetFont($textfont,'',8);
// seznam zbozi
$i = 0;
// produkty chci vypisovat ve 2 sloupcich
while ($i < count($store_items)) {
	// nactu produktu do leveho sloupce
	$first_column_product = $store_items[$i];
	$first_column_product_info = $first_column_product['Product']['name'] . '   ' . $first_column_product['StoreItem']['quantity'] . ' ' . $first_column_product['Unit']['shortcut'];
	if (!empty($first_column_product['Product']['vzp_code'])) {
		$first_column_product_info = $first_column_product['Product']['vzp_code'] . '   ' . $first_column_product_info;
	}
	// a pokud existuje po nem dalsi produkt
	$second_column_product = null;
	if (isset($store_items[$i+1])) {
		// nactu ho do praveho sloupce
		$second_column_product = $store_items[$i+1];
		$second_column_product_info = $second_column_product['Product']['name'] . ' ' . $second_column_product['StoreItem']['quantity'] . ' ' . $second_column_product['Unit']['shortcut'];
		if (!empty($second_column_product['Product']['vzp_code'])) {
			$second_column_product_info = $second_column_product['Product']['vzp_code'] . ' ' . $second_column_product_info;
		}
	}
	// pokud produkt do praveho sloupce neexistuje, ukoncim za produktem v levem sloupci radek
	$line_break = !isset($second_column_product);
	// zapisu
	$tcpdf->Cell(100, 0, $first_column_product_info, 0, $line_break, 'L', false);
	if (isset($second_column_product)) {
		$tcpdf->Cell(100, 0, $second_column_product_info, 0, 1, 'L', false);
	}
	$i = $i + 2;
}
$tcpdf->Cell(190, 10, "", 0, 1, 'L', false);

if (!empty($forward_products)) {
	$tcpdf->SetFont($textfont,'BU',11);
	$tcpdf->Cell(190, 0, 'Seznam zboží vydaného dopředu', 0, 1, 'L', false);
	$tcpdf->SetFont($textfont,'',8);
	// seznam zbozi
	$i = 0;
	// produkty chci vypisovat ve 2 sloupcich
	while ($i < count($forward_products)) {
		// nactu produktu do leveho sloupce
		$first_column_product = $forward_products[$i];
		$first_column_product_info = $first_column_product['Product']['name'] . '   ' . abs($first_column_product['StoreItem']['quantity']) . ' ' . $first_column_product['Unit']['shortcut'];
		if (!empty($first_column_product['Product']['vzp_code'])) {
			$first_column_product_info = $first_column_product['Product']['vzp_code'] . '   ' . $first_column_product_info;
		}
		// a pokud existuje po nem dalsi produkt
		$second_column_product = null;
		if (isset($forward_products[$i+1])) {
			// nactu ho do praveho sloupce
			$second_column_product = $forward_products[$i+1];
			$second_column_product_info = $second_column_product['Product']['name'] . ' ' . abs($second_column_product['StoreItem']['quantity']) . ' ' . $second_column_product['Unit']['shortcut'];
			if (!empty($second_column_product['Product']['vzp_code'])) {
				$second_column_product_info = $second_column_product['Product']['vzp_code'] . ' ' . $second_column_product_info;
			}
		}
		// pokud produkt do praveho sloupce neexistuje, ukoncim za produktem v levem sloupci radek
		$line_break = !isset($second_column_product);
		// zapisu
		$tcpdf->Cell(100, 0, $first_column_product_info, 0, $line_break, 'L', false);
		if (isset($second_column_product)) {
			$tcpdf->Cell(100, 0, $second_column_product_info, 0, 1, 'L', false);
		}
		$i = $i + 2;
	}
	$tcpdf->Cell(190, 10, "", 0, 1, 'L', false);
}

if (!empty($products_history)) {
	$tcpdf->SetFont($textfont,'BU',11);
	$tcpdf->Cell(190, 0, 'V minulosti odebíráno', 0, 1, 'L', false);
	$tcpdf->SetFont($textfont,'', 8);
	// seznam zbozi
	$i = 0;
	// produkty chci vypisovat ve 2 sloupcich
	while ($i < count($products_history)) {
		// nactu produktu do leveho sloupce
		$first_column_product = $products_history[$i];
		$first_column_product_info = $first_column_product['Product']['name'];
		if (!empty($first_column_product['Product']['vzp_code'])) {
			$first_column_product_info = $first_column_product['Product']['vzp_code'] . ' ' . $first_column_product_info;
		}
		// a pokud existuje po nem dalsi produkt
		$second_column_product = null;
		if (isset($products_history[$i+1])) {
			// nactu ho do praveho sloupce
			$second_column_product = $products_history[$i+1];
			$second_column_product_info = $second_column_product['Product']['name'];
			if (!empty($second_column_product['Product']['vzp_code'])) {
				$second_column_product_info = $second_column_product['Product']['vzp_code'] . ' ' . $second_column_product_info;
			}
		}
		// pokud produkt do praveho sloupce neexistuje, ukoncim za produktem v levem sloupci radek
		$line_break = !isset($second_column_product);
		// zapisu
		$tcpdf->Cell(100, 0, $first_column_product_info, 0, $line_break, 'L', false);
		if (isset($second_column_product)) {
			$tcpdf->Cell(100, 0, $second_column_product_info, 0, 1, 'L', false);
		}
		$i = $i + 2;
	} 
	
	$tcpdf->Cell(190, 10, "", 0, 1, 'L', false);
}

$tcpdf->Cell(190, 0, 'Děkujeme za spolupráci.', 0, 1, 'L', false);

$tcpdf->Cell(100, 0, '', 0, 0, 'L', false);
$user_info = $delivery_note['User']['first_name'] . ' ' . $delivery_note['User']['last_name'];
$tcpdf->Cell(90, 0, 'Vystavil(a): ' . $user_info, 0, 1, 'L', false);

$tcpdf->Cell(190, 15, "", 0, 1, 'L', false);

$tcpdf->Cell(30, 0, 'Příjemce:', 0, 0, 'L', false);
$tcpdf->Cell(70, 0, '.........................', 0, 0, 'L', false);
$tcpdf->Cell(40, 0, 'Razítko a podpis:', 0, 0, 'L', false);
$tcpdf->Cell(50, 0, '.........................', 0, 1, 'L', false);

echo $tcpdf->Output('filename.pdf', 'D');

?>