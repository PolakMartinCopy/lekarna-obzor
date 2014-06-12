<div class="text"><h1>Výsledek vyhledávání</h1></div>
<?
if ( !empty($this->data) ){
	if ( empty($products) ){
		echo '<p>Nebyl nalezen žádný produkt odpovídající Vašemu dotazu.</p>';
	} else {
		echo $this->element('products_listing_grid', array('products' => $products));
	}
} ?>