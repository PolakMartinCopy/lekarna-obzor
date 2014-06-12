<h1>Přejmenovat dokument</h1>
<?php
$url = array('controller' => 'documents', 'action' => 'rename', $document['Document']['id']);

echo $form->create('Document', array('url' => $url)) ?>
<table class="left_heading">
	<tr>
		<th>Titulek</th>
		<td><?php echo $form->input('Document.title', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('Document.name', array('label' => false))?></td>
	</tr>
</table>
<?php 
echo $form->hidden('Document.id');
echo $form->submit('Přejmenovat');
echo $form->end();
?>