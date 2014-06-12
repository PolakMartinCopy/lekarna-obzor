<div class="mainContentWrapper">
	<ul class="actions">
		<li><?=$html->link('zákaznický panel', array('controller' => 'customers', 'action' => 'index'))?></li>
	</ul>
	<table id="customerLayout">
		<tr>
			<th>
				Vložení nové adresy
			</th>
		</tr>
		<tr>
			<td>
				<?=$form->Create('Address', array('url' => array('controller' => 'customers', 'action' => 'add_address')))?>
				<table class="leftHeading">
					<tr>
						<th>
							jméno a příjmení&nbsp;<sup>*</sup>
						</th>
						<td>
							<?=$form->input('Address.name', array('label' => false))?>
						</td>
					</tr>
					<tr>
						<th>
							ulice&nbsp;<sup>*</sup>
						</th>
						<td>
							<?=$form->input('Address.street', array('label' => false))?>
						</td>
					</tr>
					<tr>
						<th>
							město&nbsp;<sup>*</sup>
						</th>
						<td>
							<?=$form->input('Address.city', array('label' => false))?>
						</td>
					</tr>
					<tr>
						<th>
							psč&nbsp;<sup>*</sup>
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
							<?=$form->input('Address.state', array('label' => false, 'disabled' => true, 'value' => 'Česká republika'))?>
							<?=$form->hidden('Address.state', array('value' => 'Česká republika'))?>
						</td>
					</tr>
				</table>
				<?=$form->hidden('Address.customer_id', array('value' => $session->read('Customer.id')))?>
				<?=$form->end('uložit')?>
			</td>
		</tr>
	</table>
	<ul class="actions">
		<li><?=$html->link('zákaznický panel', array('controller' => 'customers', 'action' => 'index'))?></li>
	</ul>
</div>