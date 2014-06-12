<h2>Administrace přesměrování</h2>
<div class="actions">
	<ul>
		<li><?=$html->link('Vytvořit nové přesměrování', array('action' => 'add'))?></li>
	</ul>
</div>
<table class="topHeading" cellpadding="5" cellspacing="3">
	<tr>
		<th>ID</th>
		<th>odkud</th>
		<th>kam</th>
		<th>&nbsp;</th>
	</tr>
<?
	foreach ( $redirects as $redirect ){
?>
		<tr>
			<td>
				<?=$redirect['Redirect']['id'] ?>
			</td>
			<td>
				<?=$html->link($redirect['Redirect']['request_uri'], '/' . $redirect['Redirect']['request_uri']) ?>
			</td>
			<td>
				<?=$html->link($redirect['Redirect']['target_uri'], '/' . $redirect['Redirect']['target_uri']) ?>
			</td>
			<td>
				<?=$html->link('editovat', array('controller' => 'redirects', 'action' => 'edit', $redirect['Redirect']['id'], 'admin' => true)) ?>
				<br />
				<?=$html->link('smazat', 
					array(
						'controller' => 'redirects',
						'action' => 'delete',
						$redirect['Redirect']['id'],
						'admin' => true
					), // target
					array(), // html attributes
					'Opravdu chcete toto přesměrování vymazat?' // confirm message
				)?>
			</td>
		</tr>
<?
	} 
?>

</table>
<div class="actions">
	<ul>
		<li><?=$html->link('Vytvořit nové přesměrování', array('action' => 'add'))?></li>
	</ul>
</div>