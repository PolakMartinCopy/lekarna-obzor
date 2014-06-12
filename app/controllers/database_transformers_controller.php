<?

class DatabaseTransformersController extends AppController {

	var $name = 'DatabaseTransformers';

	

	function create_backups() {

		// vytvorit zalohu pro products

		$drop_products = "DROP TABLE IF EXISTS old_products";

		$this->DatabaseTransformer->query($drop_products);



		$products = 'CREATE TABLE IF NOT EXISTS old_products SELECT * FROM products';

		$this->DatabaseTransformer->query($products);

		

		// pro subproducts

		$drop_subproducts = "DROP TABLE IF EXISTS old_subproducts";

		$this->DatabaseTransformer->query($drop_subproducts);

		

		$subproducts = 'CREATE TABLE IF NOT EXISTS old_subproducts SELECT * FROM subproducts';

		$this->DatabaseTransformer->query($subproducts);

		

		// pro attributes

		$drop_attributes = "DROP TABLE IF EXISTS old_attributes";

		$this->DatabaseTransformer->query($drop_attributes);

		

		$attributes = 'CREATE TABLE IF NOT EXISTS old_attributes SELECT * FROM attributes';

		$this->DatabaseTransformer->query($attributes);

		

		die('hotovo');

	}

	

	/**

	 * Transformuje tabulku products_categories na categories_products

	 *

	 */

	function products_categories_transform() {

		// smazu categories_products, pokud uz tam je

		$drop = '

			DROP TABLE IF EXISTS categories_products;

		';

		$this->DatabaseTransformer->query($drop);



		// vytvorim tabulku categories_products

		$create = '

			CREATE TABLE categories_products (

				id INT(10) unsigned,

				created DATETIME,

				modified DATETIME,

				product_id INT(10) unsigned,

				category_id INT(10) unsigned

			)

		';

		$this->DatabaseTransformer->query($create);

		

		// nastavim id jako index

		$set_primary = 'ALTER TABLE categories_products ADD PRIMARY KEY (id)';

		$this->DatabaseTransformer->query($set_primary);

		

		// nakopiruju obsah z products_categories do categories_product

		$copy = '

			INSERT INTO categories_products(id, product_id, category_id)

				SELECT id, products_id, categories_id FROM products_categories

		';

		$this->DatabaseTransformer->query($copy);

		

		$set_auto_increment = 'ALTER TABLE `categories_products` CHANGE `id` `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT';

		$this->DatabaseTransformer->query($set_auto_increment);

		

		die('zkopirovano');

	}

	

	function products_transform() {

		// smazu products

		$drop = 'DROP TABLE products';

		$this->DatabaseTransformer->query($drop);

		

		$create = 'CREATE TABLE products SELECT * FROM old_products';

		$this->DatabaseTransformer->query($create);

		

		// nastavim idcko jako PRIMARY KEY auto_increment

		$primary_key = "ALTER TABLE products ADD PRIMARY KEY (id)";

		$this->DatabaseTransformer->query($primary_key);

		

		$auto_increment = "ALTER TABLE `products` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";

		$this->DatabaseTransformer->query($auto_increment);

		

		// prejmenuju atribut price na retail_price_with_dph

		$rename_price = "ALTER TABLE `products` CHANGE `price` `retail_price_with_dph` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00'";

		$this->DatabaseTransformer->query($rename_price);

		

		// pridam atributy pro VO/MO/marzi s/bez DPH

		$add_columns = "

			ALTER TABLE products ADD(

				retail_price_wout_dph DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00',

				wholesale_price_with_dph DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00',

				wholesale_price_wout_dph DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00',

				margin_with_dph DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00',

				margin_wout_dph DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00'

			)

		";

		$this->DatabaseTransformer->query($add_columns);

		

		die('zkopirovano');

	}

	

	function attributes_transform() {

		// dropnu tabulku attributes

		$drop = 'DROP TABLE IF EXISTS attributes';

		$this->DatabaseTransformer->query($drop);

		

		// zalozim si novou s atributy pro novy shop

		$create = "

		CREATE TABLE IF NOT EXISTS `attributes` (

			`id` int(10) unsigned NOT NULL auto_increment,

			`created` datetime NOT NULL,

			`modified` datetime NOT NULL,

			`option_id` int(10) unsigned NOT NULL default '0',

			`value` varchar(50) NOT NULL,

			`sort_order` int(11) NOT NULL,

			PRIMARY KEY  (`id`),

			KEY `option_id` (`option_id`,`value`),

			KEY `value_id` (`value`)

			)

		";

		$this->DatabaseTransformer->query($create);

		

		$old_attributes = $this->DatabaseTransformer->query('SELECT * FROM old_attributes AS Attribute');

		App::import('Model', 'Attribute');

		$this->Attribute = &new Attribute;

		foreach ($old_attributes as $index => $attribute) {

			$value_query = '

				SELECT *

				FROM `values` as `Value`

				WHERE

					`Value`.`id` = ' . $attribute['Attribute']['value_id'];

			$value = $this->DatabaseTransformer->query($value_query);

			$value = $value[0];

			//$value = $this->Value->read(null, $attribute['Attribute']['value_id']);

			// nastavim hodnotu Attribute->value

			$attribute['Attribute']['value'] = $value['Value']['name'];

			// vypocitam Attribute->sort_order

			$max_sort_order = $this->Attribute->find('count', array(

				'conditions' => array('option_id' => $attribute['Attribute']['option_id']),

				'recursive' => -1

			));



			$max_sort_order++;

			$attribute['Attribute']['sort_order'] = $max_sort_order;

			// ulozim aktualni atribut

			if (!$this->Attribute->save($attribute)) {

				debug($value);

				debug($attribute);

				die('kopirovani se nezdarilo');

			}

		}

		

		//$remove_values = 'ALTER TABLE values RENAME TO old_values';

		//$this->DatabaseTransformer->query($remove_values);

		

		die('zkopirovano');

	}

	

	function subproducts_transform() {

		// dropnu starou tabulku subproducts

		$drop_old_subproducts = "DROP TABLE IF EXISTS subproducts";

		$this->DatabaseTransformer->query($drop_old_subproducts);



		// vytvorim si novou

		$create_new_subproducts = "

			CREATE TABLE IF NOT EXISTS `subproducts` (

			  `id` int(10) unsigned NOT NULL auto_increment,

			  `created` datetime NOT NULL,

			  `modified` datetime NOT NULL,

			  `price_with_dph` decimal(10,2) unsigned NOT NULL default '0.00',

			  `price_wout_dph` decimal(10,2) unsigned NOT NULL,

			  `product_id` int(10) unsigned NOT NULL default '0',

			  `pieces` int(11) NOT NULL,

			  `active` tinyint(1) NOT NULL default '0',

			  `availability_id` int(10) unsigned NOT NULL,

			  PRIMARY KEY  (`id`)

			)

		";

		$this->DatabaseTransformer->query($create_new_subproducts);



		// dropnu starou tabulku attributes_subproducts

		$drop_old_attributes_subproducts = "DROP TABLE IF EXISTS attributes_subproducts";

		$this->DatabaseTransformer->query($drop_old_attributes_subproducts);



		// a vytvorim si novou

		$create_new_attributes_subproducts = "

			CREATE TABLE IF NOT EXISTS `attributes_subproducts` (

			  `id` int(10) unsigned NOT NULL auto_increment,

			  `created` datetime NOT NULL,

			  `modified` datetime NOT NULL,

			  `attribute_id` int(10) unsigned NOT NULL,

			  `subproduct_id` int(10) unsigned NOT NULL,

			  PRIMARY KEY  (`id`)

			)

		";

		$this->DatabaseTransformer->query($create_new_attributes_subproducts);

		

		// nactu si produkty

		App::import('Model', 'Product');

		$this->Product = &new Product;



		// prenastavim zdroje pro modely na zalozni tabulky

		$this->Product->setSource('old_products');

		$this->Product->Subproduct->setSource('old_subproducts');

		$this->Product->Subproduct->Attribute->setSource('old_attributes');

//		$this->Product->Subproduct->Attribute->Value->setSource('old_values');

			

		// nactu idcka produktu

		$products = $this->Product->find('all', array(

//			'conditions' => array('Product.id >' => 895),

			'order' => array('Product.id' => 'asc'),

			'contain' => array(

				'Subproduct' => array(

					'Attribute' => array(

						'Option'//, 'Value'

					),

					'fields' => array('id', 'price')

					

				)

			),

			'fields' => array('id')

		));



		// nastavim zdroje pro modely zpatky na nove tabulky

		$this->Product->setSource('products');

		$this->Product->Subproduct->setSource('subproducts');

			

		// vyfiltruju produkty, ktere nemaji subprodukty

		$products = array_filter($products, array('DatabaseTransformersController', 'leave_empty'));

	

		// pro kazdy ze zbylych produktu (co maji subprodukty)

		foreach ($products as $product) {

			// inicializace

			$subproducts = array();

			// pro kazdy subprodukt aktualniho produktu

			foreach ($product['Subproduct'] as $subproduct) {

				// generuju pole subproduktu podle option_id

				if (isset($subproduct['Attribute']['option_id'])) {

					$subproducts[$subproduct['Attribute']['option_id']][] = $subproduct;

				}

			}



			// vygeneruju si nove subprodukty s jejich atributy

			$combinations = $this->DatabaseTransformer->combine($subproducts);

			foreach ($combinations as $combination) {

				// vytvorim subprodukt

				$new_subproduct = array();

				$new_subproduct['Subproduct']['product_id'] = $product['Product']['id'];

				unset($this->Product->Subproduct->id);

				// ulozim

				if (!$this->Product->Subproduct->save($new_subproduct)) {

					debug($product);

					debug($new_subproduct);

					die('CHYBA');

				}

				debug($this->Product->Subproduct->id);

				// zapamatuju si jeho idcko, abych ho pak mohl pridavat do vztahu mezi subproduktem a atributy

				$subproduct_id = $this->Product->Subproduct->id;

				// inicializace prirustkove ceny subproduktu

				$subproduct_price = 0;

				// prochazim mozne kombinace

				foreach ($combination as $attributes_subproduct) {

					// generuju vztahy mezi subproduktem a atributy

					$new_attributes_subproduct = array();

					$new_attributes_subproduct['AttributesSubproduct']['subproduct_id'] = $subproduct_id;

					$new_attributes_subproduct['AttributesSubproduct']['attribute_id'] = $attributes_subproduct['Attribute']['id'];

					$subproduct_price += $attributes_subproduct['price'];

					// ulozim

					$insert = "

						INSERT INTO attributes_subproducts (subproduct_id, attribute_id)

						VALUES (" . $new_attributes_subproduct['AttributesSubproduct']['subproduct_id'] . ", " . $new_attributes_subproduct['AttributesSubproduct']['attribute_id'] . ")

					";

					$this->DatabaseTransformer->query($insert);

					// updatuju prirustkovou cenu subproduktu

					$this->Product->Subproduct->contain();

					$this->Product->Subproduct->read(null, $subproduct_id);

					$this->Product->Subproduct->set(array(

						'price_with_dph' => $subproduct_price

					));

					$this->Product->Subproduct->save();

				}

				// vytvorim vztahy mezi subproduktem a jeho atributy z pole combinations

			}

		}

		die('hotovo');

	}



	function discount_models_products_transform() {

		$price_column = "ALTER TABLE `discount_models_products` CHANGE `price` `price_with_dph` DECIMAL( 10, 2 ) UNSIGNED NOT NULL DEFAULT '0.00' ";

		$this->DatabaseTransformer->query($price_column);



		die('zmeneno');

	}

	

	function leave_empty($a) {

		return (!empty($a['Subproduct']));

	}

	

	function view() {

		die('test');

	}

}

?>