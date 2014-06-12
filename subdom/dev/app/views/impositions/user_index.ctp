<?php
if (isset($this->params['named']['tab'])) {
	$tab_pos = $this->params['named']['tab'] - 1;
?>
	<script>
		$(function() {
			$( "#tabs" ).tabs("option", "selected", <?php echo $tab_pos?>);
		});
	</script>
<?php } ?>

	<script>
		$(document).ready(function() {
			$('.solve').click(function(e) {
				e.preventDefault();
				
				var anchor = $(this);
				var solution_id = $(this).attr('rel')
				$.ajax({
					'url' : '/user/solutions/solve/' + solution_id,
					'dataType' : 'json',
					'success' : function(data) {
						if (data.success) {
							// odstranim radek tabulky z pohledu
							tableRow = anchor.parent().parent();
							tableRow.remove();
							// vypisu flash
							$('#flashMessage').remove();
							$('#rightContent').prepend('<div id="flashMessage" class="message">' + data.message + '</div>');
						} else {
							alert(data.message);
						}
					},
					'error' : function(jqXHR, textStatus, errorThrown) {
						alert(textStatus);
					}
				});
			});
		});
	</script>

<h1>Úkoly</h1>

<?php echo $this->element('search_forms/impositions')?>

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">K vyřešení</a></li>
		<li><a href="#tabs-2">Zadané</a></li>
<?php if (isset($all_solutions)) { ?>
		<li><a href="#tabs-3">Všechny</a></li>
<?php } ?>
	</ul>

		
<?php /* TAB 1 ****************************************************************************************************************/ ?>
	<div id="tabs-1">
		<h2>Úkoly k vyřešení</h2>
		<?php 
			echo $form->create('CSV', array('url' => array('controller' => 'solutions', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($solutions_to_solve_find)));
			echo $form->hidden('fields', array('value' => serialize($export_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		?>

		<?php if (empty($solutions_to_solve)) { ?>
		<p><em>Pro tento den nemáte žádné úkoly k vyřešení.</em></p>
		<?php
		} else {
			echo $this->element('solutions', array('solutions' => $solutions_to_solve));
		} ?>
	</div>

<?php /* TAB 2 ****************************************************************************************************************/ ?>
	<div id="tabs-2">
		<h2>Mnou zadané úkoly</h2>
		<?php 
			echo $form->create('CSV', array('url' => array('controller' => 'solutions', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($assigned_solutions_find)));
			echo $form->hidden('fields', array('value' => serialize($export_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		?>
		<ul>
			<li><?php echo $html->link('Přidat úkol', array('controller' => 'impositions', 'action' => 'add'))?></li>
		</ul>

		<?php if (empty($assigned_solutions)) { ?>
		<p><em>Pro tento den jste nezadal žádné úkoly.</em></p>
		<?php
		} else {
			echo $this->element('solutions', array('solutions' => $assigned_solutions));
		} ?>
	</div>

<?php /* TAB 3 ****************************************************************************************************************/ ?>
	<div id="tabs-3">
		<?php if (isset($all_solutions)) { ?>
		<h2>Všechny úkoly</h2>
		<?php 
			echo $form->create('CSV', array('url' => array('controller' => 'solutions', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($all_solutions_find)));
			echo $form->hidden('fields', array('value' => serialize($export_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		?>
		<?php if (empty($all_solutions)) { ?>
		<p><em>V databázi pro tento den nejsou žádné úkoly.</em></p>
		<?php
		} else {
			echo $this->element('solutions', array('solutions' => $all_solutions));
		} ?>
		<?php } ?>
	</div>
</div>