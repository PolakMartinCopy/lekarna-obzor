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
if ($invoice['Language']['shortcut'] == 'cs') {
	$tcpdf->Line(10, 87, 200, 87, $linestyle);
	$tcpdf->Line(10, 102, 200, 102, $linestyle);
} else {
	$tcpdf->Line(10, 92, 200, 92, $linestyle);
	$tcpdf->Line(10, 107, 200, 107, $linestyle);
}

$tcpdf->SetFont($textfont, 'B', 14);
$tcpdf->Cell(190, 0, ($invoice['Language']['shortcut'] == 'cs' ? 'Faktura č. ' : 'Invoice ') . $invoice['CSInvoice']['code'], 0, 0, 'R', false);


// mezera
$tcpdf->Cell(190, 7, "", 0, 1, 'L', false);
$tcpdf->Ln();
$tcpdf->Cell(190, 7, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'B',11);
$tcpdf->Cell(100, 0, ($invoice['Language']['shortcut'] == 'cs' ? 'Dodavatel' : 'Supplier (from)'), 0, 0, 'L', false);
$tcpdf->Cell(90, 0, ($invoice['Language']['shortcut'] == 'cs' ? 'Odběratel' : 'Customer (to)'), 0, 1, 'L', false);

// mezera
$tcpdf->Cell(190, 3, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell(100, 0, 'MeaVita s.r.o.', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $invoice['BusinessPartner']['name'], 0, 1, 'L', false);

$street_info = '';
$city_info = '';
if (!empty($invoice['Address'])) {
	$street_info = $invoice['Address']['street'] . ' ' . $invoice['Address']['number'];
	if (!empty($invoice['Address']['o_number'])) {
		$street_info .= '/' . $invoice['Address']['o_number'];
	}
	$city_info = $invoice['Address']['zip'] . ' ' . $invoice['Address']['city'];
}
$ico_info = '';
if (!empty($invoice['BusinessPartner']['ico'])) {
	$ico_info = ($invoice['Language']['shortcut'] == 'cs' ? 'IČ: ' : 'ID (IČ): ') . $invoice['BusinessPartner']['ico'];
}

$dic_info = '';
if (!empty($invoice['BusinessPartner']['dic'])) {
	$dic_info = ($invoice['Language']['shortcut'] == 'cs' ? 'DIČ: ' : 'VAT reg. no. (DIČ): ') .  $invoice['BusinessPartner']['dic'];
}

$tcpdf->Cell(100, 0, 'Cejl 37/62', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $street_info, 0, 1, 'L', false);

$tcpdf->Cell(100, 0, '60200 Brno', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $city_info, 0, 1, 'L', false);

$tcpdf->Cell(100, 0, 'IČ: 29248400', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $ico_info, 0, 1, 'L', false);

$tcpdf->Cell(100, 0, 'DIČ: CZ29248400', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $dic_info, 0, 1, 'L', false);

$tcpdf->Cell(190, 10, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont, 'B',11);
$tcpdf->Cell(190, 0, ($invoice['Language']['shortcut'] == 'cs' ? 'Platební podmínky' : 'Payment conditions'), 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);

$date_of_issue = explode(' ', $invoice['CSInvoice']['date_of_issue']);
$date_of_issue = $date_of_issue[0];
$date_of_issue_info = ($invoice['Language']['shortcut'] == 'cs' ? 'Datum vystavení: ' : 'Date: ') . db2cal_date($date_of_issue);

$tcpdf->Cell(100, 0, ($invoice['Language']['shortcut'] == 'cs' ? 'Forma úhrady: převodem' : 'Payment: 100% in advance'), 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $date_of_issue_info, 0, 1, 'L', false);

if ($invoice['Language']['shortcut'] == 'cs') {
	$tcpdf->Cell(100, 0, 'Číslo účtu: 2400317559/2010', 0, 0, 'L', false);
	$tcpdf->SetFont($textfont, 'B', 8);
	$tcpdf->Cell(90, 0, 'Datum splatnosti: ' . db2cal_date($invoice['CSInvoice']['due_date']), 0, 1, 'L', false);
	$tcpdf->SetFont($textfont,'', 8);
	
	$taxable_event_info = 'Datum zdanitelného plnění: ' . db2cal_date($date_of_issue);
	$tcpdf->Cell(100, 0, 'Variabilní symbol: ' . $invoice['CSInvoice']['code'], 0, 0, 'L', false);
	$tcpdf->Cell(90, 0, $taxable_event_info, 0, 1, 'L', false);	
} else {
	$tcpdf->Cell(100, 0, 'Bank name: Fio banka, a.s.', 0, 0, 'L', false);
	$tcpdf->Cell(90, 0, 'Order no.: ' . $invoice['CSInvoice']['order_number'], 0, 1, 'L', false);
	
	$tcpdf->Cell(100, 0, 'IBAN Account no.: CZ0620100000002000098174', 0, 0, 'L', false);
	$tcpdf->Cell(90, 0, 'Country of origin: EU', 0, 1, 'L', false);
	
	$tcpdf->Cell(100, 0, 'SWIFT Code: FIOBCZPPXXX', 0, 0, 'L', false);
	$tcpdf->Cell(90, 0, '', 0, 1, 'L', false);
}

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont, 'B', 11);
$tcpdf->Cell(190, 5, ($invoice['Language']['shortcut'] == 'cs' ? 'Poznámka' : 'Note'), 0, 1, 'L', false);

$note = "";
if (!empty($invoice['CSInvoice']['note'])) {
	$note = $invoice['CSInvoice']['note'];
}
$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell(190, 5, $note, 0, 1, 'L', false);

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont, 'B',11);
$tcpdf->Cell(190, 0, ($invoice['Language']['shortcut'] == 'cs' ? 'Fakturujeme Vám' : ''), 0, 1, 'L', false);
$tcpdf->Cell(190, 3, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);

$tbl = '
<table cellspacing="0" cellpadding="1" border="0" style="text-align:center">
    <tr>
        <th><strong>' . ($invoice['Language']['shortcut'] == 'cs' ? 'Popis zboží' : 'Description') . '</strong></th>
        <th><strong>' . ($invoice['Language']['shortcut'] == 'cs' ? 'Množství' : 'Quantity') . '</strong></th>
        <th><strong>' . ($invoice['Language']['shortcut'] == 'cs' ? 'Cena za MJ bez DPH' : 'Unit price without VAT') . '</strong></th>
		<th><strong>' . ($invoice['Language']['shortcut'] == 'cs' ? 'Cena za MJ vč. DPH' : 'Subtotal price without VAT') . '</strong></th>
    </tr>
';

foreach ($invoice['CSTransactionItem'] as $transaction_item) {
	$tbl .= '
	<tr>
		<td nowrap="nowrap">' . ($invoice['Language']['shortcut'] == 'cs' ? $transaction_item['product_name'] : $transaction_item['product_en_name']) . '</td>
		<td>' . $transaction_item['quantity'] . '</td>
		<td>' . number_format($transaction_item['price'], 2, ',', ' ') . ' ' . $invoice['Currency']['shortcut'] . '</td>
		<td>' . ($invoice['Language']['shortcut'] == 'cs' ? number_format($transaction_item['price_vat'], 2, ',', ' ') : number_format($transaction_item['price'] * $transaction_item['quantity'], 2, ',', ' ')) . ' ' . $invoice['Currency']['shortcut'] . '</td>
	</tr>
';
}
$tbl .= '
</table>
';
				
$tcpdf->writeHTML($tbl, true, false, false, false, '');

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

if ($invoice['Language']['shortcut'] == 'cs') {
	$tcpdf->SetFont($textfont, '', 8);
	$tcpdf->Cell(190, 0, 'Základ: ' . number_format($invoice['CSInvoice']['amount'], 2, ',', ' '). ' ' . $invoice['Currency']['shortcut'], 0, 1, 'R', false);
	$tcpdf->Cell(190, 0, 'Daň: ' . number_format($invoice['CSInvoice']['amount_vat'] - $invoice['CSInvoice']['amount'], 2, ',', ' '). ' ' . $invoice['Currency']['shortcut'], 0, 1, 'R', false);
}

$tcpdf->SetFont($textfont, 'B', 8);

if ($invoice['Language']['shortcut'] == 'cs') {
	$tcpdf->Cell(190, 0, 'Celkem k úhradě: ' . number_format($invoice['CSInvoice']['amount_vat'], 2, ',', ' ') . ' ' . $invoice['Currency']['shortcut'], 0, 1, 'R', false);
} else {
	$tcpdf->Cell(190, 0, 'Total price without VAT: ' . number_format($invoice['CSInvoice']['amount'], 2, ',', ' '). ' ' . $invoice['Currency']['shortcut'], 0, 1, 'R', false);
}

$tcpdf->SetFont($textfont,'', 8);
$user_info = $invoice['User']['first_name'] . ' ' . $invoice['User']['last_name'];

$tcpdf->Cell(190, 15, "", 0, 1, 'L', false);

$tcpdf->Cell(100, 0, '.........................', 0, 0, 'C', false);
$tcpdf->Cell(90, 0, '.........................', 0, 1, 'C', false);
$tcpdf->Cell(100, 0, 'Vystavil: ' . $user_info, 0, 0, 'C', false);
$tcpdf->Cell(90, 0, 'Přijal', 0, 1, 'C', false);
 
echo $tcpdf->Output('invoice_' . $invoice['CSInvoice']['code'] . '.pdf', 'D');

?>