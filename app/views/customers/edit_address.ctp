<div class="mainContentWrapper">
	<ul class="actions">
		<li><?=$html->link('zákaznický panel', array('controller' => 'customers', 'action' => 'index'))?></li>
	</ul>
	<table id="customerLayout">
		<tr>
			<th>
				Změna adresy
			</th>
		</tr>
		<tr>
			<td>
				<?=$form->Create('Address', array('url' => array('controller' => 'customers', 'action' => 'edit_address')))?>
				<table class="leftHeading">
					<tr>
						<th>
							jméno a příjmení
						</th>
						<td>
							<?=$form->input('Address.name', array('label' => false))?>
						</td>
					</tr>
					<tr>
						<th>
							ulice
						</th>
						<td>
							<?=$form->input('Address.street', array('label' => false))?>
						</td>
					</tr>
					<tr>
						<th>
							město
						</th>
						<td>
							<?=$form->input('Address.city', array('label' => false))?>
						</td>
					</tr>
					<tr>
						<th>
							psč
						</th>
						<td>
							<?=$form->input('Address.zip', array('label' => false))?>
						</td>
					</tr>
					<tr>
						<th>
							stát
						</th>
						<td>
							<?=$form->input('Address.state', array('label' => false, 'disabled' => true))?>
							<?=$form->hidden('Address.state')?>
						</td>
					</tr>
				</table>
				<?=$form->hidden('Address.id')?>
				<?=$form->end('upravit')?>
			</td>
		</tr>
	</table>
	<ul class="actions">
		<li><?=$html->link('zákaznický panel', array('controller' => 'customers', 'action' => 'index'))?></li>
	</ul>
</div>
