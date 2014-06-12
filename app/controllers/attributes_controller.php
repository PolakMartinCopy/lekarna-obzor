<?php
class AttributesController extends AppController {

	var $name = 'Attributes';
	var $helpers = array('Html', 'Form', 'Javascript' );

	var $paginate = array(

			'Attribute' => array(

				'limit' => 25,

				'order' => array(

					'Attribute.option_id' => 'asc',

					'Attribute.sort_order' => 'asc',

				),

				'fields' => array(

					'`Option`.`name` as `option_name`',

					'Attribute.*',

					'Option.*',

				)

			)

		);

	function admin_index() {
		$this->Attributes->recursive = -1;
		
		$options = $this->Attribute->Option->find('all', array(
			'contain' => array()
		));
		$this->set('options', $options);
		
		if (isset($this->params['named']['option_id'])) {
			$script = '
<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript">
// when the entire document has loaded, run the code inside this function
$(document).ready(function(){
// Wow! .. One line of code to make the unordered list drag/sortable!
$(\'#attribute_table tbody\').sortable().disableSelection();
});
</script>
			';
		
			$this->set('script', $script);
			$this->paginate['Attribute']['limit'] = 1000;
			$this->paginate['Attribute']['conditions'] = array('option_id' => $this->params['named']['option_id']);
			
			if (isset($this->data)) {
				$order = 1;
				foreach ($this->data['Attribute'] as $attribute_id) {
					$save = array(
						'id' => $attribute_id,
						'sort_order' => $order
					);
					$this->Attribute->save($save);
					$order++;
				}
				$this->Session->setFlash('Úpravy byly provedeny.');
				$this->redirect(array('controller' => 'attributes', 'action' => 'index', 'option_id' => $this->params['named']['option_id']));
			}
		}
		$this->set('attributes', $this->paginate('Attribute'));
	}

	function admin_add() {
		$this->set('options_options', $this->Attribute->Option->find('list'));
		if ( !empty($this->data) ) {
			// hledam jestli v databazi uz neni takova hodnota VALUE
			// pokud je, mam ji ulozenou
			$attribute = $this->Attribute->find(array('value' => $this->data['Attribute']['value'], 'option_id' => $this->data['Attribute']['option_id']));

			// testuju, jestli jsem nasel vkladany atribut uz v db
			if (empty($attribute)){
				// nenacetl, vlozim novou value
				//zkontroluju, jestli jsou vlozena data ve formulari validni
				$this->Attribute->set($this->data);
				if ($this->Attribute->validates()) {
					//pred vlozenim musim nastavit atribut sort_order na maximum(sort_order) + 1 pro dany option
					$max_sort_order = $this->Attribute->find('first', array(
						'conditions' => array('option_id' => $this->data['Attribute']['option_id']),
						'contain' => array(),
						'fields' => array('MAX(sort_order) as max_sort_order')
					));
					$this->data['Attribute']['sort_order'] = $max_sort_order[0]['max_sort_order'] + 1;
					$this->Attribute->save($this->data);
					$this->Session->setFlash('Atribut byl vytvořen');
					$this->redirect(array('controller' => 'attributes', 'action' => 'index'));
				} else {
					$this->Session->setFlash('Atribut se nepodařilo vytvořit, opakujte akci');
				}
			} else{
				$this->Session->setFlash('Atribut se nepodařilo vytvořit. Zadaný atribut již v databázi existuje s ID = ' . $attribute['Attribute']['id']);
				$this->redirect(array('controller' => 'attributes', 'action' => 'index'));
			}
		}
	}

	function admin_edit($id = null) {

			$this->set('options_options', $this->Attribute->Option->find('list'));

		if (!$id) {

			$this->Session->setFlash('Neexistující atribut.');

			$this->redirect(array('action'=>'index'), null, true);

		} else {

			$this->set('id', $id);

			

		}

		if (!empty($this->data)) {

			if ($this->Attribute->save($this->data)) {

				$this->Session->setFlash('Atribut byl uložen.');

				$this->redirect(array('action'=>'index'), null, true);

			} else {

				$this->Session->setFlash('Atribut nemohl být uložen, vyplňte prosím správně všechna pole.');

			}

		} else {

			$this->data = $this->Attribute->read(null, $id);

		}

	}

	

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující atribut.');
			$this->redirect(array('action'=>'index'), null, true);
		}

		$result = $this->Attribute->query("SELECT id FROM subproducts WHERE attribute_id = '" . $id . "'");
		$rows = $this->Attribute->getNumRows();
		if ( $rows != 0 ){
			$this->Session->setFlash('Některé produkty mají tento atribut přiřazen, proto jej nelze vymazat.');
			$this->redirect(array('action'=>'index'), null, true);
		} else {
			$this->Attribute->recursive = -1;
			$data = $this->Attribute->read(null, $id);

			// smazu ATRIBUT
			if ( !$this->Attribute->delete($id) ){
				$this->Session->setFlash('Došlo k chybě, atribut č. ' . $id . ' nelze vymazat.');
				$this->redirect(array('action'=>'index'), null, true);
			}

			// po smazani ATRIBUTU si zkontroluju,
			// jestli je OPTION prirazen jeste nejakemu jinemu ATRIBUT
			// pokud neni, muzu ho vymazat
			if ( !$this->Attribute->hasAny(array('value_id' => $data['Attribute']['value_id'])) ){
				$this->Attribute->Value->delete($data['Attribute']['value_id']);
			}

			$this->Session->setFlash('Atribut č. ' . $id . ' byl smazán.');
			$this->redirect(array('action'=>'index'), null, true);
		}
	}


	function admin_move_up($id = null) {

		if (!$id) {

			$this->Session->setFlash('Neexistující atribut');

			$this->redirect(array('controller' => 'attributes', 'action' => 'index'));

		}

		

		// nactu si atribut, ktery budu posunovat

		$moved_attribute = $this->Attribute->find('first', array(

			'conditions' => array('id' => $id),

			'contain' => array()

		));

		// pokud ma presouvany atribut nejnizsi sort_order (== 1), tak nic nepresouvam

		if ($moved_attribute['Attribute']['sort_order'] == 1) {

			$this->Session->setFlash('Atribut nelze přesunout, je již na nejvrchnější pozici');

			$this->redirect(array('controller' => 'attributes', 'action' => 'index'));

		} else {

			// nactu atribut, se kterym chci zadany prohodit

			$pre_attribute = $this->Attribute->find('first', array(

				'conditions' => array('sort_order' => $moved_attribute['Attribute']['sort_order'] - 1, 'option_id' => $moved_attribute['Attribute']['option_id']),

				'contain' => array()

			));



			// nastavim si nove hodnoty sort_order

			$moved_attribute['Attribute']['sort_order'] = $moved_attribute['Attribute']['sort_order'] - 1;

			$pre_attribute['Attribute']['sort_order'] = $pre_attribute['Attribute']['sort_order'] + 1;

			

			$this->Attribute->save($moved_attribute);

			$this->Attribute->save($pre_attribute);

			

			$this->Session->setFlash('Atribut byl přesunut');

			$this->redirect(array('controller' => 'attributes', 'action' => 'index'));

		}

	}

	

	function admin_move_down($id = null) {

		if (!$id) {

			$this->Session->setFlash('Neexistující atribut');

			$this->redirect(array('controller' => 'attributes', 'action' => 'index'));

		}

		

		// nactu si atribut, ktery chci posouvat

		$moved_attribute = $this->Attribute->find('first', array(

			'conditions' => array('id' => $id),

			'contain' => array()

		));

		

		// nactu si atribut, ktery je za nim

		$post_attribute = $this->Attribute->find('first', array(

			'conditions' => array('sort_order' => $moved_attribute['Attribute']['sort_order'] + 1, 'option_id' => $moved_attribute['Attribute']['option_id']),

			'contain' => array()

		));

		// pokud neexistuje post_attribute, moved_attribute je posledni a proto nemuze byt presunut dal dolu

		if (empty($post_attribute)) {

			$this->Session->setFlash('Atribut nelze přesunout, je již na nejnižší pozici');

			$this->redirect(array('controller' => 'attributes', 'action' => 'index'));

		} else {

			// nastavim si nove hodnoty sort_order

			$moved_attribute['Attribute']['sort_order'] = $moved_attribute['Attribute']['sort_order'] + 1;

			$post_attribute['Attribute']['sort_order'] = $post_attribute['Attribute']['sort_order'] - 1;

			

			$this->Attribute->save($moved_attribute);

			$this->Attribute->save($post_attribute);

			

			$this->Session->setFlash('Atribut byl přesunut');

			$this->redirect(array('controller' => 'attributes', 'action' => 'index'));

		}

	}

	

}
?>