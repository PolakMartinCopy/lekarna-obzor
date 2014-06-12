<h3>Přihlášení k administraci</h3>
<?=$form->Create('Administrator', array('url' => array('action' => 'login')))?>
<fieldset>
	<label>LOGIN:</label>
	<?=$form->text('Administrator.login')?>
</fieldset>

<fieldset>
	<label>HESLO:</label>
	<?=$form->password('Administrator.password')?>
</fieldset>
<fieldset>
	<?=$form->checkbox('Administrator.longlogin')?>
	<label>Pamatovat heslo na 2 hodiny?</label>
	
</fieldset>
<?=$form->submit('přihlásit se')?>
<?=$form->end()?>