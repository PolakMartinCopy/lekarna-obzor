<?
class RelatedProductsController extends AppController {

	var $name = 'RelatedProducts';
	
	function admin_generate() {
		$truncate = 'TRUNCATE TABLE related_products';
		$this->RelatedProduct->query($truncate);
		// vytahnu si kategorie, kde jsou umisteny produkty urcene na prodej
		
		$categories = $this->RelatedProduct->Product->CategoriesProduct->Category->find('all', array(
			'contain' => array(),
			'fields' => array('id', 'name')
		));
		
		foreach ($categories as $category) {
			// pro kazdou z techto kategorii vytahnu vsechny produkty v ni
			$products = $this->RelatedProduct->Product->CategoriesProduct->find('all', array(
				'conditions' => array(
					'CategoriesProduct.category_id' => $category['Category']['id'],
					'Product.active' => true
				),
				'contain' => array('Product'),
				'fields' => array('Product.id', 'Product.name', 'Product.retail_price_with_dph', 'Product.discount_common', 'Product.discount_member')
			));
			
			if (!empty($products)) {
				foreach ($products as $key => $product) {
					$products[$key]['Product']['discount_price'] = $this->RelatedProduct->Product->assign_discount_price($product);
				}

				usort($products, array('RelatedProductsController', 'sort_by_final_price_asc'));

				// projdu produkty a pokud nema produkt pribuzne, budu ukladat pribuzne produkty podle kontextu v poli
				foreach ($products as $index => $product) {
					$related_count = $this->RelatedProduct->find('count', array(
						'conditions' => array('RelatedProduct.product_id' => $product['Product']['id']),
						'contain' => array()
					));
					
					if ($related_count == 0) {
						// musim kontrolovat, jestli jsem nenarazil na hranice pole!!!
						$related_products = array();
						// pokud je produkt na zacatku pole, vezmu 5 produktu za nim
						if ($index == 0) {
							$rest = 5;
						} elseif ($index == 1) {
							$related_products[] = $products[0];
							$rest = 4;
						} elseif ($index == count($products)-3 ) {
							if (isset($products[$index-3])) {
								$related_products[] = $products[$index-3];
							}
							if (isset($products[$index-2])) {
								$related_products[] = $products[$index-2];
							}
							if (isset($products[$index-1])) {
								$related_products[] = $products[$index-1];
							}
						} elseif ($index == count($products)-2 ) {
							if (isset($products[$index-4])) {
								$related_products[] = $products[$index-4];
							}
							if (isset($products[$index-3])) {
								$related_products[] = $products[$index-3];
							}
							if (isset($products[$index-2])) {
								$related_products[] = $products[$index-2];
							}
							if (isset($products[$index-1])) {
								$related_products[] = $products[$index-1];
							}
						} elseif ($index == count($products)-1 ) {
							if (isset($products[$index-5])) {
								$related_products[] = $products[$index-5];
							}
							if (isset($products[$index-4])) {
								$related_products[] = $products[$index-4];
							}
							if (isset($products[$index-3])) {
								$related_products[] = $products[$index-3];
							}
							if (isset($products[$index-2])) {
								$related_products[] = $products[$index-2];
							}
							if (isset($products[$index-1])) {
								$related_products[] = $products[$index-1];
							}
						} else {
							if (isset($products[$index-2])) {
								$related_products[] = $products[$index-2];
							}
							if (isset($products[$index-1])) {
								$related_products[] = $products[$index-1];
							}
							$rest = 3;
						}
						
						$j = $index+1;
						$end = $j+$rest-1;
						while ($j<=(count($products)-1) && $j<=$end) {
							$related_products[] = $products[$j];
							$j++;
						}
				
						foreach ($related_products as $related_product) {
							$this->RelatedProduct->create();
							$save = array(
								'RelatedProduct' => array(
									'product_id' => $product['Product']['id'],
									'related_product_id' => $related_product['Product']['id']
								)
							);
							$this->RelatedProduct->save($save);
						}
					}
				}
			}
		}
		die('hotovo');
	}
	
	function sort_by_final_price_asc($a, $b){
		$a_final_price = $a['Product']['retail_price_with_dph'];
		if ( !empty($a['Product']['discount_price']) ){
			$a_final_price = $a['Product']['discount_price'];
		}
		
		$b_final_price = $b['Product']['retail_price_with_dph'];
		if ( !empty($b['Product']['discount_price']) ){
			$b_final_price = $b['Product']['discount_price'];
		}
		
		return $b_final_price < $a_final_price;
	}
}
?>