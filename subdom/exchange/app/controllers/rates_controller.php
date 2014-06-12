<?php 
class RatesController extends AppController {
	var $name = 'Rates';
	
	var $paginate = array(
		'limit' => 30
	);
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('run', 'delete_old');
	}
	
	/**
	 * stahne aktualni kurzy a ulozi do db
	 */
	function run() {
		// cron se spousti jen v od PO do PA v casech, kdy jsou dostupne kurzy
		// chci zakazat spousteni skriptu ve statni svatky
		// data, kdy se nema spoustet skript
		$forbidden_dates = array('1.1.', '1.5.', '8.5.', '5.7.', '6.7.', '28.9.', '28.10.', '17.11.', '24.12', '25.12.', '26.12.');
		// velikonoce
		$easter_date = easter_date(date('Y'));
		$easter_date = strtotime('+1 day', $easter_date);
		$easter_date = intval(date('d', $easter_date)) . '.' . intval(date('m', $easter_date)) . '.';
		$forbidden_dates[] = $easter_date;
		
		$date = intval(date('d')) . '.' . intval(date('m')) . '.';
		if (!in_array($date, $forbidden_dates)) {
		
			$messages = array();
			$url = 'http://www.exchange.cz/deviza.php';
	
			if ($html = file_get_contents($url)) {
				$pattern = '/<table class="kurzovniListek".+alt="EUR" \/>EUR<\/td><td>1<\/td><td>([^<]+)</';
				if (preg_match($pattern, $html, $matches)) {
					$rate = array(
						'Rate' => array(
							'value' => $matches[1]
						)
					);
					
					if (!$this->Rate->save($rate)) {
						$messages[] = 'nepodarilo se ulozit aktualni kurz - ' . $rate['Rate']['value'];
						$messages = array_merge($messages, $this->Rate->validationErrors);
					}
				} else {
					$messages[] = 'Nesedi pattern pri zjistovani kurzu.';
				}
			} else {
				$messages[] = 'Nepodarilo se stahnout http://www.exchange.cz/deviza.php';
			}
			// pokud doslo k nejake chybe, poslu email
			if (!empty($messages)) {
				$messages = implode("\n", $messages);
				$this->Rate->notify($messages);
			}
		}
		
		die();
	}
	
	/**
	 * smaze zaznamy z tabulky rates starsi nez zadana hodnota
	 */
	function delete_old($interval = '-3 months') {
		$date = date('Y-m-d H:i:s', strtotime($interval));
		if (!$this->Rate->deleteAll(array('Rate.created <=' => $date))) {
			$this->Rate->notify('Nepodařilo se odstranit záznamy starší než ' . $date);
		}
		die();
	}
	
	function admin_index() {
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'rates') {
			$this->Session->delete('Search.Rate');
			$passedArgs = $this->passedArgs;
			unset($passedArgs['reset']);
			$this->redirect(array('action' => 'index') + $passedArgs);
		}
		
		$conditions = array();
		
		// pokud chci vysledky vyhledavani
		if ( isset($this->data)) {
			$this->Session->write('Search.Rate', $this->data['Rate']);
			$conditions = array_merge($conditions, $this->Rate->do_form_search($this->data['Rate']));
		} elseif ($this->Session->check('Search.Rate')) {
			$this->data['Rate'] = $this->Session->read('Search.Rate');
			$conditions = array_merge($conditions, $this->Rate->do_form_search($this->data['Rate']));
		}
		$this->paginate['conditions'] = $conditions;
		$this->paginate['order'] = array('Rate.created' => 'asc');
		$rates = $this->paginate();
		$this->set('rates', $rates);
	}
	
	function admin_csv_export() {
		$conditions = array();
		// natahnu si ze sesny ulozene podminky vyhledavani
		if ($this->Session->check('Search.Rate')) {
			$conditions = array_merge($conditions, $this->Rate->do_form_search($this->Session->read('Search.Rate')));
		}
		// pokud chci data nejak serazena
		$order = array('Rate.created' => 'asc');
		if (isset($this->params['named']['sort'])) {
			$order = array($this->params['named']['sort'] => 'asc');
			if (isset($this->params['named']['direction']) && $this->params['named']['direction'] == 'desc') {
				$order = array($this->params['named']['sort'] => 'desc');
			}
		}
		$this->Rate->virtualFields['date'] = 'date(Rate.created)';
		$this->Rate->virtualFields['time'] = 'time(Rate.created)';

		$rates = $this->Rate->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'order' => $order,
			'fields' => array('date(Rate.created) AS Rate__date', 'time(Rate.created) AS Rate__time', 'Rate.value')
		));

		$this->Rate->create_csv($rates);
		$this->redirect('/' . $this->Rate->export_file);
	}
}
?>