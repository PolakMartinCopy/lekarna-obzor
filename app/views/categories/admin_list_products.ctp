<h2>Seznam produktů v kategorii</h2>
<table class="topHeading" cellpadding="5" cellspacing="3">
<tr><? $paginator->options(array('url' => $this->params['pass'][0])) ?>
	<th><?=$paginator->sort('Id', 'Product.id' );?></th>
	<th><?=$paginator->sort('Název', 'Product.name' );?></th>
	<th><?=$paginator->sort('MO cena s DPH', 'Product.retail_price_with_dph' );?></th>
	<th>Smazáno</th>
	<th>&nbsp;</th>
</tr>
<?
	$admin_id = $session->read('Administrator.id');
	$superadmin = false;
	if (in_array($admin_id, array(1))) {
		$superadmin = true;
	}

	foreach ( $products as $product ){
		$style = '';
		if (!$product['Product']['active']) {
			$style = ' style="color:grey"';
		}
?>
	<tr <?php echo  $style?>>
		<td><?=$html->link($product['Product']['id'], '/' . $product['Product']['url']);?></td>
		<td><?=$product['Product']['name']?></td>
		<td><?=$product['Product']['retail_price_with_dph']?></td>
		<td><?php echo ($product['Product']['active'] == 1) ? 'Ne' : 'Ano'?></td>
		<td style="font-size:12px;">
			<a href="/admin/products/edit/<?=$product['Product']['id']?>/<?=$product['CategoriesProduct']['category_id']?>">Editovat</a> |
			<a href="/admin/products/attributes_list/<?=$product['Product']['id']?>">Varianty</a> |
			<a href="/admin/products/related/<?=$product['Product']['id']?>">Související</a> |
			<a href="/admin/products/images_list/<?=$product['Product']['id']?>">Obrázky</a> |
			<a href="/admin/dirimages/list/<?=$product['Product']['id']?>">FTP</a> |
			<?php echo $html->link('Kategorie', array('controller' => 'products', 'action' => 'category_actions', $product['Product']['id'], $opened_category_id))?> |
			<?php
			if ($product['Product']['active']) { 
				echo $html->link('Smazat', array('controller' => 'products', 'action' => 'delete', $product['Product']['id']), array(), 'Opravdu chcete tento produkt smazat?');
			} else {
				echo $html->link('Obnovit', array('controller' => 'products', 'action' => 'activate', $product['Product']['id']));
			}
			if ($superadmin) {
				echo ' | ' . $html->link('Smazat z DB', array('controller' => 'products', 'action' => 'delete_from_db', $product['Product']['id']), array(), 'Opravdu chcete tento produkt a vše, co k němu náleží, zcela odstranit z databáze?');
			}
			?>
		</td>
	</tr>
<?
	}
?>
</table>
<?
	//debug($this->passedArgs);
	$paginator->options(array('url' => $this->passedArgs));
	echo $paginator->prev('<< '.__('předchozí ', true), array(), null, array('class'=>'disabled'));
	echo $paginator->numbers();
	echo $paginator->next(__(' další', true).' >>', array(), null, array('class'=>'disabled'));
?>
<div class="actions">
	<ul>
		<li><?php echo $html->link('Vložit nový produkt', array('controller' => 'products', 'action' => 'add',$opened_category_id)); ?> </li>
	</ul>
</div>


<? if (1==2) { ?>
<h2>Seznam produktů v kategorii</h2>
<table class="topHeading" cellpadding="5" cellspacing="3">
<tr>
	<th><?php echo $paginator->sort('Id', 'Product.id', array('url' => array('id' => $this->params['pass'][0])) );?></th>
	<th><?php echo $paginator->sort('Název', 'Product.name', array('url' => array('id' => $this->params['pass'][0])) );?></th>
	<th><?php echo $paginator->sort('Cena', 'Product.price', array('url' => array('id' => $this->params['pass'][0])) );?></th>
	<th>&nbsp;</th>
</tr>
<?
	foreach ( $products as $product ){
?>
	<tr>
		<td><?=$product['Product']['id']?></td>
		<td><?=$product['Product']['name']?></td>
		<td><?=$product['Product']['price']?></td>
		<td style="font-size:12px;">
			<a href="/admin/products/edit/<?=$product['Product']['id']?>/<?=$product['ProductsCategory']['categories_id']?>">Editovat</a> |
			<a href="/admin/products/attributes_list/<?=$product['Product']['id']?>">Varianty</a> |
			<a href="/admin/products/images_list/<?=$product['Product']['id']?>">Obrázky</a> |
			<a href="/admin/dirimages/list/<?=$product['Product']['id']?>">FTP</a> |
			<a href="/admin/products/copy/<?=$product['Product']['id']?>">Duplikovat</a> |
			<a href="/admin/products_categories/edit/<?=$product['ProductsCategory']['id']?>">Přesunout</a> |
			<?=$html->link('Kopírovat', array('controller' => 'products_categories', 'action' => 'add',$product['Product']['id'])) ?> |
			<?=$html->link('Smazat', array('controller' => 'products', 'action' => 'delete',$product['Product']['id']), array(), 'Opravdu chcete tento produkt smazat?')?> |
			<?=$html->link('Kategorie', array('controller' => 'products_categories', 'action' => 'product',$product['Product']['id']))?>
			<br /><br />
			<?=$html->link('opravit url', array('controller' => 'products', 'action' => 'make_new_url',$product['Product']['id'], 'category_id' => $this->params['pass'][0])) ?>
			<?=$html->link('novy shop', '/' . $product['Product']['url']) ?> |
			<?=$html->link('stary shop', 'http://old.e-lyze.cz/' . $product['Product']['url']) ?>
		</td>
	</tr>
<?
	}
?>
</table>
<?
	echo $paginator->prev('<< '.__('předchozí ', true), array('url' => array('id' => $this->params['pass'][0])), null, array('class'=>'disabled'));
	echo $paginator->numbers(array('url' => array('id' => $this->params['pass'][0])));
	echo $paginator->next(__(' další', true).' >>', array('url' => array('id' => $this->params['pass'][0])), null, array('class'=>'disabled'));
?>
<div class="actions">
	<ul>
		<li><?php echo $html->link('Vložit nový produkt', array('controller' => 'products', 'action' => 'add',$opened_category_id)); ?> </li>
	</ul>
</div>

<? } ?>