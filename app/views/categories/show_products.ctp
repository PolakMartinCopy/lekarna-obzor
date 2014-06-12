<?
	if ( !empty($products) ){
		switch ( $listing_style ){
			case "products_listing_grid":
				echo '<div class="listingChoices"><a href="/categories/show_products/' . $category['Category']['id'] . '/ls:list">zobrazit jednoduchý seznam</a></div>';
			break;
			case "products_listing_list":
				echo '<div class="listingChoices"><a href="/categories/show_products/' . $category['Category']['id'] . '">zobrazit tabulku s obrázky</a></div>';
			break;
		}

		echo $this->element($listing_style);
	} else {
?>
	<div id="mainContentWrapper">
		<p>Pokračujte ve výběru:</p>
		<div id="subcategoriesWrapper">
			<?
				$first = true;
				foreach ( $subcategories as $subcategory ){
					if ( !$first ){
						echo ' | ';
					}
					echo '<a href="/categories/show_products/' . $subcategory['Category']['id'] . '">' . $subcategory['Category']['name'] . '</a>';
					$first = false;
				}
			?>
		</div>
		<h2>Novinky z kategorie:</h2>
<?
		$this->set('products', $products_new);
//		$products = $products_new;
		echo $this->element('products_listing_grid');?>
	</div>
<?
	}
?>