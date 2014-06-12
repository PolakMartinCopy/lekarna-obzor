<div class="mainContentWrapper">
	<table id="customerLayout">
		<tr>
			<th>
				Změna údajů zákazníka
			</th>
		</tr>
		<tr>
			<td>
				<?=$form->Create('Customer', array('url' => array('action' => 'edit')))?>
				<table class="leftHeading">
					<tr>
						<th>
							jméno
						</th>
						<td>
							<?=$form->input('Customer.first_name', array('label'=> false))?>
						</td>
					</tr>
					<tr>
						<th>
							příjmení
						</th>
						<td>
							<?=$form->input('Customer.last_name', array('label'=> false))?>
						</td>
					</tr>
					<tr>
						<th>
							telefon
						</th>
						<td>
							<?=$form->input('Customer.phone', array('label'=> false))?>
						</td>
					</tr>
					<tr>
						<th>
							email
						</th>
						<td>
							<?=$form->input('Customer.email', array('label'=> false))?>
						</td>
					</tr>
					<tr>
						<th>
							login
						</th>
						<td>
							<?=$form->input('Customer.login', array('label'=> false))?>
						</td>
					</tr>
				</table>
				<?=$form->hidden('Customer.edit', array('value' => 'info'))?>
				<?=$form->end('uložit')?>
			</td>
		</tr>
		<tr>
			<th>
				Změna hesla
			</th>
		</tr>
		<tr>
			<td>
				<?=$form->Create('Customer', array('url' => array('action' => 'edit')))?>
				<table class="leftHeading">
					<tr>
						<th>
							původní heslo
						</th>
						<td>
							<?=$form->input('Customer.old_password', array('label'=> false, 'type' => 'password'))?>
						</td>
					</tr>
					<tr>
						<th>
							nové heslo
						</th>
						<td>
							<?=$form->input('Customer.new_password', array('label'=> false, 'type' => 'password'))?>
						</td>
					</tr>
					<tr>
						<th>
							zopakujte nové heslo
						</th>
						<td>
							<?=$form->input('Customer.new_password_rep', array('label'=> false, 'type' => 'password'))?>
						</td>
					</tr>
				</table>
				<?=$form->hidden('Customer.edit', array('value' => 'pass'))?>
				<?=$form->end('změnit heslo')?>
			</td>
		</tr>
	</table>
</div>