<?
class StatisticsController extends AppController {
	var $name = 'Statistics';

	function admin_index(){
		if ( isset($this->data) ){

		} else {
			$this->data['Statistic']['from']['hour'] = '0';
			$this->data['Statistic']['from']['min'] = '00';
			$this->data['Statistic']['from']['day'] = '01';
			$this->data['Statistic']['from']['month'] = date('m');
			$this->data['Statistic']['from']['year'] = date('Y');

			$this->data['Statistic']['to']['hour'] = '23';
			$this->data['Statistic']['to']['min'] = '59';
			$this->data['Statistic']['to']['day'] = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')); // 31  ;
			$this->data['Statistic']['to']['month'] = date('m');
			$this->data['Statistic']['to']['year'] = date('Y');
		}
		
		$date_from = $this->data['Statistic']['from']['year'] . '-' . $this->data['Statistic']['from']['month'] . '-' . $this->data['Statistic']['from']['day'] . ' ' . $this->data['Statistic']['from']['hour'] . ':' . $this->data['Statistic']['from']['min'];
		$date_to = $this->data['Statistic']['to']['year'] . '-' . $this->data['Statistic']['to']['month'] . '-' . $this->data['Statistic']['to']['day'] . ' ' . $this->data['Statistic']['to']['hour'] . ':' . $this->data['Statistic']['to']['min'] . ':59';

		$this->set('sold_products', $this->Statistic->most_sold($date_from, $date_to));
		$this->set('stats', $this->Statistic->stats($date_from, $date_to));
		$this->set('orders', $this->Statistic->orders($date_from, $date_to));
		
		$this->set(compact('date_from', 'date_to'));
	}
	
	function admin_orders_download() {
		$this->layout = 'csv_file';
		$this->set('file_name', 'prodeje.csv');
		$orders = $this->Statistic->orders($this->data['Statistic']['date_from'], $this->data['Statistic']['date_to']);
		$this->set('orders', $orders);
	}

	function admin_p(){
		if ( !isset($this->data) ){
			$this->data['Statistic']['from']['hour'] = '0';
			$this->data['Statistic']['from']['min'] = '00';
			$this->data['Statistic']['from']['day'] = '1';
			$this->data['Statistic']['from']['month'] = date('m');
			$this->data['Statistic']['from']['year'] = date('Y');

			$this->data['Statistic']['to']['hour'] = '0';
			$this->data['Statistic']['to']['min'] = '00';
			$this->data['Statistic']['to']['day'] = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')); // 31  ;
			$this->data['Statistic']['to']['month'] = date('m');
			$this->data['Statistic']['to']['year'] = date('Y');
		}

		$date_from = $this->data['Statistic']['from']['year'] . '-' . $this->data['Statistic']['from']['month'] . '-' . $this->data['Statistic']['from']['day'] . ' ' . $this->data['Statistic']['from']['hour'] . ':' . $this->data['Statistic']['from']['min'];
		$date_to = $this->data['Statistic']['to']['year'] . '-' . $this->data['Statistic']['to']['month'] . '-' . $this->data['Statistic']['to']['day'] . ' ' . $this->data['Statistic']['to']['hour'] . ':' . $this->data['Statistic']['to']['min'];

		$this->set('provisions', $this->Statistic->orders_wout_tax($date_from, $date_to));
	}
	

	function most_sold($id){
		if ( $id == 5 ){
			$id = null;
		}
		return array('most_sold' => $this->Statistic->most_sold(null, null, 5, $id));
	}
	
	function similar_products($id){
		return array('similar_products' => $similar_products = $this->Statistic->similar_products($id));
	}
}
?>