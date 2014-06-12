<div class="mainContentWrapper">
	<?=$form->Create('Address', array('url' => array('controller' => 'orders', 'action' => 'address_edit', 'type' => $this->params['named']['type']))) ?>
		<fieldset>
			<legend><?=( $this->params['named']['type'] == 'd' ? 'Adresa doručení' : 'Fakturační adresa' ) ?></legend>
			<table id="orderForm">
				<tr>
					<th><sup>*</sup>Jméno</th>
					<td><?=$form->input('Address.name', array('label' => false, 'div' => false))?></td>
				</tr>
				<tr>
					<th><sup>*</sup>Ulice</th>
					<td><?=$form->input('Address.street', array('label' => false, 'div' => false))?></td>
				</tr>	
				<tr>
					<th><sup>*</sup>Číslo popisné</th>
					<td><?=$form->input('Address.street_no', array('label' => false, 'div' => false))?></td>
				</tr>	
				<tr>
					<th><sup>*</sup>PSČ</th>
					<td><?=$form->input('Address.zip', array('label' => false, 'div' => false))?></td>
				</tr>	
				<tr>
					<th><sup>*</sup>Město</th>
					<td><?=$form->input('Address.city', array('label' => false, 'div' => false))?></td>
				</tr>
				<tr>
					<th><sup>*</sup>Stát</th>
					<td><?=$form->select('Address.state', array('Česká Republika' => 'Česká Republika'), null, array('empty' => false)) ?></td>
				</tr>
				<tr>
					<td colspan="2">
						<?=$form->submit('Upravit')?>
					</td>
				</tr>
			</table>
		</fieldset>
	<?=$form->end() ?>
</div>