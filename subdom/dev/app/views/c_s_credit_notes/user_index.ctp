<h1>Dobropisy</h1>
<button id="search_form_show">vyhledávací formulář</button>
<?php
	echo $this->element('search_forms/c_s_credit_notes', array('url' => array('controller' => 'c_s_credit_notes', 'action' => 'index')));

	echo $form->create('CSV', array('url' => array('controller' => 'c_s_credit_notes', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($credit_notes)) { ?>
<p><em>V systému nejsou žádné dobropisy.</em></p>
<?php
} else {
	// pouziju element spolecnej pro c_s_invoices/user_index a business_partners/user_view
	echo $this->element('c_s_credit_notes/index_table', array('c_s_credit_notes' => $credit_notes));
} ?>

<script type="text/javascript">
$("#search_form_show").click(function () {
	if ($('#search_form_c_s_credit_notes').css('display') == "none"){
		$("#search_form_c_s_credit_notes").show("slow");
	} else {
		$("#search_form_c_s_credit_notes").hide("slow");
	}
});
</script>