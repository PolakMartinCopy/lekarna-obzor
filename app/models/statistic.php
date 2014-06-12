<?php
class Statistic extends AppModel {
	var $name = 'Statistic';

	var $useTable = false;

	var $hasMany = array('OrderedProduct', 'Order');

	function most_sold($date_from = null, $date_to = null, $limit = 10, $category_id = null){
		$category_condition = "";
		if ( $category_id != null ){
			App::import('model', 'Category');
			$this->Category = &new Category;
			$children = $this->Category->children($category_id, false, array('id'));

			$category_condition = "AND `CategoriesProduct`.`category_id` = '" . $category_id . "'";
			if ( !empty($children) ){
				$category_ids = array();
				foreach ( $children as $child ){
					$category_ids[] = $child['Category']['id'];
				}
				$category_ids[] = $category_id;
				$category_condition = "AND `CategoriesProduct`.`category_id` IN ('" . implode("', '", $category_ids) . "')";
				
			}
		}

		$date_condition = "";
		if ( !empty($date_from) AND !empty($date_to) ){
			$date_condition = " AND `OrderedProduct`.`created` > '" . $date_from . "' AND `OrderedProduct`.`created` < '" . $date_to . "'";
		}

		App::import('Model', 'OrderedProduct');
		$this->OrderedProduct = new OrderedProduct;
		
		$date_from = explode(' ', $date_from);
		$date_from = $date_from[0];
		
		$sold_products = $this->OrderedProduct->find('all', array(
			'conditions' => array(
				'Order.created >=' => $date_from,
				'Order.created <=' => $date_to
			),
			'contain' => array('Product', 'Order'),
			'fields' => array(
				'OrderedProduct.id',
				'OrderedProduct.created',
				'Product.id',
				'Product.name',
				'Product.url',
				'SUM(OrderedProduct.product_quantity) AS OrderedProduct__quantity',
				'SUM(OrderedProduct.product_quantity) * Product.retail_price_with_dph AS OrderedProduct__total_price'
			),
			'group' => 'OrderedProduct.product_id',
//			'limit' => $limit,
			'order' => array('OrderedProduct__quantity' => 'desc')
		));
		
		return $sold_products;
	}

	function stats($date_from = null, $date_to = null){
		App::import('Model', 'Order');
		$this->Order = &new Order;

		$conditions = array();
		if ($date_from != null AND $date_to != null ){
			$date_from = explode(' ', $date_from);
			$date_from = $date_from[0];
			$conditions[] = array(
				'Order.created >=' => $date_from,
				'Order.created <=' => $date_to
			);
		}
		// pocet objednavek v danem rozmezi
		$orders['Order']['count'] = $this->Order->find('count', array(
			'conditions' => $conditions,
		));
		// hodnota objednavek v danem rozmezi
		$orders['Order']['total'] = $this->Order->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('SUM(subtotal_with_dph) as total')
		));
		$orders['Order']['total'] = $orders['Order']['total'][0][0]['total'];
		
		return $orders;
	}
	
	function orders($date_from = null, $date_to = null){
		App::import('Model', 'Order');
		$this->Order = &new Order;
		
		$date_from = explode(' ', $date_from);
		$date_from = $date_from[0];
		
		// vytahnu si vsechny objednavky vcetne prodanych produktu
		$conditions = array(
			'Order.created >=' => $date_from,
			'Order.created <=' => $date_to
		);
		
		$contain = array(
			'OrderedProduct' => array(
				'fields' => array(
					'created', 'modified', 'product_id', 'product_price_with_dph', 'product_quantity'
				),
				'Product' => array(
					'fields' => array(
						'id', 'name', 'tax_class_id'
					),
					'TaxClass'
				)
			),
			'Status' => array(
				'fields' => array(
					'id', 'name'
				)
			)
		);
		
		$fields = array('id', 'status_id', 'customer_zip', 'delivery_zip', 'customer_id', 'customer_name');
		
		$orders = $this->Order->find('all', array(
			'conditions' => $conditions,
			'fields' => $fields,
			'contain' => $contain
		));

		return $orders;
	}

	function orders_wout_tax($date_from = null, $date_to = null){
		$this->bindModel(array('hasMany' => array('Order')));
		
		$conditions = array();
		if ($date_from != null AND $date_to != null ){
			$conditions[] = 'Order.created > \'' . $date_from . '\' AND Order.created < \'' . $date_to . '\'';
		}
		
		$this->Order->recursive = -1;
		$orders = $this->Order->find('all', array('conditions' => $conditions));
		$total_with = 0;
		$total_wout = 0;
		foreach ( $orders as $order ){
			$products = $this->Order->OrderedProduct->find('all', array('conditions' => array('order_id' => $order['Order']['id'])));
			foreach ( $products as $product ){
				$total_with = $total_with + $product['OrderedProduct']['product_price_with_dph'] * $product['OrderedProduct']['product_quantity']; 
				$this->Order->OrderedProduct->Product->recursive = -1;
				$tax = $this->Order->OrderedProduct->Product->read(array('Product.tax_class_id'), $product['OrderedProduct']['product_id']);
				$tax = ( $tax['Product']['tax_class_id'] == 1 ) ? 1.19 : 1.05;
				$total_wout = $total_wout + ( $product['OrderedProduct']['product_price_with_dph'] * $product['OrderedProduct']['product_quantity'] ) / $tax;
			}
		}
		
		return array('with' => $total_with, 'wout' => $total_wout, 'prov' => ceil($total_wout * 0.03));
	}
	
	function similar_products($product_id){
		App::import('model', 'OrderedProduct');
		$this->OrderedProduct = &new OrderedProduct;

		$similar_products = array();

		// najdu si nejdriv vsechny objednavky,
		// ve kterych se vyskytoval tento produkt
		$this->OrderedProduct->recursive = -1;
		$order_ids = $this->OrderedProduct->find('all', array(

			'conditions' => array('OrderedProduct.product_id' => $product_id),

			'fields' => array('DISTINCT (order_id)')

		));

		if ( !empty($order_ids) ){
			$oIDs = array();
			foreach( $order_ids as $order_id ){
				$oIDs[] = $order_id['OrderedProduct']['order_id'];
			}

			$similar_products = $this->OrderedProduct->find('all', array(

				'conditions' => array('order_id' => $oIDs, 'product_id' => 'not ' . $product_id ),

				'fields' => array('DISTINCT( product_id )'),

				'order' => array('product_id' => 'asc'),

				'recursive' => 2

			));
			if ( !empty($similar_products) ){
				$similar_product_ids = array();
				foreach ( $similar_products as $similar_product ){
					$similar_product_ids[] = $similar_product['OrderedProduct']['product_id'];
				}

				App::import('model', 'Product');
				$this->Product = &new Product;
				$this->Product->recursive = 1;
				$this->Product->unbindModel( array(
					'hasAndBelongsToMany' => array('Category', 'Cart', 'Flag'),
					'hasMany' => array('Subproduct', 'CartsProduct'),
					'belongsTo' => array('TaxClass', 'Availability')
					)
				);
				$similar_products = $this->Product->find('all', array(

					'conditions' => array('Product.id' => $similar_product_ids),

					'fields' => array('Product.id', 'Product.name', 'Product.url', 'Product.short_description', 'Product.price', 'Manufacturer.id', 'Manufacturer.name')

				));
			}
		}
		return $similar_products;
	}
}
?>