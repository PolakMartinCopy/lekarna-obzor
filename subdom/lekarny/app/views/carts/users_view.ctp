<h1>Náhled objednávky</h1>

<?
	if ( !empty($cart['CartsProduct']) ){

	echo $form->create('Cart', array(
		'url' => array(
			'users' => true,
			'action' => 'edit',
			$cart['Cart']['id']
		)
	));
?>
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
				<? } ?>
			</td>
			<td>
				<?	// vysledna cena je cena produktu + prirustkova cena subproduktu
					echo $cp['Product']['price'] + $subproduct_price ?> Kč</br>(<?=$cp['Product']['price_tax'] + $subproduct_price_tax?> Kč)
			</td>
			<td>
				<?=$cp['Product']['TaxClass']['value'] ?>%
			</td>
			<td>
				<?
					echo $form->text('CartsProduct.' . $cp['id'] . '.quantity', array('value' => $cp['quantity'], 'size' => 2));
				?>
			</td>
			<td>
				 <?=$cp['quantity'] * ($cp['Product']['price'] + $subproduct_price)?> Kč</br>(<?=$cp['quantity'] * ($cp['Product']['price_tax'] + $subproduct_price_tax)?> Kč)
			</td>
		</tr>
<?
			$total = $total + $cp['quantity'] * ($cp['Product']['price'] + $subproduct_price);
			$total_wdph = $total_wdph + $cp['quantity'] * ($cp['Product']['price_tax'] + $subproduct_price_tax);
		}
?>
		<tr>
			<td colspan="3">
				&nbsp;
			</td>
			<td>
				<?=$form->submit('Změnit');?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
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
?>
		<tr>
			<td colspan="5">
				<div id="flashMessage" class="message">Při nákupu nad 3000 Kč vč. DPH je poštovné zdarma.</div>
			</td>
		</tr>
<?php
	}
?>
	</table>
<?
	echo $form->end();
?>
	
	<div><?=$html->link('zvolit způsob platby a dopravy', array('users' => true, 'controller' => 'orders', 'action' => 'recap')); ?></div>

<?
	} else {
?>
		<p>Objednávka je zatím prázdná, neobsahuje žádné produkty.</p>
<?
	}
?>