<?php 
class Transaction extends AppModel {
	var $name = 'Transaction';
	
	var $useTable = false;
	
	var $virtualFields = array(
		'date' => 'asd',
		'type' => 'asd',
		'price' => 'asd'
	);
	
	var $export_fields = array(
		'Transaction.date',
		'Transaction.type',
		'Transaction.price'
	);
	
	function paginateCount($conditions = array(), $recursive = null, $extra = array()) {
		if (isset($extra['customer_id'])) {
			$customer_id = $extra['customer_id'];
		}
		
		if (!isset($customer_id)) {
			return false;
		}
		
		$count = $this->find('count', array(
			'conditions' => array('customer_id' => $customer_id)
		));
		
		return $count;
	}
	
	function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
		$transactions = array();
		if ($page > 0) {
			if (isset($extra['customer_id'])) {
				$customer_id = $extra['customer_id'];
			} else {
				return false;
			}
	
			$transactions = $this->find('all', array(
				'conditions' => array('customer_id' => $customer_id),
				'order' => $order,
				'limit' => $limit,
				'page' => $page
			));
		}
		
		return $transactions;
	}
	
	function find($type, $options = array()) {
		if (!isset($options['conditions']['customer_id'])) {
			return false;
		}
		
		$customer_id = $options['conditions']['customer_id'];
		
		if ($type == 'count') {
			$fields = 'COUNT(*)';
		} elseif ($type == 'all') {
			$fields = '*';
		} else {
			return false;
		}

		$query = '
			SELECT ' . $fields . '
			FROM (
				SELECT
					Sale.customer_bonus AS price,
					Sale.date AS date,
					"prodej" AS type,
					Sale.modified AS modified
				FROM sales AS Sale
				WHERE
					Sale.customer_id = ' . $customer_id . '
				UNION
				SELECT
					Sale.recommending_customer_bonus AS price,
					Sale.date AS date,
					"prodej doporučené" AS type,
					Sale.modified AS modified
				FROM sales AS Sale
				WHERE
					Sale.recommending_customer_id = ' . $customer_id . '
				UNION
				SELECT
					-(PayOut.amount) AS price,
					PayOut.date AS date,
					"výplata" AS type,
					PayOut.modified AS modified
				FROM pay_outs AS PayOut
				WHERE
					PayOut.customer_id = ' . $customer_id . '
			) AS Transaction
		';

		if (isset($options['order']) && !empty($options['order'])) {
			$order = $options['order'];
			if (is_array($order)) {
				$order = array();
				$order_items = $options['order'];
				foreach ($order_items as $key => $direction) {
					$order[] = $key . ' ' . $direction;
					// pokud radim podle date
					if ($key == 'date') {
						// pridam razeni podle modified, protoze by mi jinak nesedelo poradi v ramci jednoho dne
						$order[] = 'modified' . ' ' . $direction;
					}
				}
				$order = implode(', ', $order);
			}
			$query .= 'ORDER BY ' . $order;
		}
		
		
		if (isset($options['limit'])) {
			$limit = $options['limit'];
			if (isset($options['page'])) {
				$page = $options['page'];
				$offset = (($page - 1) * $limit);
				$limit = $offset . ',' . $limit;
			}
			$query .= '
				LIMIT ' . $limit;
		}

		$result = $this->query($query);
		
		if ($type == 'count') {
			$result = $result[0][0][$fields];
		}
		
		return $result;
	}
}
?>