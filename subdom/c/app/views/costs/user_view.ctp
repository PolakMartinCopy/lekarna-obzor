<h1>Detaily nákladu</h1>
<ul>
	<li><?php echo $html->link('Detail obchodního jednání', array('controller' => 'business_sessions', 'action' => 'view', $cost['BusinessSession']['id']))?>
</ul>

<table class="left_heading">
	<tr>
		<th>ID</th>
		<td><?php echo $cost['Cost']['id']?></td>
	</tr>
	<tr>
		<th>Popis</th>
		<td><?php echo $cost['Cost']['description']?></td>
	</tr>
	<tr>
		<th>Částka</th>
		<td><?php echo $cost['Cost']['amount']?></td>
	</tr>
	<tr>
		<th>Datum</th>
		<td><?php echo $cost['Cost']['date']?></td>
	</tr>
	<tr>
		<th>Vloženo</th>
		<td><?php echo $cost['Cost']['created']?></td>
	</tr>
	<tr>
		<th>Upraveno</th>
		<td><?php echo $cost['Cost']['modified']?></td>
	</tr>
</table>