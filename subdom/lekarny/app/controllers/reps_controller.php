<?
class RepsController extends AppController {
	var $name = 'Reps';
	
	function admin_index() {
		$this->layout = 'admin';
		
		$reps = $this->Rep->find('all');
		$this->set('reps', $reps);
	}
	
	function admin_add() {
		$this->layout = 'admin';
		
		if (isset($this->data)) {
			$this->Rep->set($this->data);
			if ($this->Rep->validates()) {
//				$password = $this->Rep->create_password($this->data['Rep']['email']);
//				$this->data['Rep']['login'] = $this->Rep->create_login($this->data['Rep']['last_name'], $this->data['Rep']['email']);
//				$this->data['Rep']['password'] = md5($password);
				$this->Rep->save($this->data);
//				$this->Rep->notify_rep($this->data, $password);
				$this->Session->setFlash('Nový rep byl vytvořen');
				$this->redirect(array('controller' => 'reps', 'action' => 'index'));
			} else {
				unset($this->data['Rep']['password']);
				$this->Session->setFlash('Uložení se nezdařilo, opravte chyby a opakujte akci prosím');
			}
		}
	}

	function admin_edit($id) {
		$this->layout = 'admin';
		
		$this->set('id', $id);

		if (isset($this->data)) {
			$this->Rep->set($this->data);
			if ($this->Rep->validates()) {
				$this->Rep->save($this->data);
				$this->Session->setFlash('Rep byl upraven');
				$this->redirect(array('controller' => 'reps', 'action' => 'index'));
			} else {
				unset($this->data['Rep']['password']);
				$this->Session->setFlash('Upravení se nezdařilo, opravte chyby a opakujte akci prosím');
			}
		} else {
			$this->Rep->id = $id;
			$this->data = $this->Rep->read();
			unset($this->data['Rep']['password']);
		}
	}
	
	function admin_activate($id) {
		$rep['Rep']['id'] = $id;
		$rep['Rep']['active'] = true;
		if ($this->Rep->save($rep)) {
			$this->Session->setFlash('Rep byl aktivován');
		} else {
			$this->Session->setFlash('Aktivace repa se nezdařila, opakujte akci');
		}
		$this->redirect(array('controller' => 'reps', 'action' => 'index'));
	}
	
	function admin_deactivate($id) {
		$this->Rep->id = $id;
		$rep = $this->Rep->read();
		
		$rep['Rep']['active'] = false;
		if ($this->Rep->save($rep)) {
			$this->Session->setFlash('Rep byl deaktivován');
		} else {
			$this->Session->setFlash('Deaktivace se nezdařila, opakujte akci');
		}
		
		$this->redirect(array('controller' => 'reps', 'action' => 'index'));
	}

	/**
	 * Vygeneruje repovy prihlasovaci udaje a pokud ma zadany email, tak se mu poslou
	 *
	 * @param int $id - ID repa
	 */
	function admin_notify_rep($id) {
		$this->Rep->id = $id;
		$this->Rep->contain();
		$rep = $this->Rep->read();
		
		// jestlize nema rep zadany email, tak se vypise chyba a dal se nepokracuje
		if ( empty($rep['Rep']['email']) ) {
			$this->Session->setFlash('Tento rep nemá zadaný email. Vložte email a pošlete přihlašovací údaje znovu');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		// vygeneruju prihlasovaci udaje, ulozim do db a poslu je repovi
		$password = $this->Rep->create_password($rep['Rep']['email']);
		$rep['Rep']['login'] = $this->Rep->create_login($rep['Rep']['last_name'], $rep['Rep']['email']);
		$rep['Rep']['password'] = md5($password);
		$this->Rep->save($rep);
		$this->Rep->notify_rep($rep, $password);
		
		$this->Session->setFlash('Přihlašovací údaje byly odeslány na repův email');
		$this->redirect(array('controller' => 'reps', 'action' => 'index'));
	}
	
	function rep_login() {
			if (  isset($this->data) ){
			$conditions = array('login' => $this->data['Rep']['login']);
			$rep = $this->Rep->find('first', array(
				'conditions' => $conditions,
				'contain' => array()
			));
			if ( empty( $rep ) ){
				$this->Session->setFlash('Neplatné uživatelské jméno!');
			} else {
				if ( !$rep['Rep']['active'] ) {
					$this->Session->setFlash('Váš uživatelský účet není aktivní, kontaktujte administrátora za účelem aktivace účtu.');
				} elseif ( $rep['Rep']['password'] != md5($this->data['Rep']['password']) ){
					$this->Session->setFlash('Neplatné heslo!');
				} else {
					$this->Session->write('Rep', $rep['Rep']);
					$actual_month = array(
						'start_date' => array(
							'day' => 1,
							'month' => date('m'),
							'year' => date('Y')
						),
						'end_date' => array(
							'day' => date('t'),
							'month' => date('m'),
							'year' => date('Y')
						)
					);
					$this->Session->write('Rep.order_dates', $actual_month);
					$this->redirect(array('rep' => 'true', 'controller' => 'reps', 'action' => 'index'), null, true);
				}
			}
		}
	}
	
	function rep_index() {
		$this->layout = 'rep';
		
		$rep_id = $this->Session->read('Rep.id');
		$this->Rep->id = $rep_id;
		$rep = $this->Rep->read();
		
		// pocet objednavek v aktualnim mesici pro prihlaseneho repa
		App::import('Model', 'Order');
		$this->Order = &new Order;
		
		if (!empty($rep['RepArea'])) {
			// podminka pro vyber objednavek pouze z repovych oblasti
			$areas_conditions = array();
			foreach ($rep['RepArea'] as $area) {
				$area_condition = array(
					'AND' => array(
						'delivery_postal_code >=' => $area['start_zip'],
						'delivery_postal_code <=' => $area['end_zip'],
					)
				);
				
				$areas_conditions['OR'][] = $area_condition;
			}
			
			// podminka pro vyber objednavek v zadanem casovem obdobi
			$date_condition = array(
				'AND' => array(
					'created >=' => date('Y') . '-' . date('m') . '-01',
					'created <=' => date('Y') . '-' . date('m') . '-' . date('t')
				)
			);
			
			// spojim si pripravene podminky do vysledne
			$rep_conditions = array(
				'AND' => $date_condition + $areas_conditions
			);
			
			// celkovy pocet objednavek
			$orders = $this->Order->find('all', array(
				'conditions' => $rep_conditions,
				'contain' => array()
			));
			$this->set('orders_count', sizeof($orders));
			
			// pocet neprevzatych objednavek
			$not_taken_over_orders_count = $this->Order->find('count', array(
				'conditions' => $rep_conditions + array('status_id' => 1),
				'contain' => array()
			));
			$this->set('not_taken_over_orders_count', $not_taken_over_orders_count);
			
			// pocet prevzatych objednavek
			$taken_over_orders_count = $this->Order->find('count', array(
				'conditions' => $rep_conditions + array('status_id' => 2),
				'contain' => array()
			));
			$this->set('taken_over_orders_count', $taken_over_orders_count);
			
			// pocet objednavek - zbozi odeslano
			$goods_sent_orders_count = $this->Order->find('count', array(
				'conditions' => $rep_conditions + array('status_id' => 3),
				'contain' => array()
			));
			$this->set('goods_sent_orders_count', $goods_sent_orders_count);
			
			// prumerna hodnota objednavky a celkovy soucet objednavek
			$total = 0;
			foreach ($orders as $order) {
				$total += $order['Order']['subtotal'];
			}
			$this->set('average_order', (sizeof($orders)>0) ? round($total / sizeof($orders), 2) : '-');
			$this->set('sum_order', $total);
			
			// pocet registrovanych zakazniku
			$companies_count = $this->Order->Company->find('count', array(
				'conditions' => $areas_conditions + array('active' => true),
				'contain' => array()
			));
			$this->set('companies_count', $companies_count);
		} else {
			$this->set('orders_count', 0);
			$this->set('not_taken_over_orders_count', 0);
			$this->set('taken_over_orders_count', 0);
			$this->set('goods_sent_orders_count', 0);
			$this->set('average_order', 0);
			$this->set('sum_order', 0);
			$this->set('companies_count', 0);
		}
	}
	

	function rep_edit() {
		$this->layout = 'rep';
		
		if (isset($this->data)) {
			if (empty($this->data['Rep']['password'])) {
				unset($this->data['Rep']['password']);
			} else {
				$this->data['Rep']['password'] = md5($this->data['Rep']['password']);
			}
			if ($this->Rep->save($this->data)) {
				$this->Session->setFlash('Vaše osobní údaje byly upraveny');
				$this->redirect(array('rep' => true, 'controller' => 'reps', 'action' => 'index'));
			} else {
				unset($this->data['Rep']['password']);
				$this->Session->setFlash('Vaše osobní údaje se nepodařilo upravit, opravte prosím chyby a opakujte akci');
			}
		} else {
			$rep_id = $this->Session->read('Rep.id');
			$this->Rep->id = $rep_id;
			$this->Rep->contain();
			$rep = $this->Rep->read();
			$this->data = $rep;
			unset($this->data['Rep']['password']);
		}
	}
	
	
	function rep_logout() {
		$this->Session->del('Rep');
		$this->Session->setFlash('Byl jste odhlášen(a)');
		$this->redirect(array('rep' => true, 'controller' => 'reps', 'action' => 'login'), null, true);
	}

/*	function init() {
		$rep_csv = '"Maroušek";10000;10999;"PRAHA 10";"Maroušek"
"Maroušek";11000;11999;"PRAHA 1";"Maroušek"
"Maroušek";12000;12999;"PRAHA 2";"Maroušek"
"Maroušek";13000;13999;"PRAHA 3";"Maroušek"
"Maroušek";14000;14999;"PRAHA 4";"Maroušek"
"Maroušek";15000;15999;"PRAHA 5";"Maroušek"
"Maroušek";16000;16999;"PRAHA 6";"Maroušek"
"Maroušek";17000;17999;"PRAHA 7";"Maroušek"
"Maroušek";18000;18999;"PRAHA 8";"Maroušek"
"Maroušek";19000;19999;"PRAHA 9";"Maroušek"
"Maroušek";20000;24999;"Praha OST";"Maroušek"
"Maroušek";25001;25169;"Praha-východ";"Maroušek"
"Maroušek";25201;25401;"Praha-západ";"Maroušek"
"Sedláček";25601;25999;"Benešov";"Sedláček"
"Vávra";26100;26499;"Příbram";"Vávra"
"Vávra";26601;26801;"Beroun";"Vávra"
"Maroušek";26901;27199;"Rakovník";"Maroušek"
"Maroušek";27201;27499;"Kladno";"Maroušek"
"Maroušek";27601;27899;"Mělník";"Maroušek"
"Sedláček";28000;28299;"Kolín";"Sedláček"
"Sedláček";28401;28699;"Kutná Hora";"Sedláček"
"Sedláček";28801;29099;"Nymburk";"Sedláček"
"Polívka";29301;29999;"Mladá Boleslav";"Polívka"
"Vávra";30000;33000;"Plzeň-město";"Vávra"
"Vávra";33001;33165;"Plzeň-sever";"Vávra"
"Vávra";33201;33699;"Plzeň-jih";"Vávra"
"Vávra";33701;33845;"Rokycany";"Vávra"
"Vávra";33901;34399;"Klatovy";"Vávra"
"Vávra";34401;34699;"Domažlice";"Vávra"
"Vávra";34701;34999;"Tachov";"Vávra"
"Vávra";35001;35599;"Cheb";"Vávra"
"Vávra";35600;35899;"Sokolov";"Vávra"
"Vávra";36001;36472;"Karlovy Vary";"Vávra"
"Vávra";37001;37599;"České Budějovice";"Vávra"
"Sedláček";37701;38099;"Jindřichův Hradec";"Sedláček"
"Vávra";38101;38293;"Český Krumlov";"Vávra"
"Vávra";38301;38599;"Prachatice";"Vávra"
"Vávra";38601;38999;"Strakonice";"Vávra"
"Sedláček";39001;39299;"Tábor";"Sedláček"
"Sedláček";39301;39699;"Pelhřimov";"Sedláček"
"Vávra";39701;39999;"Písek";"Vávra"
"Polívka";40000;40340;"Ústí nad Labem";"Polívka"
"Polívka";40501;40899;"Děčín";"Polívka"
"Polívka";41001;41399;"Litoměřice";"Polívka"
"Polívka";41500;41999;"Teplice";"Polívka"
"Vávra";43001;43199;"Chomutov";"Vávra"
"Polívka";43200;43790;"Most";"Polívka"
"Maroušek";43801;44199;"Louny";"Maroušek"
"Polívka";46001;46499;"Liberec";"Polívka"
"Polívka";46601;46871;"Jablonec nad Nisou";"Polívka"
"Polívka";47001;47399;"Česká Lípa";"Polívka"
"Polívka";50001;50499;"Hradec Králové";"Polívka"
"Polívka";50601;50999;"Jičín";"Polívka"
"Polívka";51101;51480;"Semily";"Polívka"
"Polívka";51601;51899;"Rychnov nad Kněžnou";"Polívka"
"Holub";53001;53700;"Pardubice";"Holub"
"Sedláček";53701;53976;"Chrudim";"Sedláček"
"Polívka";54101;54477;"Trutnov";"Polívka"
"Polívka";54701;55225;"Náchod";"Polívka"
"Holub";56000;56699;"Ústí nad Orlicí";"Holub"
"Holub";56801;57299;"Svitavy";"Holub"
"Sedláček";58001;58599;"Havlíčkův Brod";"Sedláček"
"Sedláček";58600;58999;"Jihlava";"Sedláček"
"Sedláček";59101;59599;"Žďár nad Sázavou";"Sedláček"
"Sedláček";60001;66399;"Brno-město";"Sedláček"
"Sedláček";66401;66799;"Brno-venkov";"Sedláček"
"Holub";66901;67299;"Znojmo";"Holub"
"Sedláček";67401;67799;"Třebíč";"Sedláček"
"Holub";67801;68099;"Blansko";"Holub"
"Holub";68201;68599;"Vyškov";"Holub"
"Holub";68600;68999;"Uherské Hradiště";"Holub"
"Holub";69000;69499;"Břeclav";"Holub"
"Holub";69500;69999;"Hodonín";"Holub"
"Ranocha";70000;73299;"Ostrava-město";"Ranocha"
"Ranocha";73301;73799;"Karviná";"Ranocha"
"Ranocha";73801;73998;"Frýdek-Místek";"Ranocha"
"Ranocha";74101;74499;"Nový Jičín";"Ranocha"
"Ranocha";74601;74999;"Opava";"Ranocha"
"Holub";75000;75368;"Přerov";"Holub"
"Holub";75501;75999;"Vsetín";"Holub"
"Holub";76000;76699;"Zlín";"Holub"
"Holub";76701;76999;"Kroměříž";"Holub"
"Ranocha";77000;78599;"Olomouc";"Ranocha"
"Ranocha";78701;78991;"Šumperk";"Ranocha"
"Ranocha";79001;79084;"Jeseník";"Ranocha"
"Ranocha";79201;79599;"Bruntál";"Ranocha"
"Holub";79600;79862;"Prostějov";"Holub"
';
		$rep_arr = explode("\n", $rep_csv);
		foreach ($rep_arr as $rep_line) {
			$data = explode(';', $rep_line);
			$data[0] = str_replace('"', '', $data[0]);
			$data[3] = str_replace('"', '', $data[3]);
			$rep = $this->Rep->find('first', array(
				'conditions' => array('last_name' => $data[0])
			)); 
			$rep_area = array(
				'RepArea' => array(
					'rep_id' => $rep['Rep']['id'],
					'start_zip' => $data[1],
					'end_zip' => $data[2],
					'area' => $data[3]
				)
			);
			unset($this->Rep->RepArea->id);
			if (!$this->Rep->RepArea->save($rep_area)) {
				debug($rep_area);
				die('na tomhle to spadlo');
			}
		}
		
		die('hotovo');
	}*/
}
?>