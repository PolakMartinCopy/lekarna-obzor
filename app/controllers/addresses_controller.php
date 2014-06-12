<?
class AddressesController extends AppController{
	var $name = 'Addresses';
	
	function admin_delete($id){
		$this->Address->recursive = -1;
		if ( $address = $this->Address->read(null, $id) ){
			if ( $this->Address->delete($id) ){
				$this->Session->setFlash('Adresa byla smazána.');
				$this->redirect(array('controller' => 'customers', 'action' => 'view',$address['Address']['customer_id']), null, true);
			} else {
				$this->Session->setFlash('Smazání adresy se nezdařilo, zkuste to znovu prosím.');
				$this->redirect(array('controller' => 'customers', 'action' => 'view',$address['Address']['customer_id']), null, true);
			}
		} else {
			$this->Session->setFlash('Neexistující adresa.');
			$this->redirect(array('controller' => 'customers', 'action' => 'view',$address['Address']['customer_id']), null, true);
		}
	}
}
?>