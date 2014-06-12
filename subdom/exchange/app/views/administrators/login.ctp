<h1>Přihlášení do administrace</h1>
<?php
	echo $this->Session->flash('auth');
	echo $this->Form->create('Administrator');
?>
<table class="left_heading">
	<tr>
		<th>Login</th>
		<td><?php echo $this->Form->input('login', array('label' => false));?></td>
	</tr>
	<tr>
		<th>Heslo</th>
		<td><?php echo $this->Form->input('password', array('label' => false));?></td>
	</tr>
</table>
<div class="clearer"></div>
<?php 
	echo $this->Form->submit('Přihlásit se');	
	echo $this->Form->end();
?>