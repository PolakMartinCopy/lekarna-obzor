<div class="menu_header">
	Kontaktní osoba
</div>
<ul class="menu_links">
	<li><?php echo $html->link('Detail kontaktní osoby', array('controller' => 'contact_people', 'action' => 'view', $contact_person['ContactPerson']['id']))?></li>
	<li><?php echo $html->link('Upravit kontaktní osobu', array('controller' => 'contact_people', 'action' => 'edit', $contact_person['ContactPerson']['id']))?></li>
	<li><?php echo $html->link('Smazat kontaktní osobu', array('controller' => 'contact_people', 'action' => 'delete', $contact_person['ContactPerson']['id']), null, 'Opravdu chcete kontaktní osobu smazat?')?></li>
	<li><?php echo $html->link('Přidat výročí', array('controller' => 'anniversaries', 'action' => 'add', 'contact_person_id' => $contact_person['ContactPerson']['id']))?></li>
</ul>