<h2>Statistiky</h2>
<form action="/admin/statistics" method="post">
	od:&nbsp;<?=$form->dateTime('Statistic.from', 'DMY', 24)?><br />
	do:&nbsp;<?=$form->dateTime('Statistic.to', 'DMY', 24)?><br />
	<?=$form->submit('Změnit')?>
</form>
<h3>Prodané produkty v daném období</h3>
<?php if (empty($sold_products)) { ?>
<p><em>V zadaném období nebyly uskutečněny žádné objednávky.</em></p>
<?php } else {?>
<table class="topHeading">
	<tr>
		<th>Název produktu</th>
		<th>Prodané množství</th>
		<th>Cena (vč. DPH)</th>
	</tr>
<? foreach ( $sold_products as $sold_product ){ ?>
	<tr>
		<td><?php echo $this->Html->link($sold_product['Product']['name'], array('controller' => 'products', 'action' => 'view', $sold_product['Product']['id'], 'admin' => false), array('escape' => false))?></td>
		<td><?php echo $sold_product[0]['OrderedProduct__quantity'] ?></td>
		<td><?php echo $sold_product[0]['OrderedProduct__total_price']?></td>
	</tr>
<?php } ?>
</table>
<?php } ?>

<h3>Objednávky</h3>
<p>V daném období bylo uskutečněno <strong><?=$stats['Order']['count']?> objednávek</strong> v celkové hodnotě <strong><?=$stats['Order']['total']?> Kč</strong>.</p>

<h3>Rozpis prodejů</h3>
<?php if (empty($orders)) { ?>
<p><em>V zadaném obdobé nebyly uskutečněny žádné objednávky.</em></p>
<?php } else { ?>
<?=$form->create('Statistic', array('url' => array('action' => 'orders_download')))?>
<?=$form->hidden('Statistic.date_from', array('value' => $date_from))?>
<?=$form->hidden('Statistic.date_to', array('value' => $date_to))?>
<?=$form->end('stáhnout')?>
<table class="topHeading">
	<tr>
		<th>ID obj.</th>
		<th>Název produktu</th>
		<th>Datum prodeje</th>
		<th>Množství</th>
		<th>kusová cena<br />(bez DPH)</th>
		<th>kusová cena<br />(vč. DPH)</th>
		<th>celková cena<br />(vč. DPH)</th>
		<th>aktuální stav objednávky</th>
	</tr>
<?
	foreach ( $orders as $order ){
		foreach ( $order['OrderedProduct'] as $op ){
			$op['Product']['name'] = !empty($op['Product']['name']) ? $op['Product']['name'] : 'produkt vymazán z db';
?>
		<tr>
			<td><?=$op['order_id'] ?></td>
			<td><?=$op['Product']['name'] ?></td>
			<td><?=$op['created'] ?></td>
			<td><?=$op['product_quantity'] ?></td>
			<td>
				<?
					$op['Product']['TaxClass']['value'] = !empty($op['Product']['TaxClass']['value']) ? $op['Product']['TaxClass']['value'] : '0';
					$tax = 1 + intval($op['Product']['TaxClass']['value']) / 100;
					$wout_tax = round($op['product_price_with_dph'] / $tax, 2);
					echo $wout_tax;
				?>
			</td>
			<td><?=$op['product_price_with_dph'] ?></td>
			<td><?=$op['product_price_with_dph'] * $op['product_quantity'] ?></td>
			<td><?=$order['Status']['name'] ?></td>
		</tr>
<?
		}
	}
?>
</table>
<?php } ?>