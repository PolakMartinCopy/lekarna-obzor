<? if (isset($company_name)) { ?>
<h1>Objednávky společnosti "<?=$company_name ?>"</h1>
<? } else { ?>
<h1>Seznam přijatých objednávek</h1>
<? } ?>

<? if ( isset($no_areas) ) { ?>
<p style="font-style:italic">Nemáte přiřazeny žádné oblasti, proto Vám nejsou přiděleny žádné objednávky.</p>
<? } else {
	echo $this->renderElement('rep_orders_filter' );
	
	$url_arr = array('rep' => true, 'controller' => 'orders', 'action' => 'index');
	foreach ($this->passedArgs as $arr_key => $arr_value) {
		$url_arr = array_merge(array($arr_key => $arr_value), $url_arr);
	}
	echo $form->create('Order', array('url' => $url_arr));
?>
<h2>Filtrovat objednávky dle období</h2>
<table class="left_headed">
	<tr>
		<th>Počáteční den</th>
		<td><?=$form->dateTime('Order.start_date', 'DMY', 'NONE', null, array('minYear' => 2009, 'maxYear' => date('Y'), 'monthNames' => false), true) ?></td>
	</tr>
	<tr>
		<th>Koncový den</th>
		<td><?=$form->dateTime('Order.end_date', 'DMY', 'NONE', null, array('minYear' => 2009, 'maxYear' => date('Y'), 'monthNames' => false), true) ?></td>
	</tr>
</table>
<?
	echo $form->submit('Filtrovat');
	echo $form->end();

	echo $paginator->counter(array(
			        'format' => '<p>Strana <strong>%page%</strong> z <strong>%pages%</strong> stran celkem,
					zobrazuji %current% objednávek z %count% objednávek celkem.</p>'
			));
?>


<div class="paging">
<?
	$paginator->options(array('url' => am(array('rep' => true), $this->passedArgs)));
	echo $paginator->prev('<< Předchozí', array(), '<< Předchozí', array('class' => 'disabled_link'));
	echo '&nbsp;&nbsp;' . $paginator->numbers(array('separator' => false)) . '&nbsp;&nbsp;';
	echo $paginator->next('Další >>', array(), 'Další >>', array('class' => 'disabled_link'));
?>
	<div style="clear:both"></div>
</div>

<table class="top_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>
			<?=$paginator->sort('ID', 'id')?>
		</th>
		<th>
			<?=$paginator->sort('datum vytvoření', 'created')?>
		</th>
		<th>
			<?=$paginator->sort('zákazník', 'customer_last_name')?>
		</th>
		<th>
			<?=$paginator->sort('cena', 'subtotal')?>
		</th>
		<th>
			<?=$paginator->sort('stav', 'status_id')?>
		</th>
		<th>
			&nbsp;
		</th>
	</tr>
<?
	$odd = ' class="odd"';
	foreach ( $orders as $order ){
?>
	<tr<?php echo $odd?>>
		<td>
			<?=$order['Order']['id']?>
		</td>
		<td>
			<?=$order['Order']['created']?>
		</td>
		<td>
			<?=$order['Order']['company_name']?>
		</td>
		<td>
			<?=$order['Order']['subtotal']?>
		</td>
		<td>
			<?
				$color = '';
				if ( !empty($order['Status']['color']) ){
					$color = ' style="color:#' . $order['Status']['color'] . '"';
				}
				echo '<span' . $color . '>' . $order['Status']['name'] . '</span>';
			?>
		</td>
		<td>
			<?=$html->link('zobrazit', '/rep/orders/view/' . $order['Order']['id'])?>
		</td>
	</tr>
<?	
		$odd = $odd == ' class="odd"' ? '' : ' class="odd"';
	}
?>
</table>


<div class="paging">
<?
			$paginator->options(array('url' => am(array('rep' => true), $this->passedArgs)));
			echo $paginator->prev('<< Předchozí', array(), '<< Předchozí', array('class' => 'disabled_link'));
			echo '&nbsp;&nbsp;' . $paginator->numbers(array('separator' => false)) . '&nbsp;&nbsp;';
			echo $paginator->next('Další >>', array(), 'Další >>', array('class' => 'disabled_link'));
?>
	<div style="clear:both"></div>
</div>
<? } ?>