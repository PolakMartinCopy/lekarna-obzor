<?
	if ( !empty($product) ){

		if ( isset($product['Image'][0]) ){
?>
			<div class="images_holder">
				<a href="/product-images/<?php echo $product['Image'][0]['name']?>"><img src="/product-images/medium/<?php echo $product['Image'][0]['name']?>" /></a>
<?php
				if ( count($product['Image']) > 1 ){
?>
					<div class="thumbs_holder">
<?php
					for ( $i = 1; $i < count($product['Image']); $i++ ){
?>
						<a href="/product-images/<?php echo $product['Image'][$i]['name']?>"><img src="/product-images/small/<?php echo $product['Image'][$i]['name']?>" /></a>
<?php
					}
?>
					</div>
<?php
				}
?>
			</div>
<?php
		}
?>

		<h1><?=$product['Product']['name'] ?></h1>
		<table class="left_headed">
			<tr>
				<th valign="top">
					popis produktu
				</th>
				<td>
					<?=$product['Product']['description'] ?>
				</td>
			</tr>
			<tr>
				<th valign="top">
					kód VZP
				</th>
				<td>
					<?=$product['Product']['vzp_code'] ?>
				</td>
			</tr>
			<tr>
				<th valign="top">
					výrobce
				</th>
				<td>
					<?=$product['Manufacturer']['name'] ?>
				</td>
			</tr>
			<tr>
				<th valign="top">
					dostupnost
				</th>
				<td>
					<?=$product['Availability']['name'] ?>
				</td>
			</tr>
			<tr>
				<th valign="top" nowrap="nowrap">
					cena ks bez DPH
				</th>
				<td>
					<?=$product['Product']['price'] ?>&nbsp;Kč
				</td>
			</tr>
			<tr>
				<th valign="top">
					DPH
				</th>
				<td>
					<?=$product['TaxClass']['name'] ?>
				</td>
			</tr>
			<tr>
				<th valign="top" nowrap="nowrap">
					cena ks s DPH
				</th>
				<td>
					<?
						$tax_value = 1 + $product['TaxClass']['value'] / 100;
						$tax_price = round($product['Product']['price'] * $tax_value, 2);
						echo $tax_price . '&nbsp;Kč';
					?>
				</td>
			</tr>
<?php
			if ( !empty($product['ProductDocument']) ){
				$docs = array();
				foreach ( $product['ProductDocument'] as $d ){
					$docs[] = '<a href="/files/documents/' . $d['name'] . '">' . $d['name'] . '</a>';
				}
?>
			<tr>
				<th valign="top">
					Dokumenty
				</th>
				<td>
					<?php echo implode(', ', $docs);?>
				</td>
			</tr>
<?php
			}
?>
		</table>

			<h2>Vložit do objednávky</h2>
			<?=$form->create('Product', array('url' => array('users' => 'true', 'action' => 'view', $product['Product']['id'])));?>
			<table id="product_order_variants">
				<tr>
					<th>
						<?=( !empty($product['Subproduct']) ? 'Varianta' : '&nbsp;' ) ?>
					</th>
					<th>
						počet ks
					</th>
				</tr>
<?
				// inicializuju pocitadlo jednotlivych variant, abych je mohl od sebe odlisit
				$i = 0;
				foreach ($product['Subproduct'] as $subproduct) {
					// stridani barvy pozadi jednotlivych variant, pro prehlednost ve vypisu
					$bg_color = empty($bg_color) ? ' style="background-color:#E5F2DE"' : '';

					// inicializace
					$output_variants = array();
					$form_variants = array();
					
					// kosik se uklada v databazi, do kosiku si pridam vzdy vyjmenovane atributy produktu
					foreach ( $subproduct['AttributesSubproduct'] as $attributes_subproduct ){
						// ukladam si varianty pro vypis na obrazovku
						$output_variants[] = $attributes_subproduct['Attribute']['Option']['name'] . ": " . $attributes_subproduct['Attribute']['value'];
						
						// a ukladam si varianty pro ulozeni do kosiku
						$form_variants[] = array(
							'Option' => array(
								'id' => $attributes_subproduct['Attribute']['Option']['id'],
								'name' => $attributes_subproduct['Attribute']['Option']['name']
							),
							'Value' => array(
								'name' => $attributes_subproduct['Attribute']['value']
							) 
						);
					}
					
?>
						<tr<?=$bg_color?>>
							<td>
								<?=implode(', ', $output_variants); ?>
							</td>
							<td>
								<?=$form->input('CartsProduct.' . $i . '.quantity', array('label' => false, 'size' => '2')); ?>
								<?php echo $form->hidden('CartsProduct.' . $i . '.subproduct_id', array('value' => $subproduct['id']))?>
								<?=$form->hidden('CartsProduct.' . $i . '.product_attributes', array('value' => serialize($form_variants))) ?>
								<?=$form->hidden('CartsProduct.' . $i . '.product_id', array('value' => $product['Product']['id']));?>
							</td>
						</tr>
<?
					$i++;
				}

				// zmena barvy pozadi pro prehlednost
				$bg_color = empty($bg_color) ? ' style="background-color:#E5F2DE"' : '';

				// pokud dany produkt nema zadne varianty, bude vypis
				// objednavaciho formu vypadat trosku jinak
				if ( empty($product['Subproduct']) ){
?>
					<tr>
						<td>
							&nbsp;
						</td>
						<td>
								<?=$form->input('CartsProduct.0.quantity', array('label' => false, 'size' => '2')); ?>
								<?=$form->hidden('CartsProduct.0.product_attributes', array('value' => serialize(array()))) ?>
								<?=$form->hidden('CartsProduct.0.product_id', array('value' => $product['Product']['id']));?>
						</td>
					</tr>
<?
				}
?>
				<tr<?=$bg_color?>>
					<td>
						&nbsp;
					</td>
					<td>
						<?=$form->submit('přidat do objednávky'); ?>
					</td>
				</tr>
			</table>
		<?=$form->end(); ?>
<?
	} else {
?>
		<h1>Detail produktu</h1>
		<p>Nebyl nalezen žádný produkt.</p>
<?
	}
?>