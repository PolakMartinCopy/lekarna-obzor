<h1>Přizvat kontaktní osoby k jednání <?php echo $business_session['BusinessSession']['id']?></h1>

<ul>
	<li><?php echo $html->link('Detail obchodního jednání', array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id']))?></li>
</ul>

<?php if (empty($contact_people)) {?>
<p><em>Tento obchodní partner nemá přiděleny žádné kontaktní osoby, nejprve prosím <?php echo $html->link('přidejte kontaktní osoby', array('controller' => 'contact_people', 'action' => 'add', 'business_partner_id' => $business_session['BusinessSession']['business_partner_id']))?>.</em></p>
<?php } else {
	echo $form->create('BusinessSessionsContactPerson', array('url' => array('controller' => 'business_sessions', 'action' => 'invite', $business_session['BusinessSession']['id'])))
?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Křestní jméno</th>
		<th>Příjmení</th>
		<th>Titul</th>
		<th>Telefon</th>
		<th>Mobilní telefon</th>
		<th>Email</th>
		<th>Obchodní partner</th>
		<th>Pozvána</th>
	</tr>
<?php
	$odd = '';
	foreach ($contact_people as $contact_person) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $contact_person['ContactPerson']['id']?></td>
		<td><?php echo $contact_person['ContactPerson']['first_name']?></td>
		<td><?php echo $contact_person['ContactPerson']['last_name']?></td>
		<td><?php echo $contact_person['ContactPerson']['prefix']?></td>
		<td><?php echo $contact_person['ContactPerson']['phone']?></td>
		<td><?php echo $contact_person['ContactPerson']['cellular']?></td>
		<td><?php echo $html->link($contact_person['ContactPerson']['email'], 'mailto:' . $contact_person['ContactPerson']['email'])?></td>
		<td><?php echo $html->link($contact_person['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $contact_person['BusinessPartner']['id']))?></td>
		<td>
			<?php echo $form->input('BusinessSessionsContactPerson.' . $contact_person['ContactPerson']['id'] . '.contact_person_id', array('type' => 'checkbox', 'value' => $contact_person['ContactPerson']['id'], 'label' => false))?>
			<?php echo $form->hidden('BusinessSessionsContactPerson.' . $contact_person['ContactPerson']['id'] . '.business_session_id', array('value' => $business_session['BusinessSession']['id']))?>
			<?php echo $form->hidden('BusinessSessionsContactPerson.' . $contact_person['ContactPerson']['id'] . '.id')?>
		</td>
	</tr>
<?php
	}
?>
</table>
<?php echo $form->submit('Uložit')?>
<?php echo $form->end() ?>
<?php } ?>