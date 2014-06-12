<h1>Seznam přijatých objednávek</h1>
<?
	echo $this->renderElement('admin_orders_filter' );
?>

<?echo $paginator->counter(array(
			        'format' => '<p>Strana <strong>%page%</strong> z <strong>%pages%</strong> stran celkem,
					zobrazuji %current% objednávek z %count% objednávek celkem.</p>'
			));
?>

<div class="paging">
<?
			$paginator->options(array('url' => $this->passedArgs));
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
			<?=$html->link('zobrazit', array('action' => 'view', 'id' => $order['Order']['id']))?>
			<?=$html->link('smazat', array('action' => 'delete', 'id' => $order['Order']['id']), null, 'Opravdu chcete smazat tuto objednávku?')?>
		</td>
	</tr>
<?
		$odd = $odd == ' class="odd"' ? '' : ' class="odd"';
	}
?>
</table>

<div class="paging">
<?
			$paginator->options(array('url' => $this->passedArgs));
			echo $paginator->prev('<< Předchozí', array(), '<< Předchozí', array('class' => 'disabled_link'));
			echo '&nbsp;&nbsp;' . $paginator->numbers(array('separator' => false)) . '&nbsp;&nbsp;';
			echo $paginator->next('Další >>', array(), 'Další >>', array('class' => 'disabled_link'));
?>
	<div style="clear:both"></div>
</div>