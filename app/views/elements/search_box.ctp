<?php
$wide = ' wide';
if ($_SERVER['REQUEST_URI'] == HP_URI) {
	$wide = '';
}?>

<div id="search_box">
	<?php echo $this->Form->create('Search', array('url' => array('controller' => 'searches', 'action' => 'do_search'), 'encoding' => false, 'type' => 'get'));?>
	<table cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td><?php
			echo $this->Form->input('Search.q', array('label' => false, 'class' => 'search_query' . $wide, 'type' => 'text', 'value' => (isset($this->data['Search']['q']) ? $this->data['Search']['q'] : 'zadejte dotaz'), 'div' => false, 'after' => '&nbsp;'));
			echo $this->Form->submit('Vyhledat', array('class' => 'search_submit', 'div' => false));			
			?></td>
		</tr>
	</table>
	<?php echo $this->Form->end(); ?>
</div>