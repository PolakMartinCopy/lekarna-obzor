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
$linestyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(0, 0, 0));
$tcpdf->Line(10, 20, 200, 20, $linestyle);
$tcpdf->Line(10, 65, 200, 65, $linestyle);
$tcpdf->Line(10, 87, 200, 87, $linestyle);
$tcpdf->Line(10, 102, 200, 102, $linestyle);

$tcpdf->SetFont($textfont, 'B', 14);
$tcpdf->Cell(190, 0, 'Opravný daňový doklad č. ' . $credit_note['CSCreditNote']['code'], 0, 0, 'R', false);


// mezera
$tcpdf->Cell(190, 7, "", 0, 1, 'L', false);
$tcpdf->Ln();
$tcpdf->Cell(190, 7, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'B',11);
$tcpdf->Cell(100, 0, 'Dodavatel', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, 'Odběratel', 0, 1, 'L', false);

// mezera
$tcpdf->Cell(190, 3, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell(100, 0, 'PharmaCorp CZ s.r.o.', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $credit_note['BusinessPartner']['name'], 0, 1, 'L', false);

$street_info = '';
$city_info = '';
if (!empty($credit_note['Address'])) {
	$street_info = $credit_note['Address']['street'] . ' ' . $credit_note['Address']['number'];
	if (!empty($credit_note['Address']['o_number'])) {
		$street_info .= '/' . $credit_note['Address']['o_number'];
	}
	$city_info = $credit_note['Address']['zip'] . ' ' . $credit_note['Address']['city'];
}
$ico_info = '';
if (!empty($credit_note['BusinessPartner']['ico'])) {
	$ico_info = 'IČ: ' . $credit_note['BusinessPartner']['ico'];
}

$dic_info = '';
if (!empty($credit_note['BusinessPartner']['dic'])) {
	$dic_info = 'DIČ: ' . $credit_note['BusinessPartner']['dic'];
}

$tcpdf->Cell(100, 0, 'Fillova 260/1', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $street_info, 0, 1, 'L', false);

$tcpdf->Cell(100, 0, '63800 Brno', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $city_info, 0, 1, 'L', false);

$tcpdf->Cell(100, 0, 'IČ: 29372828', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $ico_info, 0, 1, 'L', false);

$tcpdf->Cell(100, 0, 'DIČ: CZ29372828', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $dic_info, 0, 1, 'L', false);

$tcpdf->Cell(190, 10, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont, 'B',11);
$tcpdf->Cell(190, 0, 'Platební podmínky', 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);

$date_of_issue = explode(' ', $credit_note['CSCreditNote']['date_of_issue']);
$date_of_issue = $date_of_issue[0];
$date_of_issue_info = 'Datum vystavení: ' . db2cal_date($date_of_issue);

$tcpdf->Cell(100, 0, 'Forma úhrady: převodem', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $date_of_issue_info, 0, 1, 'L', false);

$tcpdf->Cell(100, 0, 'Číslo účtu: 2400317559/2010', 0, 0, 'L', false);
$tcpdf->SetFont($textfont, 'B', 8);
$tcpdf->Cell(90, 0, 'Datum splatnosti: ' . db2cal_date($credit_note['CSCreditNote']['due_date']), 0, 1, 'L', false);

$taxable_event_info = 'Datum zdanitelného plnění: ' . db2cal_date($date_of_issue);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell(100, 0, 'Variabilní symbol: ' . $credit_note['CSCreditNote']['code'], 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $taxable_event_info, 0, 1, 'L', false);

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont, 'B', 11);
$tcpdf->Cell(190, 5, 'Poznámka', 0, 1, 'L', false);

$note = "";
if (!empty($credit_note['CSCreditNote']['note'])) {
	$note = $credit_note['CSCreditNote']['note'];
}
$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell(190, 5, $note, 0, 1, 'L', false);

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont, 'B',11);
$tcpdf->Cell(190, 0, 'Fakturujeme Vám', 0, 1, 'L', false);
$tcpdf->Cell(190, 3, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);

$tbl = "
<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" style=\"text-align:center\">
    <tr>
        <th><strong>Popis zboží</strong></th>
        <th><strong>Množství</strong></th>
        <th><strong>Cena za MJ bez DPH</strong></th>
		<th><strong>Cena za MJ vč. DPH</strong></th>
		<th><strong>Popis</strong></th>
    </tr>
";

foreach ($credit_note['CSTransactionItem'] as $transaction_item) {
	$tbl .= "
	<tr>
		<td>" . $transaction_item['product_name'] . "</td>
		<td>" . $transaction_item['quantity'] . "</td>
		<td>" . number_format(-$transaction_item['price'], 2, ',', ' ') . "</td>
		<td>" . number_format(-$transaction_item['price_vat'], 2, ',', ' ') . "</td>
		<td>" . $transaction_item['description'] . "</td>
	</tr>
";
}
$tbl .= "
</table>
";
$tcpdf->writeHTML($tbl, true, false, false, false, '');

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont, '', 8);
$tcpdf->Cell(190, 0, 'Základ: ' . number_format(-$credit_note['CSCreditNote']['amount'], 2, ',', ' ') . ' Kč', 0, 1, 'R', false);
$tcpdf->Cell(190, 0, 'Daň: ' . number_format(-($credit_note['CSCreditNote']['amount_vat'] - $credit_note['CSCreditNote']['amount']), 2, ',', ' ') . ' Kč', 0, 1, 'R', false);
$tcpdf->SetFont($textfont, 'B', 8);
$tcpdf->Cell(190, 0, 'Celkem k úhradě: ' . number_format(-$credit_note['CSCreditNote']['amount_vat'], 2, ',', ' ') . ' Kč', 0, 1, 'R', false);

$tcpdf->SetFont($textfont,'', 8);
$user_info = $credit_note['User']['first_name'] . ' ' . $credit_note['User']['last_name'];

$tcpdf->Cell(190, 15, "", 0, 1, 'L', false);

$tcpdf->Cell(100, 0, '.........................', 0, 0, 'C', false);
$tcpdf->Cell(90, 0, '.........................', 0, 1, 'C', false);
$tcpdf->Cell(100, 0, 'Vystavil: ' . $user_info, 0, 0, 'C', false);
$tcpdf->Cell(90, 0, 'Přijal', 0, 1, 'C', false);
 
echo $tcpdf->Output($credit_note['CSCreditNote']['code'] . '.pdf', 'D');

?>