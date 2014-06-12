<h2>Seznam komentářů</h2>
<ul style="font-size:11px;">
	<li>
		"Notifikace" - Ukazuje, zda byla odpoved odeslana mailem dotazovateli. Kliknutim na `NE` odeslete zakaznikovi mail s odpovedi.
	</li>
	<li>
		"Odpoved" - Ukazuje, zda byla ke komentari jiz vlozena odpoved.
	</li>
	<li>
		"Publikovan" - Ukazuje, zda je dotaz/komentar viditelny na internetovych strankach.
	</li>
</ul>
<table class="topHeading">
	<tr>
		<th>ID</th>
		<th>Vytvořen</th>
		<th>Předmět</th>
		<th>Autor</th>
		<th>Notifikace</th>
		<th>Odpoveď</th>
		<th>Publikovan</th>
		<th>&nbsp;</th>
	</tr>
	<? foreach ($comments as $comment) { ?>
	<tr>
		<td><?=$comment['Comment']['id'] ?></td>
		<td><?=$comment['Comment']['created'] ?></td>
		<td><?=$comment['Comment']['subject'] ?></td>
		<td><?=$comment['Comment']['author'] ?></td>
		<td><?=( $comment['Comment']['sent'] == '0' ? $html->link('<span style="color:red">NE</span>', array('controller' => 'comments', 'action' => 'notify', $comment['Comment']['id']), array('escape' => false), false) : '<span style="color:green">ANO</span>' ) ?>
		<td><?=( empty($comment['Comment']['reply']) ? '<span style="color:red">NE</span>' : '<span style="color:green">ANO</span>' ) ?>
		<td><?=( $comment['Comment']['confirmed'] == '0' ? '<span style="color:red">NE</span>' : '<span style="color:green">ANO</span>' ) ?>
		<td>
			<?=$html->link('zobrazit', array('controller' => 'comments', 'action' => 'view', $comment['Comment']['id'])) ?>
			<?=$html->link('smazat', array('controller' => 'comments', 'action' => 'admin_delete', $comment['Comment']['id']), array(), 'Opravdu chcete tenoto komentář smazat?') ?>
		</td>

	</tr>
		
	<? } ?>
</table>