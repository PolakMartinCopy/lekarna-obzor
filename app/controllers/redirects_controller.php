<?

class RedirectsController extends AppController {



	var $name = 'Redirects';



	function admin_index(){

		$redirects = $this->Redirect->find('all');

		$this->set('redirects', $redirects);

	}

	

	function admin_add(){

		if ( isset($this->data) ){

			// ostripuju si http www

			$this->data['Redirect']['request_uri'] = str_replace('http://www.e-kola.cz', '', $this->data['Redirect']['request_uri']);

			$this->data['Redirect']['target_uri'] = str_replace('http://www.e-kola.cz', '', $this->data['Redirect']['target_uri']);

			

			if ($this->data['Redirect']['request_uri'] == $this->data['Redirect']['target_uri']) {

				$this->Session->setFlash('Zdrojová a cílová adresa přesměrování jsou stejné, přesměrování nelze uložit');

				$this->redirect(array('controller' => 'redirects', 'action' => 'index'), null, true);

			}

			

			// musim si najit, jestli uz podobny redirect je zavedeny a upravit presmerovani podle toho

			// hledam zda uz redirect pro toto uri neexistuje

			$r = $this->Redirect->find('all', array(

				'conditions' => array(

					'request_uri' => $this->data['Redirect']['request_uri']

				)

			));

			if ( !empty($r) ){

				$this->Session->setFlash('Presmerovani z <em>' . $this->data['Redirect']['request_uri'] . '</em> už existuje, nelze jej proto zavést znovu.');

				$this->redirect(array('controller' => 'redirects', 'action' => 'index', 'admin' => true), null, true);

			}

			

			// hledam zda neexistuji redirecty ktere maji za cil stranku, ktera uz je presmerovana jinam

			// pokud ano, musim je upravit aby se zbytecne neredirectovalo nekolikrat po sobe

			// musim zmenit cil podle 

			$r = $this->Redirect->find('all', array(

				'conditions' => array(

					'request_uri' => $this->data['Redirect']['target_uri']

				)

			));

			if ( !empty($r) ){

				$this->Session->setFlash('Cilova stranka presmerovani <em>' . $this->data['Redirect']['target_uri'] . '</em> je jiz presmerovana na jinou stranku, je potreba upravit skript, aby se s tim umel vyporadat.');

				$this->redirect(array('controller' => 'redirects', 'action' => 'index', 'admin' => true), null, true);

			} 

			

			// hledam, zda neexistuji redirecty ktere jsou cilem jineho redirectu

			// pokud ano, musim upravit cile techto redirectu, aby se zbytecne neredirectovalo nekolikrat po sobe

			$r = $this->Redirect->find('all', array(

				'conditions' => array(

					'target_uri' => $this->data['Redirect']['request_uri']

				)

			));

			if ( !empty($r) ){

				foreach ($r as $item) {

					$item['Redirect']['target_uri'] = $this->data['Redirect']['target_uri'];

					$this->Redirect->save($item);

				}

				$this->Redirect->create();

			}

			

			if ( $this->Redirect->save($this->data) ){

				$this->Session->setFlash('Přesměrování bylo uloženo.');

				$this->redirect(array('controller' => 'redirects', 'action' => 'index', 'admin' => true), null, true);

			}

		}

	}

	

	function admin_edit($id){

		if ( isset($this->data) ){

			// ostripuju si http www

			$this->data['Redirect']['request_uri'] = str_replace('http://www.e-kola.cz', '', $this->data['Redirect']['request_uri']);

			$this->data['Redirect']['target_uri'] = str_replace('http://www.e-kola.cz', '', $this->data['Redirect']['target_uri']);

			if ( $this->Redirect->save($this->data) ){

				$this->Session->setFlash('Přesměrování bylo upraveno.');

				$this->redirect(array('controller' => 'redirects', 'action' => 'index', 'admin' => true), null, true);

			}

		}

		$this->data = $this->Redirect->find('first', array('conditions' => array('id' => $id)));

	}

	

	function admin_delete($id){

		if ( $this->Redirect->delete($id) ){

			$this->Session->setFlash('Přesměrování bylo vymazáno.');

			$this->redirect(array('controller' => 'redirects', 'action' => 'index', 'admin' => true), null, true);

		}

		$this->Session->setFlash('Přesměrování se nezdařilo vymazat, zkuste to prosím znovu.');

		$this->redirect(array('controller' => 'redirects', 'action' => 'index', 'admin' => true), null, true);

	}

}

?>