<?
class SitemapsController extends AppController{
	var $name = 'Sitemaps';
	
	function admin_generate(){
		// otevrit soubor pro zapis, s vymazanim obsahu
		$fp = fopen('files/sitemap.xml', 'w+');

	$start_string = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
    		<loc>http://www.' . CUST_ROOT . '/</loc>
    		<changefreq>daily</changefreq>
    		<priority>1</priority>
	</url>';

		fwrite($fp, $start_string);

		// projdu vsechny produkty
		App::import('Model', 'Product');
		$this->Sitemap->Product = new Product;
		
		$this->Sitemap->Product->recursive = -1;
		$products = $this->Sitemap->Product->find('all', array('fields' => array('Product.url', 'Product.modified')));

		foreach ( $products as $product ){
			// pripnout k sitemape
			$mod = explode(' ', $product['Product']['modified']);
			$mod = $mod[0];
			$string = '
	<url>
    		<loc>http://www.' . CUST_ROOT . '/' . $product['Product']['url'] . '</loc>
    		<lastmod>' . $mod . '</lastmod>
    		<changefreq>weekly</changefreq>
    		<priority>0.9</priority>
	</url>';  

			fwrite($fp, $string);
		}
		
		// projdu vsechny kategorie
		App::import('Model', 'Category');
		$this->Sitemap->Category = new Category;
		
		$this->Sitemap->Category->recursive = -1;
		$categories = $this->Sitemap->Category->find('all', array('fields' => array('Category.id', 'Category.url')));

		$skip = array(0 => '5', '25', '26', '53');
		foreach ( $categories as $category ){
			if ( in_array($category['Category']['id'], $skip) ){
				continue;
			}
			$mod = date('Y-m-d');

			// pripnout k sitemape
			$string = '
	<url>
    		<loc>http://www.' . CUST_ROOT . '/' . $category['Category']['url'] . '</loc>
    		<changefreq>weekly</changefreq>
    		<priority>0.8</priority>
	</url>';  

			fwrite($fp, $string);
			
		}
		
		// projdu vsechny vyrobce
		App::import('Model', 'Manufacturer');
		$this->Sitemap->Manufacturer = new Manufacturer;
		
		$this->Sitemap->Manufacturer->recursive = -1;
		$manufacturers = $this->Sitemap->Manufacturer->find('all', array('fields' => array('Manufacturer.id', 'Manufacturer.name')));
		
		foreach ( $manufacturers as $manufacturer ){
			// pripnout k sitemape
			// vytvorim si url z name a id
			$string = '
	<url>
    		<loc>http://www.' . CUST_ROOT . '/' . strip_diacritic($manufacturer['Manufacturer']['name']) . '-v' . $manufacturer['Manufacturer']['id'] . '</loc>
    		<changefreq>weekly</changefreq>
    		<priority>0.8</priority>
	</url>';
			fwrite($fp, $string);
		}
		
		// projdu vsechny obsahove stranky
		App::import('Model', 'Content');
		$this->Sitemap->Content = new Content;
		
		$this->Sitemap->Content->recursive = -1;
		$contents = $this->Sitemap->Content->find('all', array('fields' => array('Content.path')));
		
		foreach ( $contents as $content ){
			// pripnout k sitemape
			if ( $content['Content']['path'] == 'index' ){
				continue;
			}
			$string = '
	<url>
    		<loc>http://www.' . CUST_ROOT . '/' . $content['Content']['path'] . '</loc>
    		<changefreq>weekly</changefreq>
    		<priority>0.7</priority>
	</url>';
			fwrite($fp, $string);
		}
		
		$end_string = '
</urlset>';
		fwrite($fp, $end_string);
		fclose($fp);
		// uzavrit soubor
		die('here');
	}
}
?>