<?
class ExportsController extends AppController{
	var $name = 'Exports';
	
	function get_products() {
		// natahnu si model Product
		App::Import('model', 'Product');
		$this->Product = &new Product;

		$products = $this->Product->find('all', array(
			'conditions' => array(
				"Product.short_description != ''",
				'Availability.cart_allowed' => true,
				'Product.active' => true
			),
			'contain' => array(
				'TaxClass' => array(
					'fields' => array('id', 'value')
				),
				'Image' => array(
					'conditions' => array('Image.is_main' => '1'),
					'fields' => array('id', 'name')
				),
				'Manufacturer' => array(
					'fields' => array('id', 'name')
				),
				'CategoriesProduct' => array(
					'Category' => array(
						'fields' => array('id', 'name')
					)
				),
				'Availability' => array(
					'fields' => array('id', 'cart_allowed')
				)
			),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.short_description',
				'Product.url',
				'Product.retail_price_with_dph',
				'Product.ean',
				'Product.zbozi_name',
				'Product.discount_common',
				'Product.zbozi_cpc',
				'Product.heureka_cpc'
			)
		));
		
		$products = array_filter($products, array('ExportsController', 'empty_category'));
		
		foreach ($products as $i => $product) {
			$products[$i]['Product']['retail_price_with_dph'] = $this->Product->assign_discount_price($products[$i]);
			$products[$i]['Product']['name'] = str_replace('&times;', 'x', $products[$i]['Product']['name']);
			$products[$i]['Product']['short_description'] = str_replace('&times;', 'x', $products[$i]['Product']['short_description']);
		}

		return $products;
	}
	
	function seznam_cz(){
		// nastavim si layout do ktereho budu cpat data v XML
		$this->layout = 'xml/seznam_zbozi';
		
		$products = $this->get_products();
		
		$this->set('products', $this->get_products());
		
		// produkty zobrazovane na detailu na firmy.cz
		$this->set('firmy_cz_products', array(762, 971, 880, 363, 654));
	}
	
	function heureka_cz() {
		$this->layout = 'xml/heureka';
		
		$products = $this->get_products();
		
		// sparovani kategorii na heurece s kategoriemi u nas v obchode
		$pairs = array(
			'Kosmetika a zdraví | Zdraví | Léčebné přípravky' => array(70),
			'Kosmetika a zdraví | Zdraví | Léčebné přípravky | Přípravky na posílení imunity' => array(50),
			'Kosmetika a zdraví | Zdraví | Léčebné přípravky | Dětské vitamíny' => array(54),
			'Kosmetika a zdraví | Zdraví | Léčebné přípravky | Přípravky na chřipku' => array(8, 40, 41, 42, 66, 25),
			'Kosmetika a zdraví | Zdraví | Léčebné přípravky | Přípravky na kašel' => array(43, 44),
			'Kosmetika a zdraví | Zdraví | Léčebné přípravky | Přípravky na rýmu' => array(45),
			'Kosmetika a zdraví | Zdraví | Léčebné přípravky | Přípravky na bolesti hlavy' => array(9, 49, 65, 67, 28),
			'Kosmetika a zdraví | Zdraví | Léčebné přípravky | Přípravky na bolesti zad' => array(64, 62),
			'Kosmetika a zdraví | Zdraví | Léčebné přípravky | Přípravky na osteoporózu' => array(61),
			'Kosmetika a zdraví | Zdraví | Léčebné přípravky | Přípravky proti nadýmání' => array(27),
			'Kosmetika a zdraví | Zdraví | Léčebné přípravky | Přípravky na obezitu' => array(39),
			'Kosmetika a zdraví | Zdraví | Léčebné přípravky | Přípravky na zácpu' => array(69),
			'Kosmetika a zdraví | Zdraví | Sportovní medicína | Zdravotní bandáže a ortézy' => array(56),
			'Kosmetika a zdraví | Kosmetika' => array(13, 19, 53, 71),
			'Kosmetika a zdraví | Kosmetika | Dětská kosmetika' => array(37, 32),
			'Kosmetika a zdraví | Kosmetika | Pleťová kosmetika' => array(29, 31, 33),
			'Kosmetika a zdraví | Kosmetika | Pleťová kosmetika | Přípravky na problematickou pleť' => array(20, 21, 30),
			'Sport | Sportovní výživa | Kloubní výživa' => array(10, 60, 68),
			'Sport | Sportovní výživa | Vitamíny a minerály' => array(6, 34, 35, 55),
			'Dětské zboží | Dětská výživa' => array(11, 15, 18),
			'Auto-moto | Autodoplňky | Autolékárny' => array(58),
			'Hobby | Chovatelství' => array(72),
			'Hobby | Jídlo a nápoje | Nápoje | Nealkoholické nápoje | Bylinné čaje' => array(14, 38)
		);

		foreach ($products as $index => $product) {
			// pokud je kategorie produktu sparovana s heurekou, nastavi se rovnou jako 'Sportovni vyziva | *odpovidajici nazev kategorie*
			foreach ($pairs as $name => $array) {
				if (in_array($product['CategoriesProduct'][0]['category_id'], $array)) {
					$products[$index]['CATEGORYTEXT'] = $name;
					break;
				}
			}

			// jinak se vytvori retezec ze stromu kategorii v obchode
			if (!isset($products[$index]['CATEGORYTEXT'])) {
				$path = $this->Product->CategoriesProduct->Category->getPath($product['CategoriesProduct'][0]['category_id']);
				$keys = Set::extract('/Category/name', $path);
				unset($keys[0]);
				$products[$index]['CATEGORYTEXT'] = 'Kosmetika a zdraví | Zdraví | Léčebné přípravky | ' . implode(' | ', $keys);
			}
		}
	
		$this->set('products', $products);
		
		// udaje o moznych variantach dopravy
		App::import('Model', 'Shipping');
		$this->Shipping = new Shipping;
		// vytahnu si vsechny zpusoby dopravy mimo osobniho odberu (id = 1)
		$shippings = $this->Shipping->find('all', array(
				'conditions' => array('NOT' => array('Shipping.heureka_id' => null)),
				'contain' => array(),
				'fields' => array('Shipping.id', 'Shipping.name', 'Shipping.price', 'Shipping.free', 'Shipping.heureka_id')
		));
		$this->set('shippings', $shippings);
	}
	
	/**
	 * 
	 * Vyfiltruju produkty, ktery nejsou prirazeny do zadne kategorie
	 * 
	 * @param unknown_type $a
	 */
	function empty_category($a) {
		return !empty($a['CategoriesProduct']);
	}
}
?>