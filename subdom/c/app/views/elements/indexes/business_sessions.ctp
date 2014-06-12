<table class="top_heading">
	<tr>
		<th><?php echo $paginator->sort('ID', 'BusinessSession.id')?></th>
		<th><?php echo $paginator->sort('Datum jednání', 'BusinessSession.date')?></th>
		<th><?php echo $paginator->sort('Obchodní partner', 'BusinessPartner.name')?></th>
		<th><?php echo $paginator->sort('Typ jednání', 'BusinessSessionType.name')?></th>
		<th><?php echo $paginator->sort('Stav jednání', 'BusinessSessionState.name')?></th>
		<th><?php echo $paginator->sort('Datum vložení', 'BusinessSession.created')?></th>
		<th><?php echo $paginator->sort('Založil', 'User.last_name')?></th>
		<th><?php echo $paginator->sort('Náklady', 'celkem')?></th>
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = '';
	foreach ($business_sessions as $business_session) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $business_session['BusinessSession']['id']?></td>
		<td><?php echo $business_session['BusinessSession']['date']?></td>
		<td><?php echo $html->link($business_session['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $business_session['BusinessPartner']['id']))?></td>
		<td><?php echo $business_session['BusinessSessionType']['name']?></td>
		<td><?php echo $business_session['BusinessSessionState']['name']?></td>
		<td><?php echo $business_session['BusinessSession']['created']?></td>
		<td><?php echo $business_session['User']['last_name']?></td>
		<td><?php echo floatval($business_session[0]['celkem'])?></td>
		<td class="actions">
			<?php echo $html->link('Detail', array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id']))?>
			<?php echo $html->link('Upravit', array('controller' => 'business_sessions', 'action' => 'edit', $business_session['BusinessSession']['id']))?>
			<?php echo $html->link('Uzavřít', array('controller' => 'business_sessions', 'action' => 'close', $business_session['BusinessSession']['id']), null, 'Opravdu chcete obchnodní jednání ' . $business_session['BusinessSession']['id'] . ' označit jako uzavřené?')?>
			<?php echo $html->link('Storno', array('controller' => 'business_sessions', 'action' => 'storno', $business_session['BusinessSession']['id']), null, 'Opravdu chcete obchodní jednání ' . $business_session['BusinessSession']['id'] . ' stornovat?')?>
		</td>
	</tr>
<?php } ?>
</table>