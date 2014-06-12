	<?
		if ( $session->check('Company') ){
			header('Location: http://lekarny.lekarna-obzor.cz/users/companies/index');
			header('Connection: close');
			die();
		}
	?>
	<h1>Vyžádání ztraceného hesla</h1>
	<?=$form->Create('Company', array('url' => array('users' => true, 'controller' => 'companies', 'action' => 'password_recovery')))?>
	<table class="left_headed">
		<tr>
			<th>
				Váš email
			</th>
			<td>
				<?=$form->input('Company.person_email', array('label' => false, 'size' => 50))?>
			</td>
		</tr>
		<tr>
			<th>
				&nbsp;
			</th>
			<td>
				<?=$form->submit('odeslat heslo')?>
			</td>
		</tr>
	</table>
	<?=$form->end()?>