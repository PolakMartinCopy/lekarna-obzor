<h2><?=$product['Product']['name'] ?> - přiložené dokumenty</h2>
<?
	if ( empty($product['ProductDocument']) ){
		echo '<p>K produktu není zatím přiložen žádný dokument.</p>';
	} else {
?>
		<table class="topHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					název
				</th>
				<th>
					typ
				</th>
				<th>
					&nbsp;
				</th>
			</tr>
<?
			foreach ( $product['ProductDocument'] as $document ){
				echo '<tr>
					<td>
						 ' . $document['name'] . '
					</td>
					<td>
						' . $document['type'] . '
					</td>
					<td>
						<a href="/admin/products/documents_delete/' . $document['id'] . '">vymazat</a> |
						<a href="/admin/products/documents_edit/' . $document['id'] . '">upravit</a>
					</td>
				</tr>';
			}
?>
		</table>
<?
	}
?>
<h3>Přiložit dokument</h3>
<?
	echo $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'documents_add', 'id' => $id), 'type' => 'file'));
	echo 'zvolte typ: ' . $form->select('Document.type', array('doc' => 'Dokument Word', 'pdf' => 'Dokument PDF', 'xls' => 'Dokument Excel'), array(), array(), false);
	echo '<br />';
	echo 'přikládaný soubor:' . $form->file('Document.name');
	echo '<br />';
	echo $form->submit('vložit');
	echo $form->end();
?>