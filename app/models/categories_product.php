<?php

class CategoriesProduct extends AppModel {



	var $actsAs = array('Containable');

	

	var $name = 'CategoriesProduct';

	

	var $belongsTo = array('Category', 'Product');

	

/*	function paginate($conditions = array(), $fields = array(), $order = array(), $limit = 20, $page = 1, $recursive = null, $extra = array()) {

		// pokud chci vybirat aktivni produkty (pro vypis kategorii nebo podle vyrobcu)

		if (isset($extra['active_products'])) {

			$conditions += array('Product.active' => true, 'Product.confirmed' => true);

			// pokud chci radit podle nazvu vyrobce

			if (isset($extra['sort_by_manufacturer_name'])) {

				$manufacturers = $this->Product->Manufacturer->find('all', array(

					'conditions' => array(),

					'order' => $extra['sort_by_manufacturer_name'],

					'contain' => array()

				));

				$manufacturers_order = Set::extract('/Manufacturer/id', $manufacturers);

				$products = $this->find('all', array(

					'conditions' => $conditions,

					// musis si vytahnout vsechna pole '*' a k tomu se do field prida ta funkce se seznamem idecek vyrobcu

					'fields' => "*, FIELD(manufacturer_id, '" . implode("', '", $manufacturers_order) . "') as sort_order",

					// a radit se musi podle vysledku te dane funkce

					'order' => array("sort_order" => 'asc'),

					'contain' => array(

						'Product' => array(

							'Manufacturer',

							'Subproduct' => array(

								'conditions' => array(

									'Subproduct.active' => 1

								)

							),

							'Image' => array(

								'conditions' => array(

									'is_main' => '1'

								),

								'fields' => array('name')

							)

						)

					),

					'page' => $page,

					'limit' => $limit

				));

			} else {

				// radim podle ceny nebo nazvu produktu

				$contain = array(

					'Product' => array(

						'Manufacturer',

						'Subproduct' => array(

							'conditions' => array(

								'Subproduct.active' => 1

							)

						),

						'Image' => array(

							'conditions' => array(

								'is_main' => '1'

							),

							'fields' => array('name')

						)

					)

				);

				$products = $this->find('all', compact('conditions', 'limit', 'page', 'contain', 'order'));

			}

			return $products;

		} else {

			return $this->find('all', compact('conditions', 'contain', 'fields', 'order', 'limit', 'page', 'recursive'));

		}

	}

	

	function paginateCount($conditions = null, $recursive = 0, $extra = array()) {

		if (isset($extra['active_products'])) {

			$conditions += array('Product.active' => true, 'Product.confirmed' => true);

			// radim podle ceny nebo nazvu produktu

			$contain = array(

				'Product' => array(

					'conditions' => array(

						'Product.active' => 1

					),

					'Manufacturer',

					'Subproduct' => array(

						'conditions' => array(

							'Subproduct.active' => 1

						)

					)

				)

			);

			$products_count = $this->find('count', compact('conditions', 'limit', 'page', 'contain', 'order'));

			return $products_count;

		} else {

			$count = $this->find('count', compact('conditions', 'contain', 'fields', 'order', 'limit', 'page', 'recursive'));

			return $count;

		}

	}*/

}

?>