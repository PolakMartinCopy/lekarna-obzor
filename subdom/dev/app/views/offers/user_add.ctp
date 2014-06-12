<h1>Přidat nabídku</h1>
<ul>
	<li><?php echo $html->link('Detaily obchodního jednání', array('controller' => 'business_sessions', 'action' => 'view', $business_session_id))?></li>
</ul>

<?php echo $form->create('Offer', array('url' => array('controller' => 'offers', 'action' => 'add', 'business_session_id' => $business_session_id), 'type' => 'file'))?>
<table class="left_heading">
	<tr>
		<th>Popis</th>
		<td><?php echo $form->input('Offer.content', array('label' => false)) ?></td>
	</tr>
	<tr>
		<th>Název dokumentu</th>
		<td><?php echo $form->input('Document.0.title', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Dokument</th>
		<td><?php echo $form->input('Document.0.name', array('type' => 'file', 'label' => false))?></td>
	</tr>
</table>
<?php echo $form->hidden('Offer.business_session_id', array('value' => $business_session_id))?>
<?php echo $form->submit('Uložit')?>
<?php echo $form->end() ?>