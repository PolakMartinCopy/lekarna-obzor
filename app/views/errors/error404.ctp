<?php $this->layout = 'content'?>

<h2>Stránka nenalezena</h2>
<p class="error">
	<strong>chyba: </strong>
	<?php echo sprintf(__("Požadovaná stránka %s nebyla nalezena.", true), "<strong>'{$message}'</strong>")?>
</p>
<p>
	Chyba byla zaznamenána a bude co nejdříve odstraněna. Pokračovat v prohlížení můžete na <a href="/">domovské stránce</a>.
	<br />Na <a href="/">domovskou stránku</a> se dostanete kliknutím <a href="/">zde</a>.
</p>