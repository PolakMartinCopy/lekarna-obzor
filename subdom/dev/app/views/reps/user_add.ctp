<h1>Přidat uživatele</h1>
<?php
	echo $form->create('User', array('url' => array('controller' => 'reps', 'action' => 'add')));
	echo $this->element('reps/add_edit_table');
	echo $form->hidden('Rep.user_type_id', array('value' => 4));
	echo $form->submit('Uložit');
	echo $form->end();
?>