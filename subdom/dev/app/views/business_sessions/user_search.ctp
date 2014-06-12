<h1>Formulář pro hledání obchodního jednání</h1>

<ul>
	<li><?php echo $html->link('Zpět na seznam obchodních jednání', array('controller' => 'business_partners', 'action' => 'index'))?></li>
</ul>

<?php
	if (isset($business_sessions)) {
		if (empty($business_sessions)) {
?>
			<p><em>Zadanemu dotazu neodpovídají žádné položky z tabulky obchodních jednání.</em></p>
<?php
		} else {
		
			$options = array();
			App::import('Model', 'BusinessSession');
			$this->BusinessSession = &new BusinessSession;
			if (isset($this->data['BusinessSession']['from']['checked']) && $this->data['BusinessSession']['from']['checked']) {
				$options['BusinessSession.from.date'] = $this->BusinessSession->built_date($this->data['BusinessSession']['from']['date']);
			}
			if (isset($this->data['BusinessSession']['to']['checked']) && $this->data['BusinessSession']['to']['checked']) {
				$options['BusinessSession.to.date'] = $this->BusinessSession->built_date($this->data['BusinessSession']['to']['date']);
			}
			if (!empty($this->data['BusinessPartner']['name'])) {
				$options['BusinessPartner.name'] = $this->data['BusinessPartner']['name'];
			}
			if (!empty($this->data['ContactPerson']['name'])) {
				$options['ContactPerson.name'] = $this->data['ContactPerson']['name'];
			}
			$options['BusinessSession.business_session_type_id'] = $this->data['BusinessSession']['business_session_type_id'];
			$options['BusinessSession.business_session_state_id'] = $this->data['BusinessSession']['business_session_state_id'];
			if (!empty($this->data['Address']['city'])) {
				$options['Address.city'] = $this->data['Address']['city'];
			}
			if (!empty($this->data['BusinessPartner']['ico'])) {
				$options['BusinessPartner.ico'] = $this->data['BusinessPartner']['ico'];
			}
			if (!empty($this->data['BusinessSession']['description_query'])) {
				$options['BusinessSession.description_query'] = $this->data['BusinessSession']['description_query'];
			}

			$paginator->options(array('url' => $options));
			echo $this->element('indexes/business_sessions', array('business_sessions' => $business_sessions));
			echo $paginator->numbers();
			echo $paginator->prev('« Předchozí ', null, null, array('class' => 'disabled'));
			echo $paginator->next(' Další »', null, null, array('class' => 'disabled'));
		}
	}
	
?>

<?php echo $form->create('BusinessSession', array('url' => array('controller' => 'business_sessions', 'action' => 'search')))?>
<table class="left_heading">
	<tr>
		<th>Datum od</th>
		<td><?php
			echo $form->checkbox('BusinessSession.from.checked', array('value' => true, 'checked' => (isset($this->data['BusinessSession']['from']['checked'])) ? $this->data['BusinessSession']['from']['checked'] : (isset($this->data) ? false : true)));
			echo $form->dateTime('BusinessSession.from.date', 'DMY', null, null, array('monthNames' => $monthNames, 'label' => false, 'maxYear' => date('Y') + 3, 'minYear' => 2010, 'empty' => false));
			?>
		</td>
	</tr>
	<tr>
		<th>Datum do</th>
		<td><?php
			echo $form->checkbox('BusinessSession.to.checked', array('value' => true, 'checked' => (isset($this->data['BusinessSession']['to']['checked'])) ? $this->data['BusinessSession']['to']['checked'] : (isset($this->data) ? false : true)));	
			echo $form->dateTime('BusinessSession.to.date', 'DMY', null, null, array('monthNames' => $monthNames, 'label' => false, 'maxYear' => date('Y') + 3, 'minYear' => 2010, 'empty' => false));
			?>
		</td>
	</tr>
	<tr>
		<th>Obchodní partner</th>
		<td><?php echo $form->input('BusinessPartner.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Kontakt</th>
		<td><?php echo $form->input('ContactPerson.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Typ obchodního jednání</th>
		<td><?php echo $form->input('BusinessSession.business_session_type_id', array('options' => $business_session_types, 'empty' => false, 'label' => false))?></td>
	</tr>
	<tr>
		<th>Stav obchodního jednání</th>
		<td><?php echo $form->input('BusinessSession.business_session_state_id', array('options' => $business_session_states, 'empty' => false, 'label' => false))?></td>
	</tr>
	<tr>
		<th>Město</th>
		<td><?php echo $form->input('Address.city', array('label' => false))?></td>
	</tr>
	<tr>
		<th>IČO</th>
		<td><?php echo $form->input('BusinessPartner.ico', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Popis obchodního jednání</th>
		<td><?php echo $form->input('BusinessSession.description_query', array('label' => false))?></td>
	</tr>
</table>
<?php 
if ($user['User']['user_type_id'] == 3) {
	echo $form->hidden('BusinessSession.user_id', array('value' => $user['User']['id']));
}
echo $form->submit('Odeslat');
echo $form->end();
?>
