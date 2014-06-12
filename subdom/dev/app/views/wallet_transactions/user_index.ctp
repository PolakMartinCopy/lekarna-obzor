<h1>Transakce v peněžence</h1>
<button id="search_form_show">vyhledávací formulář</button>
<?php
	echo $this->element('search_forms/wallet_transactions', array('url' => array('controller' => 'wallet_transactions', 'action' => 'index')));

	echo $form->create('CSV', array('url' => array('controller' => 'wallet_transactions', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($wallet_transactions)) { ?>
<p><em>V systému nejsou žádné transakce v peněžence.</em></p>
<?php } else {
	echo $this->element('wallet_transactions/index_table');
} ?>

<script>
$("#search_form_show").click(function () {
	if ($('#search_form_wallet_transactions').css('display') == "none"){
		$("#search_form_wallet_transactions").show("slow");
	} else {
		$("#search_form_wallet_transactions").hide("slow");
	}
});
</script>