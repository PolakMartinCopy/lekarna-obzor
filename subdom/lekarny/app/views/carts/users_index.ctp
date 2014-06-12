<h1>Seznam rozpracovaných objednávek</h1>

<?
	if ( !empty($carts) ){
?>
	<table class="top_headed" cellpadding="5" cellspacing="3">
		<tr>
			<th>
				ID
			</th>
			<th>
				Vytvořena
			</th>
			<th>
				Poslední modifikace
			</th>
			<th>
				Název
			</th>
			<th>
				&nbsp;
			</th>
		</tr>
<?
		foreach( $carts as $cart ){
?>
		<tr>
			<td>
				<?=$cart['Cart']['id'] ?>
			</td>
			<td>
				<?=$cart['Cart']['created'] ?>
			</td>
			<td>
				<?=$cart['Cart']['modified'] ?>
			</td>
			<td>
				<?= empty ( $cart['Cart']['name'] ) ? '<em>nepojmenovaná</em>' : $cart['Cart']['name'] ?>
			</td>
			<td>
				<?=$html->link('přejmenovat', array('users' => true, 'controller' => 'carts', 'action' => 'edit_name', $cart['Cart']['id'])) ?><br />
				<?=$html->link('upravovat', array('users' => true, 'controller' => 'carts', 'action' => 'set_active', $cart['Cart']['id'])) ?><br />
				<?=$html->link('smazat', array('users' => true, 'controller' => 'carts', 'action' => 'delete', $cart['Cart']['id'])) ?>
			</td>
		</tr>
<?
		}
?>
	</table>
<?
	} else {
?>
	<p>V současnosti nemáte žádné rozpracované objednávky.</p>
<?
	}
?>