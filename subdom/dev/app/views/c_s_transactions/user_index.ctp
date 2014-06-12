<h1>Pohyby na centrálním skladě</h1>
<button id="search_form_show">vyhledávací formulář</button>
<?php
	echo $this->element('search_forms/c_s_transactions', array('url' => array('controller' => 'c_s_transactions', 'action' => 'index')));

	echo $form->create('CSV', array('url' => array('controller' => 'c_s_transactions', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($transactions)) { ?>
<p><em>V systému nejsou žádné pohyby.</em></p>
<?php 
} else {
	echo $this->element('c_s_transactions/index_table', array('c_s_transactions' => $transactions));
} ?>

<script>
$("#search_form_show").click(function () {
	if ($('#search_form_c_s_transactions').css('display') == "none"){
		$("#search_form_c_s_transactions").show("slow");
	} else {
		$("#search_form_c_s_transactions").hide("slow");
	}
});
</script>