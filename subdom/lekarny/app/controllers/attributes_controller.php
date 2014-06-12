<?
class AttributesController extends AppController {
	var $name = 'Attributes';
	var $helpers = array('Html', 'Form', 'Javascript' );

	var $paginate = array(
			'Attribute' => array(
				'limit' => 25,
				'order' => array(
					'Attribute.id' => 'asc',
				),
				'fields' => array(
					'Attribute.*',
					'Option.*',
				)
			)
	);
	
	function admin_index() {
		$this->layout = 'admin';
		$this->Attributes->recursive = 0;
		$this->set('attributes', $this->paginate('Attribute'));
	}
	
	function admin_add() {
		$this->layout = 'admin';
		$this->set('options_options', $this->Attribute->Option->find('list'));
		if (!empty($this->data)) {
			//nejdriv se musim podivat, jestli uz nemam vkladanou value ve values a pokud jo, tak jestli uz neexistuje takovy vztah
			//option - value v attributes
			$this->Attribute->Value->set($this->data);
			//nejdriv zvaliduju pole pro nazev value (jestli neni prazdne)
			if ($this->Attribute->Value->validates()) {
				//najdu vsechny values, ktere maji stejne name jako vkladana
				$values = $this->Attribute->Value->find('all', array(
					'conditions' => array('name' => $this->data['Value']['name']),
				));
				//pokud najdu v attributes vkladanou kombinaci value_id, option_id, value nevkladam a nastavim value_id na tu,
				//ktera v databazi je. v nasledne validaci pri vkladani attribute mi to vyhodi chybu (unique)
				foreach ($values as $value) {
					$attribute = $this->Attribute->find('first', array(
						'conditions' => array('option_id' => $this->data['Attribute']['option_id'], 'value_id' => $value['Value']['id']),
						'recursive' => -1
					));
					if (!empty($attribute)) {
						$this->data['Attribute']['value_id'] = $value['Value']['id'];
						$break;
					}
				}
				//pokud neni nastaven atribut value_id, tak jsem v databazi kombinaci value - option nenasel a vkladana je nova
				//takze musim vlozit value a zjisit jeji id pred vlozenim do attributes
				if (!isset($this->data['Attribute']['value_id'])) {
					$this->Attribute->Value->save($this->data);
					$value_id = $this->Attribute->Value->id;
					$this->data['Attribute']['value_id'] = $value_id;
				}
				if ($this->Attribute->save($this->data)) {
					$this->Session->setFlash('Varianta byla uložena.');
					$this->redirect(array('action'=>'index'), null, true);
				} else {
					$this->Session->setFlash('Varianta nemohla být uložena, vyplňte prosím správně všechna pole.');
				}
			}
		}
	}
	
	function admin_edit($id = null) {
		$this->layout = 'admin';
		$this->set('id', $id);
		$this->set('options_options', $this->Attribute->Option->find('list'));

		//kdyz menim u value atribut name, musim se podivat, jestli ho nemenim na neco, co uz ve values je a co je svazano s timto option
		if (!empty($this->data)) {
			//nejdriv se musim podivat, jestli uz nemam vkladanou value ve values a pokud jo, tak jestli uz neexistuje takovy vztah
			//option - value v attributes
			$this->Attribute->Value->set($this->data);
			//nejdriv zvaliduju pole pro nazev value (jestli neni prazdne)
			if ($this->Attribute->Value->validates()) {
				//najdu vsechny values, ktere maji stejne name jako vkladana
				$values = $this->Attribute->Value->find('all', array(
					'conditions' => array('name' => $this->data['Value']['name']),
				));
				//pokud najdu v attributes vkladanou kombinaci value_id, option_id, value nevkladam a nastavim value_id na tu,
				//ktera v databazi je. v nasledne validaci pri vkladani attribute mi to vyhodi chybu (unique)
				$found = false;
				foreach ($values as $value) {
					$attribute = $this->Attribute->find('first', array(
						'conditions' => array('option_id' => $this->data['Attribute']['option_id'], 'value_id' => $value['Value']['id'], 'id !=' => $id),
						'recursive' => -1
					));
					//tady zjistim, jestli uz v databazi mam kombinaci option - value s novym name
					if (!empty($attribute)) {
						$found = true;
						break;
					}
				}
				if ($found) {
					$this->Session->setFlash('Variantu nelze změnit, tento vztah již v databázi existuje');
				} else {
					if ($this->Attribute->Value->save($this->data)) {
						$this->Session->setFlash('Varianta byla upravena');
						$this->redirect(array('action'=>'index'), null, true);
					} else {
						$this->Session->setFlash('Variantu se nepodařilo upravit');
					}
				}
			}
		} else {
			$this->Attribute->id = $id;
			$this->Attribute->contain();
			$attribute = $this->Attribute->read();
			$this->Attribute->Value->id = $attribute['Attribute']['value_id'];
			$value = $this->Attribute->Value->read();
			$this->data = $attribute;
			$this->data['Value']['name'] = $value['Value']['name'];
			$this->data['Value']['id'] = $value['Value']['id'];
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
			$this->redirect(array('controller' => 'products', 'action'=>'list_products_with_attribute', 'attribute_id' => $id), null, true);
		} else {
			$this->Attribute->recursive = -1;
			$data = $this->Attribute->read(null, $id);

			// smazu ATRIBUT
			if ( !$this->Attribute->del($id) ){
				$this->Session->setFlash('Došlo k chybě, atribut č. ' . $id . ' nelze vymazat.');
				$this->redirect(array('action'=>'index'), null, true);
			}

			// po smazani ATRIBUTU si zkontroluju,
			// jestli je OPTION prirazen jeste nejakemu jinemu ATRIBUT
			// pokud neni, muzu ho vymazat
			if ( !$this->Attribute->hasAny(array('value_id' => $data['Attribute']['value_id'])) ){
				$this->Attribute->Value->del($data['Attribute']['value_id']);
			}

			$this->Session->setFlash('Atribut č. ' . $id . ' byl smazán.');
			$this->redirect(array('action'=>'index'), null, true);
		}
	}
	
	function get_attribute($option_id, $value) {
		$attribute = $this->Attribute->find('first', array(
			'conditions' => array('option_id' => $option_id, 'value' => $value),
			'contain' => array(),
			'fields' => array('id')
		));
		
		return $attribute;
	}
}
?>