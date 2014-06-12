<?php
if (isset($this->params['named']['tab'])) {
	$tab_pos = $this->params['named']['tab'];
?>
	<script>
		$(function() {
			$( "#tabs" ).tabs("select", "#tabs-<?php echo $tab_pos?>");
		});
	</script>
<?php } ?>

<h1><?php echo $rep['Rep']['name']?></h1>

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Info</a></li>
		<li><a href="#tabs-2">Peněženka</a></li>
		<li><a href="#tabs-3">Sklad</a></li>
		<li><a href="#tabs-4">Nákupy / Prodeje</a></li>
		<li><a href="#tabs-5">Fasování</a></li>
	</ul>
	
	<?php /* TAB 1 ****************************************************************************************************************/ ?>
	<div id="tabs-1">
		<h2>Základní informace</h2>
		<?php
			echo $form->create('Rep', array('url' => array('controller' => 'reps', 'action' => 'view', $rep['Rep']['id'], 'tab' => 1)));
			echo $this->element('reps/add_edit_table');
			echo $form->hidden('Rep.id');
			echo $form->submit('Uložit');
			echo $form->end();
		?>
	</div>
	
	<?php /* TAB 2 ****************************************************************************************************************/ ?>
	<div id="tabs-2">
		<h2>Transakce v peněžence</h2>
		<button id="search_form_show_wallet_transactions">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/wallet_transactions', array('url' => array('controller' => 'reps', 'action' => 'view', $rep['Rep']['id'], 'tab' => 2)));
		
			echo $form->create('CSV', array('url' => array('controller' => 'wallet_transactions', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($wallet_transactions_find)));
			echo $form->hidden('fields', array('value' => serialize($wallet_transactions_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		
			if (empty($wallet_transactions)) { ?>
		<p><em>V systému nejsou žádné transakce v peněžence.</em></p>
			<?php } else {
				$paginator->options(array(
					'url' => array('tab' => 2, 0 => $rep['Rep']['id'])
				));
					
				$paginator->params['paging'] = $wallet_transactions_paging;
				$paginator->__defaultModel = 'WalletTransaction';
		
				echo $this->element('wallet_transactions/index_table');
			} ?>
		
		<script>
			$("#search_form_show_wallet_transactions").click(function () {
				if ($('#search_form_wallet_transactions').css('display') == "none"){
					$("#search_form_wallet_transactions").show("slow");
				} else {
					$("#search_form_wallet_transactions").hide("slow");
				}
			});
		</script>
	</div>
</div>