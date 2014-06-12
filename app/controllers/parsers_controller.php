<?php
class ParsersController extends AppController {
	var $name = 'Parsers';
	
	function truncate_all() {
		$truncate_categories = 'TRUNCATE TABLE categories';
		$truncate_categories_products = 'TRUNCATE TABLE categories_products';
		$truncate_products = 'TRUNCATE TABLE products';
		$truncate_images = 'TRUNCATE TABLE images';
		$truncate_subproducts = 'TRUNCATE TABLE subproducts';
		$truncate_attributes_subproducts = 'TRUNCATE TABLE attributes_subproducts';
		$truncate_attributes = 'TRUNCATE TABLE attributes';
		$truncate_options = 'TRUNCATE TABLE options';
		
		$this->Parser->query($truncate_categories);
		$this->Parser->query($truncate_categories_products);
		$this->Parser->query($truncate_products);
		$this->Parser->query($truncate_images);
		$this->Parser->query($truncate_subproducts);
		$this->Parser->query($truncate_attributes_subproducts);
		$this->Parser->query($truncate_attributes);
		$this->Parser->query($truncate_options);
	}
	
	function parse_categories() {
		App::import('Model', 'Category');
		$this->Category = &new Category;
		
		$truncate_categories = 'TRUNCATE TABLE categories';
		$this->Parser->query($truncate_categories);
		
		// vlozim korenovou kategorii
		$root_category = array(
			'Category' => array(
				'name' => 'ROOT',
				'parent_id' => 0
			)
		);
		$this->Category->save($root_category);
		
		$root_category_id = $this->Category->id;
		
		// vytahnu kategorie z top bazeny
		$tb_categories = $this->Parser->query("
			SELECT *
			FROM tb_categories_lng
			ORDER BY category_id ASC
		");
		// u kazde kategorie z tb
		foreach ($tb_categories as $tb_category) {
			// zjistim jejiho rodice
			$tb_parent = $this->Parser->query("
				SELECT *
				FROM tb_category_dependences
				WHERE category_id = " . $tb_category['tb_categories_lng']['category_id'] . "
			");
			
			$parent_id = $root_category_id;
			if (!empty($tb_parent)) {
				$root_id = $tb_parent[0]['tb_category_dependences']['root_id'];
				// zjistim id rodice v me db
				$parent = $this->Category->find('first', array(
					'conditions' => array('Category.tb_id' => $root_id),
					'contain' => array(),
				));
				if (empty($parent)) {
					debug($tb_category); die('nemuzu najit rodicovsky uzel');
				}
				$parent_id = $parent['Category']['id'];
			}

			$new_category = array(
				'Category' => array(
					'parent_id' => $parent_id,
					'tb_id' => $tb_category['tb_categories_lng']['category_id'],
					'name' => $tb_category['tb_categories_lng']['name'],
					'heading' => $tb_category['tb_categories_lng']['name'],
					'breadcrumb' => $tb_category['tb_categories_lng']['name'],
					'title' => $tb_category['tb_categories_lng']['name'],
					'description' => $tb_category['tb_categories_lng']['name'],
					'content' => $tb_category['tb_categories_lng']['description']
				)
			);
			
			$this->Category->create();
			if ($this->Category->save($new_category)) {
				$new_category['Category']['url'] = strip_diacritic($new_category['Category']['title']) . '-c' . $this->Category->id;
				$this->Category->save($new_category);
			} else {
				debug($new_category);
				die('ulozeni kategorie se nezdarilo');
			}
		}
		die('hotovo');
	}
	
	function parse_products() {
/*		$truncate_categories_products = 'TRUNCATE TABLE categories_products';
		$truncate_products = 'TRUNCATE TABLE products';
		$truncate_images = 'TRUNCATE TABLE images';
		$truncate_subproducts = 'TRUNCATE TABLE subproducts';
		$truncate_attributes_subproducts = 'TRUNCATE TABLE attributes_subproducts';
		$truncate_attributes = 'TRUNCATE TABLE attributes';
		$truncate_options = 'TRUNCATE TABLE options';
		
		$this->Parser->query($truncate_categories_products);
		$this->Parser->query($truncate_products);
		$this->Parser->query($truncate_images);
		$this->Parser->query($truncate_subproducts);
		$this->Parser->query($truncate_attributes_subproducts);
		$this->Parser->query($truncate_attributes);
		$this->Parser->query($truncate_options);*/
		
		App::import('Model', 'Product');
		$this->Product = &new Product;
		
		$processed = $this->Product->find('all', array(
			'contain' => array(),
			'fields' => array('id', 'tb_id')
		));
		
		$processed = Set::extract('/Product/tb_id', $processed);
		
		$where = '';
		if (!empty($processed)) {
			$where = ' WHERE product_id NOT IN (' . implode(',', $processed) . ')';
		}
		
		// vytahnu si data k produktum z top-bazeny, ktere jeste nemam v db
		$tb_products = $this->Parser->query("
			SELECT *
			FROM tb_products_lng" . $where);

		foreach ($tb_products as $tb_product) {
			// sestavim novy produkt
			$new_product = array(
				'Product' => array(
					'tb_id' => $tb_product['tb_products_lng']['product_id'],
					'name' => $tb_product['tb_products_lng']['name'],
					'heading' => $tb_product['tb_products_lng']['name'],
					'breadcrumb' => $tb_product['tb_products_lng']['name'],
					'related_name' => $tb_product['tb_products_lng']['name'],
					'zbozi_name' => $tb_product['tb_products_lng']['name'],
					'title' => $tb_product['tb_products_lng']['detail_name'],
					'short_description' => $tb_product['tb_products_lng']['short_description'],
					'description' => $tb_product['tb_products_lng']['description'],
					'retail_price_with_dph' => $tb_product['tb_products_lng']['price'],
					'tax_class_id' => ($tb_product['tb_products_lng']['vat'] == 'vat' ? 1 : 2),
					'active' => $tb_product['tb_products_lng']['active'],
					'availability_id' => 1
				)
			);
			
			if (empty($new_product['Product']['short_description'])) {
				$new_product['Product']['short_description'] = $new_product['Product']['name'];
			}
			
			$this->Product->create();
			if ($this->Product->save($new_product)) {
				 $new_product['Product']['url'] = strip_diacritic($new_product['Product']['name']) . '-p' . $this->Product->id;
				 $this->Product->save($new_product);
			} else {
				debug($new_product);
				debug($this->Product->validationErrors);
				die('ulozeni produktu se nezdarilo');
			}
			
			// zjistim, do kterych kategorii ho vlozit
			$tb_categories = $this->Parser->query("
				SELECT *
				FROM tb_category_products
				WHERE product = " . $tb_product['tb_products_lng']['product_id'] . "
			");
			
			$tb_categories_ids = Set::extract('/tb_category_products/category', $tb_categories);
			
			$categories = $this->Product->CategoriesProduct->Category->find('all', array(
				'conditions' => array('Category.tb_id' => $tb_categories_ids),
				'contain' => array(),
				'fields' => array('id', 'name')
			));
			
			foreach ($categories as $category) {
				$new_categories_product = array(
					'CategoriesProduct' => array(
						'product_id' => $this->Product->id,
						'category_id' => $category['Category']['id']
					)
				);
				
				$this->Product->CategoriesProduct->create();
				if (!$this->Product->CategoriesProduct->save($new_categories_product)) {
					debug($new_categories_product);
					die('nepodarilo se vlozit produkt do kategorie');
				}
			}

			// vlozim k nemu obrazky
			// hlavni obrazek ma produkt v tabulce tb_products (fyzicky v adresari /files/pictures)
			// obrazek musim stahnout z umisteni, ulozit na disk do db
			$tb_images = $this->Parser->query("
				SELECT *
				FROM tb_products
				WHERE id = " . $tb_product['tb_products_lng']['product_id'] . "
			");
			
			foreach ($tb_images as $tb_image) {
				if (!empty($tb_image['tb_products']['pict'])) {
					$image_url = 'http://www.top-bazeny.cz/files/pictures/' . $tb_image['tb_products']['pict'];
					$this->save_image($image_url, $this->Product->id, $new_product['Product']['name'], true);
				}
			}
			
			// dalsi muze mit v tb_fotogaleries (fyzicky v adresari /files/fotogaleries/)
			$tb_fotogaleries = $this->Parser->query("
				SELECT *
				FROM tb_product_fotogaleries
				WHERE product_id = " . $tb_product['tb_products_lng']['product_id'] . "
			");
			
			foreach ($tb_fotogaleries as $tb_fotogalery) {
				if (!empty($tb_fotogalery['tb_product_fotogaleries']['file'])) {
					$image_url = 'http://www.top-bazeny.cz/files/fotogaleries/' . $tb_fotogalery['tb_product_fotogaleries']['file'];
					$this->save_image($image_url, $this->Product->id, $new_product['Product']['name'], false);
				}
			}
		}
		die('hotovo');
	}
	
	function save_image($url, $product_id, $product_name, $is_main) {
		$url = str_replace(' ', '%20', $url);

		App::import('Model', 'Image');
		$this->Image = &new Image;
		
		// zjistim priponu obrazku
		$suffix = explode('.', $url);
		$suffix = $suffix[count($suffix)-1];

		// musim si zkontrolovat, abych si neprepsal
		// jiz existujici soubor
		$image_name = strip_diacritic($product_name) . '.' . $suffix;
		$image_name = $this->Image->checkName('product-images/' . $image_name);
	
		if ($image = file_get_contents($url)){
			if (file_put_contents($image_name, $image)) {

				// potrebuju zmenit prava u obrazku
				chmod($image_name, 0644);
				// nez ulozim do databaze, potrebuju si vytvorit nahledy
				// nejdriv musim ale otestovat obrazek, jestli se bude dat
				// nacist pomoci imagecreatefrom...
				// vyrezervovana pamet pro operace je 8MegaByte to je 8388608 bytes
				if ( !$this->Image->isLoadable($image_name) ){
		
					// pred presmerovani musim vymazat obrazek z disku,
					// aby mi tam zbytecne nezustaval
					unlink($image_name);
		
					// presmeruju a vypisu hlasku
					$message[] = 'Obrázek <strong>' . $image_name . '</strong> má příliš velké rozměry, zmenšete jej a zkuste nahrát znovu.';
				} else {
					$this->Image->makeThumbnails($image_name);
					$image_name = explode("/", $image_name);
					$image_name = $image_name[count($image_name) -1];
					$new_image = array(
						'Image' => array(
							'product_id' => $product_id,
							'name' => $image_name,
							'is_main' => $is_main
						)
					);
					// ulozim obrazek do db
					$this->Image->create();
					if (!$this->Image->save($new_image)) {
						debug($image);
						die('obrazek se nepodarilo ulozit');
					}
					
					// pokud se mi to nepovede, musim smazat obrazek i z disku
				} // isLoadable
			} else {
				debug($image_name);
				die('nepodarilo se ulozit obrazek na disk');
			}
		} else {
			debug($url);
			die('neporadilo se stahnout obrazek');
		}
		
	}
	
	function categories_repair_urls() {
		App::import('Model', 'Category');
		$this->Category = &new Category;
		
		$categories = $this->Category->find('all', array(
			'contain' => array(),
			'fields' => array('id', 'tb_id')
		));
		
		foreach ($categories as $category) {
			$tb_category = $this->Parser->query("
				SELECT rewrite
				FROM tb_categories_lng
				WHERE category_id = " . $category['Category']['tb_id'] . "
			");
			
			if (!empty($tb_category)) {
				$category['Category']['url'] = $tb_category[0]['tb_categories_lng']['rewrite'];
				if (!$this->Category->save($category)) {
					debug($category);
					die('kategorii se nepodarilo ulozit');
				}
			}
		}
		die('hotovo');
	}
	
	function categories_repair_titles() {
		App::import('Model', 'Category');
		$this->Category = &new Category;
		
		$categories = $this->Category->find('all', array(
			'contain' => array(),
			'fields' => array('id', 'tb_id')
		));
		
		foreach ($categories as $category) {
			$tb_category = $this->Parser->query("
				SELECT detail_name
				FROM tb_categories_lng
				WHERE category_id = " . $category['Category']['tb_id'] . "
			");
			
			if (!empty($tb_category)) {
				if (!empty($tb_category[0]['tb_categories_lng']['detail_name'])) {
					$category['Category']['title'] = $tb_category[0]['tb_categories_lng']['detail_name'];
					$category['Category']['heading'] = $tb_category[0]['tb_categories_lng']['detail_name'];
					$category['Category']['breadcrumb'] = $tb_category[0]['tb_categories_lng']['detail_name'];
					if (!$this->Category->save($category)) {
						debug($category);
						die('kategorii se nepodarilo ulozit');
					}
				}
			}
		}
		die('hotovo');
	}
	
	function products_repair_urls() {
		App::import('Model', 'Product');
		$this->Product = &new Product;
		
		$products = $this->Product->find('all', array(
			'contain' => array(),
			'fields' => array('id', 'tb_id')
		));
		
		foreach ($products as $product) {
			$tb_product = $this->Parser->query("
				SELECT rewrite
				FROM tb_products_lng
				WHERE product_id = " . $product['Product']['tb_id'] . "
			");
			
			if (!empty($tb_product)) {
				$product['Product']['url'] = $tb_product[0]['tb_products_lng']['rewrite'] . '.html';
				if (!$this->Product->save($product)) {
					debug($product);
					die('produkt se nepodarilo ulozit');
				}
			}
		}
		die('hotovo');
	}
	
	function products_repair_titles() {
		App::import('Model', 'Product');
		$this->Product = &new Product;
		
		$products = $this->Product->find('all', array(
			'contain' => array(),
			'fields' => array('id', 'tb_id')
		));
		
		foreach ($products as $product) {
			$tb_product = $this->Parser->query("
				SELECT detail_name
				FROM tb_products_lng
				WHERE product_id = " . $product['Product']['tb_id'] . "
			");
			
			if (!empty($tb_product)) {
				if (!empty($tb_product[0]['tb_products_lng']['detail_name'])) {
					$product['Product']['title'] = $tb_product[0]['tb_products_lng']['detail_name'];
					$product['Product']['heading'] = $tb_product[0]['tb_products_lng']['detail_name'];
					$product['Product']['related_name'] = $tb_product[0]['tb_products_lng']['detail_name'];
					if (!$this->Product->save($product)) {
						debug($product);
						die('produkt se nepodarilo ulozit');
					}
				}
			}
		}
		die('hotovo');
	}
	
	function products_repair_codes() {
		App::import('Model', 'Product');
		$this->Product = &new Product;
		
		$products = $this->Product->find('all', array(
			'contain' => array(),
			'fields' => array('id', 'tb_id')
		));
		
		foreach ($products as $product) {
			$tb_product = $this->Parser->query("
				SELECT code
				FROM tb_products
				WHERE id = " . $product['Product']['tb_id'] . "
			");
			
			if (!empty($tb_product)) {
				if (!empty($tb_product[0]['tb_products']['code'])) {
					$product['Product']['code'] = $tb_product[0]['tb_products']['code'];
					if (!$this->Product->save($product)) {
						debug($product);
						die('produkt se nepodarilo ulozit');
					}
				}
			}
		}
		die('hotovo');
	}
}
?>