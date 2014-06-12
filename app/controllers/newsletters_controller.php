<?
class NewslettersController extends AppController{
	var $name = 'Newsletters';

	var $scaffold = 'admin';

	/*
	 * @description						Vytvoření nového newsletteru.
	 */
	function admin_add(){
		if ( isset($this->data) ){
			if ( $this->Newsletter->save($this->data) ){
				$this->Session->setFlash('Newsletter byl vytvořen, můžete začít přidávat produkty.');
				$this->redirect(array('controller' => 'newsletters', 'action' => 'edit', $this->Newsletter->id), null, true);
			} else {
				$this->Session->setFlash('Newsletter nelze uložit, formulář obsahuje chyby.');
			}
		}
	}

	/*
	 * @description						Umoznuje pracovat s newsletterem, pridat a odebrat produkty.
	 */
	function admin_edit($id = null){
		if ( !isset($id) ){
			$this->Session->setFlash('Zkoušíte editovat neexistující newsletter.');
			$this->redirect(array('controller' => 'newsletters', 'action' => 'index'), null, true);
		}
		
		if ( isset($this->data) ){
			// osetrim si, jestli se nehleda nejaky produkt
			if ( isset($this->data['Newsletter']['product_query']) ){
				$found_products = $this->Newsletter->search_products($this->data['Newsletter']['product_query']);
				$this->set('found_products', $found_products);
			}
			
			if ( isset($this->data['Newsletter']['save_data']) ){
				unset($this->data['Newsletter']['save_data']);
				$this->Session->setFlash('Newsletter se nepodařilo uložit.');
				if ( $this->Newsletter->save($this->data, false) ){
					$this->Session->setFlash('Newsletter byl upraven.');
				}
				$this->redirect(array('controller' => 'newsletters', 'action' => 'edit', $id));
			}
		}
		// musim si nacist vsechny informace do dat
		$contain = array(
			'Product' => array(
				'fields' => array('id', 'name', 'price', 'url'),
			)
		);
		$this->Newsletter->contain($contain);
		$this->data = $this->Newsletter->read(null, $id);
	}

	/*
	 * @description						Zobrazi seznam newsletteru.
	 */
	function admin_index(){
		
	}

	function admin_fill_recips(){
		$this->Newsletter->fill_recipients(6);
		die('dokonceno');
	}
	
	/*
	 * @description						Prida produkt do newsletteru.
	 */
	function admin_product_add($id){
		$data = array(
			'NewslettersProduct' => array(
				'product_id' => $this->params['named']['product_id'],
				'newsletter_id' => $id
			)
		);

		$this->Session->setFlash('Produkt se nepodailo vložit, zkuste to znovu prosím.');
		if ( $this->Newsletter->NewslettersProduct->save($data) ){
			$this->Session->setFlash('Produkt byl vložen do newsletteru.');
		}
		$this->redirect(array('controller' => 'newsletters', 'action' => 'edit', $id));
	}

	/*
	 * @description						Vymaze z newsletteru produkt.
	 */
	function admin_product_delete($id){
		$this->Session->setFlash('Odstranění produktu se nezdařilo, zkuste to prosím znovu.');
		if ( $this->Newsletter->NewslettersProduct->deleteAll(array('product_id' => $this->params['named']['product_id'], 'newsletter_id' => $id)) ){
			$this->Session->setFlash('Produkt byl odstraněn.');
		}
		$this->redirect(array('controller' => 'newsletters', 'action' => 'edit', $id));
	}

	/*
	 * @description						Rozesle newsletter zakaznikum.
	 */
	function admin_send($id){
		$this->Newsletter->send($id);
		die('done');
	}
	
	function send85698($id){
		$this->Newsletter->send($id);
		die('done');
	}
	
	function admin_transform(){
		$scs = $this->Newsletter->CustomersNewsletter->find('all', array(
			'conditions' => array(
				'newsletter_id' => 5,
				'sent' => 1
			),
			'contain' => array()
		));

		foreach ( $scs as $sc ){
			$uc = $this->Newsletter->CustomersNewsletter->find('first', array(
				'conditions' => array(
					'newsletter_id' => 5,
					'sent' => 0,
					'customer_id' => $sc['CustomersNewsletter']['customer_id']
				),
				'contain' => array()
			));

			if ( !empty($uc) ){
				$uc['CustomersNewsletter']['sent'] = '1';
				$this->Newsletter->CustomersNewsletter->id = $uc['CustomersNewsletter']['id'];
				$this->Newsletter->CustomersNewsletter->save($uc);
	
				
				$this->Newsletter->CustomersNewsletter->delete($sc['CustomersNewsletter']['id'], false);
			}
		}
		die();
	}
	
	/*
	 * @description						Zobrazi newsletter zakaznikovi.
	 */
	function view($id){
		$this->Newsletter->recursive = 2;
		$this->Newsletter->Product->unbindModel(array(
			'hasAndBelongsToMany' => array('Cart', 'Flag', 'Category'),
			'hasMany' => array('Subproduct', 'CartsProduct', 'Comment'),
			'belongsTo' => array('TaxClass', 'Availability')
		));
		
		$this->Newsletter->unbindModel(array(
			'hasMany' => array('NewslettersProduct')
		));
		$newsletter = $this->Newsletter->read(null, $id);
		$this->layout = 'newsletter';
		if ( !empty($newsletter) ){
			$this->set('newsletter', $newsletter);
		} else {
			// nplatny newsletter,
			// zakazu zobrazeni
			die('here');
		}
	}
}
?>