<?

class AttributesSubproductsController extends AppController {

	var $name = 'AttributesSubproducts';

	

	function admin_edit($id) {

		if (!$id && empty($this->data)) {

			$this->Session->setFlash('Neexistující atribut.');

			$this->redirect(array('action'=>'index'), null, true);

		}

		if (!empty($this->data)) {

			// musim upravit cenu u vsech vztahu atribut - subprodukt pro dany produkt

			$attributes_subproducts = $this->AttributesSubproduct->find('all', array(

				'conditions' => array(

					'Subproduct.product_id' => $this->data['Subproduct']['product_id'],

					'Attribute.id' => $this->data['Attribute']['id']

				),

				'contain' => array(

					'Subproduct',

					'Attribute'

				)

			));

			$saved = true;

			foreach ($attributes_subproducts as $attributes_subproduct) {

				$attributes_subproduct['AttributesSubproduct']['additional_price'] = $this->data['AttributesSubproduct']['additional_price'];

				unset($this->AttributesSubproduct->id);

				$saved = $saved && $this->AttributesSubproduct->save($attributes_subproduct);

			}

			if ($saved) {

				$this->Session->setFlash('Atribut byl uložen.');

				$this->redirect(array('controller' => 'products', 'action'=>'attributes_list', $this->data['Subproduct']['product_id'], $this->AttributesSubproduct->Subproduct->optionFilter($this->params['pass'])	), null, true);

			} else {

				$this->Session->setFlash('Atribut nemohl být uložen.');

			}

		}

		if (empty($this->data)) {

			$this->data = $this->AttributesSubproduct->read(null, $id);

		}

	}

	

}

?>