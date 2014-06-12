	<?
		if ( $session->check('Company') ){
			header('Location: http://lekarny.lekarna-obzor.cz/users/companies/index');
			header('Connection: close');
			die();
		}
	?>
	<h1>Přihlášení k účtu</h1>
	<?=$form->Create('Company', array('url' => array('users' => true, 'controller' => 'companies', 'action' => 'login')))?>
	<table class="left_headed" style="padding-right:85px;background-color:#FFF;border:1px solid #0BCF01">
		<tr>
			<th>
				LOGIN:
			</th>
			<th>
				<?=$form->text('Company.login')?>
			</th>
		</tr>
		<tr>
			<th>
				HESLO:
			</th>
			<th>
				<?=$form->password('Company.password')?>
			</th>
		</tr>
		<tr>
			<th>
				&nbsp;
			</th>
			<td>
				<?=$form->submit('přihlásit se')?>
			</td>
		</tr>
	</table>
	<p><?=$html->link('Zapomněl(a) jsem heslo.', array('users' => true, 'controller' => 'companies', 'action' => 'password_recovery')) ?></p>
	<?=$form->end()?>