<h2>Detaily kategorie: <?php echo $category['Category']['name']?>(ID:<?php echo $category['Category']['id']?>)</h2>
<? if ( $category['Category']['id'] != 1 ){
?>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Upravit kategorii', true),   array('action'=>'edit', $category['Category']['id'])); ?> </li>
		<li><? echo $html->link('Posun nahoru v hierarchii', array('action' => 'moveup', $category['Category']['id'])); ?></li>
		<li><? echo $html->link('Posun dolů v hierarchii', array('action' => 'movedown', $category['Category']['id'])); ?></li>
		<li><? echo $html->link('Přesun do jiného uzlu', array('action' => 'movenode', $category['Category']['id'])); ?></li>
		<li><?php echo $html->link(__('Smazat kategorii', true), array('action'=>'delete', $category['Category']['id']), null, __('Opravdu chcete smazat tuto kategorii?', true)); ?> </li>
		<li><?php echo $html->link(__('Vložit novou podkategorii', true), array('action'=>'add', $category['Category']['id'])); ?> </li>
		<li><?php echo $html->link(__('Zobrazit produkty', true), array('controller' => 'categories', 'action'=>'list_products', $category['Category']['id'])); ?> </li>
		<li><?php echo $html->link('Vložit nový produkt', array('controller' => 'products', 'action' => 'add', $category['Category']['id'])); ?> </li>
		<li><?php echo $html->link('Importovat nový produkt', array('controller' => 'products', 'action' => 'import', $category['Category']['id'])); ?> </li>
	</ul>
</div>
<?
} else {
?>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Vložit novou podkategorii', true), array('action'=>'add', $category['Category']['id'])); ?> </li>
	</ul>
</div>
<h3>Základní stránka administrace</h3>
	<p>V levém menu zvolte předmět administrace. Jméno kategorie je odkazem na administraci dané kategorie,
	číslo za kategorií vyjadřuje počet produktů v dané kategorii a je odkazem na seznam produktů a operace s nimi.</p>
<?
}?>
