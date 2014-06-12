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
$tcpdf->Cell(190, 0, 'Stav skladu', 0, 0, 'L', false);

// mezera
$tcpdf->Cell(190, 15, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'B',11);
$tcpdf->Cell(100, 0, 'Dodavatel', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, 'Odběratel', 0, 1, 'L', false);

// mezera
$tcpdf->Cell(190, 3, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell(100, 0, 'PharmaCorp CZ s.r.o.', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $business_partner['BusinessPartner']['name'], 0, 1, 'L', false);

$street_info = '';
$city_info = '';
if (!empty($business_partner['Address'])) {
	$street_info = $business_partner['Address'][0]['street'] . ' ' . $business_partner['Address'][0]['number'];
	if (!empty($business_partner['Address'][0]['o_number'])) {
		$street_info .= '/' . $business_partner['Address'][0]['o_number'];
	}
	$city_info = $business_partner['Address'][0]['zip'] . ' ' . $business_partner['Address'][0]['city'];
}
$ico_info = '';
if (!empty($business_partner['BusinessPartner']['ico'])) {
	$ico_info = 'IČO: ' . $business_partner['BusinessPartner']['ico'];
}

$tcpdf->Cell(100, 0, 'Fillova 260/1', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $street_info, 0, 1, 'L', false);

$tcpdf->Cell(100, 0, '63800 Brno', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $city_info, 0, 1, 'L', false);

$tcpdf->Cell(190, 15, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'BU',11);
$tcpdf->Cell(190, 0, 'Datum vystavení: ' . date('d.m.Y'), 0, 1, 'R', false);

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

if (!empty($store_items)) {
	$tcpdf->SetFont($textfont,'', 8);
	// seznam zbozi
	$tbl = <<<EOD
<table cellspacing="1" cellpadding="1" border="0">
	<tr>
		<th><strong>VZP kód zboží</strong></th>
		<th><strong>Název zboží</strong></th>
		<th><strong>Množství</strong></th>
	</tr>
EOD;
	foreach ($store_items as $store_item) {
		$tbl .= "
	<tr>
		<td>" . $store_item['Product']['vzp_code'] . "</td>
		<td>" . $store_item['Product']['name'] . "</td>
		<td>" . $store_item['StoreItem']['quantity'] . " " . $store_item['Unit']['shortcut'] . "</td>
	</tr>";
	}
	$tbl .= "
</table>";
	
	$tcpdf->writeHTML($tbl, true, false, false, false, '');

	$tcpdf->Cell(190, 10, "", 0, 1, 'L', false);
}

$tcpdf->Cell(190, 0, 'Děkujeme za spolupráci.', 0, 1, 'L', false);

$tcpdf->Cell(100, 0, '', 0, 0, 'L', false);
$user_info = $user['User']['first_name'] . ' ' . $user['User']['last_name'];
$tcpdf->Cell(90, 0, 'Vystavil(a): ' . $user_info, 0, 1, 'L', false);

$tcpdf->Cell(190, 15, "", 0, 1, 'L', false);

$tcpdf->Cell(30, 0, 'Příjemce:', 0, 0, 'L', false);
$tcpdf->Cell(70, 0, '.........................', 0, 0, 'L', false);
$tcpdf->Cell(40, 0, 'Razítko a podpis:', 0, 0, 'L', false);
$tcpdf->Cell(50, 0, '.........................', 0, 1, 'L', false);

echo $tcpdf->Output('filename.pdf', 'D');
?>