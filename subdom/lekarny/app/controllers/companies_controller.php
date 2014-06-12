<?
class CompaniesController extends AppController {

	var $name = 'Companies';

	function admin_authorize($id = null){
		if ( empty($id) ){
			$this->Session->setFlash('Neexistující zákazník.');
			$this->redirect(array('controller' => 'companies', 'action' => 'index'), null, true);
		}
		
		// nactu si info o spolecnosti
		$company = $this->Company->find('first', array(
			'conditions' => array(
				'Company.id' => $id
			),
			'contain' => array(),
			'fields' => array('id', 'active', 'login', 'password', 'person_email', 'person_first_name', 'person_last_name', 'notified')
		));
		
		// pokud uz je zakaznik schvalen, nepotrebuju ho znovu schvalovat
		if ( $company['Company']['active'] == 1 ){
			$this->Session->setFlash('Zákazník již schválen je.');
			$this->redirect(array('controller' => 'companies', 'action' => 'index'), null, true);
		}
		
		// zakaznik schvalen neni, musim ho schvalit
		// zkontroluju, zda neschvaluju zablokovaneho zakaznika,
		// ktery uz byl notifikovan o vytvoreni jeho uctu
		$notify_authorization = false;
		if ( $company['Company']['notified'] != '1' ){
			// jeste nebyl schvalen, takze mu musim poslat email
			$notify_authorization = true;
			// heslo zahashuju
			$this->data['Company']['password'] = md5($company['Company']['password']);
			// nastavim priznak, ze byl notifikovan mailem
			$this->data['Company']['notified'] = '1';

		}
		
		$this->data['Company']['active'] = 1;

		// ulozim potrebna data
		if ( !$this->Company->save($this->data, false) ){
			$this->Session->setFlash('Nepodařilo se uložit zákazníkova data.');
			$this->redirect(array('controller' => 'companies', 'action' => 'index'), null, true);
		}
		
		if ( $notify_authorization ){
			// notifikuju o vytvoreni prihlasovacich udaju
			// a schvaleni registrace
			if ( !$this->Company->send_notification($company) ){
				$this->Session->setFlash('Nepodařilo se odeslat notifikační email, zkuste to prosím znovu.');
				$this->redirect(array('controller' => 'companies', 'action' => 'index'), null, true);
			}
		}

		$this->Session->setFlash('Zákazník byl schválen.');
		$this->redirect(array('controller' => 'companies', 'action' => 'index'), null, true);
	}
	
	function admin_block($id) {
		// zakaznik je ve stavu zablokovany, pokud mu byla poslana notifikace - notified = 1, a soucasne nebyl schvalen - active = 0
		// nactu si info o spolecnosti
		$company = $this->Company->find('first', array(
			'conditions' => array(
				'Company.id' => $id
			),
			'contain' => array(),
			'fields' => array('id', 'password', 'person_email', 'person_first_name', 'person_last_name')
		));

		// chci si poskladat vysledny flash
		$flash = '';
		
		// nastavim si notified na 1
		$company['Company']['notified'] = 1;
		// active nastavim na 0
		$company['Company']['active'] = 0;
		// updatuju zakaznika
		if ($this->Company->save($company)) {
			$flash .= 'Zákazník byl zablokován';
			// poslu mail, ze zadost o schvaleni byla zamitnuta
			if ($this->Company->send_rejection($company)) {
				$flash .= 'Informace o zamítnutí byla zaslána na emailovou adresu zákazníka.<br/>';
			} else {
				$flash .= 'Informaci o zamítnutí se nepodařilo zaslat na emailovou adresu zákazníka.</br>';
			}
		} else {
			$flash .= 'Zákazníka se nepodařilo zablokovat, opakujte prosím akci';
		}
		$this->Session->setFlash($flash);
		$this->redirect(array('controller' => 'companies', 'action' => 'index'), null, true);
	}

	function admin_edit($id) {
		$this->layout = 'admin';
		if ( isset($this->data) ){
			// musim si otestovat, jestli nedochazi ke schvaleni zakaznika,
			// ktery jeste nebyl notifikovan
			$notified = true;
			if ( $this->data['Company']['notified'] == '0' && $this->data['Company']['active'] == '1' ){
				// jedna se o zakaznika ktery jeste nebyl notifikovan
				// a snazime se o schvaleni
				$notified = false;
				
				// abych ho mohl schvalit, a poslal se mu email,
				// musim nechat active na 0
				$this->data['Company']['active'] = 0;
			}
			
			// otestuju jestli chce menit login a heslo,
			// pokud ne, vymazu data
			if ( empty($this->data['Company']['password']) ){
				unset($this->data['Company']['password']);
			} else {
				$this->data['Company']['password'] = md5($this->data['Company']['password']);
			}
			
			if ( $this->Company->save($this->data) ){
				// data jsou ulozena, otestuju si, zda nebyla vyzadano schvaleni zakaznika
				// ktery jeste nebyl notifikovan, presmeruju si na jeho schvaleni,
				// jinak se jedna o obycejnou editaci
				
				if ( !$notified ){
					$this->redirect(array('admin' => true, 'controller' => 'companies', 'action' => 'authorize', $id), null, true);
				}
				
				$this->Session->setFlash('Údaje o společnosti byly upraveny.');
				$this->redirect(array('controller' => 'companies', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Některá pole nebyla správně vyplněna, zkontrolujte prosím formulář.');
			}
		} else {
			$this->data = $this->Company->find('first', array(
				'conditions' => array(
					'Company.id' => $id
				)
			));
			unset($this->data['Company']['password']);
		}
	}
	
	function admin_index(){
		$this->layout = 'admin';

		$conditions = array();
		
		$contain = array();
		
		if ( isset($this->params['named']['authorized']) && $this->params['named']['authorized'] == 0 ){
			$conditions['Company.active'] = 0;
		}
		
		$companies = $this->Company->find('all', array(
			'conditions' => $conditions,
			'contain' => $contain,
			'order' => array(
				'Company.name' => 'asc'
			)
		));
		
		$this->set('companies', $companies);
		
		
		
	}

	function admin_delete($id) {
		if ($this->Company->del($id)) {
			$this->Session->setFlash('Společnost byla smazána.');
		} else {
			$this->Session->setFlash('Společnost se nepodařilo smazat, opakujte prosím akci.');
		}
		$this->redirect(array('controller' => 'companies', 'action' => 'index'));
	}
	
	function add(){
		$this->layout = 'users';
		if ( isset($this->data) ){
			// natvrdo nadefinuju ze spolecnost neni schvalena
			$this->data['Company']['active'] = 0;

			// kvuli moznosti pridat si login a heslo samostatne
			// nebo nechat si generovat, musim testovat, zda jsou tato pole
			// prazdna
			
			// indikace zda mazat automaticky zvoleny login
			$clear_login = false;
			
			// indikace zda probehl test hesel
			$pass_ok = true;
			
			if ( empty($this->data['Company']['login']) ){
				// vygeneruju si login
				$this->data['Company']['login'] = $this->Company->generate_login();
				// nastavim priznak, ze pri selhani validace ostatnich poli ho mam vymazat,
				// protoze je generovan automaticky, jinak zustane vyplneny, protoze si ho
				// zvolil registrujici
				$clear_login = true;
			}
			
			// zustane-li prazdne heslo, vygeneruju si ho
			if ( empty($this->data['Company']['password']) ){
				// vygeneruju si heslo
				$this->data['Company']['password'] = $this->Company->generate_password();
			} else {
				// pokud je heslo zadane, musim zkontrolovat, jestli souhlasi
				// i pole pro zopakovani hesla, pokud ne, musim vyvolat chybu
				if ( $this->data['Company']['password'] != $this->data['Company']['password_repeat'] ){
					// hesla se lisi, neni to validni
					$this->Company->invalidate('password', 'Hesla se od sebe navzájem liší, zvolte si prosím heslo znovu.');
					$pass_ok = false;
				}
			}
			
			// password reapeat zahazuju vzdycky, neuklada se, ani se nema zobrazovat
			unset($this->data['Company']['password_repeat']);
			
			if ( $pass_ok && $this->Company->save($this->data) ){
				//vypisu hlasku
				$this->Session->setFlash('Vaše registrace byla přijata.');
				//poslu mail s upozornenim na novou registraci
				$this->Company->notify_new_registration($this->data);
				//presmeruju
				$this->redirect(array('controller' => 'pages', 'action' => 'register_success'), null, true);
			} else {
				// pri selhani validace vzdy zrusim heslo
				$this->data['Company']['password'] = '';
				
				// pri selhani validace zrusim login v pripade ze byl generovany
				if ( $clear_login ){
					$this->data['Company']['login'] = '';
				}
				
				$this->Session->setFlash('Některá pole nebyla správně vyplněna, zkontrolujte prosím formulář.');
			}

		}
	}

	function users_access_change() {
		$this->layout = 'users';
		
		if ( isset($this->data) ){
			// osetrim ukladani dat
			
			// musim zjistit, zda login neni pouzivany a zda ma alespon 10 znaku
			// nejdrive delka
			$valid = true;
			if ( strlen($this->data['Company']['login']) < 10 ){
				$this->Company->invalidate('login', 'Login musí být sestaven minimálně z 10ti znaků.');
				$valid = false;
			} else {
				// kontroluju zda je unikatni
				$login_data = $this->Company->find('count', array(
					'conditions' => array(
						'login' => $this->data['Company']['login'],
						'NOT' => array(
							'id' => $this->Session->read('Company.id')
						)
					)
				));
				
				if ( $login_data > 0 ){
					// login uz se pouziva u jineho zakaznika
					$this->Company->invalidate('login', 'Tento login je již používán někým jiným, zvolte si prosím jiný login.');
					$valid = false;
				}
			}
			
			// osetrim zmenu hesla
			// zmena se dela tehdy, je-li vyplneno nektere z poli
			// a probehla spravne validace loginu
			if (
					(
					!empty($this->data['Company']['password'])
					OR !empty($this->data['Company']['new_password'])
					OR  !empty($this->data['Company']['new_password_retype'])
					)
					AND $valid
				){
				// nejdriv zjsituju, jestli je stare heslo zadane spravne
				$this->Company->contain();
				$company = $this->Company->read(array('id', 'password'), $this->Session->read('Company.id'));
				if ( $company['Company']['password'] != md5($this->data['Company']['password']) ){
					// zadane puvodni heslo je spatne
					$this->Company->invalidate('password', 'Původní heslo je zadáno špatně');
					$valid = false;
				} else {
					// heslo je dobre, musim overit, zda je nove heslo delsi nez 10 znaku
					if ( strlen($this->data['Company']['new_password']) < 10 ){
						$this->Company->invalidate('new_password', 'Nové heslo musí být sestaveno minimálně z 10ti znaků.');
						$valid = false;
					} else {
						// musim otestovat zda souhlasi password retype
						if ( $this->data['Company']['new_password'] != $this->data['Company']['new_password_retype'] ){
							$this->Company->invalidate('new_password', 'Pole pro nové heslo a pole pro zopakování nového hesla nesouhlasí.');
							$valid = false;
						}
					}
				}
			} else {
				// pokud zustanou pole pro hesla prazdna, chci menit jen login,
				// takze je musim odstranit z dat
				unset($this->data['Company']['password']);
				unset($this->data['Company']['new_password']);
				unset($this->data['Company']['new_password_retype']);
			}
			
			// vsechny testy jsou OK, muzu ulozit
			if ( $valid ){
				$this->Company->id = $this->Session->read('Company.id');
				
				// uklada se pouze nove zahashovane heslo a login
				$this->data['Company']['password'] = md5($this->data['Company']['new_password']);
				unset($this->data['Company']['new_password']);
				unset($this->data['Company']['new_password_retype']);
				
				if ( $this->Company->save($this->data, false) ){
					$this->Session->setFlash('Vaše přihlašovací údaje byly změneněny.');
					$this->redirect(array('users' => true, 'controller' => 'companies', 'action' => 'access_change'), null, true);
				}
			}
			
			$this->data['Company']['password'] = '';
			$this->data['Company']['new_password'] = '';
			$this->data['Company']['new_password_retype'] = '';
		} else {
			$this->data = $this->Company->read(null, $this->Session->read('Company.id'));
			unset($this->data['Company']['password']);
		}
	}
	
	function users_edit() {
		$this->layout = 'users';
		$id = $this->Session->read('Company.id');
		if ( isset($this->data) ){
			if ( $this->Company->save($this->data) ){
				$this->Session->setFlash('Vaše údaje byly upraveny.');
				$this->redirect('/users/companies/index', null, true);
				$this->redirect(array('users' => true, 'controller' => 'companies', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Některá pole nebyla správně vyplněna, zkontrolujte prosím formulář.');
			}
		} else {
			$this->data = $this->Company->find('first', array(
				'conditions' => array(
					'Company.id' => $id
				)
			));
		}
	}
	
	function users_index(){
		$this->layout = 'users';
		if ( $this->Session->check('Company.id') ){
			$company_id = $this->Session->read('Company.id');
		} else {
			$this->Session->setFlash('Vaše přihlášení bylo ukončeno po delší době nečinnosti, přihlašte se prosím znovu.');
			$this->redirect(array('users' => true, 'controller' => 'companies', 'action' => 'login'), null, true);
		}
		
		$company = $this->Company->find('first', array(
			'conditions' => array(
				'id' => $company_id
			),
			'contain' => array()
		));
		$this->set('company', $company);
	}

	function users_login(){
		$this->layout = 'users';
		if (  isset($this->data) ){
			$conditions = array('login' => $this->data['Company']['login']);
			$company = $this->Company->find('first', array(
				'conditions' => $conditions,
				'contain' => array()
			));

			if ( empty( $company ) ){
				$this->Session->setFlash('Neplatné uživatelské jméno!');
			} else {
				if ( $company['Company']['password'] != md5($this->data['Company']['password']) ){
					$this->Session->setFlash('Neplatné heslo!');
				} else {
					if ( $company['Company']['active'] != 1 && $company['Company']['notified'] == 0 ){
						$this->Session->setFlash('Vaše registrace ještě nebyla schválena, vyčkejte prosím, až Vám potvrdíme registraci emailem.');
					} elseif ($company['Company']['active'] == 0 && $company['Company']['notified'] == 1) {
						$this->Session->setFlash('Váš účet byl zablokován. Pro zjištění důvodu blokace nás prosím kontaktujte (viz níže).');
					} else {
						$this->Session->write('Company', $company['Company']);
						$this->redirect(array('users' => true, 'controller' => 'companies', 'action' => 'index'), null, true);
					}
				}
			}
		}
	}

	function users_logout(){
		$this->Session->destroy();
		$this->Session->setFlash('Byl(a) jste odhlášen(a).');
		$this->redirect('/', null, true);
	}

	function users_password_recovery() {
		$this->layout = 'users';
		
		if ( isset($this->data) ){
			if ( empty($this->data['Company']['person_email']) ){
				$this->Company->invalidate('email', 'Vyplňte prosím Vaši emailovou adresu, kterou jste použil(a) při registraci.');
			} else {
				// musim si najit, zda je nekdo pod takovym mailem registrovan
				$company = $this->Company->find('first', array(
					'conditions' => array(
						'person_email' => $this->data['Company']['person_email']
					),
					'fields' => array('id', 'person_email', 'person_first_name', 'person_last_name', 'login'),
					'contain' => array()
				));
				
				if ( !empty($company) ){
					$new_pass = $this->Company->generate_password();
					$this->Company->id = $company['Company']['id'];
					if ( $this->Company->save(array('password' => md5($new_pass)), false) ){
						$this->Company->notify_new_password($company, $new_pass);
						$this->Session->setFlash('Na Váš email ' . $this->data['Company']['email'] . ' bylo zasláno nové heslo do systému.');
						$this->redirect(array('users' => true, 'controller' => 'companies', 'action' => 'login'), null, true);
					}
				}
				$this->Session->setFlash('V systému nebyl nalezen uživatel s emailem ' . $this->data['Company']['person_email']);
			}
		}
	}

	function rep_index() {
		$this->layout = 'rep';
		
		$rep_id = $this->Session->read('Rep.id');
		
		Router::connectNamed(array('company_id', 'month', 'all'));
		
		App::import('Model', 'Rep');
		$this->Rep = &new Rep;

		$this->Rep->id = $rep_id;
		$rep = $this->Rep->read();
		
		if (!empty($rep['RepArea'])) {
			$area_conditions = array();
			foreach ($rep['RepArea'] as $area) {
				$area_conditions['OR'][] = array(
					'AND' => array(
						'delivery_postal_code >=' => $area['start_zip'],
						'delivery_postal_code <=' => $area['end_zip']
					)
				);
			}
			
			$companies = $this->Company->find('all', array(
				'conditions' => $area_conditions + array('active' => true),
				'contain' => array()
			));
		
			$this->set('companies', $companies);
		} else {
			$this->set('no_areas', true);
		}
	}
	
	function rep_search() {
		$this->layout = 'rep';
		
		$rep_id = $this->Session->read('Rep.id');
		
		Router::connectNamed(array('company_id'));
		
		App::import('Model', 'Rep');
		$this->Rep = &new Rep;

		$this->Rep->id = $rep_id;
		$rep = $this->Rep->read();
		
		if (!empty($rep['RepArea'])) {
			$area_conditions = array();
			foreach ($rep['RepArea'] as $area) {
				$area_conditions['OR'][] = array(
					'AND' => array(
						'delivery_postal_code >=' => $area['start_zip'],
						'delivery_postal_code <=' => $area['end_zip']
					)
				);
			}

			// je vyplnenej formular pro vyhledavani v zakaznicich
			if ( isset($this->data) ) {
				$search_conditions = array(
					'OR' => array(
						'Company.ico LIKE' => '%%' . $this->data['Company']['query'] . '%%',
						'OR' => array(
							'Company.person_first_name LIKE' => '%%' . $this->data['Company']['query'] . '%%',
							'Company.person_last_name LIKE' => '%%' . $this->data['Company']['query'] . '%%'
						),
						'Company.name LIKE' => '%%' . $this->data['Company']['query'] . '%%'
					)
				);
				
				$conditions = array('AND' => array(
					$area_conditions,
					$search_conditions,
					array('active' => true)
				));
				
				$companies = $this->Company->find('all', array(
					'conditions' => $conditions,
					'contain' => array()
				));
				$this->set('companies', $companies);
				$this->set('query', $this->data['Company']['query']);
			}
		} else {
			$this->set('no_areas', true);
		}
	}
}
?>