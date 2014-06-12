<h1>Registrace nového účtu</h1>
<p>Jestliže již máte vlastní účet na webu <?php echo CUST_ROOT ?>, <?php echo $this->Html->link('přihlašte se', array('controller' => 'customers', 'action' => 'login'))?> prosím <?php echo $this->Html->link('zde', array('controller' => 'customers', 'action' => 'login'))?>.</p>
<?=$form->Create('Customer', array('id' => 'orderForm'))?>
	<fieldset>
		<legend>Registrační údaje</legend>
		<table id="orderForm">
			<tr>
				<th><sup>*</sup>Jméno</th>
				<td><?=$form->input('Customer.first_name', array('label' => false, 'div' => false))?></td>
			</tr>
			<tr>
				<th><sup>*</sup>Příjmení</th>
				<td><?=$form->input('Customer.last_name', array('label' => false, 'div' => false))?></td>
			</tr>	
			<tr>
				<th><sup>*</sup>Kontaktní telefon</th>
				<td><?=$form->input('Customer.phone', array('label' => false, 'div' => false))?></td>
			</tr>
			<tr>
				<th><sup>*</sup>Emailová adresa</th>
				<td><?=$form->input('Customer.email', array('label' => false, 'div' => false))?></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>Doručovací adresa - nepovinné</legend>
		<table id="orderForm">
			<tr>
				<th>Ulice</th>
				<td><?=$form->input('Address.0.street', array('label' => false, 'div' => false))?></td>
			</tr>	
			<tr>
				<th>Číslo popisné</th>
				<td><?=$form->input('Address.0.street_no', array('label' => false, 'div' => false))?></td>
			</tr>	
			<tr>
				<th>PSČ</th>
				<td><?=$form->input('Address.0.zip', array('label' => false, 'div' => false))?></td>
			</tr>	
			<tr>
				<th>Město</th>
				<td><?=$form->input('Address.0.city', array('label' => false, 'div' => false))?></td>
			</tr>
			<tr>
				<th>Stát</th>
				<td>
					<input type="text" name="fakeState" value="Česká republika" disabled />
					<?=$form->hidden('Address.0.state', array('value' => 'Česká republika'))?>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<table id="orderForm">
		<tr>
			<th>&nbsp;</th>
			<td><?=$form->Submit('zaregistrovat');?></td>
		</tr>
	</table>
<?=$form->end()?>