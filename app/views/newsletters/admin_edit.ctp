<div>
	<h2>Editace newsletteru - "<?=$this->data['Newsletter']['name'] ?>"</h2>
	<p><?=$html->link('preview', array('controller' => 'newsletters', 'action' => 'view', 'admin' => false, $this->data['Newsletter']['id']), array('target' => '_blank')) ?></p>
	<h3>Podrobnosti newsletteru</h3>
	<?=$form->Create('Newsletter') ?>
	<table class="leftHeading">
		<tr>
			<th>předmět</th>
			<td><?=$form->input('Newsletter.subject', array('label' => false, 'size' => 50)) ?></td>
		</tr>
		<tr>
			<th>obsah mailu</th>
			<td><?=$form->input('Newsletter.body', array('label' => false, 'rows' => 10, 'cols' => 60)) ?></td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<td>
				<?=$form->hidden('Newsletter.save_data', array('value' => '1')) ?>
				<?=$form->submit('Upravit') ?>
			</td>
		</tr>
	</table>
	<?=$form->end() ?>
	<h3>Seznam produktů zařazených do newsletteru:</h3>
	<table class="topHeading">
		<tr>
			<th>ID</th>
			<th>Název produktu</th>
			<th>Původní cena</th>
			<th>Akční cena</th>
			<th>Ušetří</th>
			<th>&nbsp;</th>
		</tr>
	<?
		foreach ( $this->data['Product'] as $product ){
	?>
		<tr>
			<td><?=$product['id'] ?></td>
			<td><?=$product['name'] ?></td>
			<td><?=$product['price'] ?>&nbsp;Kč</td>
			<td>
				<?
					$dp = $product['price']; // discountprice
					foreach ( $product['DiscountModelsProduct'] as $dm ){
						if ( $dm['discount_model_id'] == 2 ){
							$dp = $dm['price'];
						}
					}
					echo $dp . '&nbsp;Kč'
				?>
			</td>
			<td><?=( $product['price'] - $dp ) ?>&nbsp;Kč</td>
			<td>
				<?=$html->link('smazat', array('controller' => 'newsletters', 'action' => 'product_delete', $this->data['Newsletter']['id'], 'product_id' => $product['id'])) ?>
			</td>
		</tr>
	<?
		}
	?>
		<tr>
			<td colspan="6">
				<?=$form->Create('Newsletter') ?>
				<?=$form->text('Newsletter.product_query') ?>
				<?=$form->end('hledat produkt') ?>
			
				<?
					if ( isset($found_products) && !empty($found_products) ){
				?>
						<table class="topHeading" cellpadding="3">
							<tr>
								<td colspan="3">
									<h4>Nalezeno:</h4>
								</td>
							</tr>
							<tr>
								<th>ID</th>
								<th>název produktu</th>
								<th>&nbsp;</th>
							</tr>
							<?
								foreach ( $found_products as $fp ){
							?>
									<tr>
										<td>
											<?=$html->link($fp['Product']['id'], '/' . $fp['Product']['url']) ?>
										</td>
										<td>
											<?=$fp['Product']['name'] ?><br />
											<p style="font-size:9px;"><?=$fp['Product']['short_description'] ?></p>
										</td>
										<td>
											<?=$html->link('Vložit do newsletteru', array('controller' => 'newsletters', 'action' => 'product_add', $this->data['Newsletter']['id'], 'product_id' =>  $fp['Product']['id'])) ?>
										</td>
									</tr>
							<?
							
								}
							?>
						</table>
				<?
					}
				?>
			</td>
		</tr>
	</table>
</div>