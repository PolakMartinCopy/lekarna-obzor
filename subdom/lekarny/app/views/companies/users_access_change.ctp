<h1>Úprava loginu a hesla</h1>
<?=$form->Create('Company', array('url' => '/users/companies/access_change/')) ?>

<fieldset>
	<legend>login a heslo</legend>
	<table class="left_headed register">
		<tr>
			<td colspan="2" style="font-size:10px;">
				Chcete-li změnit pouze login, ponechte pole pro hesla prázdná.<br />
				Chcete-li změnit pouze heslo, ponechte pole pro login vyplněno Vašim původním loginem.<br />
				Login i heslo musí mít nejméně 10 znaků.
			</td>
		</tr>
		<tr>
			<th>
				login
			</th>
			<td>
				<?=$form->input('login', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				původní heslo
			</th>
			<td>
				<?=$form->input('password', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				nové heslo
			</th>
			<td>
				<?=$form->input('new_password', array('label' => false, 'type' => 'password'));?>
			</td>
		</tr>
		<tr>
			<th>
				zopakujte nové heslo
			</th>
			<td>
				<?=$form->input('new_password_retype', array('label' => false, 'type' => 'password'));?>
			</td>
		</tr>
	</table>
</fieldset>

<?=$form->submit('odeslat') ?>
<?=$form->end() ?>