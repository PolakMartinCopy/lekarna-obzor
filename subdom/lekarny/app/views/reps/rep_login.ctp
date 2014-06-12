<h1>Přihlášení k administraci</h1>
<?=$form->Create('Rep', array('url' => array('rep' => true, 'controller' => 'reps', 'action' => 'login')))?>
<fieldset>
	<label>LOGIN:</label>
	<?=$form->text('Rep.login')?>
</fieldset>

<fieldset>
	<label>HESLO:</label>
	<?=$form->password('Rep.password')?>
</fieldset>

<?=$form->submit('přihlásit se')?>
<?=$form->end()?>