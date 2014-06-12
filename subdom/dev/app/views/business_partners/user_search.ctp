<h1>Formulář pro hledání obchodních partnerů</h1>
<ul>
	<li><?php echo $html->link('Zpět na seznam obchodních partnerů', array('controller' => 'business_partners', 'action' => 'index'))?></li>
</ul>

<?php
	if (isset($business_partners)) {
		if (empty($business_partners)) {
?>
			<p><em>Zadanemu dotazu neodpovídají žádné položky z tabulky obchodních partnerů.</em></p>
<?php
		} else {
		
			$options = array();
			if (isset($this->data['BusinessPartner'])) {
				foreach ($this->data['BusinessPartner'] as $key => $item) {
					if ($key == 'active') {
						$options['BusinessPartner.active'] = $item;
					} elseif (!empty($item)) {
						$options['BusinessPartner.' . $key] = $item;
					}
				}
			}
			if (isset($this->data['Address'])) {
				foreach ($this->data['Address'] as $key => $item) {
					if (!empty($item)) {
						$options['Address.' . $key] = $item;
					}
				}
			}
			$paginator->options(array('url' => $options));
			
			echo $html->link('XLS', array('controller' => 'business_partners', 'action' => 'xls_export', 'data' => urlencode(base64_encode(serialize($find)))));
			
			echo $this->element('indexes/business_partners', array('business_partners' => $business_partners));
			
			echo $paginator->numbers();
			echo $paginator->prev('« Předchozí ', null, null, array('class' => 'disabled'));
			echo $paginator->next(' Další »', null, null, array('class' => 'disabled'));
		}
	}
?>

<?php echo $form->create('BusinessPartner', array('url' => array('controller' => 'business_partners', 'action' => 'search')))?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('BusinessPartner.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>IČO</th>
		<td><?php echo $form->input('BusinessPartner.ico', array('label' => false))?></td>
	</tr>
	<tr>
		<th>DIČ</th>
		<td><?php echo $form->input('BusinessPartner.dic', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Aktivní</th>
		<td>
			<?php
				$active_options = array('label' => false, 'checked' => true);
				if (isset($this->data['BusinessPartner']['active']) && !$this->data['BusinessPartner']['active']) {
					unset($active_options['checked']);
				}
				echo $form->input('BusinessPartner.active', $active_options);
			?>
		</td>
	</tr>
	<tr>
		<th>Poznámka</th>
		<td><?php echo $form->input('BusinessPartner.note', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Bonita</th>
		<td>
			<table>
				<tr>
					<th>&nbsp;</th>
					<th>A</th>
					<th>B</th>
					<th>C</th>
				</tr>
				<tr>
					<th>1</th>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="1"<?php echo (isset($this->data['BusinessPartner']['bonity']) && $this->data['BusinessPartner']['bonity'] == 1) ? ' checked ' : ''?>/></td>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="4"<?php echo (isset($this->data['BusinessPartner']['bonity']) && $this->data['BusinessPartner']['bonity'] == 4) ? ' checked ' : ''?>/></td>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="7"<?php echo (isset($this->data['BusinessPartner']['bonity']) && $this->data['BusinessPartner']['bonity'] == 7) ? ' checked ' : ''?>/></td>
				</tr>
				<tr>
					<th>2</th>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="2"<?php echo (isset($this->data['BusinessPartner']['bonity']) && $this->data['BusinessPartner']['bonity'] == 2) ? ' checked ' : ''?>/></td>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="5"<?php echo (isset($this->data['BusinessPartner']['bonity']) && $this->data['BusinessPartner']['bonity'] == 5) ? ' checked ' : ''?>/></td>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="8"<?php echo (isset($this->data['BusinessPartner']['bonity']) && $this->data['BusinessPartner']['bonity'] == 8) ? ' checked ' : ''?>/></td>
				</tr>
				<tr>
					<th>3</th>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="3"<?php echo (isset($this->data['BusinessPartner']['bonity']) && $this->data['BusinessPartner']['bonity'] == 3) ? ' checked ' : ''?>/></td>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="6"<?php echo (isset($this->data['BusinessPartner']['bonity']) && $this->data['BusinessPartner']['bonity'] == 6) ? ' checked ' : ''?>/></td>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="9"<?php echo (isset($this->data['BusinessPartner']['bonity']) && $this->data['BusinessPartner']['bonity'] == 9) ? ' checked ' : ''?>/></td>
				</tr>
			</table>
			<?php echo $form->error('BusinessPartner.bonity')?>
		</td>
	</tr>
	<tr>
		<th>Provozní doba</th>
		<td><?php echo $form->input('BusinessPartner.opening_hours', array('label' => false))?></td>
	</tr>
	<tr>
		<td colspan="2">Adresa</td>
	</tr>
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('Address.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Jméno osoby</th>
		<td><?php echo $form->input('Address.person_first_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Příjmení osoby</th>
		<td><?php echo $form->input('Address.person_last_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Ulice</th>
		<td><?php echo $form->input('Address.street', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Číslo popisné</th>
		<td><?php echo $form->input('Address.number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Orientační číslo</th>
		<td><?php echo $form->input('Address.o_number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Město</th>
		<td><?php echo $form->input('Address.city', array('label' => false))?></td>
	</tr>
	<tr>
		<th>PSČ</th>
		<td><?php echo $form->input('Address.zip', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Okres</th>
		<td><?php echo $form->input('Address.region', array('label' => false))?></td>
	</tr>
</table>

<?php
	//echo $form->hidden('Address.address_type_id', array('value' => 1));
	if ($session->read('User.user_type_id' == 3)) {
		echo $form->hidden('BusinessPartner.user_id', array('value' => $user_id));
	}
	echo $form->submit('Vyhledat');
	echo $form->end();
?>

