<?
/**
	menu pro administraci
*/
?>

<ul id="categoriesMenu">
	<li><a href="/admin/categories/index">KATALOG</a></li>
<?
	// prochazim si vsechny kategorie
	foreach ( $categories as $category ){
		// nastavim si isActive
		$isActive = false;
		if ( $category['Category']['id'] == $opened_category_id ){
			$isActive = true;
		}

		// nadefinuju si zakladni odsazeni
		$spaces = '&nbsp;&nbsp;';

		// prenastavim zakladni odsazeni podle
		// hloubky konkretni kategorie
		$offset = array_search($category['Category']['parent_id'], $ids_to_find);
		for ( $i = 0; $i < $offset; $i++ ){
			$spaces .= '&nbsp;&nbsp;';
		}

		// otevru si list item
		echo '<li>';
		
		// otestuju, jestli je to aktivni kategorie
		// pokud ano, potrebuju to tucne
		if ( $isActive ){
			echo '<strong>';
		}
		
		// vypisu si odsazeni
		echo $spaces;
		
		// vypisu link
		echo '<a href="/admin/categories/list_products/' . $category['Category']['id'] . '">' . $category['Category']['name'] . '</a>';
		
		// vypisu si pocet produktu v kategorii
		echo '(' . $category['Category']['activeProductCount'] . '/' . $category['Category']['productCount'] . ')';

		// uzavru si strong, pokud se jedna o aktivni kategorii
		if ( $isActive ){
			// vypisu si odkaz na operace s kategorii
			echo '&nbsp;&nbsp;<a href="/admin/categories/view/' . $category['Category']['id'] . '">&gt;&gt;&gt;</a>';

			echo '</strong>';
		}
		
		// uzavru list item
		echo '</li>';
	}
?>
</ul>