<h2>Seznam přijatých objednávek</h2>

<?
	echo $this->element('admin_orders_filter' );
?>

<?
$paginator->options(array('url' => $this->passedArgs));
echo $paginator->counter(array(
			        'format' => '<p>Strana <strong>%page%</strong> z <strong>%pages%</strong> stran celkem,
					zobrazuji %current% objednávek z %count% objednávek celkem.</p>'
			));
?>


<div class="paging">
<?
			echo $paginator->prev('<< Předchozí', array(), '<< Předchozí');
			echo '&nbsp;&nbsp;' . $paginator->numbers() . '&nbsp;&nbsp;';
			echo $paginator->next('Další >>', array(), 'Další >>');
?>
</div>


<table class="topHeading" cellpadding="5" cellspacing="3">
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
	foreach ( $orders as $order ){
?>
	<tr>
		<td>
			<?=$order['Order']['id']?>
		</td>
		<td>
			<?=$order['Order']['created']?>
		</td>
		<td>
			<?=$order['Order']['customer_name']?>
		</td>
		<td>
			<?=$order['Order']['subtotal_with_dph']?>
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
			<?=$html->link('zobrazit', array('action' => 'view', $order['Order']['id']))?>
			<?=$html->link('smazat', array('action' => 'delete', $order['Order']['id']), null, 'Opravdu chcete smazat tuto objednávku?')?>
		</td>
	</tr>
<?}?>
</table>


<div>
<?
			echo $paginator->prev('<< Predchozi', array(), '<< Predchozi');
			echo '&nbsp;&nbsp;' . $paginator->numbers() . '&nbsp;&nbsp;';
			echo $paginator->next('Dalsi >>', array(), 'Dalsi >>');
?>
</div>
<?//debug($orders)?>