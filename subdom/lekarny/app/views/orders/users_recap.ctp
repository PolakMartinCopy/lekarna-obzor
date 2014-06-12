<h1>Rekapitulace objednávky</h1>
<h2>Objednané zboží</h2>

	<table class="top_headed" cellpadding="5" cellspacing="3">
		<tr>
			<th>produkt</th>
			<th>jedn. cena bez DPH</br>( s DPH )</th>
			<th>DPH</th>
			<th>množství</th>
			<th>celkem bez DPH</br>( s DPH )</th>
		</tr>
<?
		$total = 0;
		$total_wdph = 0;
		foreach ( $cart['CartsProduct'] as $cp ){
?>
		<tr>
			<td>
				<?
					echo $cp['Product']['name'];
					$subproduct_price = 0;
					$subproduct_price_tax = 0;
					$atts = unserialize($cp['product_attributes']);
					if ( !empty($atts) ){
						$atts_output = array();
						foreach ( $atts as $att ){
							$atts_output[] = '- ' . $att['Option']['name'] . ': ' . $att['Value']['name'];
						}
						$subproduct = $this->requestAction('/subproducts/get_subproduct/' . $cp['Product']['id'] . '/' . base64_encode(serialize($atts)));
						if (!empty($subproduct)) {
							$subproduct_price = $subproduct['Subproduct']['price'];
						}
						// vypocitam si prirustkovou cenu s DPH, musim brat miru DPH podle prirazene hodnoty TaxClass-value
						$subproduct_price_tax = $subproduct_price + ($subproduct_price *  $cp['Product']['TaxClass']['value']) / 100;
				?>
					<div class="attributes_list">
						<?=implode('<br />', $atts_output);?>
					</div>
				<?
					}
				?>
			</td>
			<td>
				<?=$cp['Product']['price'] + $subproduct_price ?> Kč</br>(<?=$cp['Product']['price_tax'] + $subproduct_price_tax ?> Kč)
			</td>
			<td>
				<?=$cp['Product']['TaxClass']['value'] ?>%
			</td>
			<td>
				<?=$cp['quantity']?>
			</td>
			<td>
				<?=$cp['quantity'] * ($cp['Product']['price'] + $subproduct_price) ?> Kč</br>(<?=$cp['quantity'] * ($cp['Product']['price_tax'] + $subproduct_price_tax) ?> Kč)
			</td>
		</tr>
<?
			$total = $total + $cp['quantity'] * ($cp['Product']['price'] + $subproduct_price);
			$total_wdph = $total_wdph + $cp['quantity'] * ($cp['Product']['price_tax'] + $subproduct_price_tax);
		}
?>
		<tr>
			<td colspan="4">
				&nbsp;
			</td>
			<th>
				celkem bez DPH</br>
				( s DPH )
			</th>
		</tr>
		<tr>
			<td colspan="4">
				&nbsp;
			</td>
			<td>
				<?=$total ?> Kč
				(<?=$total_wdph ?> Kč)
			</td>
		</tr>
<?php
	if ( $total < 2727.28 ){
		$shippings[1] = $shippings[1] . ' (77.00,- Kč)';
		$shippings[2] = $shippings[2] . ' (0.00,- Kč)';
		$shippings[4] = $shippings[4] . ' (88.00,- Kč)';
?>
		<tr>
			<td colspan="5">
				<div id="flashMessage" class="message">Při nákupu nad 3000 Kč vč. DPH je poštovné zdarma.</div>
			</td>
		</tr>
<?php
	} else {
		// vsechna doprava je zadarmo
		$shippings[1] = $shippings[1] . ' (0.00,- Kč)';
		$shippings[2] = $shippings[2] . ' (0.00,- Kč)';
		$shippings[4] = $shippings[4] . ' (0.00,- Kč)';
	}
?>
	</table>
<?=$html->link('upravit objednané zboží', array('users' => true, 'controller' => 'carts', 'action' => 'view')) ?>

<table class="left_headed">
	<tr>
		<td valign="top">
			<h2>Fakturační adresa</h2>
			<?=$company['Company']['payment_name'] ?><br />
			<?=$company['Company']['payment_street'] . ' ' . $company['Company']['payment_street_number'] ?><br />
			<?=$company['Company']['payment_postal_code'] . ' ' . $company['Company']['payment_city'] ?>
			<br />
			IČO:<?=$company['Company']['ico'] ?><br />
			DIČ:<?=$company['Company']['dic'] ?>
		</td>
		<td valign="top">
			<h2>Doručovací adresa</h2>
			<?=$company['Company']['delivery_name'] ?><br />
			<?=$company['Company']['delivery_street'] . ' ' . $company['Company']['delivery_street_number'] ?><br />
			<?=$company['Company']['delivery_postal_code'] . ' ' . $company['Company']['delivery_city'] ?>
		</td>
	</tr>
</table>

<?=$form->Create('Order', array('url' => array('users' => true, 'controller' => 'orders', 'action' => 'finalize'))) ?>
<h2>Platba a dodání</h2>
<p style="font-size:11px; font-weight:bold;">S účinností od 15.8.2011 došlo ke změně sazeb dopravného a limitů pro dopravu zdarma! Nárok na dopravu zdarma vzniká při objednávce přesahující 3000,- Kč včetně DPH.</p>
<table class="left_headed">
	<tr>
		<th>
			způsob platby
		</th>
		<td>
			<?=$form->input('Order.payment_id', array('label' => false)) ?>
		</td>
	</tr>
	<tr>
		<th>
			způsob dodání
		</th>
		<td>
			<?
				// podle total ceny si jeste pridam do zavorky cenu za dopravu
				echo $form->input('Order.shipping_id', array('label' => false, 'type' => 'select', 'options' => $shippings))
			?>
		</td>
	</tr>
</table>

<h2>Poznámka k objednávce</h2>
<?=$form->input('Order.comments', array('label' => false, 'cols' => 80)) ?>
<?=$form->submit('Dokončit objednávku') ?>
<?=$form->end() ?>