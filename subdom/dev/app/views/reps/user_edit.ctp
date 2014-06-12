<h1>Upravit uživatele</h1>
<?php
	echo $form->create('Rep', array('url' => array('controller' => 'reps', 'action' => 'edit')));
	echo $this->element('reps/add_edit_table');
	echo $form->hidden('Rep.id');
	echo $this->Form->hidden('RepAttribute.id');
	echo $form->submit('Uložit');
	echo $form->end();
?>