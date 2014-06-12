<h1>Výročí</h1>

<?php echo $this->element('search_forms/anniversaries')?>

<h2>Dnešní výročí</h2>
<?php if (empty($today_anniversaries)) { ?>
<p><em>Pro dnešní den nejsou žádná výročí</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Příjmení</th>
		<th>Křestní jméno</th>
		<th>Titul</th>
		<th>Obchodní partner</th>
		<th>Datum</th>
		<th>Typ</th>
		<th>Akce</th>
	</tr>
<?php
	$odd = '';
	foreach ($today_anniversaries as $today_anniversary) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $today_anniversary['Anniversary']['id']?></td>
		<td><?php echo $html->link($today_anniversary['ContactPerson']['last_name'], array('controller' => 'contact_people', 'action' => 'view', $today_anniversary['ContactPerson']['id']))?></td>
		<td><?php echo $today_anniversary['ContactPerson']['first_name']?></td>
		<td><?php echo $today_anniversary['ContactPerson']['prefix']?></td>
		<td><?php echo $html->link($today_anniversary['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $today_anniversary['BusinessPartner']['id']))?></td>
		<td><?php echo $today_anniversary['Anniversary']['date']?></td>
		<td><?php echo $today_anniversary['AnniversaryType']['name']?></td>
		<td><?php echo $today_anniversary['AnniversaryAction']['name']?></td>
	</tr>
<?php } ?>
</table>
<?php } ?>

<h2>Výročí v intervalu -2 až +10 dní</h2>
<?php if (empty($interval_anniversaries)) { ?>
<p><em>V intervalu nejsou žádná výročí</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Příjmení</th>
		<th>Křestní jméno</th>
		<th>Titul</th>
		<th>Obchodní partner</th>
		<th>Datum</th>
		<th>Typ</th>
		<th>Akce</th>
	</tr>
<?php
	$odd = '';
	foreach ($interval_anniversaries as $interval_anniversary) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $interval_anniversary['Anniversary']['id']?></td>
		<td><?php echo $html->link($interval_anniversary['ContactPerson']['last_name'], array('controller' => 'contact_people', 'action' => 'view', $interval_anniversary['ContactPerson']['id']))?></td>
		<td><?php echo $interval_anniversary['ContactPerson']['first_name']?></td>
		<td><?php echo $interval_anniversary['ContactPerson']['prefix']?></td>
		<td><?php echo $html->link($interval_anniversary['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $interval_anniversary['BusinessPartner']['id']))?></td>
		<td><?php echo $interval_anniversary['Anniversary']['date']?></td>
		<td><?php echo $interval_anniversary['AnniversaryType']['name']?></td>
		<td><?php echo $interval_anniversary['AnniversaryAction']['name']?></td>
	</tr>
<?php } ?>
</table>
<?php } ?>

<h2>Všechna výročí</h2>
<?php if (empty($anniversaries)) { ?>
<p><em>V intervalu nejsou žádná výročí</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $paginator->sort('ID', 'Anniversary.id')?></th>
		<th><?php echo $paginator->sort('Příjmení', 'ContactPerson.last_name')?></th>
		<th><?php echo $paginator->sort('Křestní jméno', 'ContactPerson.first_name')?></th>
		<th><?php echo $paginator->sort('Titul', 'ContactPerson.prefix')?></th>
		<th><?php echo $paginator->sort('Obchodní partner', 'BusinessPartner.name')?></th>
		<th><?php echo $paginator->sort('Datum', 'Anniversary.date')?></th>
		<th><?php echo $paginator->sort('Typ', 'AnniversaryType.name')?></th>
		<th><?php echo $paginator->sort('Akce', 'AnniversaryAction.name')?></th>
	</tr>
<?php
	$odd = '';
	foreach ($anniversaries as $anniversary) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $anniversary['Anniversary']['id']?></td>
		<td><?php echo $html->link($anniversary['ContactPerson']['last_name'], array('controller' => 'contact_people', 'action' => 'view', $anniversary['ContactPerson']['id']))?></td>
		<td><?php echo $anniversary['ContactPerson']['first_name']?></td>
		<td><?php echo $anniversary['ContactPerson']['prefix']?></td>
		<td><?php echo $html->link($anniversary['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $anniversary['BusinessPartner']['id']))?></td>
		<td><?php echo $anniversary['Anniversary']['date']?></td>
		<td><?php echo $anniversary['AnniversaryType']['name']?></td>
		<td><?php echo $anniversary['AnniversaryAction']['name']?></td>
	</tr>
<?php } ?>
</table>

<?php
echo $paginator->numbers();
echo $paginator->prev('« Předchozí ', null, null, array('class' => 'disabled'));
echo $paginator->next(' Další »', null, null, array('class' => 'disabled'));
?>
<?php } ?>