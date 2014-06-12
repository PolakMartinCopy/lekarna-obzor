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
$tcpdf->Line(10, 95, 200, 95, $linestyle);

$tcpdf->SetFont($textfont, 'B', 14);
$tcpdf->Cell(190, 0, 'Příjmový doklad', 0, 1, 'R', false);

$tcpdf->Cell(190, 10, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont, '', 11);

$street_info = $wallet_transaction['WalletTransaction']['rep_street'] . ' ' . $wallet_transaction['WalletTransaction']['rep_street_number'];
$city_info = $wallet_transaction['WalletTransaction']['rep_zip'] . ' ' . $wallet_transaction['WalletTransaction']['rep_city'];
$ico_info = (!empty($wallet_transaction['WalletTransaction']['rep_ico']) ? 'IČ: ' . $wallet_transaction['WalletTransaction']['rep_ico'] : '');
$dic_info = (!empty($wallet_transaction['WalletTransaction']['rep_dic']) ? 'IČ: ' . $wallet_transaction['WalletTransaction']['rep_dic'] : '');

$tblx = '
<table cellspacing="0" cellpadding="1" border="0" style="text-align:center">
    <tr>
        <th><strong>Příjemce:</strong></th>
        <th><strong>Přijato od</strong></th>
    </tr>
 	<tr>
		<td>' . ($wallet_transaction['WalletTransaction']['amount'] > 0 ? $wallet_transaction['WalletTransaction']['rep_name'] : 'MeaVita s.r.o.') . '</td>
		<td>' . ($wallet_transaction['WalletTransaction']['amount'] > 0 ? 'MeaVita s.r.o.' : $wallet_transaction['WalletTransaction']['rep_name']) . '</td>
	</tr>
	<tr>
		<td>' . ($wallet_transaction['WalletTransaction']['amount'] > 0 ? $street_info : 'Cejl 37/62') . '</td>
		<td>' . ($wallet_transaction['WalletTransaction']['amount'] > 0 ? 'Cejl 37/62' : $street_info) . '</td>
	</tr>
	<tr>
		<td>' . ($wallet_transaction['WalletTransaction']['amount'] > 0 ? $city_info : '60200 Brno') . '</td>
		<td>' . ($wallet_transaction['WalletTransaction']['amount'] > 0 ? '60200 Brno' : $city_info) . '</td>
	</tr>
	<tr>
		<td>' . ($wallet_transaction['WalletTransaction']['amount'] > 0 ? $ico_info : 'IČ: 29248400') . '</td>
		<td>' . ($wallet_transaction['WalletTransaction']['amount'] > 0 ? 'IČ: 29248400' : $ico_info) . '</td>
	</tr>
	<tr>
		<td>' . ($wallet_transaction['WalletTransaction']['amount'] > 0 ? $dic_info : 'DIČ: CZ29248400') . '</td>
		<td>' . ($wallet_transaction['WalletTransaction']['amount'] > 0 ? 'DIČ: CZ29248400' : $dic_info) . '</td>
	</tr>
</table>
';
$tcpdf->writeHTML($tblx, true, false, false, false, '');

$tcpdf->Cell(190, 10, "", 0, 1, 'L', false);

$tbl = '
<table cellspacing="0" cellpadding="1" border="0" style="text-align:center">
    <tr>
        <th><strong>Datum úhrady:</strong></th>
        <td>' . czech_date($wallet_transaction['WalletTransaction']['created']) . '</td>
    </tr>
	<tr>
		<th><strong>Přijatá částka:</strong></th>
		<td>' . abs($wallet_transaction['WalletTransaction']['amount']) . ' Kč</td>
	</tr>
</table>
';
$tcpdf->writeHTML($tbl, true, false, false, false, '');

$tcpdf->Cell(190, 10, "", 0, 1, 'L', false);

$tcpdf->Cell(190, 0, 'Přijal:', 0, 1, 'L', false);

echo $tcpdf->Output('nabiti-penezenky-' . $wallet_transaction['WalletTransaction']['id'] . '.pdf', 'D');

?>