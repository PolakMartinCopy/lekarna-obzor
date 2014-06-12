<table class="left_heading">
	<tr>
		<th>Křestní jméno<sup>*</sup></th>
		<td><?php echo $form->input('Rep.first_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Příjmení<sup>*</sup></th>
		<td><?php echo $form->input('Rep.last_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Telefon<sup>*</sup></th>
		<td><?php echo $form->input('Rep.phone', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Email<sup>*</sup></th>
		<td><?php echo $form->input('Rep.email', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Login<sup>*</sup></th>
		<td><?php echo $form->input('Rep.login', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Heslo<sup>*</sup></th>
		<td><?php echo $form->input('Rep.password', array('label' => false))?></td>
	</tr>
	<tr>
		<th>IČ</th>
		<td><?php echo $this->Form->input('RepAttribute.ico', array('label' => false))?></td>
	</tr>
	<tr>
		<th>DIČ</th>
		<td><?php echo $this->Form->input('RepAttribute.dic', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Ulice<sup>*</sup></th>
		<td><?php echo $this->Form->input('RepAttribute.street', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Číslo popisné<sup>*</sup></th>
		<td><?php echo $this->Form->input('RepAttribute.street_number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Město<sup>*</sup></th>
		<td><?php echo $this->Form->input('RepAttribute.city', array('label' => false))?></td>
	</tr>
	<tr>
		<th>PSČ<sup>*</sup></th>
		<td><?php echo $this->Form->input('RepAttribute.zip', array('label' => false))?></td>
	</tr>
</table>
<ul>
	<li><small><sup>*</sup> - Pole musí být neprázdné</small></li>
</ul>