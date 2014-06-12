<h1>Vyhledat obchodního partnera v systému Ares</h1>
<?php echo $form->create('BusinessPartner', array('url' => array('controller' => 'business_partners', 'action' => 'ares_search')))?>
<table class="left_heading">
	<tr>
		<th>Obchodní firma</th>
		<td><?php echo $form->input('BusinessPartner.company', array('label' => false))?></td>
		<th>IČ</th>
		<td><?php echo $form->input('BusinessPartner.ico', array('label' => false))?></td>
		<th>Diakritika</th>
		<td><?php echo $form->input('BusinessPartner.diacritic', array('label' => false, 'type' => 'select', 'options' => array(0 => 'česká', 'ASCII')))?></td>
	</tr>
	<tr>
		<th>Obec</th>
		<td><?php echo $form->input('BusinessPartner.city', array('label' => false))?></td>
		<th>Fin. úřad</th>
		<td><?php echo $form->input('BusinessPartner.inland_revenue', array('label' => false))?></td>
		<th>Zobrazit</th>
		<td><?php echo $form->input('BusinessPartner.items', array('label' => false, 'type' => 'select', 'options' => array(200 => 200, 500 => 500, 1000 => 1000)))?></td>
	</tr>
	<tr>
		<th>Ulice</th>
		<td><?php echo $form->input('BusinessPartner.street', array('label' => false))?></td>
		<th>Číslo domu</th>
		<td><?php echo $form->input('BusinessPartner.number', array('label' => false))?></td>
		<th>Třídění</th>
		<td><?php echo $form->input('BusinessPartner.sort', array('label' => false, 'type' => 'select', 'options' => array('ZADNE' => 'netříděno', 'ICO' => 'IČ', 'OBEC' => 'Obec', 'OBCHJM' => 'Obchodní firma')))?></td>
	</tr>
	<tr>
		<th>Právní norma</th>
		<td><?php echo $form->input('BusinessPartner.law_form', array('label' => false))?></td>
		<th>CZ-NACE</th>
		<td><?php echo $form->input('BusinessPartner.cz_nace', array('label' => false))?></td>
		<th>Filtr</th>
		<td><?php echo $form->input('BusinessPartner.filter', array('label' => false, 'type' => 'select', 'options' => array(1 => 'jen aktivní', 0 => 'všechny subjekty', 2 => 'jen zaniklé')))?></td>
	</tr>		
</table>

<?php echo $form->submit('Vyhledat')?>
<?php echo $form->end() ?>

<!-- Vypis pripadnych vysledku vyhledavani -->
<?php if (isset($search_results)) { ?>
<?php 	if (empty($search_results)) { ?>
<p><em>Pro zadané údaje nebyly nalezeny žádné záznamy.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>IČO</th>
		<th>Právní forma</th>
		<th>Obchodní firma</th>
		<th>Adresa</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($search_results as $search_result) { ?>
	<tr>
		<?php echo $form->create('BusinessPartner', array('controller' => 'business_partners', 'action' => 'add'))?>
		<td><?php
			$ico = '&nbsp;';
			if (isset($search_result['ico'])) {
				$ico = $search_result['ico'];
			}
			echo $ico;
			echo $form->hidden('BusinessPartner.ico', array('value' => $ico));
		?></td>
		<td><?php echo (isset($search_result['pf']) ? $search_result['pf'] : '&nbsp;')?></td>
		<td><?php
			echo $search_result['ojm'];
			echo $form->hidden('BusinessPartner.name', array('value' => $search_result['ojm']));
			echo $form->hidden('Address.0.name', array('value' => $search_result['ojm']));
		?></td>
		<td><?php
			echo $search_result['jmn'];
			$address = explode(',', $search_result['jmn']);
			if (count($address) > 1) {
				$street = explode(' ', $address[count($address) - 1]);
				unset($address[count($address) - 1]);
				echo $form->hidden('Address.0.city', array('value' => implode(', ', $address)));
				echo $form->hidden('Address.0.number', array('value' => $street[count($street) - 1]));
				unset($street[count($street) - 1]);
				echo $form->hidden('Address.0.street', array('value' => implode(' ', $street)));
			} else {
				$street = explode(' ', $address[0]);
				echo $form->hidden('Address.0.number', array('value' => $street[count($street) - 1]));
				unset($street[count($street) - 1]);
				echo $form->hidden('Address.0.city', array('value' => implode(' ', $street)));
			}
		?></td>
		<td><?php
			echo $form->hidden('BusinessPartner.ares_search', array('value' => true));
			echo $form->submit('Vybrat')
		?></td>
		<?php echo $form->end()?>
	</tr>
	<?php } ?>
</table>
<?php } ?>
<?php } ?>