<?php
App::import('Vendor','xtcpdf');

function ic($string){
//	return iconv('windows-1250', 'utf-8', $string);
	return $string;
}

// predpripravim si data
$payment_name = $order['Order']['customer_name'];
if ( !empty($order['Order']['customer_ico']) ){
	// zakaznik nakupuje na ICO, v company_name muze byt nazev spolecnosti
	if ( !empty($order['Order']['company_name']) ){
		$payment_name = $order['Order']['company_name'];
	}
}



$tcpdf = new XTCPDF();
$textfont = 'dejavusans'; // looks better, finer, and more condensed than 'dejavusans'

$tcpdf->SetAuthor("Léky a léčiva - Lékárna Obzor Brno");
$tcpdf->SetAutoPageBreak( false );
$tcpdf->setHeaderFont(array($textfont,'',40));
$tcpdf->xheadercolor = array(150,0,0);
$tcpdf->xheadertext = 'Lékárna Obzor Brno';
$tcpdf->xfootertext = 'Copyright © %d Lékárna Obzor Brno. All rights reserved.';

// add a page (required with recent versions of tcpdf)
$tcpdf->AddPage();

// Now you position and print your page content
// example:
$tcpdf->SetTextColor(0, 0, 0);
$tcpdf->SetFont($textfont,'B',14);
$tcpdf->SetFillColor(200,200,200);

// sirka bunky je celkem 190
// do pulky zarovnane doleva
$tcpdf->Cell(90, 1, "Zálohová faktura", 0, 0, 'L', true);
$tcpdf->Cell(100, 1, "Zálohová faktura číslo " . $order['Order']['id'], 0, 1, 'R', true);

// delici cary
$tcpdf->Line(10, 22, 200, 22);
$tcpdf->Line(105, 22, 105, 120);
$tcpdf->Line(10, 120, 200, 120);
$tcpdf->Line(10, 145, 200, 145);

$tcpdf->SetFillColor(255,255,255);
$tcpdf->Cell(90, 5, "", 0, 1, 'L', false);


$tcpdf->SetFont($textfont,'B',11);
$tcpdf->Cell(100, 0, "DODAVATEL", 0, 0, 'L', false);
$tcpdf->Cell(90, 0, "ODBĚRATEL", 0, 1, 'L', false);

// mezera
$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'',11);
$tcpdf->Cell(100, 10, "Pharmacorp CZ s.r.o.", 0, 0, 'L', false);
$tcpdf->Cell(90, 10, ic($payment_name), 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'',10);
$tcpdf->Cell(100, 10, "Fillova 260/1", 0, 0, 'L', false);
$tcpdf->Cell(90, 10, ic($order['Order']['customer_street']), 0, 1, 'L', false);

$tcpdf->Cell(100, 10, "638 00 Brno", 0, 0, 'L', false);
$tcpdf->Cell(90, 10, $order['Order']['customer_zip'] . " " . ic($order['Order']['customer_city']), 0, 1, 'L', false);

// mezera
$tcpdf->Cell(190, 10, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'B',10);
$tcpdf->Cell(8, 10, "IČ:", 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'',10);
$tcpdf->Cell(30, 10, "29372828", 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B',10);
$tcpdf->Cell(10, 10, "DIČ:", 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'',10);
$tcpdf->Cell(52, 10, "CZ29372828", 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B',10);
$tcpdf->Cell(8, 10, "IČ:", 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'',10);
$tcpdf->Cell(30, 10, $order['Order']['customer_ico'], 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B',10);
$tcpdf->Cell(10, 10, "DIČ:", 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'',10);
$tcpdf->Cell(42, 10, strtoupper($order['Order']['customer_dic']), 0, 1, 'L', false);


$tcpdf->SetFont($textfont,'B',10);
$tcpdf->Cell(8, 10, "tel.:", 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'',10);
$tcpdf->Cell(30, 10, "601 351 448", 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B',10);
$tcpdf->Cell(10, 10, "fax:", 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'',10);
$tcpdf->Cell(52, 10, "", 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B',10);
$tcpdf->Cell(8, 10, "tel.:", 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'',10);
$tcpdf->Cell(30, 10, $order['Order']['customer_phone'], 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B',10);
$tcpdf->Cell(10, 10, "fax:", 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'',10);
$tcpdf->Cell(42, 10, "", 0, 1, 'L', false);

$tcpdf->Cell(14, 10, "Účet: ", 0, 0, 'L', false);
$tcpdf->Cell(10, 10, "2400317559/2010", 0, 1, 'L', false);
$tcpdf->Cell(14, 10, "Banka: ", 0, 0, 'L', false);
$tcpdf->Cell(10, 10, "Fio banka", 0, 1, 'L', false);

// mezera
$tcpdf->Cell(190, 10, "", 0, 1, 'L', false);

// datumy
$vystaveni = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
$vystaveni = strftime("%d.%m.%Y", $vystaveni);
$splatnost = mktime(0, 0, 0, date("m")  , date("d") + 7, date("Y"));
$splatnost = strftime("%d.%m.%Y", $splatnost);

$tcpdf->SetFont($textfont,'',8);
$tcpdf->Cell(60, 10, "Datum vystavení:", 0, 0, 'L', false);
$tcpdf->Cell(25, 10, $vystaveni, 0, 0, 'L', false);
$tcpdf->Cell(35, 10, "Variabilní symbol:", 0, 0, 'L', false);
$tcpdf->Cell(15, 10, $order['Order']['id'], 0, 0, 'L', false);
$tcpdf->Cell(31, 10, "Datum splatnosti:", 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B',10);
$tcpdf->Cell(25, 10, $splatnost, 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'',8);
$tcpdf->Cell(60, 10, "Datum uskutečnění zdanitelného plnění:", 0, 0, 'L', false);
$tcpdf->Cell(25, 10, $vystaveni, 0, 0, 'L', false);
$tcpdf->Cell(35, 10, "", 0, 0, 'L', false);
$tcpdf->Cell(15, 10, "", 0, 0, 'L', false);
$tcpdf->Cell(31, 10, "Způsob platby:", 0, 0, 'L', false);
$tcpdf->Cell(25, 10, "převodem", 0, 1, 'L', false);

// mezera
$tcpdf->Cell(190, 10, "", 0, 1, 'L', false);

$html = '
<style>
td.spec{
	border-top:1px solid black;
}
td.cenacelkem{
	font-weight:bold;
	font-size:10;
}
</style>
<table>
	<tr>
		<th width="270" align="left">
			popis
		</th>
		<th width="70" align="right">
			ks
		</th>
		<th width="90" align="right">
			cena/ks
		</th>
		<th width="90" align="right">
			cena celkem
		</th>
	</tr>
	<tr>
		<td colspan="4" class="spec">&nbsp;</td>
	</tr>
';

foreach ( $order['OrderedProduct'] as $op ){
	$html .= '<tr>
		<td align="left">
			' . ic($op['Product']['name']) . '
		</td>
		<td align="right">
			' . ic($op['product_quantity']) . '
		</td>
		<td align="right">
			' . ic($op['product_price_with_dph']) . '&nbsp;Kč
		</td>
		<td align="right">
			' . $op['product_price_with_dph'] * $op['product_quantity'] . '&nbsp;Kč
		</td>
	</tr>';
}

if ( $order['Order']['shipping_cost'] != 0 ){
	$html .= '
	<tr>
		<td align="left">
			doprava: ' . ic($order['Shipping']['name']) . '
		</td>
		<td align="right">
			&nbsp;
		</td>
		<td align="right">
			&nbsp;
		</td>
		<td align="right">
			' . $order['Order']['shipping_cost'] . '&nbsp;Kč
		</td>
	</tr>
	';
}

$html .='
	<tr>
		<td>&nbsp;</td>
		<td colspan="3" class="spec">&nbsp;</td>
	</tr>
	<tr>
		<td align="left">
			&nbsp;
		</td>
		<td align="left" colspan="2">
			celkem k platbě:
		</td>
		<td width="90" align="right" class="cenacelkem">
			' . ( $order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost'] ) . '&nbsp;Kč
		</td>
	</tr>
';

$html .= '</table>';


$tcpdf->writeHTML($html, true, false, true, false, '');

for ( $i = 0; $i < 190; $i++ ){
	//$tcpdf->Cell(1, 1, "", 0, 0, 'L', true);
}
// ...
// etc.
// see the TCPDF examples

echo $tcpdf->Output('filename.pdf', 'D');

?>