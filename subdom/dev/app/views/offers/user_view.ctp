<h1>Detaily nabídky</h1>
<ul>
	<li><?php echo $html->link('Detail obchodního jednání', array('controller' => 'business_sessions', 'action' => 'view', $offer['BusinessSession']['id']))?>
</ul>

<table class="left_heading">
	<tr>
		<th>ID</th>
		<td><?php echo $offer['Offer']['id']?></td>
	</tr>
	<tr>
		<th>Obsah</th>
		<td><?php echo $offer['Offer']['content']?></td>
	</tr>
	<tr>
		<th>Vytvořeno</th>
		<td><?php echo $offer['Offer']['created']?></td>
	</tr>
</table>

<h2>Dokumenty</h2>
<button id="search_form_show_documents">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['DocumentForm1']) ){
		$hide = '';
	}
?>
<div id="search_form_documents"<?php echo $hide?>>
	<?php echo $form->create('Document', array('url' => array('controller' => 'offers', 'action' => 'view', $offer['Offer']['id']))); ?>
	<table class="left_heading">
		<tr>
			<th>Název</th>
			<td><?php echo $form->input('DocumentForm1.Document.title', array('label' => false))?></td>
			<th>Vloženo</th>
			<td><?php echo $form->input('DocumentForm1.Document.created', array('label' => false, 'type' => 'text'))?></td>
		</tr>
		<tr>
			<td colspan="4"><?php echo $html->link('reset filtru', array('controller' => 'offers', 'action' => 'view', $offer['Offer']['id'], 'reset' => 'documents'))?></td>
		</tr>
	</table>
	<?php
		echo $form->hidden('DocumentForm1.Document.search_form', array('value' => 1));
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
		var dates = $( "#DocumentForm1DocumentCreated" ).datepicker({
			defaultDate: "+1w",
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "DocumentForm1DocumentCreated" ? "minDate" : "maxDate",
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

<?php if (empty($offer['Document'])) { ?>
<p><em>K této nabídce nejsou přiděleny žádné dokumenty.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Vytvořeno</th>
		<th>Název</th>
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = '';
	foreach ($offer['Document'] as $document) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );	
?>
	<tr<?php echo $odd?>>
		<td><?php echo $document['id']?></td>
		<td><?php echo $document['created']?></td>
		<td><?php echo $html->link($document['title'], '/files/documents/' . $document['name'])?></td>
		<td class="actions">
			<?php echo $html->link('Přejmenovat', array('controller' => 'documents', 'action' => 'rename', $document['id']))?>
			<?php echo $html->link('Smazat', array('controller' => 'documents', 'action' => 'delete', $document['id']), null, 'Opravdu chcete dokument ' . $document['title'] . ' smazat?')?>
		</td>
	</tr>
<?php } ?>
</table>
<?php } ?>

<h3>Nahrát dokument z disku</h3>
<?
echo $form->create('Offer', array('url' => array('controller' => 'offers', 'action' => 'view', $offer['Offer']['id'])));
echo $form->submit('Zobrazit', array('div' => false));
echo $form->text('Offer.document_fields', array('size' => '1')) . ' polí';
echo $form->end();

echo $form->Create('Document', array('url' => array('controller' => 'documents', 'action' => 'add'), 'type' => 'file')); ?>
<fieldset>
	<legend>Nový dokument z disku</legend>
	<table class="leftHeading" cellpadding="5" cellspacing="3">
		<tr>
			<th>&nbsp;</th>
			<td>
				<?
					if ( !isset($this->data['Offer']['document_fields']) OR $this->data['Offer']['document_fields'] > 10 OR $this->data['Offer']['document_fields'] < 1 ) {
						$this->data['Offer']['document_fields'] = 1;
					}
					for ( $i = 0; $i < $this->data['Offer']['document_fields']; $i++ ){
				?>
						<input type="file" name="data[Document][document<?php echo $i?>]" />
						<br />
				<?php 	echo $form->input('Document.document' . $i . '.title', array('label' => 'Titulek:', 'size' => 40));
					}
				?>
			</td>
		</tr>
	</table>
<?
	echo $form->hidden('Document.document_fields', array('value' => $this->data['Offer']['document_fields']));
	echo $form->hidden('Document.offer_id', array('value' => $offer['Offer']['id']));
?>
</fieldset>
<?
echo $form->submit('Nahrát dokument');
echo $form->end();
?>

		<h3>Nahrát dokument z webu</h3>
<?php 
		echo $form->create('Offer', array('url' => array('controller' => 'offers', 'action' => 'view', $offer['Offer']['id'])));
		echo $form->submit('Zobrazit', array('div' => false));
		echo $form->text('Offer.web_document_fields', array('size' => '1')) . ' polí';
		echo $form->end();
?>
		
		<?php echo $form->create('Document', array('url' => array('controller' => 'documents', 'action' => 'add_from_web')))?>
		<fieldset>
			<legend>Nový dokument z webu</legend>
			<table class="leftHeading" cellpadding="5" cellspacing="5">
			<?
				if ( !isset($this->data['Offer']['web_document_fields']) OR $this->data['Offer']['web_document_fields'] > 10 OR $this->data['Offer']['web_document_fields'] < 1 ) {
					$this->data['Offer']['web_document_fields'] = 1;
				}
				for ( $i = 0; $i < $this->data['Offer']['web_document_fields']; $i++ ){
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
			echo $form->hidden('Document.offer_id', array('value' => $offer['Offer']['id']));
		?>
		</fieldset>
<?php 	
	echo $form->submit('Nahrát dokument z webu');
	echo $form->end();
?>