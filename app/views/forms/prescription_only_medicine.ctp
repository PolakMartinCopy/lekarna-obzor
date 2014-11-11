<h1>Objednávka léků na předpis</h1>
<p>Vážení zákazníci, nyní Vám přinášíme možnost <strong>objednání Vašich léků na předpis</strong>, takže budete mít jistotu, že nebudete cestovat nadvakrát. Léky pro Vás připravíme k odběru
v naší lékárně a o průběhu zpracování Vás budeme informovat.</p>
<p>Chcete zjistit výši doplatku na Vaše léky nebo cenu volně prodejného preparátu v naší lékárně? Využijte jednoduchý formulář a <strong>zjistěte, kolik s námi ušetříte</strong>!</p>
<p>Vyplnění a odeslání formuláře <strong>není závazná objednávka</strong>. Na základě Vámi vložených údajů Vás kontaktují naši pracovníci a další postup s Vámi dohodnou emailem nebo telefonicky.</p>

<p><strong>U léků vázaných na recept je nutné při převzetí objednávky předložit recept.</strong></p>
<p>V případě větších objednávek lze po domluvě zajistit <strong>dovoz domů</strong>.</p>
<?php echo $this->Form->create('Form')?>
<table>
	<tr>
		<th>Jméno a příjmení<sup>*</sup></th>
		<td><?php echo $this->Form->input('Form.name', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr>
		<th>Telefon<sup>**</sup></th>
		<td><?php echo $this->Form->input('Form.phone', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Email<sup>**</sup></th>
		<td><?php echo $this->Form->input('Form.email', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr>
		<th>Zpráva<sup>*</sup></th>
		<td><?php echo $this->Form->input('Form.message', array('label' => false, 'type' => 'textarea', 'cols' => 46, 'rows' => 10))?></td>
	</tr>
</table>
<ul>
	<li><sup>*</sup> - pole musí být vyplněno</li>
	<li><sup>**</sup> - alespoň jedno z polí musí být vyplněno</li>
</ul>
<?php echo $this->Form->submit('Odeslat požadavek')?>
<?php echo $this->Form->end()?>
 