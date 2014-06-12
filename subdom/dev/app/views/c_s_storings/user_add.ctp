<script type="text/javascript">
	// musim si nastavit globalni promennou, abych mohl ve skriptu pri pridavani radku generovat select pro vyber meny
	var currencies = <?php echo json_encode($currencies)?>;
</script>
<script type="text/javascript" src="/js/c_s_storing_add_edit.js"></script>
<?php echo $this->element('c_s_storings/add_edit_new_product_management')?>
<h1>Naskladnit zboží</h1>
<?php
	echo $this->Form->create('CSStoring');
	echo $this->element('c_s_storings/add_edit_form');
	echo $this->Form->hidden('CSStoring.user_id', array('value' => $user['User']['id']));
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>