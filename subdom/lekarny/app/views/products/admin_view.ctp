<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Editace</a></li>
		<li><a href="#tabs-2">Atributy</a></li>
		<li><a href="#tabs-3">Obrázky</a></li>
		<li><a href="#tabs-4">Dokumenty</a>
		<li><a href="#tabs-5">Další operace</a>
	</ul>
	
	<!-- EDITACE PRODUKTU -->
	<div id="tabs-1">
		<h1>Editace produktu</h1>
		<?=$form->create('Product', array('url' => array('controller' => 'products', 'action' => 'view', $product['Product']['id'])))?>
		<table class="left_headed" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Název produktu<sup>*</sup>
				</th>
				<td>
					<?=$form->input('name', array('label' => false, 'size' => 60))?>
				</td>
			</tr>
			<tr>
				<th>
					Kód VZP<sup>*</sup>
				</th>
				<td>
					<?=$form->input('vzp_code', array('label' => false))?>
				</td>
			</tr>
			<tr>
				<th>
					Výrobce
				</th>
				<td>
					<?=$form->input('manufacturer_id', array('label' => false))?>
				</td>
			</tr>
			<tr>
				<th>
					Dostupnost
				</th>
				<td>
					<?=$form->input('availability_id', array('label' => false))?>
				</td>
			</tr>
			<tr>
				<th valign="top">
					Popis produktu
				</th>
				<td>
					<?=$form->input('description', array('label' => false, 'type' => 'textarea', 'cols' => 60, 'rows' => 12))?>
				</td>
			</tr>
			<tr>
				<th valign="top">
					Cena bez DPH<sup>*</sup>
				</th>
				<td>
					<?=$form->input('price', array('label' => false))?>
				</td>
			</tr>
			<tr>
				<th valign="top">
					Hladina DPH
				</th>
				<td>
					<?=$form->input('tax_class_id', array('label' => false))?>
				</td>
			</tr>
			<tr>
				<th valign="top">
					Cena s DPH
				</th>
				<td>
					<?=$form->input('price_tax', array('label' => false)) ?>
				</td>
			</tr>
			<tr>
				<th valign="top">
					Nákupní cena
				</th>
				<td>
					<?=$form->input('price_buy', array('label' => false)) ?>
				</td>
			</tr>
		</table>
		<?php echo $form->hidden('Product.action', array('value' => 'edit'))?>
		<?=$form->submit('uložit') ?>
		<?=$form->end();?>
	</div>
	
	<!-- ATRIBUTY PRODUKTU -->
	<div id="tabs-2">
		<h2>Atributy produktu <?=$product['Product']['name']?> </h2>
		<?
		echo $form->create('Product', array('url' => '/admin/products/view/' . $product['Product']['id']));
		foreach ($options as $option) {
		?>
		<h3><?=$option['Option']['name'] ?></h3>
		<?
			echo $form->textarea('Attributes.' . $option['Option']['id'], array('cols' => 50, 'rows' => 5));
		}
		echo $form->hidden('Product.id', array('value' => $product['Product']['id']));
		echo $form->hidden('Product.action', array('value' => 'attributes'));
		echo $form->submit('Odeslat');
		echo $form->end();
		
		echo $this->element('admin_subproducts_control', $this->requestAction('admin/subproducts/control/' . $product['Product']['id']));
		?>
	</div>
	
	<!-- OBRAZKY PRODUKTU -->
	<div id="tabs-3">
		<h2>Obrázky k produktu <?=$product['Product']['name']?></h2>
		<a href="#imagesform">vložit nový obrázek</a>
		<?
			if ( count($images) > 0 ){
		?>
			<table class="top_headed" cellpadding="5" cellspacing="3">
				<tr>
					<th>ID</th>
					<th>Náhled</th>
					<th>Název</th>
					<th>&nbsp;</th>
				</tr>
		<?
			foreach( $images as $image ){
				$imageSize = @getimagesize('product-images/small/' . $image['Image']['name']);
		?>
				<tr>
					<td><?=$image['Image']['id']?></td>
					<td valign="middle" style="height:100px;width:100px;"><img style="border:1px solid black;" src="/product-images/small/<?=$image['Image']['name']?>" width="<?=$imageSize[0]?>px" height="<?=$imageSize[1]?>" alt="" /></td>
					<td><?=$image['Image']['name']?></td>
					<td>
						<?php
							echo $html->link('odstranit', array('controller' => 'images', 'action' => 'delete', $image['Image']['id']), null, 'Opravdu chcete obrázek odstranit?') . ' | ';
							if ( $image['Image']['is_main'] ){
								echo '<strong>hlavní obrázek</strong>';
							} else {
								echo '<a href="/admin/images/set_as_main/' . $image['Image']['id'] . '">nastavit jako hlavní obrázek</a>';
							}
							echo ' | ';
							echo $html->link('editovat', array('controller' => 'images', 'action' => 'edit', 'id' => $image['Image']['id']))
						?>
					</td>
				</tr>
		<?
			}
		?>
			</table>
		<?
			} else {
				echo '<p>Produkt zatím nemá žádné obrázky</p>';
			}
		?>
			<a name="imagesform"></a>
			<h3>Vložit nový obrázek</h3>
		<?
			echo $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'view', $product['Product']['id'])));
			echo $form->submit('Zobrazit', array('div' => false));
			echo $form->text('Product.image_fields', array('size' => '1')) . ' polí';
			echo $form->hidden('Product.action', array('value' => 'image_fields'));
			echo $form->end();
		
			echo $form->Create('Image', array('url' => '/admin/images/add', 'type' => 'file')); ?>
			<fieldset>
				<legend>Nový obrázek</legend>
				<table class="leftHeading" cellpadding="5" cellspacing="3">
					<tr>
						<th></th>
						<td>
							<?
								if ( !isset($this->data['Product']['image_fields']) OR $this->data['Product']['image_fields'] > 10 OR $this->data['Product']['image_fields'] < 1 ) {
									$this->data['Product']['image_fields'] = 1;
								}
								for ( $i = 0; $i < $this->data['Product']['image_fields']; $i++ ){
									echo '<input type="file" name="data[Image][image' . $i . ']" /><br />';
								}
							?>
						</td>
					</tr>
				</table>
		<?
			echo $form->hidden('Image.image_fields', array('value' => $this->data['Product']['image_fields']));
			echo $form->hidden('Image.product_id', array('value' => $product['Product']['id']));
		?>
			</fieldset>
		<?
			echo $form->submit('Vložit obrázek');
			echo $form->end();
			
			echo '<br />';
			
			echo $form->Create('Image', array('url' => '/admin/images/add_url')); ?>
			<fieldset>
				<legend>Obrázek z http</legend>
				<table class="leftHeading" cellpadding="5" cellspacing="3">
					<tr>
						<th>url</th>
						<td>
							<?=$form->text('Image.url', array('size' => 90)) ?>
						</td>
					</tr>
					<tr>
						<th>název pro obrázek</th>
						<td>
							<?=$form->text('Image.new_name') ?>
						</td>
					</tr>
				</table>
		<?
			echo $form->hidden('Image.product_id', array('value' => $product['Product']['id']));
		?>
			</fieldset>
		<?
			echo $form->submit('Vložit obrázek');
			echo $form->end();
			
			
		?>
	</div>
	<!--  DOKUMENTY -->
	<div id="tabs-4">
		<h2><?=$product['Product']['name'] ?> - přiložené dokumenty</h2>
		<?
			if ( empty($product['ProductDocument']) ){
				echo '<p>K produktu není zatím přiložen žádný dokument.</p>';
			} else {
		?>
				<table class="top_headed" cellpadding="5" cellspacing="3">
					<tr>
						<th>název</th>
						<th>typ</th>
						<th>&nbsp;</th>
					</tr>
		<?			foreach ( $product['ProductDocument'] as $document ){ ?>
					<tr>
						<td><?php echo $document['name'] ?></td>
						<td><?php echo $document['type'] ?></td>
						<td>
							<?php echo $html->link('vymazat', array('controller' => 'products', 'action' => 'documents_delete', $document['id']), null, 'Opravdu chcete dokument odstranit?')?> |
							<?php echo $html->link('upravit', array('controller' => 'products', 'action' => 'documents_edit', $document['id']))?>
						</td>
					</tr>
		<?php 		} ?>
				</table>
		<? } ?>
		<h3>Přiložit dokument</h3>
		<? echo $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'documents_add', 'id' => $product['Product']['id']), 'type' => 'file')); ?>
		<table class="left_headed">
			<tr>
				<th>Typ</th>
				<td><?php echo $form->select('Document.type', array('doc' => 'Dokument Word', 'pdf' => 'Dokument PDF', 'xls' => 'Dokument Excel'), array(), array(), false); ?></td>
			</tr>
			<tr>
				<th>Přikládaný soubor</th>
				<td><?php echo $form->file('Document.name')?></td>
			</tr>
		</table>
		<?php 
			echo $form->submit('vložit');
			echo $form->end();
		?>
	</div>
	
	<!-- DALSI OPERACE - presun a vymazani produktu -->
	<div id="tabs-5">
		<ul>
			<li><?php echo $html->link('Přesunout produkt', array('controller' => 'products', 'action' => 'move', $product['Product']['id'], 'category_id' => $product['Category'][0]['id']))?></li>
			<li><?php echo $html->link('Smazat produkt', array('controller' => 'products', 'action' => 'delete', $product['Product']['id']), null, 'Opravdu chcete produkt odstranit?')?></li>
		</ul>
	</div>
</div>