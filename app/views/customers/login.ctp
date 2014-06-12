<div class="mainContentWrapper">
	<?=$form->Create('Customer', array('url' => array('action' => 'login'), 'id' => 'orderForm'));?>
	<fieldset>
		<legend>Přihlašovací údaje</legend>
		<table id="orderForm">
			<tr>
				<th>
					Login:
				</th>
				<td>
					<?=$form->text('Customer.login')?>
				</td>
			</tr>
			<tr>
				<th>
					Heslo:
				</th>
				<td>
					<?=$form->password('Customer.password')?>
				</td>
			</tr>
			<tr>
				<th>
					&nbsp;
				</th>
				<td>
					<?=$html->link('zapomněl(a) jsem heslo', array('controller' => 'customers', 'action' => 'password')) ?>
				</td>
		</table>
	</fieldset>
	<?=$form->end('přihlásit');?>
</div>