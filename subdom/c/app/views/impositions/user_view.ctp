<script>
	$(document).ready(function() {
		$('.solve').click(function(e) {
			e.preventDefault();
			var link = $(this);
			var solution_id = link.attr('rel');

			$.ajax({
				'url' : '/user/solutions/solve/' + solution_id,
				'dataType' : 'json',
				'success' : function(data) {
					if (data.success) {
						// upravim radek s resenim
						var linkSpan = link.parent();
						var tableRow = linkSpan.parent().parent();
						// odstranim span s odkazem na oznaceni reseni za "vyresene"
						linkSpan.remove();
						// a upravim textovy popis stavu reseni
						tableRow.find('.stateName').html(data.state_name);
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

<h1>Detail úkolu</h1>
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Info</a></li>
		<li><a href="#tabs-2">Dokumenty</a></li>
		<li><a href="#tabs-3">Řešení</a></li>
	</ul>
	
<?php /* TAB 1 ****************************************************************************************************************/ ?>
	<div id="tabs-1">
		<h2>Základní informace</h2>
		<table class="left_heading">
			<tr>
				<th>ID</th>
				<td><?php echo $imposition['Imposition']['id']?></td>
			</tr>
			<tr>
				<th>Předmět</th>
				<td><?php echo $imposition['Imposition']['title']?></td>
			</tr>
			<tr>
				<th>Popis</th>
				<td><?php
				// chci vykreslovat na obrazovku tagy, ale br ma stale fungovat jako novy radek
				$description = str_replace('<br/>', '##br/##', $imposition['Imposition']['description']);
				$description =  htmlspecialchars($description);
				$description = str_replace('##br/##', '<br/>', $description);
				echo $description;
				?></td>
			</tr>
			<tr>
				<th>Datum a čas vložení</th>
				<td><?php echo $imposition['Imposition']['created']?></td>
			</tr>
			<tr>
				<th>Zadavatel</th>
				<td><?php echo $imposition['User']['last_name'] . ' ' . $imposition['User']['first_name']?></td>
			</tr>
			<tr>
				<th>Řešitelé</th>
				<td>
					<?php if (empty($imposition['ImpositionsUser'])) { ?>
					<p><em>Nejsou vloženi žádní řešitelé.</em></p>
					<?php } else { ?>
					<ul>
					<?php foreach ($imposition['ImpositionsUser'] as $user) { ?>
					<li><?php echo $user['User']['last_name'] . ' ' . $user['User']['first_name']?></li>
					<?php } ?>
					</ul>
					<?php } ?>
				</td>
			<tr>
				<th>Stav</th>
				<td><?php echo $imposition['ImpositionState']['name']?></td>
			</tr>
			<tr>
				<th>Obchodní partner</th>
				<td><?php echo $html->link($imposition['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $imposition['BusinessPartner']['id']))?></td>
			</tr>
			<tr>
				<th>Rekurzivní?</th>
				<td><?php echo ($imposition['RecursiveImposition']['id']) ? 'ano' : 'ne'?></td>
			</tr>
			<?php if ($imposition['RecursiveImposition']['id']) { ?>
			<tr>
				<th>Rekurze od</th>
				<td><?php echo czech_date($imposition['RecursiveImposition']['from'])?></td>
			</tr>
			<tr>
				<th>Rekurze do</th>
				<td><?php
					if ($imposition['RecursiveImposition']['to']) {
						echo czech_date($imposition['RecursiveImposition']['to']);
					} else {
						echo '---';
					}
				?></td>
			</tr>
			<tr>
				<th>Interval</th>
				<td><?php echo $imposition['RecursiveImposition']['ImpositionPeriod']['name']?></td>
			</tr>
			<?php } ?>

		</table>

		<ul>
			<li><?php echo $html->link('Upravit úkol', array('controller' => 'impositions', 'action' => 'edit', $imposition['Imposition']['id']))?></li>
		</ul>
	</div>

<?php /* TAB 2 ****************************************************************************************************************/ ?>
	<div id="tabs-2">
		<h2>Dokumenty</h2>
		<button id="search_form_show_documents">vyhledávací formulář</button>
		<?php
			$hide = ' style="display:none"';
			if ( isset($this->data['DocumentForm']) ){
				$hide = '';
			}
		?>
		<div id="search_form_documents"<?php echo $hide?>>
			<?php echo $form->create('Document', array('url' => array('controller' => 'impositions', 'action' => 'view', $imposition['Imposition']['id'], 'tab' => 2))); ?>
			<table class="left_heading">
				<tr>
					<th>Název</th>
					<td><?php echo $form->input('DocumentForm.Document.title', array('label' => false))?></td>
					<th>Vloženo</th>
					<td><?php echo $form->input('DocumentForm.Document.created', array('label' => false, 'type' => 'text'))?></td>
				</tr>
				<tr>
					<td colspan="4"><?php echo $html->link('reset filtru', array('controller' => 'impositions', 'action' => 'view', $imposition['Imposition']['id'], 'reset' => 'documents'))?></td>
				</tr>
			</table>
			<?php
				echo $form->hidden('DocumentForm.Document.search_form', array('value' => 1));
				echo $form->submit('Vyhledávat');
				echo $form->end();
			?>
		</div>
		
		<script>
			$("#search_form_show_documents").click(function () {
				if ($('#search_form_documents').css('display') == "none"){
					$("#search_form_documents").show("slow");
				} else {
					$("#search_form_documents").hide("slow");
				}
			});
		
			$(function() {
				var dates = $( "#DocumentFormDocumentCreated" ).datepicker({
					defaultDate: "+1w",
					changeMonth: false,
					numberOfMonths: 1,
					onSelect: function( selectedDate ) {
						var option = this.id == "DocumentFormDocumentCreated" ? "minDate" : "maxDate",
							instance = $( this ).data( "datepicker" ),
							date = $.datepicker.parseDate(
								instance.settings.dateFormat ||
								$.datepicker._defaults.dateFormat,
								selectedDate, instance.settings );
						dates.not( this ).datepicker( "option", option, date );
					}
				});
			});
			$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
		</script>
		<?php if (empty($imposition['Document'])) { ?>
		<p><em>K tomuto úkolu nejsou přiděleny žádné dokumenty.</em></p>
		<?php } else { ?>
		<table class="top_heading">
			<tr>
				<th>ID</th>
				<th>Vloženo</th>
				<th>Titulek</th>
				<th>&nbsp;</th>
			</tr>
		<?php
			$odd = '';
			foreach ($imposition['Document'] as $document) { 
				$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
		?>
			<tr<?php echo $odd?>>
				<td><?php echo $document['id']?></td>
				<td><?php echo $document['created']?></td>
				<td><?php echo $html->link($document['title'], '/files/documents/' . $document['name'])?></td>
				<td class="asctions">
					<?php echo $html->link('Přejmenovat', array('controller' => 'documents', 'action' => 'rename', $document['id']))?>
					<?php echo $html->link('Smazat', array('controller' => 'documents', 'action' => 'delete', $document['id']), null, 'Opravdu chcete dokument ' . $document['title'] . ' smazat?')?>
				</td>
			</tr>
		<?php } ?>
		</table>
		<?php } ?>
	
		<h3>Nahrát dokument z disku</h3>
<?
		echo $form->create('Imposition', array('url' => array('controller' => 'impositions', 'action' => 'view', $imposition['Imposition']['id'])));
		echo $form->submit('Zobrazit', array('div' => false));
		echo $form->text('Imposition.document_fields', array('size' => '1')) . ' polí';
		echo $form->end();
	
		echo $form->Create('Document', array('url' => array('controller' => 'documents', 'action' => 'add'), 'type' => 'file')); ?>
		<fieldset>
			<legend>Nový dokument z disku</legend>
			<table class="leftHeading" cellpadding="5" cellspacing="3">
			<?
				if ( !isset($this->data['Imposition']['document_fields']) OR $this->data['Imposition']['document_fields'] > 10 OR $this->data['Imposition']['document_fields'] < 1 ) {
					$this->data['Imposition']['document_fields'] = 1;
				}
				for ( $i = 0; $i < $this->data['Imposition']['document_fields']; $i++ ){
			?>
				<tr>
					<th>&nbsp;</th>
					<td>
						<input type="file" name="data[Document][document<?php echo $i?>]" />
						<br/>
						<?php 	echo $form->input('Document.document' . $i . '.title', array('label' => 'Titulek:', 'size' => 40))?>
					</td>
				</tr>
				<?php }?>
			</table>
<?
		echo $form->hidden('Document.document_fields', array('value' => $this->data['Imposition']['document_fields']));
		echo $form->hidden('Document.imposition_id', array('value' => $imposition['Imposition']['id']));
?>
		</fieldset>
<?
		echo $form->submit('Nahrát dokument(y) z disku');
		echo $form->end();
?>
	
		<h3>Nahrát dokument z webu</h3>
<?php 
		echo $form->create('Imposition', array('url' => array('controller' => 'impositions', 'action' => 'view', $imposition['Imposition']['id'])));
		echo $form->submit('Zobrazit', array('div' => false));
		echo $form->text('Imposition.web_document_fields', array('size' => '1')) . ' polí';
		echo $form->end();
?>
		
		<?php echo $form->create('Document', array('url' => array('controller' => 'documents', 'action' => 'add_from_web')))?>
		<fieldset>
			<legend>Nový dokument z webu</legend>
			<table class="leftHeading" cellpadding="5" cellspacing="5">
			<?
				if ( !isset($this->data['Imposition']['web_document_fields']) OR $this->data['Imposition']['web_document_fields'] > 10 OR $this->data['Imposition']['web_document_fields'] < 1 ) {
					$this->data['Imposition']['web_document_fields'] = 1;
				}
				for ( $i = 0; $i < $this->data['Imposition']['web_document_fields']; $i++ ){
					if ($i > 0) {
			?>
				<tr>
					<td colspan="2">
						<hr/>
					</td>
				</tr>
			<?php 
					}
			?>
				
				<tr>
					<th>URL</th>
					<td><?php echo $form->input('Document.data.' . $i . '.url', array('label' => false, 'size' => 100))?></td>
				</tr>
				<tr>
					<th>Název souboru</th>
					<td><?php echo $form->input('Document.data.' . $i . '.name', array('label' => false, 'size' => 50))?></td>
				</tr>
				<tr>
					<th>Titulek dokumentu</th>
					<td><?php echo $form->input('Document.data.' . $i . '.title', array('label' => false, 'size' => 50))?></td>
				</tr>
			<?php } ?>
			</table>
		<?php
			echo $form->hidden('Document.imposition_id', array('value' => $imposition['Imposition']['id']));
		?>
		</fieldset>
<?php 	
	echo $form->submit('Nahrát dokument z webu');
	echo $form->end();
?>

	</div>
<?php /* TAB 3 ****************************************************************************************************************/ ?>
	<?php $back_link = array('controller' => 'impositions', 'action' => 'view', $imposition['Imposition']['id'])?>
	<div id="tabs-3">
		<h1>Řešení úkolu</h1>
		<table class="top_heading">
			<tr>
				<th>ID</th>
				<th>Termín splnění</th>
				<th>Stav</th>
				<th>&nbsp;</th>
			</tr>
			<?php foreach ($imposition['Solution'] as $solution) { ?>
			<tr>
				<td><?php echo $solution['id']?></td>
				<td><?php echo czech_date($solution['accomplishment_date'])?></td>
				<td class="stateName"><?php echo $solution['SolutionState']['name']?></td>
				<td><?php 
					if ($solution['solution_state_id'] == 2) {
						echo '<span>' . $html->link('Vyřešeno', '#', array('class' => 'solve', 'rel' => $solution['id'])) . ' | </span>';
					}
					echo $html->link('Upravit', array('controller' => 'solutions', 'action' => 'edit', $solution['id'], 'back_link' => base64_encode(serialize($back_link)))) . ' | ';
					echo $html->link('Odstranit', array('controller' => 'solutions', 'action' => 'delete', $solution['id'], 'back_link' => base64_encode(serialize($back_link))), null, 'Opravdu chcete požadavek na vyřešení odstranit?');
				?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
</div>