<h2>Vložení nového administrátora</h2>
<?=$form->create('Administrator');?>
	<fieldset>
 		<legend>Administrátor</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Jméno
				</th>
				<td>
					<?=$form->input('Administrator.first_name', array('label' => false))?>
				</td>
			</tr>
			<tr>
				<th>
					Příjmení
				</th>
				<td>
					<?=$form->input('Administrator.last_name', array('label' => false))?>
				</td>
			</tr>
			<tr>
				<th>
					Login:
				</th>
				<td>
					<?=$form->input('Administrator.login', array('label' => false))?>
				</td>
			</tr>
			<tr>
				<th>
					Heslo:
				</th>
				<td>
					<?=$form->input('Administrator.npass', array('label' => false))?>
				</td>
			</tr>
		</table>
	</fieldset>
<?=$form->end('Uložit')?>