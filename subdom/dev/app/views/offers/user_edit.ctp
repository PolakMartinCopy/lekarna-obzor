<h1>Upravit nabídku</h1>
<ul>
	<li><?php echo $html->link('Detaily obchodního jednání', array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id']))?></li>
</ul>

<?php echo $form->create('Offer', array('url' => array('controller' => 'offers', 'action' => 'edit')))?>
<table class="left_heading">
	<tr>
		<th>Popis</th>
		<td><?php echo $form->input('Offer.content', array('label' => false)) ?></td>
	</tr>
</table>
<?php echo $form->hidden('Offer.id')?>
<?php echo $form->submit('Upravit')?>
<?php echo $form->end() ?>