<?php 
class ProductsParser {

	/**
	 * nastavi datum posledni upravy u kategorie
	*/ 
	function set_compared_category($category) {
		App::import('Model', 'Category');
		$this->Category = &new Category;
		
		$category['Category']['compared'] = date('Y-m-d H:i:s');
		
		if ($this->Category->save($category, false)) {
			echo $category['Category']['sportnutrition_url'] . " - datum poslední úpravy bylo nastaveno.<br/>\n";
		} else {
			echo $category['Category']['sportnutrition_url'] . " - datum poslední úpravy se nepodaøilo nastavit.<br/>\n";
		}
	}
	
	/**
	 * Projde kategorii a zjisti v ni zmeny - nove / odstranene produkty
	 */
	function product_urls($category, $subcategories) {
		$product_urls = array();

		// stahnu stranku kategorie
		if (!$html = file_get_contents($category['Category']['sportnutrition_url'])) {
			debug($category);
			echo $category['Category']['sportnutrition_url'] . " se nepodaøilo stáhnout.<br/>\n";
			return false;
		}

		// abych mohl poskladat DOM strom
		$dom = new DOMDocument('1.0');
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		libxml_use_internal_errors(true);
		if (!$dom->loadHTML($html)) {
			die('dokument se nenaloudoval');
		}
		$domXPath = new DOMXPath($dom);
		
		// musim vytahnout vsechny podstranky kategorie
		$pages = $domXPath->query('//div[@class=\'strankovani\']/a[last()]');
		if ($pages->length > 0) {
			$firstItem = $pages->item(0);
			$pages = $firstItem->nodeValue;
		} else {
			$pages = 1;
		}
		
		for ($page=1; $page<=$pages; $page++) {
			// stahnu stranku a vyparsuju url produktu
			if ($page == 1) {
				$page_html = $html;
			} else {
				$subpage_url = $category['Category']['sportnutrition_url'] . '/stranka:' . $page;
				// nechci porad stahovat stranky kategorii, takze je budu kesovat
				if (!$page_html = file_get_contents($subpage_url)) {
					echo $subpage_url . " se nepodaøilo stáhnout.<br/>\n";
					continue;
				}
			}
			
			// abych mohl poskladat DOM strom
			$page_dom = new DOMDocument();
			$page_dom->formatOutput = true;
			$page_dom->preserveWhiteSpace = false;
			if (!$page_dom->loadHTML($page_html)) {
				if (isset($subpage_url)) {
					echo $subpage_url . ' se nepodaøilo naloadovat.</br>';
				} else {
					echo $category['Category']['sportnutrition_url'] . ' se nepodaøilo naloadovat.</br>';
				}
				continue;
			}
			$page_domXPath = new DOMXPath($page_dom);
			
			$urls = $page_domXPath->query('//div[@class=\'products\']//h4/a/@href');
			
			$count = 0;
			while ($urls->length > $count ) {
				$countItem = $urls->item($count);
				$product_urls[] = 'http://www.sportnutrition.cz' . $countItem->nodeValue;
				$count++;
			}
		}
		// stahnu url produktu v podkategoriich a ze zjistenych je odstranim...
		
		if (!empty($subcategories)) {
			$subcategories_product_urls = array();
			foreach ($subcategories as $subcategory) {
				$subcategories_product_urls = array_merge($subcategories_product_urls, $this->product_urls($subcategory, array()));
			}
			$product_urls = array_diff($product_urls, $subcategories_product_urls);
		}

		return $product_urls;
	}
	
	/**
	 * nastavi datum posledni upravy u produktu 
	*/ 
	function set_compared($product) {
		App::import('Model', 'Product');
		$this->Product = &new Product;
		
		$product_save = array(
			'Product' => array(
				'id' => $product['Product']['id'],
				'compared' => date('Y-m-d H:i:s')
			)
		);
		
		if ($this->Product->save($product_save, false)) {
			echo $product['Product']['sportnutrition_url'] . " - datum poslední úpravy produktu bylo nastaveno.<br/>\n";
		} else {
			echo $product['Product']['sportnutrition_url'] . " - datum poslední úpravy produktu se nepodaøilo nastavit.<br/>\n";
		}
	}
	
	/**
	 * Vyparsuje a ulozi aktualni subprodukty daneho produktu
	*/
	function parse_subproducts($product, $dom) {
		App::import('Model', 'Product');
		$this->Product = &new Product;
		
		$domXPath = new DOMXPath($dom);
		
		// vyparsuju subprodukty
		$options_fragment = $domXPath->query('//table[@class=\'cenik\']//tr[@class=\'tr1\']/td[@colspan=\'4\']/table/tr');
		
		$variants = array();
		$i = 0;
		while($options_fragment->length > $i) {
			// vytahl jsem si radek z tabulky, ve kterem mam informace o nazvu option a jeho variantach
			// a musim ty data vyparsovat a spojit ... napr. velikost -> S, M, L
			// zabira to moc pameti, takze to udelat pres regularni vyrazy
			if (!preg_match('/<td>(.*)<\/td>/', $dom->saveXML($options_fragment->item($i)), $option_matches)) {
				echo "NENI NAZEV OPTION<br/>\n";
				break;
			}
			
			if (!preg_match_all('/<option value="(.*)">/', $dom->saveXML($options_fragment->item($i)), $attributes)) {
				echo "NEJSOU HODNOTY ATRIBUTU<br/>\n";
				break;
			}
			
			foreach ($attributes[1] as $index => $value) {
				$attributes[1][$index] = iconv('utf-8', 'windows-1250//IGNORE', $value);
			}
			
			// postavim si pole variant daneho produktu
			$variants[] = array(
				'name' => iconv('utf-8', 'windows-1250//IGNORE', str_replace(':', '', $option_matches[1])),
				'Attribute' => $attributes[1]
			);
			
			$i++;
		}

		if (empty($variants)) {
			// smazu subprodukty, ktere mel produkt mozna prirazene
			$this->Product->Subproduct->deleteAll(array('product_id' => $product['Product']['id']));
			echo $product['Product']['sportnutrition_url'] . " - nemá subprodukty k uložení.<br/>\n";
			return true;
		}
		
		// ulozim options a atributy (pokud neexistuji)
		// 2ai) zjistim, jestli jsou v db options a pokud ne, ulozim je a zjistim option_id
		$attributes = array();
		foreach ($variants as $option) {
			$db_option = $this->Product->Subproduct->AttributesSubproduct->Attribute->Option->find('first', array(
				'conditions' => array('name' => $option['name']),
				'contain' => array(),
				'fields' => array('Option.id')
			));

			if (empty($db_option)) {
				$this->Product->Subproduct->AttributesSubproduct->Attribute->Option->create();
				$this->Product->Subproduct->AttributesSubproduct->Attribute->Option->save(array(
					'Option' => array(
						'name' => $option['name']
					)
				), false);
				$option_id = $this->Product->Subproduct->AttributesSubproduct->Attribute->Option->id;
			} else {
				$option_id = $db_option['Option']['id'];
			}

			// 2aii) zjistim, jestli jsou v db pro dane option_id hodnoty atributu, kdyz ne, tak je vlozim a zapamatuju si attribute_id
			foreach ($option['Attribute'] as $attribute_value) {
				$db_attribute = $this->Product->Subproduct->AttributesSubproduct->Attribute->find('first', array(
					'conditions' => array('Attribute.value' => $attribute_value, 'Attribute.option_id' => $option_id),
					'contain' => array(),
					'fields' => array('id')
				));

				if (empty($db_attribute)) {
					$this->Product->Subproduct->AttributesSubproduct->Attribute->create();
					$this->Product->Subproduct->AttributesSubproduct->Attribute->save(array(
						'Attribute' => array(
							'value' => $attribute_value,
							'option_id' => $option_id
						)
					));
					$attributes[$option_id][] = $this->Product->Subproduct->AttributesSubproduct->Attribute->id;
				} else {
					$attributes[$option_id][] = $db_attribute['Attribute']['id'];
				}
			}
		}
		
		// 2aiii) vygeneruju subprodukty
		$generated_subproducts = $this->Product->combine($attributes);
		
		// porovnavam aktualni subprodukty s vyparsovanymi
		foreach ($product['Subproduct'] as $subproduct) {
			// udelam serazene pole idcek atributu pro dany subprodukt
			
			$db_attributes_ids = Set::extract('/attribute_id', $subproduct['AttributesSubproduct']);
			sort($db_attributes_ids);
			
			$found = false;
			$continuing_subproduct_ids = array();
			foreach ($generated_subproducts as $index => $generated_subproduct) {
				sort($generated_subproduct);
				if ($generated_subproduct == $db_attributes_ids) {
					// nasel jsem subprodukt z db ve vygenerovanych, takze jej nevkladam, ani nemazu
					// odstranim ho z pole vygenerovanych
					unset($generated_subproducts[$index]);
					$found = true;
					break;
				}
			}
			
			// pokud jsem subprodukt s temito atributy nenasel, musim ho smazat
			// protoze prochazim subprodukty, ktere jsou v db vuci tem, ktere jsou vygenerovane
			if (!$found) {
				$this->Product->Subproduct->delete($subproduct['id']);
			}
		}
		
		// vygenerovane subprodukty, ktere mi zbyly v poli, musim ulozit k danemu produktu
		$subproduct_save = array();
		foreach ($generated_subproducts as $generated_subproduct) {
			$subproduct_save = array(
				'Subproduct' => array(
					'product_id' => $product['Product']['id'],
					'availability' => 1,
					'active' => true
				)
			);
			foreach ($generated_subproduct as $attribute_id) {
				$subproduct_save['AttributesSubproduct'][] = array('attribute_id' => $attribute_id);
			}
			$this->Product->Subproduct->create();
			$this->Product->Subproduct->saveAll($subproduct_save);
		}
		echo $product['Product']['sportnutrition_url'] . " - subprodukty byly uloženy.<br/>\n";
		return true;
	}
	
	/**
	 * Vyparsuje a ulozi aktualni dostupnost produktu
	*/ 
	function parse_availability($product, $domXPath) {
		App::import('Model', 'Product');
		$this->Product = &new Product;
		
		$availability_fragment = $domXPath->query('//td[@align=\'center\'][@style=\'white-space: nowrap;\']');
		if ($availability_fragment->length == 0) {
			echo $product['Product']['sportnutrition_url'] . " - dostupnost se nevyparsovala - neodpovida XPath vyraz.<br/>\n";
			return false;
		}
		
		$firstItem = $availability_fragment->item(0);
		$availability = iconv('utf-8', 'windows-1250//IGNORE', $firstItem->nodeValue);

		// priradim dostupnost
		$db_availability = $this->Product->Availability->find('first', array(
			'conditions' => array('Availability.name' => $availability),
			'contain' => array(),
			'fields' => array('id')
		));
		
		if (!empty($db_availability) && $product['Product']['availability_id'] == $db_availability['Availability']['id']) {
			echo $product['Product']['sportnutrition_url'] . " - dostupnost se nezmenila<br/>\n";
			return true;
		}
		
		// pokud jsem nenasel dostupnost, musim ji ulozit
		if (empty($db_availability)) {
			echo $product['Product']['sportnutrition_url'] . ' - dostupnost nebyla nalezena, vytvarim novou dostupnost "' . $availability . "\"<br/>\n";
			$this->Product->Availability->create();
			$this->Product->Availability->save(array(
				'Availability' => array(
					'name' => $product['Product']['availability']
				)
			));
			echo $product['Product']['sportnutrition_url'] . " - nova dostupnost ulozena.<br/>\n";
			$availability_id = $this->Product->Availability->id;
		} else {
			$availability_id = $db_availability['Availability']['id'];
		}
		
		$this->Product->save(array(
			'Product' => array(
				'id' => $product['Product']['id'],
				'availability_id' => $availability_id
			)
		), false);
		echo $product['Product']['sportnutrition_url'] . " - dostupnost byla nastavena<br/>\n";
	}
	
	/**
	 * Vyparsuje a ulozi ceny (s/bez dph) a pri zmene priradi i danovou tridu
	*/ 
	function parse_price($product, $domXPath) {
		App::import('Model', 'Product');
		$this->Product = &new Product;
		
		// doporucena (puvodni) MOC		
		$price_fragment = $domXPath->query('//td[@align=\'right\'][@style=\'white-space: nowrap;\'][2]');

		if ($price_fragment->length == 0) {
			echo $product['Product']['sportnutrition_url'] . " - MOC se nevyparsovala - neodpovida XPath vyraz.<br/>\n";
			return false;
		}
		
		$firstItem = $price_fragment->item(0);
		$price_fragment = iconv('utf-8', 'windows-1250', $firstItem->nodeValue);
		if (!preg_match('/(\d+)(?:\.(\d+))?.*/', $price_fragment, $price)) {
			echo $product['Product']['sportnutrition_url'] . " - MOC cena se nevyparsovala - neodpovida RE vyraz.<br/>\n";
			return false;
		} else {
			if (isset($price[2])) {
				$price = $price[1] . $price[2];
			} else {
				$price = $price[1];
			}
		}
		
		// obycejna sleva
		$common_discount_fragment = $domXPath->query('//td[@align=\'right\'][@style=\'white-space: nowrap;\'][1]');
		
		if ($common_discount_fragment->length == 0) {
			echo $product['Product']['sportnutrition_url'] . " - obycejna sleva se nevyparsovala - neodpovida XPath vyraz.<br/>\n";
			return false;
		}
		
		$firstItem = $common_discount_fragment->item(0);
		$common_discount_fragment = iconv('utf-8', 'windows-1250', $firstItem->nodeValue);
		if (!preg_match('/(\d+)(?:\.(\d+))?.*/', $common_discount_fragment, $common_discount)) {
			echo $product['Product']['sportnutrition_url'] . " - obycejna sleva se nevyparsovala - neodpovida RE vyraz.<br/>\n";
			return false;
		} else {
			if (isset($common_discount[2])) {
				$common_discount = $common_discount[1] . $common_discount[2];
			} else {
				$common_discount = $common_discount[1];
			}
		}
		
		$product['Product']['retail_price_with_dph'] = $price;
		if ($price == 0) {
			echo $product['Product']['sportnutrition_url'] . " - dop MOC je nulova.<br/>\n";
			$product['Product']['retail_price_with_dph'] = $common_discount;
		}
		$product['Product']['discount_common'] = $common_discount;
		
		if ($this->Product->save($product, false)) {
			echo $product['Product']['sportnutrition_url'] . " - cena byla upravena.<br/>\n";
			return true;
		} else {
			echo $product['Product']['sportnutrition_url'] . " - cenu se nepodaøilo upravit.<br/>\n";
			return false;
		}
	}
	
	function parse_member_discount($product) {
		// INIT CURL
		$ch = curl_init();
		
		// SET URL FOR THE POST FORM LOGIN
		curl_setopt($ch, CURLOPT_URL, 'http://www.sportnutrition.cz/');
		
		// ENABLE HTTP POST
		curl_setopt ($ch, CURLOPT_POST, 1);
		
		// prihlaseni
		// SET POST PARAMETERS : FORM VALUES FOR EACH FIELD
		curl_setopt ($ch, CURLOPT_POSTFIELDS, 'form_login=brko&form_heslo=tc0wls&form_akce=prihlasit');
		
		// IMITATE CLASSIC BROWSER'S BEHAVIOUR : HANDLE COOKIES
		curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		
		# Setting CURLOPT_RETURNTRANSFER variable to 1 will force cURL
		# not to print out the results of its query.
		# Instead, it will return the results as a string return value
		# from curl_exec() instead of the usual true/false.
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		
		// EXECUTE 1st REQUEST (FORM LOGIN)
		$store = curl_exec ($ch);

		// SET FILE TO DOWNLOAD
		curl_setopt($ch, CURLOPT_URL, $product['Product']['sportnutrition_url']);
		
		// stahnu kod stranky po prihlaseni - muzu ho predhodit parseru pro cenu pro ziskani udaju o cene
		// EXECUTE 2nd REQUEST (FILE DOWNLOAD)
		$content = curl_exec ($ch);
	
		// CLOSE CURL
		curl_close ($ch);
		
		// parsuju slevu
		// sestavim DOM strom
		$dom = new DOMDocument();
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		libxml_use_internal_errors(true);
	
		if (!$dom->loadHTML($content)) {
			echo $product['Product']['sportnutrition_url'] . " - PARSOVANI SLEVY - nevytvoril se DOM strom.<br/>\n";
			continue;
		}

		$domXPath = new DOMXPath($dom);

		$domQuery = $domXPath->query('//div[@id=\'product\']');
		if (!$domQuery->length) {
			echo $product['Product']['sportnutrition_url'] . " - PARSOVANI SLEVY - prazdna stranka.<br/>\n";
			return false;
		}
		
		App::import('Model', 'Product');
		$this->Product = &new Product;
		
		$price_fragment = $domXPath->query('//td[@align=\'right\'][@style=\'white-space: nowrap;\'][1]');
		
		if ($price_fragment->length == 0) {
			echo $product['Product']['sportnutrition_url'] . " - PARSOVANI SLEVY - cena se nevyparsovala - neodpovida XPath vyraz.<br/>\n";
			return false;
		}
		
		$firstItem = $price_fragment->item(0);
		$price_fragment = iconv('utf-8', 'windows-1250', $firstItem->nodeValue);
		if (!preg_match('/(\d+)(?:\.(\d+))?.*/', $price_fragment, $price)) {
			echo $product['Product']['sportnutrition_url'] . " - PARSOVANI SLEVY - cena se nevyparsovala - neodpovida RE vyraz.<br/>\n";
			return false;
		} else {
			if (isset($price[2])) {
				$price = $price[1] . $price[2];
			} else {
				$price = $price[1];
			}
		}
		
		// porovnam cenu a vypisu vysledek
		if ($price == $product['Product']['discount_member']) {
			echo $product['Product']['sportnutrition_url'] . " - PARSOVANI SLEVY - cena je stejna.<br/>\n";
		} else {
			echo $product['Product']['sportnutrition_url'] . "  - PARSOVANI SLEVY - cena se zmenila.<br/>\n";
			
			// nastavim novou hodnotu slevove ceny pro prihlaseneho zakaznika
			$product_save = array(
				'Product' => array(
					'id' => $product['Product']['id'],
					'discount_member' => $price
				)
			);
				
			if ($this->Product->save($product_save, false)) {
				echo $product['Product']['sportnutrition_url'] . " - PARSOVANI SLEVY - cena byla upravena.<br/>\n";
				return true;
			} else {
				echo $product['Product']['sportnutrition_url'] . " - PARSOVANI SLEVY - cenu se nepodaøilo upravit.<br/>\n";
				return false;
			}
		}
	}
	
	function get_product($url) {
		// stahnu html daneho produktu
		$html = file_get_contents($url);
		
		// abych mohl poskladat DOM strom
		$dom = new DOMDocument();
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		libxml_use_internal_errors(true);
		
		if (!$dom->loadHTML($html)) {
			die('dokument se nenaloudoval');
		}

		$domXPath = new DOMXPath($dom);

		$values = array(
			array('name', '//div[@id=\'product\']/h1'),
			array('short_description', '//div[@id=\'product\']/strong[2]'),
			array('description', '//div[@id=\'product\']', '/Výrobce:.*<br \/><br \/>.*<strong>.*<\/strong>(.*)<table class="cenik"/msU'),
			array('availability', '//td[@align=\'center\'][@style=\'white-space: nowrap;\']'),
			array('manufacturer', '//div[@id=\'product\']', '/Výrobce: <strong><a[^>]*>(.*)<\/a><\/strong>/m'),
			array('discount_common', '//td[@align=\'right\'][@style=\'white-space: nowrap;\'][1]', '/(\d+)(?:\.(\d+))?.*/'),
			array('retail_price_with_dph', '//td[@align=\'right\'][@style=\'white-space: nowrap;\'][2]', '/(\d+)(?:\.(\d+))?.*/'),
			array('images', '//div[@class=\'fotogalerie\']/a/@href'),
			array('options', '//table[@class=\'cenik\']//tr[@class=\'tr1\']/td[@colspan=\'4\']/table/tr'),
			array('related_products', '//div[@class=\'products\']//h4'),
		);
		
		// tady si vytahnu data, ktera jdou jednoduse
		foreach ($values as $value) {
			$result = $domXPath->query($value[1]);
			// popis
			if ($value[0] == 'description') {
				$text = iconv('utf-8', 'windows-1250//IGNORE', $dom->saveXML($result->item(0)));
				if (preg_match_all($value[2], $text, $matches)) {
					// odstranim skript pro FB like a vsechno, co je za nim (protoze FB likes davaji na konec)
					$product['Product']['description'] = preg_replace('/(?:<br \/>)?<br \/>(?:<script>)?<!\[CDATA.*/ms', '', $matches[1][0]);
				}
			// obrazky
			} elseif ($value[0] == 'images') {
				$i = 0;
				while ($result->length > $i) {
					$iItem = $result->item($i);
					$product['Product']['Image'][] = 'http://www.sportnutrition.cz' . $iItem->nodeValue;
					$i++;
				}
			// atributy
			} elseif ($value[0] == 'attributes') {
				$i = 0;
				while ($result->length > $i) {
					$iItem = $result->item($i);
					$product['Product']['Attribute'][] = $iItem->nodeValue;
					$i++;
				}
			// options
			} elseif ($value[0] == 'options') {
				$variants = array();
				$i = 0;
				$resultLength = $result->length;
				while($resultLength > $i) {
					// vytahl jsem si radek z tabulky, ve kterem mam informace o nazvu option a jeho variantach
					// a musim ty data vyparsovat a spojit ... napr. velikost -> S, M, L
					// zabira to moc pameti, takze to udelat pres regularni vyrazy
					if (!preg_match('/<td>(.*)<\/td>/', $dom->saveXML($result->item($i)), $option_matches)) {
						die('neni nazev option');
					}
					
					if (!preg_match_all('/<option value="(.*)">/', $dom->saveXML($result->item($i)), $attributes)) {
						die('nejsou hodnoty atributu');
					}
					
					foreach ($attributes[1] as $index => $value) {
						$attributes[1][$index] = iconv('utf-8', 'windows-1250//IGNORE', $value);
					}
					
					// postavim si pole variant daneho produktu
					$variants[] = array(
						'name' => iconv('utf-8', 'windows-1250//IGNORE', str_replace(':', '', $option_matches[1])),
						'Attribute' => $attributes[1]
					);
					
					$i++;
				}
				$product['Product']['Option'] = $variants;
			} else {
				if (isset($value[2])) {
					$text = iconv('utf-8', 'windows-1250//IGNORE', $dom->saveXML($result->item(0)));
					if (preg_match($value[2], $text, $matches)) {
						$product['Product'][$value[0]] = $matches[1];
						if (isset($matches[2])) {
							$product['Product'][$value[0]] = $matches[1] . $matches[2];
						}
						
					}
				} else {
					$i = 0;
					$product['Product'][$value[0]] = '';
					while ($result->length > $i) {
						if ($value[0] == 'related_products') {
							$iItem = $result->item($i);
							$product['Product'][$value[0]][] = $iItem->nodeValue;
						} else {
							$iItem = $result->item($i);
							$text = $iItem->nodeValue;
							if ($value[0] == 'option') {
								$text = str_replace(':', '', $text);
							}
							$product['Product'][$value[0]] .= iconv('utf-8', 'windows-1250//IGNORE', $text);
						}
						$i++;
					}
				}
			}
		}
		
/*		if ($product['Product']['manufacturer'] == 'BioTech' || $product['Product']['manufacturer'] == 'Biotech USA') {
			echo "Produkt od Biotech nebo BioTech USA - <a href='" . $url . "'>" . $url . "</a>- nevkladam!<br/>\n";
			return false;
		}*/
		
		// doplneni dat, ktera se nevyparsovala
		$product['Product']['title'] = $product['Product']['name'];
		$product['Product']['sportnutrition_url'] = $url;
		if (preg_match('/.*:(\d+)(?:\/)?$/', $url, $matches)) {
			$product['Product']['sportnutrition_id'] = $matches[1];
		} 
		$product['Product']['active'] = true;
		$product['Product']['confirmed'] = false;
		if (isset($product['Product']['option'])) {
			$product['Product']['option'] = str_replace(':', '', $product['Product']['option']);
		}

		//$product['Product']['tax_percent'] = round($product['Product']['retail_price_with_dph'] / ($product['Product']['retail_price_wout_dph'] / 100), 0) - 100;

		// protoze danove tridy jsou 14 a 20 procent
		$product['Product']['related_products'] = serialize($product['Product']['related_products']);

		// parsovani slevy
		// INIT CURL
		$ch = curl_init();
		
		// SET URL FOR THE POST FORM LOGIN
		curl_setopt($ch, CURLOPT_URL, 'http://www.sportnutrition.cz/');
		
		// ENABLE HTTP POST
		curl_setopt ($ch, CURLOPT_POST, 1);
		
		// prihlaseni
		// SET POST PARAMETERS : FORM VALUES FOR EACH FIELD
		curl_setopt ($ch, CURLOPT_POSTFIELDS, 'form_login=brko&form_heslo=tc0wls&form_akce=prihlasit');
		
		// IMITATE CLASSIC BROWSER'S BEHAVIOUR : HANDLE COOKIES
		curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		
		# Setting CURLOPT_RETURNTRANSFER variable to 1 will force cURL
		# not to print out the results of its query.
		# Instead, it will return the results as a string return value
		# from curl_exec() instead of the usual true/false.
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		
		// EXECUTE 1st REQUEST (FORM LOGIN)
		$store = curl_exec ($ch);
		
		// SET FILE TO DOWNLOAD
		curl_setopt($ch, CURLOPT_URL, $product['Product']['sportnutrition_url']);
		
		// stahnu kod stranky po prihlaseni - muzu ho predhodit parseru pro cenu pro ziskani udaju o cene
		// EXECUTE 2nd REQUEST (FILE DOWNLOAD)
		$content = curl_exec ($ch);
		
		// CLOSE CURL
		curl_close ($ch);
		
		// parsuju slevu
		// sestavim DOM strom
		$dom = new DOMDocument();
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		libxml_use_internal_errors(true);
	
		if (!$dom->loadHTML($content)) {
			echo $product['Product']['sportnutrition_url'] . " - PARSOVANI SLEVY - nevytvoril se DOM strom.<br/>\n";
			continue;
		}

		$domXPath = new DOMXPath($dom);
		
		$domQuery = $domXPath->query('//div[@id=\'product\']');
		if (!$domQuery->length) {
			echo $product['Product']['sportnutrition_url'] . " - PARSOVANI SLEVY - prazdna stranka.<br/>\n";
			continue;
		}
		
		App::import('Model', 'Product');
		$this->Product = &new Product;
		
		$price_fragment = $domXPath->query('//td[@align=\'right\'][@style=\'white-space: nowrap;\'][1]');
		
		if ($price_fragment->length == 0) {
			echo $product['Product']['sportnutrition_url'] . " - PARSOVANI SLEVY - cena se nevyparsovala - neodpovida XPath vyraz.<br/>\n";
			return false;
		}
		
		$firstItem = $price_fragment->item(0);
		$price_fragment = iconv('utf-8', 'windows-1250', $firstItem->nodeValue);
		if (!preg_match('/(\d+)(?:\.(\d+))?.*/', $price_fragment, $price)) {
			echo $product['Product']['sportnutrition_url'] . " - PARSOVANI SLEVY - cena se nevyparsovala - neodpovida RE vyraz.<br/>\n";
			return false;
		} else {
			if (isset($price[2])) {
				$price = $price[1] . $price[2];
			} else {
				$price = $price[1];
			}
		}
		
		$product['Product']['discount_member'] = $price;
		
		App::import('Model', 'Product');
		$this->Product = &new Product;
		
/*		// priradim danovou tridu
		$tax_class = $this->Product->TaxClass->find('first', array(
			'conditions' => array('TaxClass.value' => $product['Product']['tax_percent']),
			'contain' => array(),
			'fields' => array('id')
		));
		
		$tax_class_id = '';
		
		if (empty($tax_class)) {
			echo $url . ' - pro ' . $product['Product']['tax_percent'] . " neni v db zadna danova trida.<br/>\n";
		} else {
			$product['Product']['tax_class_id'] = $tax_class['TaxClass']['id'];
		}*/
		
		$product['Product']['tax_class_id'] = NULL;
		
		// priradim vyrobce
		$db_manufacturer = $this->Product->Manufacturer->find('first', array(
			'conditions' => array('Manufacturer.name' => $product['Product']['manufacturer']),
			'contain' => array(),
			'fields' => array('id')
		));
		
		// pokud jsem nenasel vyrobce, musim ho ulozit
		if (empty($db_manufacturer)) {
			$this->Product->Manufacturer->create();
			$this->Product->Manufacturer->save(array(
				'Manufacturer' => array(
					'name' => $product['Product']['manufacturer']
				)
			));
			$manufacturer_id = $this->Product->Manufacturer->id;
		} else {
			$manufacturer_id = $db_manufacturer['Manufacturer']['id'];
		}
		
		$product['Product']['manufacturer_id'] = $manufacturer_id;
		
		// priradim dostupnost
		$db_availability = $this->Product->Availability->find('first', array(
			'conditions' => array('Availability.name' => $product['Product']['availability']),
			'contain' => array(),
			'fields' => array('id')
		));
		
		// pokud jsem nenasel dostupnost, musim ji ulozit
		if (empty($db_availability)) {
			$this->Product->Availability->create();
			$this->Product->Availability->save(array(
				'Availability' => array(
					'name' => $product['Product']['availability']
				)
			));
			$availability_id = $this->Product->Availability->id;
		} else {
			$availability_id = $db_availability['Availability']['id'];
		}
		$product['Product']['availability_id'] = $availability_id;
		
		if (empty($product['Product']['short_description'])) {
			$product['Product']['short_description'] = $product['Product']['title'];
		}
		
		$this->Product->create();
		if (!$this->Product->save($product)) {
			echo $url . " se !!!NEPODARILO ULOZIT!!!<br/>\n";
			debug($this->Product->validationErrors); die();
			$i++; return false;
		}
		
		$url_save = array(
			'Product' => array(
				'id' => $this->Product->id,
				'url' => strip_diacritic(str_replace('.', '', $product['Product']['name'])) . '-p' . $this->Product->id
			)
		);
		$this->Product->save($url_save, false);
		
		// 2a) zjistim subprodukty
		// 2ai) zjistim, jestli jsou v db options a pokud ne, ulozim je a zjistim option_id
		$attributes = array();
		foreach ($product['Product']['Option'] as $option) {
			$db_option = $this->Product->Subproduct->AttributesSubproduct->Attribute->Option->find('first', array(
				'conditions' => array('name' => $option['name']),
				'contain' => array(),
				'fields' => array('Option.id')
			));

			if (empty($db_option)) {
				$this->Product->Subproduct->AttributesSubproduct->Attribute->Option->create();
				$this->Product->Subproduct->AttributesSubproduct->Attribute->Option->save(array(
					'Option' => array(
						'name' => $option['name']
					)
				), false);
				$option_id = $this->Product->Subproduct->AttributesSubproduct->Attribute->Option->id;
			} else {
				$option_id = $db_option['Option']['id'];
			}

			// 2aii) zjistim, jestli jsou v db pro dane option_id hodnoty atributu, kdyz ne, tak je vlozim a zapamatuju si attribute_id
			foreach ($option['Attribute'] as $attribute_value) {
				$db_attribute = $this->Product->Subproduct->AttributesSubproduct->Attribute->find('first', array(
					'conditions' => array('Attribute.value' => $attribute_value, 'Attribute.option_id' => $option_id),
					'contain' => array(),
					'fields' => array('id')
				));

				if (empty($db_attribute)) {
					$this->Product->Subproduct->AttributesSubproduct->Attribute->create();
					$this->Product->Subproduct->AttributesSubproduct->Attribute->save(array(
						'Attribute' => array(
							'value' => $attribute_value,
							'option_id' => $option_id
						)
					));
					$attributes[$option_id][] = $this->Product->Subproduct->AttributesSubproduct->Attribute->id;
				} else {
					$attributes[$option_id][] = $db_attribute['Attribute']['id'];
				}
			}
		}
		
		// 2aiii) vygeneruju subprodukty
		$generated_subproducts = $this->Product->combine($attributes);
		
		$subproducts = $this->Product->Subproduct->find('all', array(
			'conditions' => array('Subproduct.product_id' => $this->Product->id),
			'contain' => array('AttributesSubproduct')
		));
		
		// 2aiv) subprodukty ulozim a ulozim vztahy mezi subprodukty a atributy
		// prochazim vygenerovane subprodukty
		foreach ($generated_subproducts as $generated_subproduct) {
			// musim projit subprodukty produktu a zjistit, jestli uz v db neni subprodukt, ktery chci vkladat
			foreach ($subproducts as $subproduct) {
				// myslim si, ze subprodukt v db je
				$found = true;
				// pokud souhlasi pocet attribute_subproducts u subproduktu z db a vygenerovaneho
				if (sizeof($subproduct['AttributesSubproduct']) == sizeof($generated_subproduct)) {
					// prochazim vztahy mezi atributy a subproduktem z db
					foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) {
						// jestlize neni attributes_subproduct soucasti vygenerovaneho subproduktu
						if (!in_array($attributes_subproduct['attribute_id'], $generated_subproduct)) {
							// nastavim, ze jsem subprodukt nenasel
							$found = false;
							// a attributes_subprodukty dal neprochazim
							break;
						}
					}
					// jestlize jsem subprodukt nasel v db
					if ($found) {
						// zapamatuju si jeho idcko v db
						$subproduct_ids[] = $subproduct['Subproduct']['id'];
						break;
					}
					// pokud se velikost lisi
				} else {
					// nastavim si, ze jsem subprodukt nenasel
					$found = false;
					break;
				}
			}
			// jestlize jsem subprodukt nenasel
			if (!isset($found) || !$found) {
				// musim vytvorit takovej subprodukt a k nemu napojeni na atributy
				$subproduct_save['Subproduct']['product_id'] = $this->Product->id;
				$subproduct_save['Subproduct']['active'] = true;
				$this->Product->Subproduct->create();
				$this->Product->Subproduct->save($subproduct_save);
				$subproduct_id = $this->Product->Subproduct->id;
				$subproduct_ids[] = $subproduct_id;
				foreach ($generated_subproduct as $attribute_id) {
					$this->Product->Subproduct->AttributesSubproduct->create();
					$attributes_subproduct_save['AttributesSubproduct']['subproduct_id'] = $subproduct_id;
					$attributes_subproduct_save['AttributesSubproduct']['attribute_id'] = $attribute_id;
					$this->Product->Subproduct->AttributesSubproduct->save($attributes_subproduct_save);
				}
			}
		}

		// 2b) ulozim obrazky
		// stahnu obrazek a ulozim ho do db i na disk
		// je obrazek prvni???
		$first = true;
		foreach ($product['Product']['Image'] as $image_url) {
			if (!$image_content = file_get_contents($image_url)) {
				echo $products_url[$i] . ' - ' . $image_url . " se nepodarilo stahnout.<br/>\n";
				continue;
			}

			$image_name = explode('/', $image_url);
			$image_name = $image_name[count($image_name) - 1];
			
			$image_name = $this->Product->Image->checkName('product-images/' . $image_name);
			
			$image_name = explode("/", $image_name);
			$image_name = $image_name[count($image_name) -1];
			$image_file = fopen('product-images/' . $image_name, 'w');
			fwrite($image_file, $image_content);
			fclose($image_file);
			$this->Product->Image->makeThumbnails('product-images/' . $image_name);
			$image_save = array(
				'Image' => array(
					'product_id' => $this->Product->id,
					'name' => $image_name,
					'is_main' => false,
					'sportnutrition_url' => $image_url
				)
			);
			if ($first) {
				$image_save['Image']['is_main'] = true;
				$first = !$first;
			}
			$this->Product->Image->create();
			if (!$this->Product->Image->save($image_save)) {
				debug($this->Product->Image->validationErrors);
				echo $url . ' - Obrázek ' . $image_name . ' nemohl být uložen.' . implode("<br/>\n", $this->Product->Image->validationErrors) . "<br/>\n";
			}
		}

		echo $url . " - produkt  byl ulozen<br/>\n";
		
		return $this->Product->id;
	}
}
?>