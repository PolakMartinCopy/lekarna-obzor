<div id="info">
<h4>RYCHLÉ INFO</h4>
	<?php 
	if ($this->Session->check('Customer')) {
		echo 'Jste přihlášen jako ' . $this->Session->read('Customer.first_name') . ' ' . $this->Session->read('Customer.last_name') . ' - ' . $this->Html->link('Odhlásit', array('controller' => 'customers', 'action' => 'logout'));
	} else {
		echo $this->Html->link('Přihlásit', array('controller' => 'customers', 'action' => 'login'));
	} ?>
</div>