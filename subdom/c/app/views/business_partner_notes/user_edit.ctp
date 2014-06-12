<h1>Upravit poznámku</h1>
<ul>
	<li><?php echo $this->Html->link('zpět na detail obchodního partnera', array('controller' => 'business_partners', 'action' => 'view', $note['BusinessPartnerNote']['business_partner_id'], 'tab' => 13))?></li>
</ul>
<?php echo $this->Form->create('BusinessPartnerNote')?>
<table>
	<tr>
		<td><?php echo $this->Form->input('BusinessPartnerNote.text', array('label' => false))?></td>
		<td>
			<?php echo $this->Form->hidden('BusinessPartnerNote.id')?>
			<?php echo $this->Form->submit('Uložit')?>
		</td>
	</tr>
</table>
<?php echo $this->Form->end()?>