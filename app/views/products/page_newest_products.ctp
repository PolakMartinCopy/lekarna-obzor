<?
	if ( !empty($products) ){
		switch ( $listing_style ){
			case "products_listing_grid":
//				echo '<div class="listingChoices">' . $html->link('zobrazit jednoduchý seznam', '/' . $category['Category']['url'] . '?ls=list') . '</div>';
			break;
			case "products_listing_list":
//				echo '<div class="listingChoices">' . $html->link('zobrazit tabulku s obrázky', '/' . $category['Category']['url']) . '</div>';
			break;
		}

		echo $this->element($listing_style);

	}
?>